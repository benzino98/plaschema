# Local Development Workflow

This document outlines the local development workflow for the PLASCHEMA project.

## Git Branching Strategy

We use a structured Git branching strategy to manage development and deployment:

### Branch Structure

1. **Main Branch (`main`)**:

    - Production-ready code only
    - Connected to automatic deployment via GitHub Actions
    - Protected from direct commits

2. **Development Branch (`development`)**:

    - Main working branch for ongoing development
    - All features and fixes are tested here before production
    - No automatic deployment to production

3. **Feature Branches (`feature/feature-name`)**:

    - Created for specific features or bug fixes
    - Branched from `development`
    - Merged back to `development` when complete

4. **Hotfix Branches (`hotfix/issue-name`)**:
    - Created for urgent production fixes
    - Branched from `main`
    - Merged to both `main` and `development`

## Local Environment Setup

### Environment Configuration

We use separate environment configurations for local development and production:

1. `.env.local` - Local development settings
2. `.env` - Current active settings (switched between local and production)
3. `.env.backup` - Backup of production settings

### Switching Environments

Use the provided PowerShell script to switch between environments:

```powershell
# Switch to local environment
.\switch-env.ps1 -Environment local

# Switch to production environment
.\switch-env.ps1 -Environment production
```

## Daily Development Workflow

### 1. Starting Work

1. Make sure you're on the development branch:

    ```
    git checkout development
    git pull
    ```

2. Switch to local environment:

    ```
    cd plas-app
    .\switch-env.ps1 -Environment local
    ```

3. Start the local development server:

    ```
    php artisan serve
    ```

4. In a separate terminal, compile assets:
    ```
    npm run dev
    ```

### 2. Making Changes

1. For small changes, work directly on the development branch.

2. For larger features, create a feature branch:

    ```
    git checkout -b feature/your-feature-name
    ```

3. Make your changes and commit frequently with descriptive messages:
    ```
    git add .
    git commit -m "Add feature: description of what was changed"
    ```

### 3. Testing Changes

1. Test all changes thoroughly in your local environment
2. Run automated tests if available:
    ```
    php artisan test
    ```
3. Verify changes don't break existing functionality

### 4. Completing Feature Development

1. Merge your feature branch back to development:

    ```
    git checkout development
    git merge feature/your-feature-name
    ```

2. Push changes to remote development branch:
    ```
    git push origin development
    ```

## Deployment Process

### 1. Preparing for Production

1. Review all changes in the development branch
2. Run final tests to ensure everything works
3. Build assets for production:
    ```
    npm run build
    ```

### 2. Deploying to Production

1. Merge development to main:

    ```
    git checkout main
    git pull
    git merge development
    git push origin main
    ```

2. The GitHub Actions workflow will automatically deploy to production

### 3. Post-Deployment Verification

1. Verify the website is functioning correctly in production
2. Check that new features work as expected
3. Monitor for any errors or issues

## Handling Hotfixes

For urgent production fixes:

1. Create a hotfix branch from main:

    ```
    git checkout main
    git pull
    git checkout -b hotfix/issue-name
    ```

2. Make the necessary fixes and commit:

    ```
    git add .
    git commit -m "Fix: description of the fix"
    ```

3. Merge to main for immediate deployment:

    ```
    git checkout main
    git merge hotfix/issue-name
    git push origin main
    ```

4. Also merge to development to keep it in sync:
    ```
    git checkout development
    git merge hotfix/issue-name
    git push origin development
    ```

## Database Management

1. Always use migrations for database schema changes
2. Never connect your local environment directly to the production database
3. Use seeders for test data in local environment

## Best Practices

1. **Commit Practices**:

    - Write descriptive commit messages
    - Make atomic commits (one logical change per commit)
    - Reference issue numbers in commits if applicable

2. **Local Environment Maintenance**:

    - Regularly update dependencies (`composer update` and `npm update`)
    - Clear caches when needed (`php artisan cache:clear`)
    - Keep your local environment in sync with the development branch

3. **Documentation**:
    - Document significant architectural changes
    - Update README.md with new setup requirements
    - Document environment-specific configurations
