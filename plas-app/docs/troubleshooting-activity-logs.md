# Activity Log Troubleshooting Guide

## Issue: Activity Logs Not Showing on Dashboard

If you're experiencing issues with activity logs not displaying on the dashboard or in the activity log section, follow these troubleshooting steps:

### 1. Clear Dashboard Cache

The dashboard data is cached to improve performance. If you've generated reports or performed other actions and don't see them reflected, try clearing the dashboard cache:

```bash
php artisan cache:clear-dashboard
```

This command will specifically clear the dashboard and analytics cache, ensuring the most recent activity is displayed.

### 2. Clear All Application Cache

If the above doesn't work, try clearing all application cache:

```bash
php artisan cache:clear
```

### 3. Verify Logs in Database

You can check if the activity logs are being properly saved in the database by running the debug script:

```bash
php tests/debug_activity_logs.php
```

This will show the most recent activity logs in the database, including Report-related activity logs.

### 4. Check Entity Types

Make sure your entity types are correctly defined in the ActivityLogService calls. For reports, we use the 'Report' entity type.

### 5. Refresh Browser Cache

Sometimes browser caching can prevent you from seeing the latest changes. Try hard-refreshing your browser (Ctrl+F5 or Cmd+Shift+R) or clearing your browser cache.

### 6. Check Users and Permissions

Ensure your user account has the required permissions to view activity logs. The permission needed is 'view-activity-logs'.

## Report Generation Logging

When generating reports, the system logs the activity with:

-   Action: "generated"
-   Entity Type: "Report"
-   Entity ID: A timestamp-based ID
-   Description: Details about the report type, format, and date range

This information should appear in both the Activity Logs page and the Recent Activity section of the dashboard.

## Common Issues

1. **Cached Data**: The most common issue is cached data. Clear the cache using the commands above.

2. **Missing Model**: Ensure the Report model exists in app/Models/Report.php.

3. **Incorrect Logging**: Check that the ActivityLogService is being called correctly in the AnalyticsController.

4. **Database Issues**: Run migrations if needed to ensure the activity_logs table exists with the correct structure.

If problems persist after following these steps, please contact the system administrator for further assistance.
