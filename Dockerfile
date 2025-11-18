# --- ETAPA 1: Dependencias PHP (Composer) ---
FROM composer:2 AS composer_builder
WORKDIR /app

# Copiar composer.json y composer.lock
COPY composer*.json ./

# Instalar dependencias de PHP (sin --dev para producción)
RUN composer install --no-dev --prefer-dist --optimize-autoloader --no-interaction --no-scripts

# Copiar el resto del código
COPY . .

# Ejecutar scripts post-install si es necesario
RUN composer dump-autoload --optimize

# --- ETAPA 2: Build de Node (Assets) ---
FROM node:22-alpine AS node_builder
WORKDIR /app

# Copiar package.json y package-lock.json
COPY package*.json ./

# Instalar dependencias de Node
RUN npm ci

# Copiar código y vendor desde composer (necesario para flux.css)
COPY --from=composer_builder /app /app

# Construir los assets
RUN npm run build

# --- ETAPA 3: Imagen Final ---
FROM php:8.4-fpm-alpine

# Paquetes + extensiones PHP necesarias para Laravel + SQLite
RUN set -eux; \
    # 1. Actualizar índice de repositorios
    apk update; \
    # 2. Instalar dependencias de construcción y extensiones de PHP
    apk add --no-cache --virtual .build-deps $PHPIZE_DEPS icu-dev sqlite-dev oniguruma-dev libzip-dev; \
    apk add --no-cache icu sqlite-libs git unzip; \
    docker-php-ext-configure intl; \
    docker-php-ext-install -j"$(nproc)" pdo_sqlite bcmath intl mbstring zip; \
    docker-php-ext-enable opcache; \
    # 3. Eliminar dependencias de construcción para reducir imagen
    apk del .build-deps

# Definir directorio de trabajo
WORKDIR /var/www/html

# Copiar código de aplicación con assets compilados y dependencias
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
