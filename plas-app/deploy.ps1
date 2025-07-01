function Write-Status {
    param (
        [string]$Message,
        [string]$Color = "White"
    )
    Write-Host $Message -ForegroundColor $Color
}

# Ensure we're in the git repository
if (-not (Test-Path ".git")) {
    Write-Status "Error: Not in the root of a git repository." "Red"
    exit 1
}

# Show warning and confirmation
Write-Status "WARNING: This script will deploy your development branch to production!" "Red"
Write-Status "This will trigger the GitHub Actions workflow to deploy to your live site." "Yellow"
$Confirm = Read-Host "Are you sure you want to continue? (yes/no)"

if ($Confirm -ne "yes") {
    Write-Status "Deployment cancelled." "Yellow"
    exit 0
}

# Pre-deployment checks
Write-Status "Running pre-deployment checks..." "Cyan"

# Check if we're on development branch
$CurrentBranch = git rev-parse --abbrev-ref HEAD
if ($CurrentBranch -ne "development") {
    Write-Status "Warning: You're not on the development branch." "Red"
    $BranchConfirm = Read-Host "Do you want to switch to development branch? (y/n)"
    if ($BranchConfirm -eq "y") {
        git checkout development
    } else {
        Write-Status "Deployment cancelled." "Yellow"
        exit 0
    }
}

# Check for uncommitted changes
$Status = git status --porcelain
if ($Status) {
    Write-Status "Error: You have uncommitted changes. Please commit or stash them first." "Red"
    exit 1
}

# Pull latest changes
Write-Status "Pulling latest changes from development..." "Cyan"
git pull origin development

# Find the Laravel app directory
$LaravelAppDir = ""
if (Test-Path "plas-app") {
    $LaravelAppDir = "plas-app"
} elseif (Test-Path "../plas-app") {
    $LaravelAppDir = "../plas-app"
} else {
    Write-Status "Error: Could not find the Laravel application directory." "Red"
    exit 1
}

# Build assets for production
Write-Status "Building assets for production..." "Cyan"
Push-Location $LaravelAppDir
try {
    npm run build
    if ($LASTEXITCODE -ne 0) {
        Write-Status "Error: Asset build failed. Please fix the errors and try again." "Red"
        exit 1
    }
} finally {
    Pop-Location
}

# Final confirmation
Write-Status "All checks passed. Ready to deploy to production." "Green"
$FinalConfirm = Read-Host "Proceed with deployment? (yes/no)"

if ($FinalConfirm -ne "yes") {
    Write-Status "Deployment cancelled." "Yellow"
    exit 0
}

# Deploy to production
Write-Status "Deploying to production..." "Cyan"

# Merge to main
Write-Status "Merging development to main..." "Yellow"
git checkout main
git pull origin main
git merge --no-ff development -m "Merge development to main for production deployment"

# Push to main
Write-Status "Pushing changes to main branch..." "Yellow"
git push origin main

Write-Status "Deployment initiated!" "Green"
Write-Status "GitHub Actions will now deploy the changes to production." "Cyan"
Write-Status "You can monitor the deployment progress on GitHub." "Cyan"

# Switch back to development branch
git checkout development

Write-Status "Deployment process completed. Remember to verify the site after deployment." "Green" 