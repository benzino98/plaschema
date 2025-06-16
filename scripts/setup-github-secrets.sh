#!/bin/bash
# Setup GitHub Secrets for CI/CD Pipeline
# This script helps set up the required GitHub secrets for the PLASCHEMA CI/CD pipeline

# Colors
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
CYAN='\033[0;36m'
GRAY='\033[0;37m'
NC='\033[0m' # No Color

# Check if GitHub CLI is installed
if ! command -v gh &> /dev/null; then
    echo -e "${RED}GitHub CLI is not installed. Please install it from https://cli.github.com/${NC}"
    echo -e "${YELLOW}After installing, run 'gh auth login' to authenticate.${NC}"
    exit 1
else
    echo -e "${GREEN}GitHub CLI is installed.${NC}"
fi

# Check if authenticated with GitHub
if ! gh auth status &> /dev/null; then
    echo -e "${RED}Not authenticated with GitHub.${NC}"
    echo -e "${YELLOW}Please authenticate with GitHub using 'gh auth login'${NC}"
    exit 1
else
    echo -e "${GREEN}Authenticated with GitHub.${NC}"
fi

# Get repository information
REPO_INFO=$(git remote -v | grep -E "origin.*github.com" | head -n 1)
if [ -z "$REPO_INFO" ]; then
    echo -e "${RED}Could not determine GitHub repository. Make sure you're in a git repository connected to GitHub.${NC}"
    exit 1
fi

# Extract owner and repo name
if [[ $REPO_INFO =~ github\.com[:/]([^/]+)/([^.]+) ]]; then
    OWNER="${BASH_REMATCH[1]}"
    REPO=$(echo "${BASH_REMATCH[2]}" | sed 's/\.git$//')
    FULL_REPO="$OWNER/$REPO"
else
    echo -e "${RED}Could not parse GitHub repository information.${NC}"
    exit 1
fi

echo -e "${CYAN}Setting up secrets for repository: $FULL_REPO${NC}"

# Prompt for FTP credentials
echo -e "\n${YELLOW}Enter FTP credentials for deployment:${NC}"
read -p "FTP Server hostname: " FTP_SERVER
read -p "FTP Username: " FTP_USERNAME
read -sp "FTP Password: " FTP_PASSWORD
echo

# Prompt for Slack webhook (optional)
echo -e "\n${YELLOW}Enter Slack webhook for notifications (leave empty to skip):${NC}"
read -p "Slack Webhook URL: " SLACK_WEBHOOK

# Set GitHub secrets
echo -e "\n${CYAN}Setting up GitHub secrets...${NC}"

# Set FTP_SERVER secret
echo -e "${GRAY}Setting FTP_SERVER secret...${NC}"
echo "$FTP_SERVER" | gh secret set FTP_SERVER -R "$FULL_REPO"
if [ $? -eq 0 ]; then
    echo -e "${GREEN}✓ FTP_SERVER secret set successfully.${NC}"
else
    echo -e "${RED}✗ Failed to set FTP_SERVER secret.${NC}"
fi

# Set FTP_USERNAME secret
echo -e "${GRAY}Setting FTP_USERNAME secret...${NC}"
echo "$FTP_USERNAME" | gh secret set FTP_USERNAME -R "$FULL_REPO"
if [ $? -eq 0 ]; then
    echo -e "${GREEN}✓ FTP_USERNAME secret set successfully.${NC}"
else
    echo -e "${RED}✗ Failed to set FTP_USERNAME secret.${NC}"
fi

# Set FTP_PASSWORD secret
echo -e "${GRAY}Setting FTP_PASSWORD secret...${NC}"
echo "$FTP_PASSWORD" | gh secret set FTP_PASSWORD -R "$FULL_REPO"
if [ $? -eq 0 ]; then
    echo -e "${GREEN}✓ FTP_PASSWORD secret set successfully.${NC}"
else
    echo -e "${RED}✗ Failed to set FTP_PASSWORD secret.${NC}"
fi

# Set SLACK_WEBHOOK secret if provided
if [ -n "$SLACK_WEBHOOK" ]; then
    echo -e "${GRAY}Setting SLACK_WEBHOOK secret...${NC}"
    echo "$SLACK_WEBHOOK" | gh secret set SLACK_WEBHOOK -R "$FULL_REPO"
    if [ $? -eq 0 ]; then
        echo -e "${GREEN}✓ SLACK_WEBHOOK secret set successfully.${NC}"
    else
        echo -e "${RED}✗ Failed to set SLACK_WEBHOOK secret.${NC}"
    fi
else
    echo -e "${YELLOW}Skipping SLACK_WEBHOOK secret (not provided).${NC}"
fi

# Verify secrets
echo -e "\n${CYAN}Verifying secrets...${NC}"
SECRETS=$(gh secret list -R "$FULL_REPO")
REQUIRED_SECRETS=("FTP_SERVER" "FTP_USERNAME" "FTP_PASSWORD")
MISSING_SECRETS=()

for SECRET in "${REQUIRED_SECRETS[@]}"; do
    if echo "$SECRETS" | grep -q "$SECRET"; then
        echo -e "${GREEN}✓ $SECRET is set.${NC}"
    else
        echo -e "${RED}✗ $SECRET is missing.${NC}"
        MISSING_SECRETS+=("$SECRET")
    fi
done

if [ -n "$SLACK_WEBHOOK" ]; then
    if echo "$SECRETS" | grep -q "SLACK_WEBHOOK"; then
        echo -e "${GREEN}✓ SLACK_WEBHOOK is set.${NC}"
    else
        echo -e "${RED}✗ SLACK_WEBHOOK is missing.${NC}"
        MISSING_SECRETS+=("SLACK_WEBHOOK")
    fi
fi

if [ ${#MISSING_SECRETS[@]} -gt 0 ]; then
    echo -e "\n${RED}Warning: Some secrets are missing. The CI/CD pipeline may not work correctly.${NC}"
    echo -e "${RED}Missing secrets: ${MISSING_SECRETS[*]}${NC}"
else
    echo -e "\n${GREEN}All required secrets are set up successfully!${NC}"
    echo -e "${GREEN}The CI/CD pipeline is ready to use.${NC}"
fi

echo -e "\n${CYAN}Next steps:${NC}"
echo "1. Push changes to the main branch to trigger a deployment"
echo "2. Monitor the deployment in the GitHub Actions tab"
echo "3. If needed, manually trigger a deployment or rollback from the GitHub Actions tab" 