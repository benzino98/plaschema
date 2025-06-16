# CI/CD Deployment Process

This document explains the CI/CD deployment process for the PLASCHEMA website using GitHub Actions to deploy to shared hosting via FTP.

## Overview

The deployment process uses GitHub Actions to automatically deploy the application to Qserver shared hosting whenever changes are pushed to the main branch. The process includes building the application, preparing the deployment structure, and uploading the files via FTP.

## Deployment Workflows

The repository contains two deployment-related workflows:

1. **Deploy to Shared Hosting (`deploy.yml`)** - The primary workflow that automatically deploys changes when pushed to the main branch.

2. **Rollback Deployment (`rollback.yml`)** - A workflow for rolling back to a previous deployment if issues are encountered.

3. **Deploy to Shared Hosting (No SSH) [DEPRECATED] (`deploy-without-ssh.yml`)** - The original deployment workflow that has been replaced by the improved `deploy.yml` workflow. It is kept for reference purposes only and has been modified to prevent automatic execution.

> **Note:** Only the primary `deploy.yml` workflow will run automatically on pushes to the main branch. The deprecated workflow has been modified to only run manually to prevent conflicts.

## Deployment Workflow

### Triggers

The deployment workflow is triggered by:

- Pushing commits to the `main` branch
- Manually triggering the workflow from the GitHub Actions tab

### Steps

1. **Checkout Code**: Retrieves the latest code from the repository
2. **Cache Dependencies**: Caches Composer and npm dependencies to speed up builds
3. **Setup Environment**: Sets up PHP and Node.js environments
4. **Install Dependencies**: Installs Composer and npm dependencies
5. **Build Assets**: Compiles frontend assets
6. **Generate Configuration**: Creates environment configuration files
7. **Pre-generate Cache Files**: Generates Laravel cache files
8. **Prepare Deployment Structure**: Organizes files for shared hosting deployment
9. **Backup Current Deployment**: Creates a backup of the current deployment
10. **Create Directory Structure**: Ensures required directories exist on the server
11. **Deploy Laravel Core Files**: Uploads Laravel application files
12. **Deploy Public Files**: Uploads public files to the web root
13. **Run Post-Deployment Script**: Clears caches and performs other post-deployment tasks
14. **Send Notification**: Notifies team members about the deployment status

## Directory Structure

The deployment process creates the following directory structure on the server:

```
/home/plaschem/
├── laravel/           # Laravel core files (not publicly accessible)
│   ├── app/
│   ├── bootstrap/
│   ├── config/
│   ├── database/
│   ├── resources/
│   ├── routes/
│   ├── storage/
│   ├── vendor/
│   └── ...
├── public_html/       # Publicly accessible files
│   ├── css/
│   ├── js/
│   ├── images/
│   ├── index.php      # Modified to point to the laravel directory
│   └── ...
└── backups/           # Deployment backups
    └── backup-YYYYMMDDHHMMSS/
```

## Environment Configuration

The deployment process sets the following environment variables:

- `APP_ENV=production`
- `APP_DEBUG=false`
- `APP_URL=https://plaschema.pl.gov.ng`
- `STORAGE_PATH=/home/plaschem/laravel/storage`
- `PUBLIC_PATH=/home/plaschem/public_html`

## GitHub Secrets

The following secrets must be configured in the GitHub repository settings:

- `FTP_SERVER`: The FTP server hostname
- `FTP_USERNAME`: The FTP username
- `FTP_PASSWORD`: The FTP password
- `SLACK_WEBHOOK`: (Optional) Slack webhook URL for notifications

## Rollback Process

If a deployment fails or causes issues, you can roll back to a previous version:

1. Go to the GitHub Actions tab
2. Select the "Rollback Deployment" workflow
3. Click "Run workflow"
4. Enter the backup folder name to restore from (e.g., `backup-20230101120000`)
5. Click "Run workflow"

The rollback process will:

1. Create a backup of the current deployment
2. Restore the selected backup
3. Clear caches
4. Send a notification about the rollback status

## Troubleshooting

### Common Issues

1. **FTP Connection Errors**

   - Check that the FTP credentials are correct
   - Verify that the FTP server is accessible
   - Check if the FTP server has connection limits

2. **Permission Issues**

   - Ensure the FTP user has write permissions to the target directories
   - Check file permissions after deployment (should be 644 for files, 755 for directories)

3. **Missing Files**

   - Check the deployment logs for any errors during file transfer
   - Verify that all necessary files are included in the deployment

4. **Application Errors After Deployment**
   - Check the Laravel logs at `/home/plaschem/laravel/storage/logs/laravel.log`
   - Run the cache clearing script at `https://plaschema.pl.gov.ng/manage_cache.php?action=clear_all`
   - Verify that the `.env` file contains the correct configuration

### Useful Commands

- **Clear Cache**: Visit `https://plaschema.pl.gov.ng/manage_cache.php?action=clear_all`
- **Initialize Deployment**: Visit `https://plaschema.pl.gov.ng/initialize_deployment.php`
- **Create Storage Link**: Visit `https://plaschema.pl.gov.ng/create_storage_link.php`

## Maintenance

### Regular Maintenance Tasks

1. **Clean Up Old Backups**

   - Periodically remove old backup folders to save disk space
   - Keep at least the last 5 successful deployment backups

2. **Monitor Deployment Logs**

   - Regularly check GitHub Actions logs for any warnings or errors
   - Address any issues promptly to prevent deployment failures

3. **Update Dependencies**
   - Regularly update Composer and npm dependencies
   - Test updates thoroughly before deploying to production

### Improving the Deployment Process

1. **Optimize Build Speed**

   - Further optimize caching strategies
   - Consider using GitHub Actions cache for build artifacts

2. **Enhance Monitoring**

   - Add more comprehensive health checks
   - Implement uptime monitoring

3. **Automate Testing**
   - Add automated tests to the CI/CD pipeline
   - Prevent deployments if tests fail
