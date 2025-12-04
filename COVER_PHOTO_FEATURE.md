# Cover Photo Feature Implementation

## Overview
Added the ability for users to upload and manage a cover photo on their profile page at `/profile`.

## Changes Made

### 1. Database Migration
**File:** `database/migrations/2025_12_04_000000_add_cover_photo_to_users_table.php`
- Added `cover_photo_path` column to the `users` table
- Column is nullable to maintain backward compatibility
- Migration has been applied to the database

### 2. Model Updates
**File:** `app/Models/User.php`
- Added `cover_photo_path` to the `$fillable` array to allow mass assignment

### 3. Request Validation
**File:** `app/Http/Requests/Settings/ProfileUpdateRequest.php`
- Added validation rule for `cover_photo` field:
  - Nullable (user can choose not to upload)
  - Must be an image file
  - Accepted formats: JPEG, PNG, JPG, GIF, WebP
  - Maximum file size: 5MB

### 4. Controller Logic
**File:** `app/Http/Controllers/Settings/ProfileController.php`
- Updated `update()` method to handle file uploads:
  - Checks if a cover photo file is present
  - Deletes the old cover photo from storage before uploading new one
  - Stores the new cover photo in `storage/app/public/cover-photos/`
  - Updates the user record with the new file path

### 5. Frontend UI
**File:** `resources/js/pages/settings/Profile.vue`

#### Cover Photo Display in Profile Card
- Shows cover photo if one exists with a "Change Cover" button
- Displays an interactive placeholder ("Add Cover Photo") if no cover photo exists
- Cover photo takes up the top 192px (h-48) of the profile card

#### File Upload Input in Settings Form
- Added a new "📸 Cover Photo" section in the Profile Information form
- Includes a drag-and-drop style upload area
- Shows selected filename after selection
- Displays validation errors if any
- Allows users to remove selected photo before saving
- Form is set to `multipart` to support file uploads

#### JavaScript Functions
- `handleCoverPhotoSelect()` - Handles file selection and creates preview
- `openCoverPhotoUpload()` - Triggers the file input dialog
- `removeCoverPhoto()` - Clears the selected file and preview
- `getCoverPhotoUrl()` - Returns the correct URL for preview or saved photo
- `onFormSubmit()` - Attaches the file to the form before submission

## File Storage
- Cover photos are stored in `storage/app/public/cover-photos/`
- Accessed via `/storage/cover-photos/{filename}`
- Files are automatically deleted when a new cover photo is uploaded

## User Experience

1. **Adding a Cover Photo:**
   - Click on "Add Cover Photo" placeholder in the profile card OR
   - Click on the file upload area in the settings form
   - Select an image file
   - Click "Save Changes" to upload

2. **Changing a Cover Photo:**
   - Click "📸 Change Cover" button on the profile card OR
   - Upload a new file in the settings form
   - Click "Save Changes" to replace the old one

3. **Visual Feedback:**
   - File name displays in the upload area after selection
   - Preview shows in the profile card immediately after selection
   - Success message appears after saving
   - Error messages display validation issues

## Technical Details

- Uses Inertia Vue 3 Form component with `multipart` attribute
- Laravel Storage facade handles file operations
- Images are publicly accessible through Laravel's public disk
- Old files are cleaned up automatically to prevent disk bloat
- Uses CSS Tailwind for responsive, modern styling

## Testing

To verify the feature:
1. Navigate to `/profile` in the application
2. Click "📸 Add Cover Photo" or the file upload area
3. Select a valid image (PNG, JPG, GIF, or WebP, max 5MB)
4. Click "Save Changes"
5. Verify the cover photo appears at the top of the profile card
6. Try uploading a different image to test the update functionality
