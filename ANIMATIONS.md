# Micro-Interactions & Animations Guide

This guide covers all the animation components and composables available in the GLPv5 application.

## Table of Contents

1. [Overview](#overview)
2. [Animation Components](#animation-components)
3. [Using Animations](#using-animations)
4. [Composables](#composables)
5. [Examples](#examples)
6. [Styling & Customization](#styling--customization)

## Overview

The animation system provides a set of reusable, composable animations for common UI interactions:

- **XP Popups**: Floating text that animates when earning points
- **Confetti Explosion**: Celebratory particle effects
- **Level Up Animation**: Full-screen level up celebration
- **Achievement Unlock**: Modal-style achievement display with sound
- **Card Flip**: 3D flip animation for card reveal
- **Ripple Button**: Material Design ripple effect on buttons
- **Skeleton Loader**: Shimmer effect during content loading

## Animation Components

### XPPopup

Shows a floating "+XP" popup that rises and fades away.

**Props:**
- `xp: number` - Amount of XP to display
- `x: number` - X coordinate (pixels)
- `y: number` - Y coordinate (pixels)
- `duration?: number` - Animation duration in ms (default: 2000)

**Usage:**
```vue
<XPPopup :xp="100" :x="500" :y="300" />
```

### ConfettiExplosion

Creates an explosion of colorful confetti particles.

**Props:**
- `count?: number` - Number of confetti pieces (default: 50)
- `duration?: number` - Animation duration in ms (default: 3000)
- `x?: number` - X position as ratio 0-1 (default: 0.5)
- `y?: number` - Y position as ratio 0-1 (default: 0.5)

**Usage:**
```vue
<ConfettiExplosion :count="50" :duration="3000" />
```

### LevelUpAnimation

Displays a large, attention-grabbing level up notification.

**Props:**
- `level: number` - New level number (required)
- `xpReward?: number` - XP bonus for leveling up
- `duration?: number` - Animation duration in ms (default: 3000)
- `onComplete?: () => void` - Callback when animation finishes

**Usage:**
```vue
<LevelUpAnimation :level="5" :xpReward="500" />
```

### AchievementUnlock

Shows an achievement unlock card with optional sound effect.

**Props:**
- `name: string` - Achievement name (required)
- `description: string` - Achievement description (required)
- `icon: string` - Emoji or icon (required)
- `xpReward?: number` - XP reward for unlocking
- `duration?: number` - Display duration in ms (default: 4000)
- `sound?: boolean` - Play sound effect (default: true)
- `onClose?: () => void` - Callback when animation finishes

**Exposed methods:**
- `close()` - Manually close the achievement

**Usage:**
```vue
<AchievementUnlock
  name="Speed Demon"
  description="Complete 10 courses in one day"
  icon="🚀"
  :xpReward="250"
/>
```

### CardFlip

A 3D card that can be flipped to reveal content.

**Props:**
- `height?: string` - Card height (default: "auto")
- `flipOnClick?: boolean` - Auto flip on click (default: true)
- `initialFlipped?: boolean` - Start flipped (default: false)

**Slots:**
- `front` - Front side content
- `back` - Back side content

**Exposed methods:**
- `flip()` - Programmatically flip the card
- `isFlipped` - Reactive ref to track flip state

**Usage:**
```vue
<CardFlip height="300px">
  <template #front>
    <div class="...">Front</div>
  </template>
  <template #back>
    <div class="...">Back</div>
  </template>
</CardFlip>
```

### RippleButton

A button with Material Design ripple effect.

**Props:**
- `variant?: 'default' | 'primary' | 'secondary' | 'danger' | 'ghost'`
- `size?: 'sm' | 'md' | 'lg'`

**Emits:**
- `click(event: MouseEvent)` - Button click event

**Usage:**
```vue
<RippleButton variant="primary" size="md" @click="handleClick">
  Click Me
</RippleButton>
```

### SkeletonLoader

Displays loading skeletons with shimmer effect.

**Props:**
- `isLoading?: boolean` - Show loading state (default: false)
- `count?: number` - Number of skeleton items (default: 3)

**Slots:**
- `skeleton` - Custom skeleton template (receives `index`)
- `default` - Content to show when loaded

**Usage:**
```vue
<SkeletonLoader :isLoading="isLoading">
  <template #skeleton="{ index }">
    <div class="h-12 bg-slate-200 rounded" />
  </template>
  
  <div>Loaded content...</div>
</SkeletonLoader>
```

## Using Animations

### Via Composable (Recommended)

The `useAnimations` composable provides functions to trigger animations globally:

```typescript
import { useAnimations } from '@/composables/useAnimations';

const {
  showXPPopup,
  showConfetti,
  showLevelUp,
  showAchievementUnlock,
  clearAnimations,
  removeAnimation,
} = useAnimations();

// Show XP popup
showXPPopup(100, event.clientX, event.clientY);

// Show confetti
showConfetti(50, 3000);

// Show level up
showLevelUp(5, 500);

// Show achievement
showAchievementUnlock('Speed Demon', 'Complete 10 courses', '🚀', 250);
```

### Direct Component Usage

You can also use components directly in your template:

```vue
<XPPopup :xp="100" :x="500" :y="300" />
<ConfettiExplosion />
```

## Composables

### useAnimations

**Functions:**

#### `showXPPopup(xp, x, y, duration?): string`
Show floating XP popup at coordinates. Returns animation ID.

#### `showConfetti(count?, duration?, x?, y?): string`
Show confetti explosion. Returns animation ID.

#### `showLevelUp(level, xpReward?, duration?): string`
Show level up celebration. Returns animation ID.

#### `showAchievementUnlock(name, description, icon, xpReward?, duration?, sound?): string`
Show achievement unlock. Returns animation ID.

#### `clearAnimations(): void`
Clear all active animations.

#### `removeAnimation(id: string): void`
Remove specific animation by ID.

## Examples

### Complete Workflow Example

```typescript
// In your component
import { useAnimations } from '@/composables/useAnimations';
import { useToast } from '@/composables/useToast';

const { showXPPopup, showLevelUp, showAchievementUnlock, showConfetti } =
  useAnimations();
const { showToast } = useToast();

async function completeLesson() {
  const xpGained = 100;
  const currentLevel = 4;
  const newLevel = 5;

  // Show XP popup where user clicked
  showXPPopup(xpGained, event.clientX, event.clientY);

  // Check if leveled up
  if (newLevel > currentLevel) {
    showLevelUp(newLevel, 500);
    showConfetti();
  }

  // Check for achievement
  if (checkAchievement('lesson_master')) {
    showAchievementUnlock(
      'Lesson Master',
      'Complete 10 lessons',
      '📚',
      250
    );
  }
}
```

### Dashboard Integration

```vue
<script setup lang="ts">
import { useAnimations } from '@/composables/useAnimations';

const { showXPPopup } = useAnimations();

async function earnXP(amount: number, event: MouseEvent) {
  // Play animation
  showXPPopup(amount, event.clientX, event.clientY);

  // Update user XP in backend
  await api.updateUserXP(amount);
}
</script>

<template>
  <div>
    <button @click="earnXP(100, $event)">
      Earn 100 XP
    </button>
  </div>
</template>
```

## Styling & Customization

### Tailwind CSS Integration

All animations use Tailwind CSS and CSS custom properties for theming. Colors automatically respond to light/dark mode.

### Custom Durations

All animations accept `duration` parameter to customize animation speed:

```typescript
showLevelUp(5, 500, 4000); // 4 second duration
showAchievementUnlock('...', '...', '🏆', 250, 5000); // 5 second duration
```

### Custom Colors

For confetti, edit the `colors` array in `ConfettiExplosion.vue`:

```typescript
const colors = [
  '#FFD700', // Your custom colors
  '#FF6B6B',
  '#4ECDC4',
];
```

### Animation Classes

Core animations are defined as Tailwind classes and CSS @keyframes:

- `animate-float-up` - XP popup
- `animate-confetti` - Confetti particles
- `animate-scale-bounce` - Level up card
- `animate-pop-in` - Achievement card
- `animate-shimmer` - Skeleton loader

## Performance Considerations

1. **Animation Container**: The `AnimationContainer` component is placed in the root layout for global access
2. **Auto Cleanup**: Animations are automatically removed after completion
3. **Z-index**: All animations use appropriate z-index (40-50) to appear above content
4. **Pointer Events**: Animations have `pointer-events-none` to not block user interaction

## Troubleshooting

### Animations not showing?
- Ensure `AnimationContainer` is mounted in the layout
- Check browser console for errors
- Verify z-index isn't being overridden by parent styles

### Sound not playing?
- Check browser console for Web Audio API errors
- Some browsers require user interaction before audio
- Ensure `sound: true` is passed to `showAchievementUnlock`

### Performance issues?
- Reduce `count` in confetti animations
- Decrease animation durations
- Check for CSS conflicts with existing styles

## Browser Support

All animations use modern CSS features:
- CSS Transforms (3D)
- CSS Animations/Transitions
- Web Audio API (for sounds)

Supported browsers:
- Chrome 60+
- Firefox 55+
- Safari 12+
- Edge 79+
