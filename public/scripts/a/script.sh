#!/bin/bash
# Script de Automatización de Backups v2.0
# Desarrollado por: Juan Chambi 

# Configuración
SOURCE_DIR="/var/www/html"
BACKUP_DIR="/mnt/backups"
MAX_BACKUPS=7
DATE=$(date +"%Y-%m-%d_%H-%M-%S")
FILENAME="backup-$DATE.tar.gz"

# Colores para mensajes
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[0;33m'
NC='\033[0m' # No Color

echo -e "${YELLOW}Iniciando proceso de backup...${NC}"

# Verificar directorios
if [ ! -d "$SOURCE_DIR" ]; then
    echo -e "${RED}Error: El directorio fuente no existe.${NC}"
    exit 1
fi

if [ ! -d "$BACKUP_DIR" ]; then
    echo -e "${YELLOW}Creando directorio de backups...${NC}"
    mkdir -p "$BACKUP_DIR"
fi

# Crear backup comprimido
echo -e "Comprimiendo archivos de $SOURCE_DIR..."
tar -czf "$BACKUP_DIR/$FILENAME" "$SOURCE_DIR" 2>/dev/null

# Verificar si el backup fue exitoso
if [ $? -eq 0 ]; then
    echo -e "${GREEN}Backup completado con éxito: $BACKUP_DIR/$FILENAME${NC}"
    
    # Limpiar backups antiguos
    echo -e "Revisando backups antiguos..."
    BACKUP_COUNT=$(ls -1 "$BACKUP_DIR"/backup-*.tar.gz 2>/dev/null | wc -l)
    
    if [ $BACKUP_COUNT -gt $MAX_BACKUPS ]; then
        echo -e "Eliminando backups antiguos (manteniendo los últimos $MAX_BACKUPS)..."
        ls -1t "$BACKUP_DIR"/backup-*.tar.gz | tail -n +$(($MAX_BACKUPS + 1)) | xargs rm -f
    fi
    
    echo -e "${GREEN}¡Proceso finalizado correctamente!${NC}"
else
    echo -e "${RED}Error al crear el backup.${NC}"
    exit 1
fi