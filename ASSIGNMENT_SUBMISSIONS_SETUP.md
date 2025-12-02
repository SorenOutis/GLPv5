# Assignment Submissions Feature Setup

This document outlines the assignment submissions feature that allows students to upload their completed assignments and admins to view/grade them.

## What Was Created

### 1. Database Changes

**Migration: `database/migrations/2025_12_01_105954_create_assignment_submissions_table.php`**

The submissions table includes:
- `id` - Primary key
- `user_id` - Foreign key to users table
- `assignment_id` - Foreign key to assignments table
- `file_path` - Path to uploaded submission file
- `notes` - Optional student notes
- `status` - Submission status (submitted, pending, graded)
- `grade` - Numeric grade (0-100)
- `feedback` - Instructor feedback text
- `timestamps` - Created and updated timestamps

### 2. Models

**Model: `app/Models/AssignmentSubmission.php`**
- Relationships with User and Assignment models
- Proper fillable attributes and casts

**Updated: `app/Models/Assignment.php`**
- Added `submissions()` relationship to access all submissions for an assignment

### 3. Admin Panel (Filament)

**Resource: `app/Filament/Resources/AssignmentSubmissionResource.php`**

Features:
- View all student submissions
- Filter by status (Submitted, Pending Review, Graded)
- Filter by assignment
- Edit submissions to add grades and feedback
- Delete submissions
- View student name, assignment title, grade, and submission date

**Pages:**
- `Pages/ListAssignmentSubmissions.php` - Main listing with filters
- `Pages/CreateAssignmentSubmission.php` - Create new submission
- `Pages/EditAssignmentSubmission.php` - Edit/grade submissions

### 4. Controller

**Updated: `app/Http/Controllers/AssignmentController.php`**

New methods:
- `upload()` - Handle file uploads from students
  - Validates file (max 100MB)
  - Creates or updates submission
  - Stores file in `/storage/submissions` directory
- `show()` - View assignment details with submission status

Methods automatically include:
- Student's submission status on assignment cards
- Feedback and grades if graded

### 5. Frontend (Vue)

**Updated: `resources/js/pages/Assignment.vue`**

New Features:
- **Upload Button** - Appears on active assignments not past due date
- **Status Badge** - Shows:
  - ✓ Submitted (blue badge)
  - ✓ Graded with percentage (green badge)
  - Pending Review (yellow badge)
- **Upload Dialog** - Modal for submitting assignments
  - File upload with drag-drop indicator
  - Optional notes field
  - Shows selected filename
  - Upload progress indicator

### 6. Routes

**New Routes in `routes/web.php`:**
```php
POST /assignments/{assignment}/upload  - Upload submission
GET  /assignments/{assignment}         - View assignment details
```

## How to Use

### For Students

1. Navigate to the Assignments page
2. Find an assignment and click the "Upload Assignment" button
3. Select a file (PDF, DOC, XLS, TXT, Images, ZIP)
4. Optionally add notes about your submission
5. Click "Submit"
6. Your submission status will update to "✓ Submitted"

**Note:** You cannot upload after the due date

### For Admins

1. Go to Admin Panel → Submissions
2. View all student submissions
3. Filter by status or assignment
4. Click on a submission to edit:
   - Add a grade (0-100)
   - Add feedback for the student
   - Change status
5. Save changes

Students will see their grade and feedback on their assignments page.

## File Uploads

- Files are stored in: `/storage/app/public/submissions/`
- Maximum file size: 100MB
- Supported formats: PDF, DOC, DOCX, XLS, XLSX, TXT, JPG, JPEG, PNG, WEBP, ZIP, RAR
- Old submissions are replaced when a new one is uploaded

## Statuses

- **submitted** - Student has uploaded the assignment
- **pending** - Admin is reviewing but hasn't graded
- **graded** - Admin has added a grade and feedback

## Database Structure

```
assignments (existing)
├── id
├── title
├── description
├── file_path (assignment file, not submission)
├── due_date
├── category
├── is_active
├── timestamps

assignment_submissions (new)
├── id
├── user_id ─────→ users.id
├── assignment_id → assignments.id
├── file_path (student submission)
├── notes (optional)
├── status
├── grade (optional)
├── feedback (optional)
└── timestamps
```

## Next Steps (Optional)

1. Add email notifications when a submission is graded
2. Add late submission penalties
3. Add submission deadline extensions
4. Create a student dashboard showing all grades
5. Add plagiarism detection
6. Export grades as CSV for grading systems

## Testing

To test the feature:

```bash
# 1. Create an assignment (via admin panel)
# 2. Go to /assignments as a logged-in student
# 3. Click "Upload Assignment"
# 4. Submit a file
# 5. Check admin panel → Submissions
# 6. Edit submission to add grade
# 7. Refresh /assignments and see the grade
```
