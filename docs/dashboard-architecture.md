# PLASCHEMA Admin Dashboard Architecture

## Model Organization

Based on our system analysis, we've organized all models into these logical groups:

### 1. Content Management

Models responsible for public-facing content displayed on the website:

-   **News**: Articles and announcements

    -   Key metrics: Total count, Published count, Featured count
    -   Related actions: Create news, View recent news

-   **Healthcare Providers**: Medical facilities in the directory

    -   Key metrics: Total count, Active count, Distribution by type
    -   Related actions: Add provider, View recent providers

-   **FAQs**: Frequently asked questions

    -   Key metrics: Total count, Active count, Distribution by category
    -   Related actions: Add FAQ, View all FAQs

-   **Resources**: Downloadable documents
    -   Key metrics: Total count, Active count, Total downloads, Popular resources
    -   Related actions: Upload resource, View download statistics

### 2. User Management

Models responsible for system access and permissions:

-   **Users**: System users

    -   Key metrics: Total count, Distribution by role
    -   Related actions: Add user, Manage permissions

-   **Roles**: User roles

    -   Key metrics: Users per role
    -   Related actions: Manage roles

-   **Permissions**: Access control
    -   Key metrics: Permissions by role
    -   Related actions: Edit permissions

### 3. Communication & Activity

Models tracking user interactions and communications:

-   **Contact Messages**: User inquiries

    -   Key metrics: Total count, New messages, In progress messages, Resolved messages
    -   Related actions: View messages, Respond to messages

-   **Activity Logs**: System activity records
    -   Key metrics: Total count, Today's activities, Activity by action type
    -   Related actions: View activity details, Filter activities

### 4. System Configuration

Models for system settings and infrastructure:

-   **Translations**: Multilingual content

    -   Key metrics: Translations by language, Missing translations
    -   Related actions: Manage translations, Import/Export translations

-   **Message Categories**: Organization for contact messages

    -   Key metrics: Messages by category
    -   Related actions: Manage categories

-   **Resource Categories**: Organization for downloadable resources
    -   Key metrics: Resources by category, Downloads by category
    -   Related actions: Manage categories

## Key Dashboard Features

### 1. Statistics Grid

-   Content counts with comparison to previous period
-   Quick access metrics for each model group
-   Status indicators for attention items (new messages, etc.)

### 2. Data Visualizations

-   Content Growth Chart: Tracks creation of different content types over time
-   Activity Timeline: Shows system activity over the past 14 days
-   Provider Distribution: Pie chart showing healthcare providers by type
-   Download Statistics: Bar chart showing most downloaded resource categories

### 3. Quick Action Center

-   Create News Article
-   Add Healthcare Provider
-   Upload Resource
-   Add FAQ
-   View Messages

### 4. Recent Activity Feed

-   Latest system activities with user information
-   Recent content changes
-   New user registrations
-   Latest messages

### 5. Personalization Options

-   Reorderable dashboard components
-   Show/hide specific metrics
-   Quick access to favorite actions

## Dashboard Flow

1. **Overview Section**: Quick glance metrics and visualizations
2. **Content Management**: Detailed metrics for website content
3. **User & Activity**: User statistics and system activity
4. **Quick Actions**: Frequently used operations

## Database Queries

All dashboard queries are optimized for performance:

1. Results are cached for 15 minutes
2. Aggregation queries use database functions
3. Recent items queries are limited to 5-10 items
4. Charts use pre-aggregated data
5. All metrics use count() and sum() operations when possible

## Design Principles

1. **At-a-glance Information**: Most important metrics visible immediately
2. **Actionable Insights**: Data leads to clear actions
3. **Progressive Disclosure**: Overview first, details available on demand
4. **Visual Hierarchy**: Most important information has visual emphasis
5. **Consistent Structure**: Similar data presented in consistent ways
