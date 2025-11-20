# --- ETAPA 1: Dependencias PHP (Composer) ---
# IMPORTANTE: Esta etapa debe ir PRIMERO para que vendor/livewire/flux exista antes de compilar assets
FROM composer:2 AS composer_builder
WORKDIR /app

# Copiar archivos necesarios para Composer
COPY composer.json composer.lock ./

# Instalar dependencias de PHP (sin --dev para producción)
RUN composer install --no-dev --prefer-dist --optimize-autoloader --no-interaction --no-scripts

# Copiar el resto del código
COPY . .

# Ejecutar scripts post-install
RUN composer dump-autoload --optimize

# --- ETAPA 2: Build de Node (Assets) ---
# Compila CSS/JS con Vite. Necesita vendor/ de la etapa anterior para acceder a flux.css
FROM node:22-alpine AS node_builder
WORKDIR /app

# Instalar dependencias necesarias para compilaciones nativas (Tailwind v4)
RUN apk add --no-cache python3 make g++

# Copiar código con dependencias de Composer ya instaladas
COPY --from=composer_builder /app /app

# Instalar dependencias de Node (incluyendo opcionales)
RUN npm ci --include=optional

# Construir los assets - Genera public/build/ con CSS/JS compilados
RUN npm run build

# --- ETAPA 3: Imagen Final ---
# Esta es la imagen que se sube a Docker Hub como alejandro240/frases-animadas:latest
FROM php:8.4-fpm-alpine

# Paquetes + extensiones PHP necesarias para Laravel + SQLite
RUN set -eux; \
    # 1. Actualizar índice de repositorios
    apk update; \
    # 2. Instalar dependencias de construcción y extensiones de PHP
    apk add --no-cache --virtual .build-deps $PHPIZE_DEPS icu-dev sqlite-dev oniguruma-dev libzip-dev; \
    apk add --no-cache icu sqlite-libs libzip git unzip; \
    docker-php-ext-configure intl; \
    docker-php-ext-install -j"$(nproc)" pdo_sqlite bcmath intl mbstring zip; \
    docker-php-ext-enable opcache; \
    # 3. Eliminar dependencias de construcción para reducir imagen (mantener libzip)
    apk del .build-deps; \
    apk add --no-cache libzip

# Definir directorio de trabajo
WORKDIR /var/www/html

# COPIAR TODO: Código + vendor/ + node_modules/ + public/build/ (assets compilados)
# Esta imagen contendrá TODO lo necesario para ejecutar la aplicación
COPY --from=node_builder /app /var/www/html

# Ajustar permisos y generar clave de aplicación
# Mantener usuario ROOT temporalmente para permisos y generación
RUN if [ ! -f .env ]; then cp .env.example .env; fi \
    && php artisan key:generate \
    && php artisan config:cache \
    && php artisan route:cache \
    && php artisan view:cache \
    && touch /var/www/html/database/database.sqlite \
    && php artisan migrate --force

# Ajustar permisos para Laravel (storage, bootstrap/cache)
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/storage \
    && chmod -R 775 /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/database

# Usuario no root por seguridad (www-data es el usuario por defecto de php-fpm)
USER www-data

# Exponer el puerto de PHP-FPM
EXPOSE 9000

# Comando por defecto
CMD ["php-fpm"]
