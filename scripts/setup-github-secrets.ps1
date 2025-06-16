# Setup GitHub Secrets for CI/CD Pipeline
# This script helps set up the required GitHub secrets for the PLASCHEMA CI/CD pipeline

# Check if GitHub CLI is installed
$ghInstalled = $null
try {
    $ghInstalled = Get-Command gh -ErrorAction Stop
    Write-Host "GitHub CLI is installed." -ForegroundColor Green
} catch {
    Write-Host "GitHub CLI is not installed. Please install it from https://cli.github.com/" -ForegroundColor Red
    Write-Host "After installing, run 'gh auth login' to authenticate." -ForegroundColor Yellow
    exit 1
}

# Check if authenticated with GitHub
$authenticated = $false
try {
    $status = gh auth status 2>&1
    if ($status -match "Logged in to") {
        $authenticated = $true
        Write-Host "Authenticated with GitHub." -ForegroundColor Green
    }
} catch {
    Write-Host "Not authenticated with GitHub." -ForegroundColor Red
}

if (-not $authenticated) {
    Write-Host "Please authenticate with GitHub using 'gh auth login'" -ForegroundColor Yellow
    exit 1
}

# Get repository information
$repoInfo = git remote -v | Select-String -Pattern "origin.*github.com" | Select-Object -First 1
if (-not $repoInfo) {
    Write-Host "Could not determine GitHub repository. Make sure you're in a git repository connected to GitHub." -ForegroundColor Red
    exit 1
}

$repoPattern = "github.com[:/]([^/]+)/([^.]+)"
$matches = [regex]::Match($repoInfo, $repoPattern)
if (-not $matches.Success) {
    Write-Host "Could not parse GitHub repository information." -ForegroundColor Red
    exit 1
}

$owner = $matches.Groups[1].Value
$repo = $matches.Groups[2].Value.TrimEnd(".git")
$fullRepo = "$owner/$repo"

Write-Host "Setting up secrets for repository: $fullRepo" -ForegroundColor Cyan

# Prompt for FTP credentials
Write-Host "`nEnter FTP credentials for deployment:" -ForegroundColor Yellow
$ftpServer = Read-Host "FTP Server hostname"
$ftpUsername = Read-Host "FTP Username"
$ftpPassword = Read-Host "FTP Password" -AsSecureString
$ftpPasswordPlain = [System.Runtime.InteropServices.Marshal]::PtrToStringAuto([System.Runtime.InteropServices.Marshal]::SecureStringToBSTR($ftpPassword))

# Prompt for Slack webhook (optional)
Write-Host "`nEnter Slack webhook for notifications (leave empty to skip):" -ForegroundColor Yellow
$slackWebhook = Read-Host "Slack Webhook URL"

# Set GitHub secrets
Write-Host "`nSetting up GitHub secrets..." -ForegroundColor Cyan

# Set FTP_SERVER secret
Write-Host "Setting FTP_SERVER secret..." -ForegroundColor Gray
echo $ftpServer | gh secret set FTP_SERVER -R $fullRepo
if ($LASTEXITCODE -eq 0) {
    Write-Host "✓ FTP_SERVER secret set successfully." -ForegroundColor Green
} else {
    Write-Host "✗ Failed to set FTP_SERVER secret." -ForegroundColor Red
}

# Set FTP_USERNAME secret
Write-Host "Setting FTP_USERNAME secret..." -ForegroundColor Gray
echo $ftpUsername | gh secret set FTP_USERNAME -R $fullRepo
if ($LASTEXITCODE -eq 0) {
    Write-Host "✓ FTP_USERNAME secret set successfully." -ForegroundColor Green
} else {
    Write-Host "✗ Failed to set FTP_USERNAME secret." -ForegroundColor Red
}

# Set FTP_PASSWORD secret
Write-Host "Setting FTP_PASSWORD secret..." -ForegroundColor Gray
echo $ftpPasswordPlain | gh secret set FTP_PASSWORD -R $fullRepo
if ($LASTEXITCODE -eq 0) {
    Write-Host "✓ FTP_PASSWORD secret set successfully." -ForegroundColor Green
} else {
    Write-Host "✗ Failed to set FTP_PASSWORD secret." -ForegroundColor Red
}

# Set SLACK_WEBHOOK secret if provided
if ($slackWebhook) {
    Write-Host "Setting SLACK_WEBHOOK secret..." -ForegroundColor Gray
    echo $slackWebhook | gh secret set SLACK_WEBHOOK -R $fullRepo
    if ($LASTEXITCODE -eq 0) {
        Write-Host "✓ SLACK_WEBHOOK secret set successfully." -ForegroundColor Green
    } else {
        Write-Host "✗ Failed to set SLACK_WEBHOOK secret." -ForegroundColor Red
    }
} else {
    Write-Host "Skipping SLACK_WEBHOOK secret (not provided)." -ForegroundColor Yellow
}

# Verify secrets
Write-Host "`nVerifying secrets..." -ForegroundColor Cyan
$secrets = gh secret list -R $fullRepo
$requiredSecrets = @("FTP_SERVER", "FTP_USERNAME", "FTP_PASSWORD")
$missingSecrets = @()

foreach ($secret in $requiredSecrets) {
    if ($secrets -match $secret) {
        Write-Host "✓ $secret is set." -ForegroundColor Green
    } else {
        Write-Host "✗ $secret is missing." -ForegroundColor Red
        $missingSecrets += $secret
    }
}

if ($slackWebhook) {
    if ($secrets -match "SLACK_WEBHOOK") {
        Write-Host "✓ SLACK_WEBHOOK is set." -ForegroundColor Green
    } else {
        Write-Host "✗ SLACK_WEBHOOK is missing." -ForegroundColor Red
        $missingSecrets += "SLACK_WEBHOOK"
    }
}

if ($missingSecrets.Count -gt 0) {
    Write-Host "`nWarning: Some secrets are missing. The CI/CD pipeline may not work correctly." -ForegroundColor Red
    Write-Host "Missing secrets: $($missingSecrets -join ", ")" -ForegroundColor Red
} else {
    Write-Host "`nAll required secrets are set up successfully!" -ForegroundColor Green
    Write-Host "The CI/CD pipeline is ready to use." -ForegroundColor Green
}

Write-Host "`nNext steps:" -ForegroundColor Cyan
Write-Host "1. Push changes to the main branch to trigger a deployment" -ForegroundColor White
Write-Host "2. Monitor the deployment in the GitHub Actions tab" -ForegroundColor White
Write-Host "3. If needed, manually trigger a deployment or rollback from the GitHub Actions tab" -ForegroundColor White 