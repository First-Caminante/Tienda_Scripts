# Obtener la ruta donde se ejecuta el script
$scriptPath = Split-Path -Parent $MyInvocation.MyCommand.Definition
$projectPath = Join-Path $scriptPath "EISPDM_PROJECTS"

# Configuración
$repoUrl = "https://github.com/First-Caminante/Tienda_Scripts.git"
$repoName = "Tienda_Scripts"
$bdPath = "$projectPath\$repoName\database\bd.sql"
$dbName = "tienda_scripts_db"
$dbUser = "root"
$dbPassword = ""  # Cambia si tienes contraseña

# Paso 1: Crear carpeta EISPDM_PROJECTS si no existe
if (-Not (Test-Path -Path $projectPath)) {
    New-Item -ItemType Directory -Path $projectPath | Out-Null
}

# Paso 2: Clonar repositorio si no existe
Set-Location -Path $projectPath
if (-Not (Test-Path -Path "$projectPath\$repoName")) {
    git clone $repoUrl
} else {
    Write-Host "Repositorio ya clonado."
}

# Paso 3: Crear base de datos y ejecutar SQL
Write-Host "Creando base de datos '$dbName' y ejecutando script SQL..."

# Verificar si mysql está disponible
$mysqlPath = Get-Command "mysql.exe" -ErrorAction SilentlyContinue

if (-not $mysqlPath) {
    Write-Error "❌ No se encontró mysql.exe en PATH. Agrega MariaDB/MySQL al PATH o especifica ruta completa en el script."
    exit 1
}

# Crear la base de datos
mysql -u $dbUser -p$dbPassword -e "CREATE DATABASE IF NOT EXISTS $dbName;"

# Ejecutar script SQL
mysql -u $dbUser -p$dbPassword $dbName < $bdPath

Write-Host "✅ Proyecto configurado correctamente."

