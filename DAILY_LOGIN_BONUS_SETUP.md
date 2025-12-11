# Daily Login Bonus Feature

## Overview
This feature provides a 20 XP daily login bonus that appears as a popup modal after the user logs in. The bonus can only be claimed once per day, and the XP is stored in the user's profile's `total_xp` column.

## What Was Implemented

### Database
- **Migration**: `2025_12_11_000000_create_daily_login_bonuses_table.php`
  - Creates `daily_login_bonuses` table to track which users have claimed their bonus on which dates
  - Stores only the date (not time) to avoid timezone issues
  - Composite unique constraint on `[user_id, bonus_date]` - ensures one claim per user per day
  - Tracks `claimed_at` timestamp for audit purposes

### Backend
- **Model**: `app/Models/DailyLoginBonus.php`
  - Represents a daily login bonus record
  - Relationship with User model

- **Service**: `app/Services/DailyLoginBonusService.php`
  - `hasReceivedBonusToday()`: Checks if user already claimed bonus today (explicit date comparison, timezone-safe)
  - `awardDailyBonus()`: Awards 20 XP to user and records the claim using database transaction
  - `getLastBonusDate()`: Gets the last date user claimed a bonus
  - **Key Features:**
    - Database transaction to prevent race conditions
    - Double-check before awarding to prevent duplicate claims
    - Catches duplicate entry errors from unique constraint
    - Uses `now()->toDateString()` for consistent date handling across timezones

- **Controller**: `app/Http/Controllers/Api/DailyBonusController.php`
  - Endpoint: `POST /api/daily-bonus/claim`
  - Handles bonus claim requests

- **Route**: Added to `routes/web.php`
  - API endpoint protected by auth middleware

- **DashboardController**: Updated to pass daily bonus data to frontend
  - Checks if user has received bonus today
  - Passes `dailyBonus` prop with `hasReceivedToday` and `xpAmount`

### Frontend
- **Component**: `resources/js/components/DailyBonusModal.vue`
  - Beautiful modal that displays after login
  - Shows 20 XP reward in large, prominent text
  - "Claim Bonus" button when not claimed
  - Success/error messages
  - Auto-closes after successful claim
  - Handles "already claimed" state gracefully

- **Dashboard Integration**: Updated `resources/js/pages/Dashboard.vue`
  - Imports and uses DailyBonusModal component
  - Automatically opens modal on page load if user hasn't claimed today
  - Handles bonus claimed event

## How It Works

1. **User Logs In**: Dashboard page loads
2. **Check Daily Bonus**: DashboardController checks if user has received bonus today
3. **Show Modal**: If not received, modal automatically appears with:
   - 🎁 Daily Login Bonus title
   - +20 XP display
   - "Claim Bonus" button
4. **Claim Bonus**: User clicks button
5. **Backend Process**:
   - Validates user hasn't claimed today
   - Adds 20 XP to user profile's `total_xp`
   - Records claim in `daily_login_bonuses` table
6. **Success**: Modal shows success message and closes

## Database Changes
- XP is stored in: `user_profiles.total_xp`
- Claim tracking in: `daily_login_bonuses` table

## Testing

To test the feature:

1. **Clear yesterday's bonus** (for testing):
   ```sql
   DELETE FROM daily_login_bonuses WHERE DATE(bonus_date) = CURDATE();
   ```

2. **Visit Dashboard**: Navigate to `/dashboard`
3. **Modal Appears**: Should see the Daily Bonus modal
4. **Claim Bonus**: Click "Claim Bonus"
5. **Success**: XP added to user profile

## Files Modified/Created

### Created:
- `database/migrations/2025_12_11_000000_create_daily_login_bonuses_table.php`
- `app/Models/DailyLoginBonus.php`
- `app/Services/DailyLoginBonusService.php`
- `app/Http/Controllers/Api/DailyBonusController.php`
- `resources/js/components/DailyBonusModal.vue`
- `DAILY_LOGIN_BONUS_SETUP.md` (this file)

### Modified:
- `routes/web.php` - Added daily bonus claim endpoint
- `app/Models/User.php` - Added dailyLoginBonuses() relationship
- `app/Http/Controllers/DashboardController.php` - Added bonus data to props
- `resources/js/pages/Dashboard.vue` - Integrated modal and modal logic

## Features

✅ Daily bonus of 20 XP  
✅ One claim per day per user  
✅ Auto-opening modal on first dashboard visit  
✅ Beautiful UI with success/error handling  
✅ XP stored in user_profiles.total_xp  
✅ Prevents duplicate claims with database constraint  
✅ Responsive design  

## Notes

- The modal uses Inertia's session flash to maintain state
- Bonus check happens server-side to prevent tampering
- Modal auto-closes after 2 seconds on success
- Users can dismiss modal with "Maybe Later" button
