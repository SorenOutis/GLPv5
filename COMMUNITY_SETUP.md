# Community Feature Setup

## Overview
Converted the Messages page into a fully functional Community feature similar to Reddit where users can create posts and admins can manage them.

## What Was Created

### 1. Database & Model
- **Model**: `app/Models/CommunityPost.php`
- **Migration**: Created `community_posts` table with columns:
  - `id` - Primary key
  - `user_id` - Foreign key to users table
  - `title` - Post title (max 255 chars)
  - `content` - Full post content
  - `excerpt` - Preview text (auto-generated)
  - `views` - View counter
  - `likes` - Like counter
  - `is_pinned` - Pin to top flag
  - `is_approved` - Approval status
  - `status` - State (pending/approved/rejected)
  - `timestamps` - created_at, updated_at
  - `soft_deletes` - For soft delete support

### 2. Routes
Updated `routes/web.php` with new community routes:
- `GET /community` - View all approved posts
- `GET /community/{post}` - View post detail
- `POST /community` - Create new post
- `PUT /community/{post}` - Update post
- `DELETE /community/{post}` - Delete post
- `POST /community/{post}/like` - Like a post

### 3. Controller
Created `app/Http/Controllers/CommunityController.php` with actions:
- **index()** - Display approved posts with filtering and pagination
- **show()** - Display post detail and increment views
- **store()** - Create new post (requires approval)
- **update()** - Update own posts
- **destroy()** - Delete own posts
- **like()** - Like a post

### 4. Frontend (Vue 3)
Created `resources/js/pages/Community.vue` with features:
- **Stats Section**: Total posts, today's activity, top contributor
- **New Post Modal**: Form to submit new posts with validation
- **Post List**: Filterable, searchable list of approved posts
- **Post Details Modal**: Full post view with engagement stats
- **Like System**: Quick like button for posts
- **Search**: Search posts by title or content
- **Pinned Posts**: Visual indicator for pinned/featured posts

### 5. Admin Panel (Filament)
Created `app/Filament/Resources/CommunityPostResource.php` for admins:
- **List View** with columns:
  - Pinned status
  - Post title
  - Author name
  - Approval status (badge)
  - Active status
  - View count
  - Like count
  - Creation date

- **Actions**:
  - ✅ Approve pending posts
  - ❌ Reject posts
  - 📌 Pin/Unpin posts
  - Edit posts
  - Delete posts

- **Bulk Actions**:
  - Approve multiple posts
  - Reject multiple posts

- **Form Fields** (for admins creating/editing):
  - Title
  - Content (textarea)
  - Excerpt (auto-generated)
  - Author selection
  - Status dropdown
  - Approved toggle
  - Pinned toggle
  - Views counter
  - Likes counter

## Features

### User Features
1. **Create Posts**: Submit posts that require admin approval
2. **Search Posts**: Filter community posts by keywords
3. **Like Posts**: Quick engagement metric
4. **View Count**: Track post popularity
5. **Community Stats**: See community health metrics
6. **Post Details**: Read full posts in modal view

### Admin Features
1. **Approval Workflow**: Approve/reject pending posts
2. **Post Management**: Edit or delete any post
3. **Pin Important Posts**: Featured posts at the top
4. **Bulk Moderation**: Approve/reject multiple posts at once
5. **Analytics**: View count, likes, and post stats
6. **Author Management**: Assign posts to different users

## How to Use

### For Users
1. Navigate to `/community` in the app
2. Click "New Post" button
3. Enter title and content
4. Submit for approval
5. Post appears once approved by admin
6. Like and read other posts

### For Admins
1. Go to Filament admin panel
2. Navigate to "Community" > "Posts"
3. View pending posts with "pending" status
4. Click "Approve" to publish or "Reject" to decline
5. Use "Pin" to feature important posts
6. Use bulk actions for mass moderation

## Database Notes
- Uses soft deletes to preserve post history
- Foreign key constraint on user_id (cascade delete)
- Status can be: "pending", "approved", "rejected"
- Approved flag should match approved status

## Next Steps (Optional Enhancements)
1. Add comments/replies to posts
2. Add post categories/tags
3. Add user reputation system based on post likes
4. Add post reporting system
5. Add social sharing features
6. Add notification system for replies
7. Add post editing history
8. Add spam detection/filter
9. Add advanced moderation features

## Files Modified/Created
- ✅ Created: `app/Models/CommunityPost.php`
- ✅ Created: `app/Http/Controllers/CommunityController.php`
- ✅ Created: `resources/js/pages/Community.vue`
- ✅ Created: `app/Filament/Resources/CommunityPostResource.php`
- ✅ Created: `app/Filament/Resources/CommunityPostResource/Pages/*.php`
- ✅ Modified: `routes/web.php` (removed messages, added community routes)
- ✅ Modified: `app/Models/User.php` (added communityPosts relationship)
- ✅ Created: `database/migrations/2025_12_07_165909_create_community_posts_table.php`
- ✅ Old: `resources/js/pages/Messages.vue` (still exists but not used)
- ✅ Old: `app/Http/Controllers/MessagesController.php` (still exists but not used)
