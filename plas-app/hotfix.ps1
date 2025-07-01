param (
    [Parameter(Mandatory=$true)]
    [string]$IssueName,
    
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

# Sanitize the issue name for branch naming
$BranchName = "hotfix/$($IssueName -replace '[^a-zA-Z0-9_-]', '-')"

if ($Action -eq "create") {
    # Create a new hotfix branch
    Write-Status "Creating new hotfix branch '$BranchName'..." "Yellow"
    
    # Make sure we're on main branch and it's up to date
    git checkout main
    git pull origin main
    
    # Create the hotfix branch
    git checkout -b $BranchName
    
    Write-Status "Hotfix branch '$BranchName' created successfully!" "Green"
    Write-Status "Make your changes, then run '.\hotfix.ps1 -IssueName $IssueName -Action complete' when done." "Cyan"
    
} elseif ($Action -eq "complete") {
    # Complete the hotfix process
    Write-Status "Completing hotfix for '$BranchName'..." "Yellow"
    
    # Check if we're on the correct branch
    $CurrentBranch = git rev-parse --abbrev-ref HEAD
    if ($CurrentBranch -ne $BranchName) {
        Write-Status "Warning: You're not on the '$BranchName' branch." "Red"
        $Confirm = Read-Host "Do you want to continue anyway? (y/n)"
        if ($Confirm -ne "y") {
            Write-Status "Hotfix process aborted." "Red"
            exit 1
        }
    }
    
    # Commit any pending changes
    $Status = git status --porcelain
    if ($Status) {
        Write-Status "You have uncommitted changes. Please commit them first." "Red"
        exit 1
    }
    
    # Merge to main
    Write-Status "Merging hotfix to main branch..." "Yellow"
    git checkout main
    git pull origin main
    git merge --no-ff $BranchName -m "Merge hotfix: $IssueName"
    
    # Push to main
    Write-Status "Pushing changes to main branch..." "Yellow"
    git push origin main
    
    # Merge to development
    Write-Status "Merging hotfix to development branch..." "Yellow"
    git checkout development
    git pull origin development
    git merge --no-ff $BranchName -m "Merge hotfix: $IssueName"
    
    # Push to development
    Write-Status "Pushing changes to development branch..." "Yellow"
    git push origin development
    
    Write-Status "Hotfix '$BranchName' has been successfully merged to main and development!" "Green"
    Write-Status "GitHub Actions will now deploy the changes to production." "Cyan"
    
    # Optional: Delete the hotfix branch
    $DeleteBranch = Read-Host "Do you want to delete the hotfix branch? (y/n)"
    if ($DeleteBranch -eq "y") {
        git branch -d $BranchName
        git push origin --delete $BranchName
        Write-Status "Hotfix branch deleted." "Green"
    }
} 