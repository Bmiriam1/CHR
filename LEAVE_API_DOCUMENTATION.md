# Leave Management API Documentation

## Overview
The Leave Management API provides endpoints for managing leave requests, balances, and related functionality. All endpoints require authentication using **Laravel Sanctum** tokens.

## Authentication with Laravel Sanctum

### Getting Started
1. **Login** to get a Sanctum token: `POST /api/auth/login`
2. **Use the token** in all subsequent requests: `Authorization: Bearer {token}`
3. **Logout** when done: `POST /api/auth/logout`

### Authentication Flow
```javascript
// 1. Login to get token
const loginResponse = await fetch('/api/auth/login', {
  method: 'POST',
  headers: { 'Content-Type': 'application/json' },
  body: JSON.stringify({ 
    email: 'user@example.com', 
    password: 'password' 
  })
});
const { data: { token } } = await loginResponse.json();

// 2. Use token for authenticated requests
const leaveResponse = await fetch('/api/leave/requests', {
  method: 'POST',
  headers: {
    'Authorization': `Bearer ${token}`,
    'Content-Type': 'application/json'
  },
  body: JSON.stringify(leaveData)
});

// 3. Logout when done
await fetch('/api/auth/logout', {
  method: 'POST',
  headers: { 'Authorization': `Bearer ${token}` }
});
```

### Security Features
- ✅ **Token-based authentication** with Laravel Sanctum
- ✅ **Automatic token revocation** on new login (single session)
- ✅ **Protected routes** with `auth:sanctum` middleware
- ✅ **User context** available in all API endpoints
- ✅ **Secure token generation** and validation
- ✅ **Proper error handling** for unauthorized requests

## Base URL
```
https://your-domain.com/api/leave
```

## Authentication Endpoints

### Login
**POST** `/api/auth/login`

Authenticate user and get Sanctum token.

#### Request Body
```json
{
    "email": "user@example.com",
    "password": "password"
}
```

#### Success Response (200)
```json
{
    "success": true,
    "message": "Login successful",
    "data": {
        "user": {
            "id": 1,
            "first_name": "John",
            "last_name": "Doe",
            "email": "user@example.com"
        },
        "token": "1|abc123def456ghi789jkl012mno345pqr678stu901vwx234yz"
    }
}
```

### Logout
**POST** `/api/auth/logout`

Revoke the current Sanctum token.

#### Headers
```
Authorization: Bearer {your-token}
```

#### Success Response (200)
```json
{
    "success": true,
    "message": "Logged out successfully"
}
```

## Leave Management Endpoints

### 1. Apply for Leave
**POST** `/api/leave/requests`

Submit a new leave request.

#### Request Body
```json
{
    "program_id": 1,
    "leave_type_id": 1,
    "start_date": "2025-10-01",
    "end_date": "2025-10-03",
    "reason": "Family vacation",
    "notes": "Optional additional notes",
    "is_emergency": false
}
```

#### Parameters
| Field | Type | Required | Description |
|-------|------|----------|-------------|
| `program_id` | integer | Yes | ID of the program the user is enrolled in |
| `leave_type_id` | integer | Yes | ID of the leave type (Annual, Sick, etc.) |
| `start_date` | date | Yes | Start date of leave (YYYY-MM-DD format) |
| `end_date` | date | Yes | End date of leave (YYYY-MM-DD format) |
| `reason` | string | Yes | Reason for leave request (max 1000 characters) |
| `notes` | string | No | Additional notes (max 1000 characters) |
| `is_emergency` | boolean | No | Whether this is an emergency request (default: false) |

#### Validation Rules
- `start_date` must be today or in the future
- `end_date` must be after or equal to `start_date`
- Minimum notice period varies by leave type:
  - Annual Leave: 14 days
  - Sick Leave: 0 days (can be same day)
  - Maternity Leave: 30 days
  - Paternity Leave: 14 days
  - Family Responsibility: 7 days
  - Study Leave: 30 days
  - Emergency Leave: 0 days

#### Success Response (201)
```json
{
    "success": true,
    "data": {
        "id": 4,
        "leave_type": "Annual Leave",
        "start_date": "2025-10-01",
        "end_date": "2025-10-03",
        "duration": 3,
        "status": "pending",
        "reason": "Family vacation",
        "notes": "Optional additional notes",
        "is_emergency": false,
        "requires_medical_certificate": false,
        "submitted_at": "2025-09-12T06:43:40.000000Z"
    },
    "message": "Leave request submitted successfully",
    "warnings": []
}
```

#### Error Response (422)
```json
{
    "success": false,
    "message": "Validation failed",
    "errors": {
        "start_date": ["The start date must be a date after or equal to today."],
        "leave_type_id": ["Insufficient Annual Leave balance. Available: 15 days, Requested: 20 days"]
    }
}
```

#### Error Response (403)
```json
{
    "success": false,
    "message": "You are not enrolled in this program"
}
```

### 2. Get Leave Balance
**GET** `/api/leave/balance?year=2025`

Get the user's leave balance summary for a specific year.

#### Query Parameters
| Parameter | Type | Required | Default | Description |
|-----------|------|----------|---------|-------------|
| `year` | integer | No | Current year | Year to get balance for |

#### Success Response (200)
```json
{
    "success": true,
    "data": {
        "balance_record": {
            "id": 1,
            "user_id": 1,
            "leave_year": 2025,
            "total_entitled": 21,
            "total_taken": 5,
            "total_balance": 16,
            "is_probationary": false,
            "employment_start_date": "2024-01-01"
        },
        "balances": {
            "annual": {
                "current": 16,
                "accrued_this_year": 21,
                "taken_this_year": 5,
                "carry_over": 0
            },
            "sick": {
                "current": 30,
                "accrued_this_year": 30,
                "taken_this_year": 0,
                "carry_over": 0
            }
        },
        "accrual_summary": [...],
        "carry_over_summary": [...],
        "expiring_carry_over": [...],
        "service_info": {
            "employment_start_date": "2024-01-01",
            "is_probation": false,
            "is_probationary": false,
            "probation_end_date": null,
            "service_months": 20
        }
    },
    "message": "Leave balance retrieved successfully"
}
```

### 3. Get Leave Types
**GET** `/api/leave/types`

Get available leave types for the authenticated user.

#### Success Response (200)
```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "code": "ANNUAL",
            "name": "Annual Leave",
            "description": "Annual vacation leave",
            "max_days_per_year": 21,
            "min_notice_days": 14,
            "max_consecutive_days": 30,
            "requires_medical_certificate": false,
            "is_paid_leave": true,
            "is_taxable": true
        }
    ],
    "message": "Leave types retrieved successfully"
}
```

### 4. Get User Programs
**GET** `/api/leave/programs`

Get programs the user is enrolled in.

#### Success Response (200)
```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "title": "Digital Skills Development Program",
            "company_id": 1,
            "status": "active",
            "daily_rate": 350.00
        }
    ],
    "message": "User programs retrieved successfully"
}
```

### 5. Get Leave Requests
**GET** `/api/leave/requests?status=pending&limit=10&offset=0`

Get user's leave requests with optional filtering.

#### Query Parameters
| Parameter | Type | Required | Default | Description |
|-----------|------|----------|---------|-------------|
| `status` | string | No | all | Filter by status (pending, approved, rejected, cancelled) |
| `limit` | integer | No | 20 | Number of records to return |
| `offset` | integer | No | 0 | Number of records to skip |

#### Success Response (200)
```json
{
    "success": true,
    "data": {
        "requests": [
            {
                "id": 4,
                "leave_type": "Annual Leave",
                "start_date": "2025-10-01",
                "end_date": "2025-10-03",
                "duration": 3,
                "status": "pending",
                "reason": "Family vacation",
                "submitted_at": "2025-09-12T06:43:40.000000Z"
            }
        ],
        "pagination": {
            "total": 1,
            "per_page": 20,
            "current_page": 1,
            "last_page": 1
        }
    },
    "message": "Leave requests retrieved successfully"
}
```

### 6. Get Single Leave Request
**GET** `/api/leave/requests/{id}`

Get details of a specific leave request.

#### Success Response (200)
```json
{
    "success": true,
    "data": {
        "id": 4,
        "leave_type": "Annual Leave",
        "program": "Digital Skills Development Program",
        "start_date": "2025-10-01",
        "end_date": "2025-10-03",
        "duration": 3,
        "status": "pending",
        "reason": "Family vacation",
        "notes": "Optional additional notes",
        "is_emergency": false,
        "requires_medical_certificate": false,
        "is_paid_leave": true,
        "daily_rate_at_time": 350.00,
        "total_leave_pay": 1050.00,
        "submitted_at": "2025-09-12T06:43:40.000000Z",
        "approved_at": null,
        "approved_by": null
    },
    "message": "Leave request retrieved successfully"
}
```

### 7. Cancel Leave Request
**PATCH** `/api/leave/requests/{id}/cancel`

Cancel a pending leave request.

#### Success Response (200)
```json
{
    "success": true,
    "data": {
        "id": 4,
        "status": "cancelled",
        "cancelled_at": "2025-09-12T07:00:00.000000Z"
    },
    "message": "Leave request cancelled successfully"
}
```

### 8. Get Accrual History
**GET** `/api/leave/accrual-history?limit=50`

Get user's leave accrual history.

#### Success Response (200)
```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "leave_type": "Annual Leave",
            "accrual_date": "2025-01-01",
            "days_accrued": 1.75,
            "balance_after": 16.75
        }
    ],
    "message": "Accrual history retrieved successfully"
}
```

### 9. Get Carry Over Summary
**GET** `/api/leave/carry-over-summary`

Get carry over leave days summary.

#### Success Response (200)
```json
{
    "success": true,
    "data": [
        {
            "leave_type": "Annual Leave",
            "from_year": 2024,
            "carried_over_days": 5,
            "expiry_date": "2025-03-31",
            "remaining_days": 3
        }
    ],
    "message": "Carry over summary retrieved successfully"
}
```

## Error Handling

All endpoints return consistent error responses:

### 400 Bad Request
```json
{
    "success": false,
    "message": "Invalid request data"
}
```

### 401 Unauthorized
```json
{
    "success": false,
    "message": "Unauthenticated"
}
```

### 403 Forbidden
```json
{
    "success": false,
    "message": "Access denied"
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
        "field_name": ["Error message"]
    }
}
```

### 500 Internal Server Error
```json
{
    "success": false,
    "message": "Internal server error"
}
```

## Rate Limiting

API endpoints are rate limited to prevent abuse. Default limits:
- 60 requests per minute per user
- 1000 requests per hour per user

## Examples

### cURL Example - Apply for Leave
```bash
curl -X POST https://your-domain.com/api/leave/requests \
  -H "Authorization: Bearer your-token-here" \
  -H "Content-Type: application/json" \
  -d '{
    "program_id": 1,
    "leave_type_id": 1,
    "start_date": "2025-10-01",
    "end_date": "2025-10-03",
    "reason": "Family vacation",
    "notes": "Optional additional notes",
    "is_emergency": false
  }'
```

### JavaScript Example - Apply for Leave
```javascript
const response = await fetch('/api/leave/requests', {
  method: 'POST',
  headers: {
    'Authorization': 'Bearer your-token-here',
    'Content-Type': 'application/json',
  },
  body: JSON.stringify({
    program_id: 1,
    leave_type_id: 1,
    start_date: '2025-10-01',
    end_date: '2025-10-03',
    reason: 'Family vacation',
    notes: 'Optional additional notes',
    is_emergency: false
  })
});

const data = await response.json();
console.log(data);
```

## South African Employment Law Compliance

The API enforces South African employment law requirements:

- **Annual Leave**: 21 days per year, 14 days notice required
- **Sick Leave**: 30 days per 36-month cycle, medical certificate required after 2 days
- **Maternity Leave**: 120 days (4 months) consecutive, medical certificate required
- **Paternity Leave**: 10 days, 14 days notice required
- **Family Responsibility Leave**: 3 days per year
- **Study Leave**: As per company policy, typically 5-10 days

All leave calculations exclude weekends and public holidays where applicable.
