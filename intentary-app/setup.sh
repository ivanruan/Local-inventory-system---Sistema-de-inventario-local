#!/bin/bash

echo "🚀 Iniciando configuración del proyecto Laravel..."

# Paso 1: Verificar que composer esté instalado
if ! command -v composer &> /dev/null
then
    echo "❌ Composer no está instalado. Instálalo antes de continuar."
    exit 1
fi

# Paso 2: Instalar dependencias
echo "📦 Instalando dependencias..."
composer install

# Paso 3: Copiar archivo .env si no existe
if [ ! -f .env ]; then
    echo "📄 Creando archivo .env desde .env.example..."
    cp .env.example .env
else
    echo "✅ El archivo .env ya existe."
fi

# Paso 4: Generar clave de aplicación
echo "🔐 Generando clave de aplicación..."
php artisan key:generate

# Paso 5: Migrar base de datos (opcional)
read -p "¿Deseas correr las migraciones ahora? (s/n): " MIGRAR
if [[ "$MIGRAR" =~ ^[Ss]$ ]]; then
    php artisan migrate
fi

# Paso 6: Arrancar servidor de desarrollo
read -p "¿Deseas arrancar el servidor local de Laravel ahora? (s/n): " SERVIDOR
if [[ "$SERVIDOR" =~ ^[Ss]$ ]]; then
    php artisan serve
fi

echo "✅ Configuración completada. ¡Tu proyecto está listo para usarse!"
