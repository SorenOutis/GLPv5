# Toast Notifications Integration Guide

## Overview

Toast notifications have been fixed and integrated. This guide shows where to add toast notifications when users earn XP or complete actions.

## Fixed Issues

✅ **DailyBonusModal.vue** - Now shows toast when XP bonus is claimed
- Shows: `+{xpAmount} XP` with source `Daily Login Bonus`
- Location: `resources/js/components/DailyBonusModal.vue`

## How Toast Works

### The Composable
```typescript
import { useToast } from '@/composables/useToast';

const { xp, achievement, quest, levelup, success, error, info } = useToast();
```

### Available Methods

**XP Toast**
```typescript
xp(amount: number, source?: string);
// Example:
xp(100, 'Quest Complete');
// Shows: "+100 XP" with subtitle "from Quest Complete"
```

**Achievement Toast**
```typescript
achievement(title: string, message?: string, icon?: string);
// Example:
achievement('Speed Demon', 'Complete 10 courses in one day', '🚀');
```

**Quest Toast**
```typescript
quest(title: string, message?: string);
// Example:
quest('Quest Complete', 'You earned 500 XP');
```

**Level Up Toast**
```typescript
levelup(level: number, xpGained?: number);
// Example:
levelup(5, 100); // "Level Up!" "You reached level 5 (+100 XP)"
```

**Success Toast**
```typescript
success(title: string, message?: string);
// Example:
success('Assignment Submitted', 'Waiting for grading...');
```

**Error Toast**
```typescript
error(title: string, message?: string);
// Example:
error('Failed to Submit', 'Please try again');
```

**Info Toast**
```typescript
info(title: string, message?: string);
// Example:
info('Submission Received', 'Your work has been received');
```

## Integration Locations

### 1. ✅ Daily Bonus (DONE)
**File:** `resources/js/components/DailyBonusModal.vue`

**Code added:**
```typescript
import { useToast } from '@/composables/useToast';
const { xp: addXPToast } = useToast();

// In handleClaimBonus():
addXPToast(props.xpAmount, 'Daily Login Bonus');
```

**When to trigger:** When user successfully claims daily bonus

---

### 2. Assignment Submission
**File:** `resources/js/pages/Assignment.vue`

**What to add:**
```typescript
import { useToast } from '@/composables/useToast';
const { success, xp } = useToast();

async function submitAssignment() {
    try {
        const response = await axios.post(`/assignments/${id}/submit`);
        
        // Show success message
        success('Assignment Submitted', 'Your work has been received');
        
        // If XP was awarded
        if (response.data.xp_awarded) {
            xp(response.data.xp_awarded, 'Assignment Submission');
        }
        
        // If leveled up
        if (response.data.level_up) {
            levelup(response.data.new_level, response.data.bonus_xp);
        }
    } catch (error) {
        error('Submission Failed', error.message);
    }
}
```

---

### 3. Quest Completion
**File:** `resources/js/pages/Quests.vue`

**What to add:**
```typescript
import { useToast } from '@/composables/useToast';
const { quest, xp, levelup } = useToast();

async function completeQuest(questId: number) {
    try {
        const response = await axios.post(`/quests/${questId}/complete`);
        
        // Show quest complete
        quest(response.data.quest_name, `+${response.data.xp_awarded} XP`);
        
        // Show XP earned
        xp(response.data.xp_awarded, 'Quest Complete');
        
        // If leveled up
        if (response.data.level_up) {
            levelup(response.data.new_level, response.data.bonus_xp);
        }
        
        // If achievement unlocked
        if (response.data.achievement_unlocked) {
            achievement(
                response.data.achievement.name,
                response.data.achievement.description,
                response.data.achievement.icon
            );
        }
    } catch (error) {
        error('Quest Failed', error.message);
    }
}
```

---

### 4. Achievement Unlock
**File:** `resources/js/pages/Achievements.vue`

**What to add:**
```typescript
import { useToast } from '@/composables/useToast';
const { achievement, xp } = useToast();

async function unlockAchievement(achievementId: number) {
    try {
        const response = await axios.post(`/achievements/${achievementId}/unlock`);
        
        // Show achievement unlocked
        achievement(
            response.data.name,
            response.data.description,
            response.data.icon
        );
        
        // Show XP reward
        if (response.data.xp_reward) {
            xp(response.data.xp_reward, 'Achievement Unlock');
        }
    } catch (error) {
        error('Unlock Failed', error.message);
    }
}
```

---

### 5. Lesson Completion
**File:** `resources/js/pages/Courses.vue`

**What to add:**
```typescript
import { useToast } from '@/composables/useToast';
const { success, xp } = useToast();

async function completeLesson(lessonId: number) {
    try {
        const response = await axios.post(`/lessons/${lessonId}/complete`);
        
        success('Lesson Completed', 'Great job! Moving on...');
        
        if (response.data.xp_awarded) {
            xp(response.data.xp_awarded, 'Lesson Complete');
        }
    } catch (error) {
        error('Completion Failed', error.message);
    }
}
```

---

### 6. Reward Claim
**File:** `resources/js/pages/Rewards.vue`

**What to add:**
```typescript
import { useToast } from '@/composables/useToast';
const { success, xp } = useToast();

async function claimReward(rewardId: number) {
    try {
        const response = await axios.post(`/rewards/${rewardId}/claim`);
        
        success('Reward Claimed!', response.data.name);
        
        if (response.data.xp_awarded) {
            xp(response.data.xp_awarded, 'Reward Claim');
        }
    } catch (error) {
        error('Claim Failed', error.message);
    }
}
```

---

### 7. Streak Milestone
**File:** `resources/js/components/StreakCard.vue`

**What to add:**
```typescript
import { useToast } from '@/composables/useToast';
const { success, xp } = useToast();

// When streak milestone reached
function onStreakMilestone(days: number, xpReward: number) {
    success(`${days} Day Streak!`, 'Keep it up!');
    xp(xpReward, 'Streak Milestone');
}
```

---

## API Response Format

Your API endpoints should return responses in this format for toast notifications to work:

```json
{
    "success": true,
    "message": "XP awarded",
    "xp_awarded": 100,
    "total_xp": 5000,
    "level": 5,
    "current_xp": 450,
    "level_up": false,
    "new_level": 5,
    "bonus_xp": 0,
    "achievement_unlocked": false,
    "achievement": {
        "name": "Speed Demon",
        "description": "Complete 10 courses in one day",
        "icon": "🚀"
    }
}
```

---

## Toast Auto-Dismiss Durations

Different toast types auto-dismiss after:

| Type | Duration |
|------|----------|
| XP | 3000ms (3s) |
| Achievement | 5000ms (5s) |
| Quest | 5000ms (5s) |
| Level Up | 6000ms (6s) |
| Success | 4000ms (4s) |
| Error | 5000ms (5s) |
| Info | 4000ms (4s) |

---

## Toast Styling

Toasts are automatically styled based on type:

| Type | Colors | Icon |
|------|--------|------|
| xp | Yellow/Gold | ✨ |
| achievement | Amber | ⭐ |
| quest | Green | ✓ |
| levelup | Purple/Pink gradient | ⚡ |
| success | Green | ✓ |
| error | Red | ⚠ |
| info | Blue | ℹ |

---

## Pattern: Show Multiple Toasts in Sequence

Sometimes you want to show multiple toasts. Use setTimeout to chain them:

```typescript
async function completeQuest(id: number) {
    const res = await axios.post(`/quests/${id}/complete`);
    
    // Show immediately
    xp(res.data.xp, 'Quest Complete');
    
    // Show after 500ms
    if (res.data.level_up) {
        setTimeout(() => {
            levelup(res.data.new_level, res.data.bonus_xp);
        }, 500);
    }
    
    // Show after 3000ms
    if (res.data.achievement) {
        setTimeout(() => {
            achievement(res.data.achievement.name, res.data.achievement.desc, res.data.achievement.icon);
        }, 3000);
    }
}
```

---

## Combine with Animations

Use animations along with toasts for better feedback:

```typescript
import { useAnimations } from '@/composables/useAnimations';
import { useToast } from '@/composables/useToast';

const { showXPPopup, showConfetti } = useAnimations();
const { xp: addXPToast, levelup } = useToast();

async function completeAssignment(event: MouseEvent) {
    const res = await axios.post('/assignments/1/submit');
    
    // Show floating XP animation
    showXPPopup(res.data.xp, event.clientX, event.clientY);
    
    // Show toast notification
    addXPToast(res.data.xp, 'Assignment Submission');
    
    // Level up animation + toast
    if (res.data.level_up) {
        setTimeout(() => {
            showConfetti();
            levelup(res.data.new_level, res.data.bonus_xp);
        }, 500);
    }
}
```

---

## Checklist: Where to Add Toast Notifications

- [ ] **Daily Bonus** - ✅ Already added
- [ ] **Assignment Submission** - Need to add to Assignment.vue
- [ ] **Quest Completion** - Need to add to Quests.vue
- [ ] **Achievement Unlock** - Need to add to Achievements.vue
- [ ] **Lesson Completion** - Need to add to Courses.vue
- [ ] **Reward Claim** - Need to add to Rewards.vue
- [ ] **Streak Milestone** - Need to add to StreakCard.vue
- [ ] **Admin Reward** - Check admin panel
- [ ] **Exam Pass** - If applicable
- [ ] **Badge Unlock** - If different from achievements

---

## Testing

To test toasts locally:

```typescript
// Open browser console and run:
const { xp, achievement, levelup, success, error } = useToast();
xp(100, 'Test Quest');
success('Test Success');
error('Test Error');
achievement('Test Achievement', 'This is a test', '🎯');
levelup(5, 100);
```

---

## Troubleshooting

**Toast not showing?**
- Check ToastContainer is mounted in AppShell.vue ✅ (already there)
- Verify useToast is imported correctly
- Check browser console for errors
- Make sure API response has `success: true`

**Toast shows but text is wrong?**
- Check API response format
- Verify parameter order in toast function calls
- Check console for data values

**Toast styling looks wrong?**
- Check Toast.vue component styling
- Verify Tailwind classes apply correctly
- Check dark mode support

---

## Future Enhancements

1. **Custom icons** - Allow passing custom emoji/icon
2. **Action buttons** - Add "Undo" or "View" buttons to toasts
3. **Toast stacking** - Better positioning when multiple toasts
4. **Persistent toasts** - Prevent auto-dismiss for important messages
5. **Sound effects** - Play sound when specific toasts appear

---

## Example: Complete Integration

Here's a complete example of integrating toasts + animations:

```typescript
<script setup lang="ts">
import { useToast } from '@/composables/useToast';
import { useAnimations } from '@/composables/useAnimations';

const { xp: showXPToast, levelup: showLevelupToast, success } = useToast();
const { showXPPopup, showConfetti, showLevelUp } = useAnimations();

async function handleQuestComplete(questId: number, event: MouseEvent) {
    try {
        const response = await axios.post(`/quests/${questId}/complete`);
        
        // Instant feedback: show animations
        showXPPopup(response.data.xp, event.clientX, event.clientY);
        
        // Toast notification (appears top-right)
        showXPToast(response.data.xp, 'Quest Complete');
        
        // After 500ms, show level up
        if (response.data.level_up) {
            setTimeout(() => {
                showConfetti();
                showLevelUp(response.data.new_level, response.data.bonus_xp);
                showLevelupToast(response.data.new_level, response.data.bonus_xp);
            }, 500);
        }
    } catch (error) {
        showError('Quest Failed', 'Could not complete quest');
    }
}
</script>
```

This provides:
- **Floating XP** animation at click point
- **Toast notification** in top-right corner
- **Level up animation** if applicable
- **Confetti celebration** for extra feedback
