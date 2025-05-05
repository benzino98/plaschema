# PLASCHEMA API Documentation

This document provides an overview of the PLASCHEMA API endpoints, authentication, and usage.

## Authentication

The API uses token-based authentication with Laravel Sanctum. To use protected endpoints, you need to:

1. Obtain an API token by logging in
2. Include the token in subsequent requests

### Login

```
POST /api/login
```

**Request Body:**

```json
{
    "email": "admin@example.com",
    "password": "password",
    "device_name": "My Device" // Optional
}
```

**Response:**

```json
{
    "token": "1|a1b2c3d4e5f6g7h8i9j0",
    "user": {
        "id": 1,
        "name": "Admin User",
        "email": "admin@example.com",
        "roles": ["admin"]
    }
}
```

### Using the Token

For all protected endpoints, include the token in the Authorization header:

```
Authorization: Bearer 1|a1b2c3d4e5f6g7h8i9j0
```

### Logout

```
POST /api/logout
```

**Headers:**

```
Authorization: Bearer YOUR_TOKEN
```

**Response:**

```json
{
    "message": "Successfully logged out"
}
```

## API Endpoints

### News

-   `GET /api/news` - Get a list of news articles
    -   Query parameters:
        -   `page`: Page number
        -   `category`: Filter by category slug
        -   `featured`: Filter featured news only (boolean)
        -   `search`: Search term
-   `GET /api/news/{id}` - Get a specific news article

-   `POST /api/news` - Create a new news article (protected)

    -   Required fields: `title`, `content`, `category_id`
    -   Optional fields: `excerpt`, `featured`, `published_at`, `image`

-   `PUT /api/news/{id}` - Update an existing news article (protected)

-   `DELETE /api/news/{id}` - Delete a news article (protected)

### Healthcare Providers

-   `GET /api/providers` - Get a list of healthcare providers
    -   Query parameters:
        -   `page`: Page number
        -   `category`: Filter by category slug
        -   `city`: Filter by city
        -   `is_featured`: Filter featured providers only (boolean)
        -   `search`: Search term
-   `GET /api/providers/{id}` - Get a specific healthcare provider

-   `POST /api/providers` - Create a new healthcare provider (protected)

    -   Required fields: `name`, `description`, `address`, `city`, `state`, `contact_info`, `category_id`
    -   Optional fields: `email`, `phone`, `specialties`, `is_active`, `is_featured`, `image`

-   `PUT /api/providers/{id}` - Update an existing healthcare provider (protected)

-   `DELETE /api/providers/{id}` - Delete a healthcare provider (protected)

### FAQs

-   `GET /api/faqs` - Get a list of FAQs
    -   Query parameters:
        -   `page`: Page number
        -   `category`: Filter by category slug
        -   `search`: Search term
-   `GET /api/faqs/{id}` - Get a specific FAQ

-   `POST /api/faqs` - Create a new FAQ (protected)

    -   Required fields: `question`, `answer`, `category_id`
    -   Optional fields: `is_published`, `order`

-   `PUT /api/faqs/{id}` - Update an existing FAQ (protected)

-   `DELETE /api/faqs/{id}` - Delete a FAQ (protected)

### Contact

-   `POST /api/contact` - Send a contact message

    -   Required fields: `name`, `email`, `subject`, `message`
    -   Optional fields: `category_id`

-   `GET /api/contact-messages` - Get a list of contact messages (protected, admin only)

    -   Query parameters:
        -   `page`: Page number
        -   `status`: Filter by status (`new`, `read`, `responded`, `archived`)
        -   `category`: Filter by category ID
        -   `search`: Search term

-   `GET /api/contact-messages/{id}` - Get a specific contact message (protected, admin only)

-   `PUT /api/contact-messages/{id}/status` - Update contact message status (protected, admin only)
    -   Required fields: `status` (one of: `new`, `read`, `responded`, `archived`)

## Error Handling

The API returns appropriate HTTP status codes:

-   `200` - Success
-   `201` - Resource created
-   `401` - Unauthorized (missing or invalid token)
-   `403` - Forbidden (insufficient permissions)
-   `404` - Resource not found
-   `422` - Validation error

For validation errors, the response includes details:

```json
{
    "errors": {
        "field_name": ["Error message for the field"]
    }
}
```

## Rate Limiting

API requests are limited to 60 per minute per user or IP address. The response headers include rate limit information:

```
X-RateLimit-Limit: 60
X-RateLimit-Remaining: 59
```

## Pagination

List endpoints return paginated results with metadata:

```json
{
  "data": [...],
  "meta": {
    "total": 100,
    "count": 15,
    "per_page": 15,
    "current_page": 1,
    "last_page": 7
  },
  "links": {
    "first": "http://example.com/api/resource?page=1",
    "last": "http://example.com/api/resource?page=7",
    "prev": null,
    "next": "http://example.com/api/resource?page=2"
  }
}
```
