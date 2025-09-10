# Attendance Register API Documentation

## Overview
This API provides endpoints for mobile applications to manage attendance records, QR code check-ins, and proof uploads.

## Base URL
```
https://your-domain.com/api
```

## Authentication
All API endpoints require authentication. Include the Bearer token in the Authorization header:
```
Authorization: Bearer {your-token}
```

## Endpoints

### 1. Check In
**POST** `/attendance/check-in`

Check in a learner using QR code validation.

#### Request Body
```json
{
    "qr_code": "HOST_ABC123_20250110120000",
    "latitude": -26.2041,
    "longitude": 28.0473,
    "device_id": "device_123",
    "device_info": {
        "model": "iPhone 12",
        "os": "iOS 15.0",
        "app_version": "1.0.0"
    }
}
```

#### Response
```json
{
    "success": true,
    "message": "Check-in successful",
    "attendance_record": {
        "id": 1,
        "date": "2025-01-10",
        "check_in_time": "08:30:00",
        "status": "present",
        "program": {
            "id": 1,
            "title": "Digital Marketing Fundamentals"
        }
    }
}
```

### 2. Check Out
**POST** `/attendance/check-out`

Check out a learner.

#### Request Body
```json
{
    "attendance_record_id": 1,
    "latitude": -26.2041,
    "longitude": 28.0473
}
```

#### Response
```json
{
    "success": true,
    "message": "Check-out successful",
    "attendance_record": {
        "id": 1,
        "date": "2025-01-10",
        "check_in_time": "08:30:00",
        "check_out_time": "17:00:00",
        "status": "present",
        "total_hours": 8.5
    }
}
```

### 3. Get Attendance Records
**GET** `/attendance`

Get attendance records for the authenticated learner.

#### Query Parameters
- `start_date` (optional): Start date (YYYY-MM-DD)
- `end_date` (optional): End date (YYYY-MM-DD)
- `status` (optional): Filter by status
- `page` (optional): Page number for pagination

#### Response
```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "date": "2025-01-10",
            "check_in_time": "08:30:00",
            "check_out_time": "17:00:00",
            "status": "present",
            "total_hours": 8.5,
            "program": {
                "id": 1,
                "title": "Digital Marketing Fundamentals"
            }
        }
    ],
    "pagination": {
        "current_page": 1,
        "total_pages": 5,
        "per_page": 20,
        "total": 100
    }
}
```

### 4. Update Attendance Status
**PUT** `/attendance/{id}`

Update attendance status (for manual corrections).

#### Request Body
```json
{
    "status": "late",
    "notes": "Traffic delay"
}
```

#### Response
```json
{
    "success": true,
    "message": "Attendance updated successfully",
    "attendance_record": {
        "id": 1,
        "status": "late",
        "notes": "Traffic delay"
    }
}
```

### 5. Upload Proof
**POST** `/attendance/{id}/upload-proof`

Upload proof document for unauthorized absence.

#### Request Body (multipart/form-data)
- `proof_document`: File (PDF, JPG, PNG, DOC, DOCX)
- `proof_type`: String (medical_certificate, emergency_document, other)
- `notes`: String (optional)

#### Response
```json
{
    "success": true,
    "message": "Proof uploaded successfully",
    "attendance_record": {
        "id": 1,
        "proof_status": "pending",
        "proof_document_type": "medical_certificate"
    }
}
```

### 6. Get Programs
**GET** `/programs`

Get available programs for the learner.

#### Response
```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "title": "Digital Marketing Fundamentals",
            "program_code": "DMF001",
            "daily_rate": 150.00,
            "start_date": "2025-01-15",
            "end_date": "2025-03-15"
        }
    ]
}
```

### 7. Get Attendance Summary
**GET** `/attendance/summary`

Get attendance summary for the learner.

#### Query Parameters
- `start_date` (optional): Start date (YYYY-MM-DD)
- `end_date` (optional): End date (YYYY-MM-DD)

#### Response
```json
{
    "success": true,
    "summary": {
        "total_days": 20,
        "present_days": 18,
        "absent_days": 2,
        "attendance_rate": 90.0,
        "total_hours": 144.0,
        "estimated_pay": 2700.00
    }
}
```

### 8. Get Current Schedule
**GET** `/schedule/current`

Get learner's current schedule for today.

#### Response
```json
{
    "success": true,
    "data": {
        "date": "2025-01-10",
        "current_time": "08:30:00",
        "schedules": [
            {
                "program": {
                    "id": 1,
                    "title": "Digital Marketing Fundamentals",
                    "program_code": "DMF001",
                    "daily_rate": 150.00
                },
                "schedule": {
                    "id": 1,
                    "day_of_week": 5,
                    "day_name": "Friday",
                    "start_time": "08:00:00",
                    "end_time": "17:00:00",
                    "break_duration": 60,
                    "is_active": true
                },
                "is_current_time": true,
                "time_until_start": null,
                "time_until_end": "08:30:00"
            }
        ],
        "has_schedule_today": true
    }
}
```

### 9. Get Programs with Schedules
**GET** `/programs/with-schedules`

Get all learner's programs with their schedules.

#### Response
```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "title": "Digital Marketing Fundamentals",
            "program_code": "DMF001",
            "description": "Comprehensive digital marketing course",
            "daily_rate": 150.00,
            "start_date": "2025-01-15",
            "end_date": "2025-03-15",
            "duration_weeks": 8,
            "schedules": [
                {
                    "id": 1,
                    "day_of_week": 1,
                    "day_name": "Monday",
                    "start_time": "08:00:00",
                    "end_time": "17:00:00",
                    "break_duration": 60
                }
            ],
            "has_schedule_today": true
        }
    ]
}
```

### 10. Get Hosts for Program
**GET** `/hosts`

Get available hosts for a specific program.

#### Query Parameters
- `program_id` (required): Program ID
- `latitude` (optional): Current latitude
- `longitude` (optional): Current longitude

#### Response
```json
{
    "success": true,
    "data": {
        "program_id": 1,
        "hosts": [
            {
                "id": 1,
                "name": "Main Campus",
                "location_name": "Building A",
                "city": "Johannesburg",
                "province": "Gauteng",
                "latitude": -26.2041,
                "longitude": 28.0473,
                "radius_meters": 100,
                "qr_code": "HOST_ABC123_20250110120000",
                "is_active": true,
                "distance_meters": 45.2,
                "is_within_radius": true
            }
        ],
        "total_hosts": 1
    }
}
```

### 11. Get Nearest Host
**GET** `/hosts/nearest`

Get the nearest host for a program based on current location.

#### Query Parameters
- `program_id` (required): Program ID
- `latitude` (required): Current latitude
- `longitude` (required): Current longitude

#### Response
```json
{
    "success": true,
    "data": {
        "id": 1,
        "name": "Main Campus",
        "location_name": "Building A",
        "city": "Johannesburg",
        "province": "Gauteng",
        "latitude": -26.2041,
        "longitude": 28.0473,
        "radius_meters": 100,
        "qr_code": "HOST_ABC123_20250110120000",
        "distance_meters": 45.2,
        "is_within_radius": true
    }
}
```

### 12. Validate QR Code
**POST** `/hosts/validate-qr`

Validate a QR code and get host information.

#### Request Body
```json
{
    "qr_code": "HOST_ABC123_20250110120000",
    "latitude": -26.2041,
    "longitude": 28.0473
}
```

#### Response
```json
{
    "success": true,
    "data": {
        "host": {
            "id": 1,
            "name": "Main Campus",
            "location_name": "Building A",
            "city": "Johannesburg",
            "province": "Gauteng",
            "latitude": -26.2041,
            "longitude": 28.0473,
            "radius_meters": 100
        },
        "program": {
            "id": 1,
            "title": "Digital Marketing Fundamentals",
            "program_code": "DMF001"
        },
        "validation": {
            "is_valid": true,
            "is_within_radius": true,
            "distance_meters": 45.2
        }
    }
}
```

### 13. Get User's Assigned Programs
**GET** `/user/programs`

Get all programs the user is assigned to with their schedules and enrollment details.

#### Response
```json
{
    "success": true,
    "data": {
        "user": {
            "id": 1,
            "first_name": "John",
            "last_name": "Doe",
            "email": "john@example.com",
            "employee_number": "EMP001"
        },
        "programs": [
            {
                "enrollment": {
                    "id": 1,
                    "enrollment_date": "2025-01-01",
                    "completion_date": null,
                    "status": "active",
                    "eti_eligible": true,
                    "eti_monthly_amount": 150.00,
                    "attendance_percentage": 95,
                    "days_remaining": 45
                },
                "program": {
                    "id": 1,
                    "title": "Digital Marketing Fundamentals",
                    "program_code": "DMF001",
                    "description": "Comprehensive digital marketing course",
                    "daily_rate": 150.00,
                    "start_date": "2025-01-01",
                    "end_date": "2025-03-01",
                    "duration_weeks": 8,
                    "total_training_days": 40,
                    "status": "active",
                    "is_approved": true
                },
                "today_schedule": {
                    "id": 1,
                    "day_of_week": 5,
                    "day_name": "Friday",
                    "start_time": "08:00:00",
                    "end_time": "17:00:00",
                    "break_duration": 60,
                    "is_active": true,
                    "is_current_time": true,
                    "time_until_start": null,
                    "time_until_end": "08:30:00"
                },
                "all_schedules": [
                    {
                        "id": 1,
                        "day_of_week": 1,
                        "day_name": "Monday",
                        "start_time": "08:00:00",
                        "end_time": "17:00:00",
                        "break_duration": 60,
                        "is_active": true,
                        "is_today": false,
                        "is_current_time": false
                    }
                ]
            }
        ],
        "total_programs": 1,
        "has_schedule_today": true,
        "attendance_summary": {
            "has_attendance_today": true,
            "status": "present",
            "check_in_time": "08:15:00",
            "check_out_time": null,
            "hours_worked": 0,
            "is_payable": true,
            "calculated_pay": 150.00
        },
        "current_date": "2025-01-10",
        "current_time": "08:30:00"
    }
}
```

### 14. Get User's Primary Program
**GET** `/user/primary-program`

Get the user's primary (most recent active) program with today's schedule.

#### Response
```json
{
    "success": true,
    "data": {
        "enrollment": {
            "id": 1,
            "enrollment_date": "2025-01-01",
            "completion_date": null,
            "status": "active",
            "eti_eligible": true,
            "eti_monthly_amount": 150.00,
            "attendance_percentage": 95,
            "days_remaining": 45
        },
        "program": {
            "id": 1,
            "title": "Digital Marketing Fundamentals",
            "program_code": "DMF001",
            "description": "Comprehensive digital marketing course",
            "daily_rate": 150.00,
            "start_date": "2025-01-01",
            "end_date": "2025-03-01",
            "duration_weeks": 8,
            "total_training_days": 40
        },
        "today_schedule": {
            "id": 1,
            "day_of_week": 5,
            "day_name": "Friday",
            "start_time": "08:00:00",
            "end_time": "17:00:00",
            "break_duration": 60,
            "is_current_time": true,
            "time_until_start": null,
            "time_until_end": "08:30:00"
        },
        "has_schedule_today": true
    }
}
```

## Error Responses

### 400 Bad Request
```json
{
    "success": false,
    "message": "Validation failed",
    "errors": {
        "qr_code": ["QR code is required"],
        "latitude": ["Latitude must be a valid coordinate"]
    }
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
    "message": "Attendance record not found"
}
```

### 422 Unprocessable Entity
```json
{
    "success": false,
    "message": "QR code validation failed",
    "error": "Invalid QR code or location not within allowed radius"
}
```

## Status Codes

| Status | Description |
|--------|-------------|
| `present` | Learner is present |
| `late` | Learner arrived late |
| `absent_unauthorized` | Learner is absent without authorization |
| `absent_authorized` | Learner is absent with authorization |
| `excused` | Learner is excused |
| `on_leave` | Learner is on leave |
| `sick` | Learner is sick |
| `half_day` | Learner worked half day |

## Proof Types

| Type | Description |
|------|-------------|
| `medical_certificate` | Medical certificate from doctor |
| `emergency_document` | Emergency documentation |
| `other` | Other supporting documentation |

## Proof Status

| Status | Description |
|--------|-------------|
| `pending` | Proof is pending approval |
| `approved` | Proof has been approved |
| `rejected` | Proof has been rejected |

## Rate Limiting
API requests are limited to 100 requests per minute per user.

## File Upload Limits
- Maximum file size: 10MB
- Allowed file types: PDF, JPG, PNG, DOC, DOCX
- Maximum files per attendance record: 3

## QR Code Format
QR codes follow the format: `HOST_{HOST_CODE}_{TIMESTAMP}`

Example: `HOST_ABC123_20250110120000`

## Location Validation
- QR codes are validated against host locations
- Check-in must be within the specified radius (default: 100 meters)
- GPS coordinates are required for check-in/out
