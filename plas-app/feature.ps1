param (
    [Parameter(Mandatory=$true)]
    [string]$FeatureName,
    
    [Parameter(Mandatory=$false)]
    [ValidateSet("create", "complete")]
    [string]$Action = "create"
)

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

# Sanitize the feature name for branch naming
$BranchName = "feature/$($FeatureName -replace '[^a-zA-Z0-9_-]', '-')"

if ($Action -eq "create") {
    # Create a new feature branch
    Write-Status "Creating new feature branch '$BranchName'..." "Yellow"
    
    # Make sure we're on development branch and it's up to date
    git checkout development
    git pull origin development
    
    # Create the feature branch
    git checkout -b $BranchName
    
    Write-Status "Feature branch '$BranchName' created successfully!" "Green"
    Write-Status "Make your changes, then run '.\feature.ps1 -FeatureName $FeatureName -Action complete' when done." "Cyan"
    
} elseif ($Action -eq "complete") {
    # Complete the feature process
    Write-Status "Completing feature for '$BranchName'..." "Yellow"
    
    # Check if we're on the correct branch
    $CurrentBranch = git rev-parse --abbrev-ref HEAD
    if ($CurrentBranch -ne $BranchName) {
        Write-Status "Warning: You're not on the '$BranchName' branch." "Red"
        $Confirm = Read-Host "Do you want to continue anyway? (y/n)"
        if ($Confirm -ne "y") {
            Write-Status "Feature completion process aborted." "Red"
            exit 1
        }
    }
    
    # Commit any pending changes
    $Status = git status --porcelain
    if ($Status) {
        Write-Status "You have uncommitted changes. Please commit them first." "Red"
        exit 1
    }
    
    # Merge to development
    Write-Status "Merging feature to development branch..." "Yellow"
    git checkout development
    git pull origin development
    git merge --no-ff $BranchName -m "Merge feature: $FeatureName"
    
    # Push to development
    Write-Status "Pushing changes to development branch..." "Yellow"
    git push origin development
    
    Write-Status "Feature '$BranchName' has been successfully merged to development!" "Green"
    
    # Optional: Delete the feature branch
    $DeleteBranch = Read-Host "Do you want to delete the feature branch? (y/n)"
    if ($DeleteBranch -eq "y") {
        git branch -d $BranchName
        git push origin --delete $BranchName
        Write-Status "Feature branch deleted." "Green"
    }
} 