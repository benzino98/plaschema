# GitHub Actions Deployment Setup

This document explains how to set up the GitHub Actions workflow that deploys this Laravel application to a shared hosting environment.

## Prerequisites

To use this deployment workflow, you need:

1. A GitHub account with permissions to the repository
2. FTP/SFTP access to your shared hosting

## Deployment Structure

The deployment workflow follows Laravel's recommended security practice of keeping most of the application files outside the web-accessible directory:

```
/home/plaschema/          # Your hosting account root
├── laravel/              # Laravel core files (outside web root)
│   ├── app/
│   ├── bootstrap/
│   ├── config/
│   ├── database/
│   ├── resources/
│   ├── routes/
│   ├── storage/
│   ├── vendor/
│   └── ...
└── public_html/          # Web-accessible directory
    ├── index.php         # Modified to point to ../laravel
    ├── css/
    ├── js/
    ├── laravel_utilities.php
    └── ...
```

## Deployment Options

Two deployment workflows are provided:

1. **Standard Deployment (`deploy.yml`)**: Uses both FTP and SSH for a complete deployment with post-deployment commands.
2. **No-SSH Deployment (`deploy-without-ssh.yml`)**: Uses only FTP and creates a PHP initialization file that must be manually accessed after deployment.

Since your shared hosting provider doesn't permit SSH access, you should use the **No-SSH Deployment** option.

## Setting Up GitHub Secrets

The deployment workflow uses several GitHub secrets to securely store sensitive information. You need to add these secrets to your repository:

1. Go to your GitHub repository
2. Click on "Settings" > "Secrets and variables" > "Actions"
3. Click "New repository secret"
4. Add the following secrets:

### FTP Deployment Secrets (Required)

- `FTP_SERVER`: Your FTP server hostname (e.g., `ftp.example.com`)
- `FTP_USERNAME`: Your FTP username
- `FTP_PASSWORD`: Your FTP password
- `FTP_LARAVEL_DIR`: The directory path for Laravel core files (e.g., `/laravel/` or `/home/plaschema/laravel/`)
- `FTP_PUBLIC_DIR`: The directory path for public files (e.g., `/public_html/` or `/home/plaschema/public_html/`)

## Using the No-SSH Deployment Workflow

The No-SSH deployment workflow (`deploy-without-ssh.yml`) is designed specifically for shared hosting environments that don't provide SSH access. Here's how it works:

1. It builds your Laravel application on GitHub's servers
2. It pre-generates cache files locally
3. It creates a deployment structure with separated Laravel core files and public files
4. It uploads the Laravel core files to a directory outside the web root
5. It uploads the public files to your web-accessible directory
6. It includes a special initialization file that handles post-deployment tasks

### Post-Deployment Steps

After the GitHub Action completes, you need to:

1. Visit `https://your-domain.com/laravel_utilities.php` in your browser
2. This utility dashboard provides several functions:
   - Check your server environment
   - Verify directory permissions
   - Run database migrations
   - Clear and regenerate caches
   - Create symbolic links if supported
   - Fix cache and log paths
   - Manage the Laravel cache system

### Manually Triggering the Workflow

To manually trigger the deployment workflow:

1. Go to your GitHub repository
2. Click on the "Actions" tab
3. Select "Deploy to Shared Hosting (No SSH)" from the list of workflows
4. Click the "Run workflow" button
5. Select the branch you want to deploy (usually "main")
6. Click the green "Run workflow" button

### Security Considerations

The initialization file includes basic security features:

- It can be restricted to specific IP addresses (you can add your IP in the file)
- It self-deletes after execution
- It uses a shutdown function to ensure deletion even if the script encounters errors

If you need to run the initialization again, you'll need to redeploy your application or manually upload the initialization file.

## Customizing the Workflow

### Deployment Triggers

By default, the workflow runs when changes are pushed to the `main` branch. To change this:

```yaml
on:
  push:
    branches: [your-branch-name]
```

### PHP Version

The workflow uses PHP 8.2. If you need a different version, update:

```yaml
php-version: "8.2" # Change to your required version
```

### Additional Environment Variables

If you need to set specific environment variables for your Laravel application, modify the "Generate .env file" step:

```yaml
run: |
  cp .env.example .env
  sed -i 's/APP_ENV=.*/APP_ENV=production/' .env
  sed -i 's/APP_DEBUG=.*/APP_DEBUG=false/' .env
  # Add more environment variables here
  sed -i 's/DB_HOST=.*/DB_HOST=your-db-host/' .env
  sed -i 's/DB_DATABASE=.*/DB_DATABASE=your-db-name/' .env
  sed -i 's/DB_USERNAME=.*/DB_USERNAME=your-db-user/' .env
  sed -i 's/DB_PASSWORD=.*/DB_PASSWORD=your-db-password/' .env
  php artisan key:generate
```

## Troubleshooting

### Common Issues:

1. **FTP Connection Failed**: Check your FTP credentials and server address.
2. **Composer/NPM Errors**: Ensure your `composer.json` and `package.json` files are valid.
3. **File Permissions**: If you experience permission issues after deployment, you may need to set proper file permissions through your hosting control panel.
4. **Initialization Script Fails**:
   - Check if your hosting provider supports the `exec()` function in PHP
   - If `exec()` is disabled, you may need to manually run migrations through your hosting control panel
   - Some hosting providers have restrictions on PHP execution time, which might cause timeouts
5. **404 Not Found for initialize_deployment.php**: Make sure the file was deployed to the correct public directory

### Viewing Logs:

You can view logs for each workflow run in the "Actions" tab of your GitHub repository.

## Additional Resources

- [GitHub Actions Documentation](https://docs.github.com/en/actions)
- [FTP Deploy Action Documentation](https://github.com/SamKirkland/FTP-Deploy-Action)
- [Laravel Deployment Best Practices](https://laravel.com/docs/deployment)
