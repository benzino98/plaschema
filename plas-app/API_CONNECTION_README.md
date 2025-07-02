# API Connection Configuration Guide

This document explains how to optimize the API connections between your PLAS application and the enrollment statistics API for improved reliability in production environments.

## Issue: API Connection Timeouts

The application has been experiencing connection timeout errors when attempting to fetch enrollment statistics from the external API. The typical error looks like:

```
[2025-07-02 13:16:59] production.ERROR: Failed to refresh enrollment statistics from external API {"exception":"cURL error 28: Connection timed out after 5001 milliseconds (see https://curl.haxx.se/libcurl/c/libcurl-errors.html) for https://enrollments.plaschema.app/api/data-records","api_url":"https://enrollments.plaschema.app/api/data-records","timeout_setting":30}
```

## Solution Implemented

We have made the following improvements to handle this issue:

1. **Increased Connection Timeouts**:

    - Added a separate connection timeout setting (`EXTERNAL_API_CONNECT_TIMEOUT`)
    - Increased retry attempts and wait time between retries
    - Set higher timeout values for production environments

2. **Improved Cache Handling**:

    - Increased cache duration to reduce API calls
    - Added ability to use expired cache data when the API is unavailable
    - Implemented a more robust fallback system

3. **Enhanced Error Handling**:
    - Added more detailed logging with additional context
    - Created multiple fallback options when API calls fail

## Configuration Settings

To apply these changes to your production environment, update your `.env` file with the following settings:

```
# External API Configuration
EXTERNAL_API_URL=https://enrollments.plaschema.app/api
EXTERNAL_API_TIMEOUT=60
EXTERNAL_API_CONNECT_TIMEOUT=30
```

## Monitoring and Troubleshooting

If you still experience issues with the API connection:

1. Check server-to-server connectivity between your application server and the enrollment API server
2. Verify there are no firewall rules blocking the connection
3. Consider setting up a scheduled task to ping/warm the API connection periodically
4. Review the Laravel logs for more detailed error information

## Additional Recommendations

For optimal performance in production:

1. Consider using Redis for caching instead of the database driver
2. Set up a cron job to pre-warm the cache during low-traffic periods
3. Monitor the API's response times and adjust timeouts accordingly
4. Set up alerting for repeated API connection failures
