# API Documentation - Steman Alumni Portal

## Base URL
- **Production:** `https://alumni-steman.my.id/api`
- **Development:** `http://localhost:8000/api`

## Authentication
Most API endpoints require authentication using Laravel Sanctum tokens. Include the token in the Authorization header:

```
Authorization: Bearer {token}
```

## Response Format
All responses follow this format:

```json
{
  "success": true|false,
  "data": {},
  "message": "Optional message",
  "errors": {}
}
```

## Endpoints

### Authentication

#### POST `/api/v1/auth/register`
Register a new user account.

**Request Body:**
```json
{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "password123",
  "password_confirmation": "password123",
  "major": "Computer Science",
  "graduation_year": 2020
}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "user": {
      "id": 1,
      "name": "John Doe",
      "email": "john@example.com"
    },
    "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9..."
  },
  "message": "Registration successful. Please wait for admin approval."
}
```

#### POST `/api/v1/auth/login`
Authenticate user and return token.

**Request Body:**
```json
{
  "email": "john@example.com",
  "password": "password123"
}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "user": {
      "id": 1,
      "name": "John Doe",
      "email": "john@example.com",
      "role": "alumni"
    },
    "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9..."
  },
  "message": "Login successful"
}
```

#### POST `/api/v1/auth/logout`
Logout user and invalidate token.

**Headers:**
```
Authorization: Bearer {token}
```

**Response:**
```json
{
  "success": true,
  "message": "Logout successful"
}
```

### Alumni

#### GET `/api/v1/alumni`
Get list of alumni with pagination and filtering.

**Query Parameters:**
- `page` (optional): Page number (default: 1)
- `per_page` (optional): Items per page (default: 15)
- `search` (optional): Search by name or email
- `major` (optional): Filter by major
- `graduation_year` (optional): Filter by graduation year
- `city` (optional): Filter by city

**Response:**
```json
{
  "success": true,
  "data": {
    "alumni": [
      {
        "id": 1,
        "name": "John Doe",
        "email": "john@example.com",
        "major": "Computer Science",
        "graduation_year": 2020,
        "city": "Jakarta",
        "profile_picture": "https://...",
        "current_job": "Software Engineer"
      }
    ],
    "pagination": {
      "current_page": 1,
      "per_page": 15,
      "total": 100,
      "last_page": 7
    }
  }
}
```

#### GET `/api/v1/alumni/{identifier}`
Get specific alumni by ID or email.

**Response:**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com",
    "major": "Computer Science",
    "graduation_year": 2020,
    "city": "Jakarta",
    "profile_picture": "https://...",
    "current_job": "Software Engineer",
    "bio": "Software engineer with 5 years experience...",
    "social_links": {
      "linkedin": "https://linkedin.com/in/johndoe",
      "github": "https://github.com/johndoe"
    }
  }
}
```

### Dashboard

#### GET `/api/dashboard`
Get dashboard data for authenticated user.

**Headers:**
```
Authorization: Bearer {token}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "user": {
      "id": 1,
      "name": "John Doe",
      "role": "alumni"
    },
    "stats": {
      "total_posts": 25,
      "total_connections": 150,
      "total_likes": 500,
      "profile_completion": 85
    },
    "recent_activity": [
      {
        "type": "post",
        "message": "Created a new post",
        "created_at": "2026-05-29T00:00:00Z"
      }
    ]
  }
}
```

### Tracking

#### POST `/api/tracking/track`
Track user activity for analytics.

**Headers:**
```
Authorization: Bearer {token}
```

**Request Body:**
```json
{
  "action": "page_view",
  "page": "/alumni/dashboard",
  "metadata": {
    "referrer": "https://google.com",
    "user_agent": "Mozilla/5.0..."
  }
}
```

**Response:**
```json
{
  "success": true,
  "message": "Activity tracked successfully"
}
```

### Chat

#### POST `/api/chat/send`
Send message to AI chat.

**Headers:**
```
Authorization: Bearer {token}
```

**Request Body:**
```json
{
  "message": "Hello, how can I help you?",
  "conversation_id": "optional-conversation-id"
}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "response": "Hello! I'm here to help you with...",
    "conversation_id": "conv_123456"
  }
}
```

## Error Responses

### 401 Unauthorized
```json
{
  "success": false,
  "message": "Unauthenticated",
  "errors": {
    "auth": "Invalid or missing authentication token"
  }
}
```

### 403 Forbidden
```json
{
  "success": false,
  "message": "Access denied",
  "errors": {
    "authorization": "You don't have permission to access this resource"
  }
}
```

### 404 Not Found
```json
{
  "success": false,
  "message": "Resource not found"
}
```

### 422 Validation Error
```json
{
  "success": false,
  "message": "Validation failed",
  "errors": {
    "email": ["The email field is required."],
    "password": ["The password must be at least 8 characters."]
  }
}
```

### 500 Internal Server Error
```json
{
  "success": false,
  "message": "Internal server error",
  "errors": {
    "exception": "Error message..."
  }
}
```

## Rate Limiting
API endpoints are rate-limited to prevent abuse:
- **Unauthenticated:** 60 requests per minute
- **Authenticated:** 120 requests per minute

Rate limit headers are included in responses:
```
X-RateLimit-Limit: 120
X-RateLimit-Remaining: 119
X-RateLimit-Reset: 1620000000
```

## Pagination
Paginated endpoints use Laravel's pagination:
- `page`: Current page number
- `per_page`: Items per page (default varies by endpoint)
- Response includes pagination metadata

## Filtering & Sorting
Most list endpoints support:
- **Filtering:** Query parameters for specific fields
- **Sorting:** `sort={field}` and `order={asc|desc}`
- **Search:** `search={query}` for text search

## Webhooks
Coming soon - webhook notifications for:
- New user registrations
- Post interactions
- System events

## SDK & Libraries
Coming soon - official SDK for:
- JavaScript/TypeScript
- Python
- PHP

## Support
For API support, contact: support@alumni-steman.my.id
