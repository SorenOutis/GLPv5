# System Logo Management Setup

## Overview
This document provides instructions on how to use the new Logo Management system in LevelUp Academy. You can now upload and manage your system logos through the admin panel, and they will automatically display across your application.

## Database Setup

### 1. Create the Database Table

Run the migration to create the `system_logos` table:

```bash
php artisan migrate
```

This will create the `system_logos` table with the following columns:
- `id` - Primary key
- `name` - Logo name/description
- `logo_path` - Path to the light mode logo
- `logo_dark_path` - Path to the dark mode logo
- `favicon_path` - Path to the favicon
- `is_active` - Boolean flag for active logo (only one can be active at a time)
- `timestamps` - Created and updated timestamps

## Admin Panel Access

### 2. Accessing Logo Management

1. Go to your admin panel: `https://glpv5.test/admin`
2. Navigate to **Settings > System Logo** in the left sidebar
3. You will see a list of all uploaded logos

## Uploading a Logo

### 3. Upload Process

1. Click **"Upload New Logo"** button
2. Fill in the following fields:

   **Logo Name**: Give your logo a descriptive name (e.g., "LevelUp Academy Logo v1")

   **Light Mode Logo** (Required):
   - Upload your logo for light mode
   - Supported formats: PNG, JPG, SVG
   - Maximum size: 5MB
   - Recommended dimensions: 200x50px or similar aspect ratio

   **Dark Mode Logo** (Optional):
   - Upload your logo for dark mode
   - Same specifications as light mode logo
   - If not provided, light mode logo will be used

   **Favicon** (Optional):
   - Upload a favicon image
   - Supported formats: ICO, PNG
   - Maximum size: 2MB
   - Recommended dimensions: 32x32px

3. Toggle **"Activate This Logo"** to make this logo active
   - Only one logo can be active at a time
   - Activating a new logo will automatically deactivate the previous one

4. Click **"Create"** to save the logo

## Managing Logos

### 4. Edit Existing Logo

1. From the logo list, click the **Edit** icon on the logo you want to modify
2. Update any of the fields
3. Click **"Save"** to apply changes

### 5. Activate a Logo

You can activate a logo in two ways:

**Method 1: During Upload/Edit**
- Toggle the "Activate This Logo" switch
- Click Save

**Method 2: From Logo List**
- Find the logo in the list
- Click the **"Activate"** button
- Confirm the action

### 6. Delete a Logo

1. From the logo list or edit page, click the **Delete** icon
2. Confirm the deletion

## Where Logos Are Displayed

Once activated, your logo will automatically appear on:

1. **Welcome Page** (`/`)
   - Header/Navigation area
   - Switches between light and dark versions based on theme

2. **Login Page** (`/login`)
   - Header/Navigation area
   - Switches between light and dark versions based on theme

3. **Register Page** (`/register`)
   - Header/Navigation area
   - Switches between light and dark versions based on theme

## Logo Storage

Uploaded logos are stored in:
```
storage/app/public/logos/
```

Make sure this directory is publicly accessible:

```bash
php artisan storage:link
```

If you haven't already created the symbolic link, run the above command.

## How It Works

### Frontend Logic

The logo is fetched via a Vue composable called `useLogo`:

```typescript
import { useLogo } from '@/composables/useLogo';

const { hasLogo, logo, getLightLogo, getDarkLogo } = useLogo();
```

- `hasLogo` - Boolean indicating if a logo is uploaded and active
- `logo` - Logo object containing paths and metadata
- `getLightLogo()` - Returns light mode logo path
- `getDarkLogo()` - Returns dark mode logo path

### API Endpoint

The logo data is retrieved from:
```
GET /api/logo
```

This endpoint returns:
```json
{
    "hasLogo": true,
    "logo": {
        "name": "LevelUp Academy Logo",
        "lightPath": "https://glpv5.test/storage/logos/...",
        "darkPath": "https://glpv5.test/storage/logos/...",
        "faviconPath": "https://glpv5.test/storage/logos/..."
    }
}
```

## Best Practices

1. **Logo Dimensions**: Use logos with consistent aspect ratios (e.g., 200x50px)
2. **Format**: PNG or SVG formats are recommended for better quality
3. **Color Contrast**: Ensure light and dark mode logos have good contrast with their respective backgrounds
4. **File Size**: Keep logos under 100KB for optimal performance
5. **Naming**: Use descriptive names with version numbers (e.g., "v1", "v2")
6. **Backup**: Keep copies of your logos before uploading new versions

## Fallback Behavior

If no logo is uploaded:
- The system displays a default gradient icon (✦)
- The text "LevelUp Academy" remains visible
- All functionality continues to work normally

## Troubleshooting

### Logo Not Appearing

1. **Check Activation**: Ensure the logo is marked as "Active" in the admin panel
2. **Clear Cache**: Clear your browser cache and refresh the page
3. **Check Storage Link**: Verify the `storage/app/public` symlink exists
4. **Verify Upload**: Check that files were uploaded to `storage/app/public/logos/`

### Logo Not Switching Between Themes

1. Ensure you uploaded both light and dark mode logos
2. Check that the file paths are correct in the database
3. Verify image files exist in the storage folder

### Image Not Loading

1. Run `php artisan storage:link` to create the symbolic link
2. Check file permissions on the storage directory
3. Verify the image file exists and is not corrupted

## Commands Reference

```bash
# Run migrations
php artisan migrate

# Create storage symlink
php artisan storage:link

# Clear application cache
php artisan cache:clear

# Clear view cache
php artisan view:clear
```

## Support

For issues or questions about the logo management system, please refer to this document or contact your system administrator.
