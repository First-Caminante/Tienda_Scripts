#!/bin/bash

# Definir carpeta principal
PROYECTO_DIR="$(pwd)/EISPDM_PROJECTS"
REPO_URL="https://github.com/First-Caminante/Tienda_Scripts.git"
REPO_NAME="Tienda_Scripts"
BD_PATH="$PROYECTO_DIR/$REPO_NAME/database/bd.sql"
DB_NAME="tienda_scripts_db"
DB_USER="root"
DB_PASSWORD=""

# Paso 1: Crear carpeta EISPDM_PROJECTS si no existe
mkdir -p "$PROYECTO_DIR"

# Paso 2: Clonar repositorio
cd "$PROYECTO_DIR"
if [ ! -d "$REPO_NAME" ]; then
  git clone "$REPO_URL"
else
  echo "Repositorio ya clonado."
fi

# Paso 3: Crear base de datos y ejecutar el script SQL
echo "Creando base de datos $DB_NAME y ejecutando el script $BD_PATH..."

# Crear la base de datos si no existe
mariadb -u "$DB_USER" -p"$DB_PASSWORD" -e "CREATE DATABASE IF NOT EXISTS $DB_NAME;"

# Importar el archivo SQL
mariadb -u "$DB_USER" -p"$DB_PASSWORD" "$DB_NAME" <"$BD_PATH"

echo "✅ Proyecto configurado correctamente."
