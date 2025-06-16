# GitHub Actions Workflows

This directory contains GitHub Actions workflows for automating the deployment and rollback processes for the PLASCHEMA website.

## Available Workflows

### 1. Deploy to Shared Hosting (`deploy.yml`)

This workflow automatically deploys the application to Qserver shared hosting whenever changes are pushed to the main branch.

**Triggers:**

- Push to `main` branch
- Manual trigger via GitHub Actions tab

**Key Features:**

- Dependency caching for faster builds
- Automated environment configuration
- Structured deployment for shared hosting
- Automatic backup creation
- Post-deployment cache clearing
- Deployment notifications

### 2. Rollback Deployment (`rollback.yml`)

This workflow allows you to roll back to a previous deployment if issues are encountered.

**Triggers:**

- Manual trigger via GitHub Actions tab (requires backup folder name)

**Key Features:**

- Lists available backups
- Creates a backup of the current deployment before rollback
- Restores from the specified backup
- Clears caches after rollback
- Sends rollback notifications

### 3. Deploy to Shared Hosting (No SSH) [DEPRECATED] (`deploy-without-ssh.yml`)

⚠️ **DEPRECATED**: This is the original deployment workflow that has been replaced by the improved `deploy.yml` workflow. It is kept for reference purposes only and has been modified to prevent automatic execution.

**Triggers:**

- Manual trigger via GitHub Actions tab only (automatic triggers have been disabled)

**Note:** This workflow should not be used for new deployments. It is recommended to use the new `deploy.yml` workflow instead.

## Required Secrets

The following secrets must be configured in the GitHub repository settings:

- `FTP_SERVER`: The FTP server hostname
- `FTP_USERNAME`: The FTP username
- `FTP_PASSWORD`: The FTP password
- `SLACK_WEBHOOK`: (Optional) Slack webhook URL for notifications

## Usage

### Deploying the Application

The application is automatically deployed when changes are pushed to the main branch. You can also manually trigger a deployment:

1. Go to the "Actions" tab in the GitHub repository
2. Select the "Deploy to Shared Hosting" workflow
3. Click "Run workflow"
4. Select the branch to deploy (usually `main`)
5. Click "Run workflow" again

### Rolling Back a Deployment

If you need to roll back to a previous deployment:

1. Go to the "Actions" tab in the GitHub repository
2. Select the "Rollback Deployment" workflow
3. Click "Run workflow"
4. Enter the backup folder name to restore from (e.g., `backup-20230101120000`)
5. Click "Run workflow"

## Directory Structure

The deployment process creates the following directory structure on the server:

```
/home/plaschem/
├── laravel/           # Laravel core files (not publicly accessible)
├── public_html/       # Publicly accessible files
└── backup-YYYYMMDDHHMMSS/  # Deployment backups
```

## Additional Information

For more detailed information about the CI/CD process, see the [CI/CD Deployment Documentation](../docs/cicd-deployment.md).
