# Animations Quick Reference Card

## Import
```typescript
import { useAnimations } from '@/composables/useAnimations';
const { showXPPopup, showConfetti, showLevelUp, showAchievementUnlock } = 
  useAnimations();
```

## API Calls

### XP Popup
```typescript
showXPPopup(
  amount,           // number (e.g., 100)
  x,               // pixel x coordinate
  y,               // pixel y coordinate
  duration         // ms (optional, default 2000)
)
```

**Example:**
```typescript
showXPPopup(100, event.clientX, event.clientY);
```

---

### Confetti Explosion
```typescript
showConfetti(
  count,           // number of particles (optional, default 50)
  duration,        // ms (optional, default 3000)
  x,              // 0-1 ratio (optional, default 0.5)
  y               // 0-1 ratio (optional, default 0.5)
)
```

**Example:**
```typescript
showConfetti(50, 3000, 0.5, 0.5);  // centered
showConfetti();                      // defaults
```

---

### Level Up
```typescript
showLevelUp(
  level,           // number (required, e.g., 5)
  xpReward,        // number (optional)
  duration         // ms (optional, default 3000)
)
```

**Example:**
```typescript
showLevelUp(5);           // just level
showLevelUp(5, 500);      // with bonus XP
showLevelUp(5, 500, 4000); // custom duration
```

---

### Achievement Unlock
```typescript
showAchievementUnlock(
  name,            // string (required)
  description,     // string (required)
  icon,            // string - emoji/icon (required)
  xpReward,        // number (optional)
  duration,        // ms (optional, default 4000)
  sound            // boolean (optional, default true)
)
```

**Example:**
```typescript
showAchievementUnlock(
  'Speed Demon',
  'Complete 10 courses in one day',
  '🚀',
  250,
  4000,
  true
);
```

---

## Component Usage

### RippleButton
```vue
<RippleButton 
  variant="primary"    <!-- 'default'|'primary'|'secondary'|'danger'|'ghost' -->
  size="md"            <!-- 'sm'|'md'|'lg' -->
  @click="handleClick"
>
  Click Me
</RippleButton>
```

### CardFlip
```vue
<CardFlip height="300px">
  <template #front>
    <div class="card-front">Front side</div>
  </template>
  <template #back>
    <div class="card-back">Back side</div>
  </template>
</CardFlip>
```

### SkeletonLoader
```vue
<SkeletonLoader :isLoading="isLoading" :count="3">
  <div>Loaded content here</div>
</SkeletonLoader>
```

---

## Common Patterns

### Basic XP Gain
```typescript
async function earnXP(amount: number, event: MouseEvent) {
  showXPPopup(amount, event.clientX, event.clientY);
  await api.updateXP(amount);
}
```

### Complete Quest with Animations
```typescript
async function completeQuest(id: number, event: MouseEvent) {
  const res = await api.completeQuest(id);
  
  // Show XP
  showXPPopup(res.xp, event.clientX, event.clientY);
  
  // Level up if applicable
  if (res.leveledUp) {
    setTimeout(() => {
      showLevelUp(res.newLevel, res.bonusXp);
      showConfetti();
    }, 500);
  }
  
  // Achievement if applicable
  if (res.achievement) {
    setTimeout(() => {
      showAchievementUnlock(
        res.achievement.name,
        res.achievement.description,
        res.achievement.icon,
        res.achievement.xp
      );
    }, 3500);
  }
}
```

### Loading with Skeleton
```vue
<script setup>
const isLoading = ref(false);
const data = ref([]);

onMounted(async () => {
  isLoading.value = true;
  data.value = await fetchData();
  isLoading.value = false;
});
</script>

<template>
  <SkeletonLoader :isLoading="isLoading">
    <div v-for="item in data" :key="item.id">
      {{ item.name }}
    </div>
  </SkeletonLoader>
</template>
```

---

## Performance Tips

| Tip | Example |
|-----|---------|
| Reduce confetti on mobile | `showConfetti(25)` instead of 50 |
| Batch animations | Use setTimeout between them |
| Disable sound on mobile | `showAchievementUnlock(..., true, false)` |
| Limit to desktop | Check `window.innerWidth` |

---

## Customization

### Duration Examples
```typescript
showXPPopup(..., 1000)      // faster
showConfetti(..., 5000)     // slower
showLevelUp(..., 2000)      // quick level up
showAchievementUnlock(..., 5000) // longer achievement
```

### Colors (in ConfettiExplosion.vue)
```typescript
const colors = [
  '#FFD700',  // gold
  '#FF6B6B',  // red
  '#4ECDC4',  // teal
  '#45B7D1',  // blue
];
```

### Variants (RippleButton)
```typescript
'default'    // gray
'primary'    // blue
'secondary'  // gray
'danger'     // red
'ghost'      // transparent
```

---

## Accessibility

### Respect prefers-reduced-motion
Already handled in CSS. Users with accessibility preferences will see instant transitions.

### Always provide non-animation feedback
```typescript
// Good: Animation + toast
showXPPopup(100, x, y);
toast.success('Earned 100 XP');

// Avoid: Animation only
showXPPopup(100, x, y); // user might miss if animation disabled
```

---

## Troubleshooting

| Issue | Solution |
|-------|----------|
| Animations not showing | Ensure `AnimationContainer` is in layout |
| Sound not playing | Check browser allows audio, set `sound: true` |
| Performance slow | Reduce confetti count, shorter durations |
| Mobile lag | Use mobile-friendly confetti count |
| Z-index issues | Check parent element styles |

---

## Keyboard Shortcuts

### In Demo Component
- Click "Show XP Popup" - Shows floating XP text
- Click "Show Confetti" - Celebrates with confetti
- Click "Show Level Up" - Shows level up animation
- Click "Show Achievement" - Shows achievement with sound
- Click card - Flips it
- Click skeleton toggle - Loads content

---

## Full Method Reference

```typescript
const {
  showXPPopup(xp, x, y, duration?)
  showConfetti(count?, duration?, x?, y?)
  showLevelUp(level, xpReward?, duration?)
  showAchievementUnlock(name, desc, icon, xp?, dur?, sound?)
  clearAnimations()
  removeAnimation(id)
  animationContainer  // reactive Map<string, any>
} = useAnimations();
```

---

## File Locations

```
resources/js/components/
  ├── animations/
  │   ├── XPPopup.vue
  │   ├── ConfettiExplosion.vue
  │   ├── LevelUpAnimation.vue
  │   ├── AchievementUnlock.vue
  │   ├── CardFlip.vue
  │   ├── RippleButton.vue
  │   ├── SkeletonLoader.vue
  │   └── AnimationDemo.vue
  └── AnimationContainer.vue

resources/js/composables/
  └── useAnimations.ts

resources/css/
  └── app.css (animations added)
```

---

## Default Values

```typescript
showXPPopup(xp, x, y, 2000)                          // 2s
showConfetti(50, 3000, 0.5, 0.5)                    // 3s, centered
showLevelUp(level, undefined, 3000)                 // 3s
showAchievementUnlock(name, desc, icon, 0, 4000, true) // 4s, sound on
```

---

## Examples by Use Case

### Button Click Feedback
```typescript
<RippleButton @click="handleClick">Submit</RippleButton>
```

### Form Submission
```typescript
async function submit(form: Form, event: SubmitEvent) {
  const target = event.target as HTMLElement;
  const rect = target.getBoundingClientRect();
  
  showXPPopup(100, rect.x + rect.width/2, rect.y + rect.height/2);
  await api.submit(form);
}
```

### Data Loading
```typescript
<SkeletonLoader :isLoading="isLoading">
  <div v-for="item in items">{{ item }}</div>
</SkeletonLoader>
```

### Unlock Item
```typescript
function unlock(item: Item, event: MouseEvent) {
  showAchievementUnlock(
    item.name,
    item.description,
    item.emoji,
    item.reward
  );
}
```

---

## Notes

- All animations auto-cleanup after completion
- No memory leaks from orphaned elements
- Dark mode automatically applied
- Mobile optimized
- Fully typed with TypeScript
- Ready for production use
