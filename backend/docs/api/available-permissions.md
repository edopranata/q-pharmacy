# Available Permissions API

## Endpoint
`GET /api/app/management/roles/available/permissions`

## Description
Retrieves all available permissions in the system with enhanced metadata for role assignment purposes.

## Authentication
- **Required**: Yes
- **Type**: Bearer Token (Sanctum)
- **Permission**: `app.management.roles.index`

## Rate Limiting
- **Limit**: 30 requests per minute per user

## Request Headers
```
Authorization: Bearer {token}
Accept: application/json
Content-Type: application/json
```

## Response Format

### Success Response (200 OK)
```json
{
    "success": true,
    "message": "Available permissions retrieved successfully",
    "data": [
        {
            "id": 1,
            "name": "app.management.users.index",
            "guard_name": "web",
            "display_name": "App Management Users Index",
            "category": "User Management",
            "description": "View users list",
            "created_at": "2024-01-15 10:30:00"
        },
        {
            "id": 2,
            "name": "app.management.roles.store",
            "guard_name": "web",
            "display_name": "App Management Roles Store",
            "category": "Role Management",
            "description": "Create new roles",
            "created_at": "2024-01-15 10:30:00"
        }
    ]
}
```

### Error Response (500 Internal Server Error)
```json
{
    "success": false,
    "message": "Failed to retrieve available permissions. Please try again later."
}
```

### Unauthorized Response (401 Unauthorized)
```json
{
    "message": "Unauthenticated."
}
```

### Forbidden Response (403 Forbidden)
```json
{
    "success": false,
    "message": "This action is unauthorized."
}
```

### Rate Limit Exceeded (429 Too Many Requests)
```json
{
    "message": "Too Many Attempts."
}
```

## Data Fields

| Field | Type | Description |
|-------|------|-------------|
| `id` | integer | Unique permission identifier |
| `name` | string | Permission name (dot notation) |
| `guard_name` | string | Guard name (usually 'web') |
| `display_name` | string | Human-readable permission name |
| `category` | string | Permission category for grouping |
| `description` | string | Detailed permission description |
| `created_at` | string | Permission creation timestamp |

## Permission Categories

- **User Management**: Permissions related to user operations
- **Role Management**: Permissions related to role operations
- **Master Data**: Permissions for master data management
- **Audit Logs**: Permissions for audit log access
- **User Activity**: Permissions for user activity monitoring
- **Product Management**: Permissions for product operations
- **Inventory Management**: Permissions for inventory operations
- **Sales Management**: Permissions for sales operations
- **Statistics & Reports**: Permissions for reports and analytics
- **General**: Other miscellaneous permissions

## Caching
- **Cache Duration**: 1 hour (3600 seconds)
- **Cache Key**: `available_permissions`
- **Cache Tags**: `permissions`, `roles`

## Security Features
- Authentication required via Sanctum
- Permission-based access control
- Rate limiting (30 requests/minute)
- Audit logging for access tracking
- Error logging for debugging
- Input sanitization and validation

## Performance Optimizations
- Database query optimization (select specific fields)
- Response caching (1 hour TTL)
- Efficient data transformation
- Minimal database queries

## Usage Examples

### JavaScript (Fetch API)
```javascript
const response = await fetch('/api/app/management/roles/available/permissions', {
    method: 'GET',
    headers: {
        'Authorization': `Bearer ${token}`,
        'Accept': 'application/json',
        'Content-Type': 'application/json'
    }
});

const data = await response.json();
console.log(data.data); // Array of permissions
```

### cURL
```bash
curl -X GET "http://localhost:8000/api/app/management/roles/available/permissions" \
  -H "Authorization: Bearer your-token-here" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json"
```

## Notes
- Permissions are cached for performance
- Cache is automatically cleared when permissions are modified
- All access attempts are logged for audit purposes
- Response includes enhanced metadata for better UX
- Permissions are grouped by categories for easier management