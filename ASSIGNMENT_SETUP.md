# Assignment Module Setup

## Overview
This document describes the Assignment module setup, which allows admins to upload and manage assignments through the Filament admin panel, and users to view and download them on their dashboard.

## Files Created

### Backend (Laravel)

#### 1. Model
- **File**: `app/Models/Assignment.php`
- **Description**: Assignment model with fillable attributes for title, description, file_path, due_date, and is_active status.

#### 2. Migration
- **File**: `database/migrations/2024_12_01_000000_create_assignments_table.php`
- **Description**: Creates assignments table with columns:
  - id (primary key)
  - title (string, required)
  - description (text, optional)
  - file_path (string, optional) - stores uploaded file path
  - due_date (datetime, optional)
  - is_active (boolean, default: true)
  - timestamps

#### 3. Controller
- **File**: `app/Http/Controllers/AssignmentController.php`
- **Description**: Handles fetching active assignments and rendering them for the user dashboard.

#### 4. Filament Resource
- **File**: `app/Filament/Resources/AssignmentResource.php`
- **Pages**:
  - `app/Filament/Resources/AssignmentResource/Pages/ListAssignments.php`
  - `app/Filament/Resources/AssignmentResource/Pages/CreateAssignment.php`
  - `app/Filament/Resources/AssignmentResource/Pages/EditAssignment.php`

**Features**:
- Title input (required)
- Description textarea
- File upload (supports: PDF, Word, Excel, images, plain text)
- Max file size: 100MB
- Due date picker
- Active/Inactive toggle
- File storage: `/storage/assignments/`

#### 5. Routes
- **File**: `routes/web.php`
- **New Route**: `GET /assignments` - Protected route requiring auth and verified email

### Frontend (Vue 3)

#### 1. Assignment Page Component
- **File**: `resources/js/pages/Assignment.vue`
- **Features**:
  - Displays all active assignments in a grid (responsive: 1 col mobile, 2 cols tablet, 3 cols desktop)
  - Shows assignment title, description preview, posted date, and due date
  - Visual status badges: "Overdue" (red) or "Pending" (green)
  - Interactive cards with hover effects
  - Click to view details in a modal dialog
  - Download functionality for attached files
  - Empty state when no assignments exist
  - Date formatting with locale support

#### 2. Sidebar Navigation
- **File**: `resources/js/components/AppSidebar.vue`
- **Change**: Added "Assignments" link with FileText icon to main navigation

## Setup Instructions

### 1. Run Migration
```bash
php artisan migrate
```

### 2. Access Admin Panel
1. Go to: `https://glpv5.test/admin`
2. Navigate to "Assignments" in the sidebar
3. Click "Create" button to add new assignments

### 3. Upload Assignments
In the Filament admin panel:
1. Fill in the assignment title
2. Add a description (optional)
3. Upload a file (PDF, Word, Excel, images, etc.)
4. Set a due date
5. Toggle "Active" if needed
6. Click "Save"

### 4. View Assignments (User Side)
1. Go to: `https://glpv5.test/dashboard`
2. Click "Assignments" in the sidebar
3. Browse available assignments
4. Click on any assignment card to see full details
5. Download the attached file if available

## Assignment Fields

| Field | Type | Description | Required |
|-------|------|-------------|----------|
| title | string | Assignment name | Yes |
| description | text | Assignment details | No |
| file_path | string | Path to uploaded file | No |
| due_date | datetime | When assignment is due | No |
| is_active | boolean | Show/hide assignment to users | No |

## Storage
- Files are stored in: `storage/app/public/assignments/`
- Access via: `/storage/assignments/{filename}`

## Status Indicators
- **Pending**: Current date is before due date (green)
- **Overdue**: Current date is after due date (red)

## UI Components Used
- Card component for assignment display
- Dialog component for detailed view
- Button component for download
- Custom icon SVGs for visual indicators
- Responsive grid layout

## Next Steps (Optional)
1. Add email notifications when assignments are posted
2. Add submission tracking
3. Add assignment categories/filtering
4. Add progress tracking for completed assignments
5. Add student submission uploads
