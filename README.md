# 🎨 Frases Animadas 2

[![CI/CD (Laravel)](https://github.com/alejandro240/frases-animadas-2/actions/workflows/ci.yml/badge.svg)](https://github.com/alejandro240/frases-animadas-2/actions/workflows/ci.yml)
[![Tests](https://github.com/alejandro240/frases-animadas-2/actions/workflows/tests.yml/badge.svg)](https://github.com/alejandro240/frases-animadas-2/actions/workflows/tests.yml)
[![Code Quality](https://github.com/alejandro240/frases-animadas-2/actions/workflows/lint.yml/badge.svg)](https://github.com/alejandro240/frases-animadas-2/actions/workflows/lint.yml)

Aplicación web de Laravel para crear y visualizar frases con animaciones futuristas.

## 🚀 Características

- ✨ **5 tipos de animaciones** diferentes (Matrix, Quantum, Nebula, Hologram, Particle)
- 🔐 **Autenticación completa** con Laravel Fortify
- 👤 **Sistema de usuarios** con gestión de perfiles
- 🔒 **Autenticación de dos factores (2FA)**
- 📱 **Diseño responsive** con Tailwind CSS
- ⚡ **Componentes reactivos** con Livewire Volt
- 🎯 **Políticas de autorización** para proteger recursos
- 🧪 **Tests completos** con Pest PHP

## 📋 Requisitos

- PHP 8.4+
- Composer
- Node.js 22+
- NPM
- SQLite (o cualquier otra base de datos compatible con Laravel)

## 🛠️ Instalación

1. **Clonar el repositorio**
```bash
git clone https://github.com/alejandro240/frases-animadas-2.git
cd frases-animadas-2
```

2. **Instalar dependencias de PHP**
```bash
composer install
```

3. **Instalar dependencias de Node.js**
```bash
npm install
```

4. **Configurar el entorno**
```bash
cp .env.example .env
php artisan key:generate
```

5. **Crear la base de datos**
```bash
touch database/database.sqlite
```

6. **Ejecutar migraciones**
```bash
php artisan migrate
```

7. **Compilar assets**
```bash
npm run build
# O para desarrollo:
npm run dev
```

8. **Iniciar el servidor**
```bash
php artisan serve
```

La aplicación estará disponible en `http://localhost:8000`

## 🧪 Testing

Ejecutar todos los tests:
```bash
php artisan test
```

Ejecutar tests con Pest:
```bash
./vendor/bin/pest
```

Ejecutar tests con cobertura:
```bash
./vendor/bin/pest --coverage
```

## 🎨 Animaciones Disponibles

- 🟢 **Matrix Digital Rain** - Efecto Matriz
- ⚛️ **Quantum Glitch** - Distorsión Cuántica
- 🌌 **Cosmic Nebula** - Explosión Cósmica
- 🔷 **Holographic Scan** - Holograma Futurista
- ✨ **Particle Explosion** - Explosión de Partículas

## 📁 Estructura del Proyecto

```
frases-animadas-2/
├── app/
│   ├── Http/Controllers/
│   │   └── FraseController.php
│   ├── Models/
│   │   ├── Frase.php
│   │   └── User.php
│   └── Policies/
│       └── FrasePolicy.php
├── database/
│   ├── factories/
│   │   ├── FraseFactory.php
│   │   └── UserFactory.php
│   └── migrations/
├── resources/
│   ├── views/
│   │   └── frases/
│   ├── css/
│   └── js/
├── routes/
│   └── web.php
├── tests/
│   ├── Feature/
│   │   ├── FraseTest.php
│   │   └── Auth/
│   └── Unit/
└── .github/
    └── workflows/
        ├── ci.yml
        ├── tests.yml
        └── lint.yml
```

## 🔄 CI/CD

El proyecto utiliza **GitHub Actions** para:

- ✅ Ejecutar tests automáticamente en cada push/PR
- 🎨 Validar estilo de código con Laravel Pint
- 🔍 Análisis estático con PHPStan
- 📦 Compilar assets automáticamente
- 🚀 Preparar para despliegue

## 🤝 Contribuir

1. Fork el proyecto
2. Crea una rama para tu feature (`git checkout -b feature/AmazingFeature`)
3. Commit tus cambios (`git commit -m 'Add some AmazingFeature'`)
4. Push a la rama (`git push origin feature/AmazingFeature`)
5. Abre un Pull Request

## 📝 Licencia

Este proyecto es de código abierto bajo la licencia MIT.

## 👨‍💻 Autor

Alejandro - [@alejandro240](https://github.com/alejandro240)

## 🙏 Agradecimientos

- Laravel Framework
- Livewire & Flux
- Tailwind CSS
- Pest PHP

