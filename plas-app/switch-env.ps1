param (
    [Parameter(Mandatory=$true)]
    [ValidateSet("local", "production")]
    [string]$Environment
)

$envPath = ".env"
$localEnvPath = ".env.local"
$backupEnvPath = ".env.backup"

# Function to display status message
function Write-Status {
    param (
        [string]$Message,
        [string]$Color = "White"
    )
    Write-Host $Message -ForegroundColor $Color
}

# Create backup of current .env file if it doesn't exist
if (-not (Test-Path $backupEnvPath)) {
    Write-Status "Creating backup of current .env file to .env.backup..." "Yellow"
    Copy-Item $envPath $backupEnvPath
}

if ($Environment -eq "local") {
    # Switch to local environment
    if (Test-Path $localEnvPath) {
        Write-Status "Switching to local environment..." "Green"
        Copy-Item $localEnvPath $envPath
        Write-Status "Environment switched to LOCAL successfully!" "Green"
        Write-Status "Run 'php artisan serve' to start the local development server." "Cyan"
    } else {
        Write-Status "Error: .env.local file not found!" "Red"
        Write-Status "Please create a .env.local file first." "Red"
        exit 1
    }
} elseif ($Environment -eq "production") {
    # Switch to production environment
    if (Test-Path $backupEnvPath) {
        Write-Status "Switching to production environment..." "Yellow"
        Copy-Item $backupEnvPath $envPath
        Write-Status "Environment switched to PRODUCTION successfully!" "Yellow"
        Write-Status "Warning: You are now using production settings. Be careful with any operations." "Red"
    } else {
        Write-Status "Error: .env.backup file not found!" "Red"
        exit 1
    }
}

# Display current environment settings
Write-Status "`nCurrent Environment Settings:" "Magenta"
Write-Status "------------------------" "Magenta"
$envContent = Get-Content $envPath
$appEnv = ($envContent | Where-Object { $_ -match "^APP_ENV=" }) -replace "APP_ENV=", ""
$appUrl = ($envContent | Where-Object { $_ -match "^APP_URL=" }) -replace "APP_URL=", ""
$dbName = ($envContent | Where-Object { $_ -match "^DB_DATABASE=" }) -replace "DB_DATABASE=", ""
$cacheDriver = ($envContent | Where-Object { $_ -match "^CACHE_DRIVER=" }) -replace "CACHE_DRIVER=", ""

Write-Status "APP_ENV: $appEnv" "Cyan"
Write-Status "APP_URL: $appUrl" "Cyan"
Write-Status "DB_DATABASE: $dbName" "Cyan"
Write-Status "CACHE_DRIVER: $cacheDriver" "Cyan"
Write-Status "------------------------" "Magenta" 