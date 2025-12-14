# Animation Integration Guide

This guide shows how to integrate the new animation system into existing components and pages.

## Quick Start

### 1. Import the composable

```typescript
import { useAnimations } from '@/composables/useAnimations';

const { 
  showXPPopup, 
  showConfetti, 
  showLevelUp, 
  showAchievementUnlock 
} = useAnimations();
```

### 2. Trigger animations on user actions

```typescript
// When user earns XP
function handleXPEarned(amount: number, event: MouseEvent) {
  showXPPopup(amount, event.clientX, event.clientY);
}

// When user levels up
function handleLevelUp(newLevel: number, bonusXP: number) {
  showLevelUp(newLevel, bonusXP);
  showConfetti();
}

// When achievement unlocked
function handleAchievementUnlock(achievement: Achievement) {
  showAchievementUnlock(
    achievement.name,
    achievement.description,
    achievement.icon,
    achievement.xp_reward
  );
}
```

## Integration Examples

### Dashboard Page

Add XP popup when completing assignments:

```vue
<script setup lang="ts">
import { useAnimations } from '@/composables/useAnimations';

const { showXPPopup, showLevelUp, showConfetti } = useAnimations();

async function completeAssignment(id: number, event: MouseEvent) {
  const response = await axios.post(`/assignments/${id}/complete`);
  
  // Show XP popup at click location
  showXPPopup(response.data.xp, event.clientX, event.clientY);
  
  // Check if leveled up
  if (response.data.leveledUp) {
    showLevelUp(response.data.newLevel, response.data.bonusXp);
    showConfetti();
  }
  
  // Refresh data
  await refreshUserStats();
}
</script>

<template>
  <button @click="completeAssignment(assignment.id, $event)">
    Complete Assignment
  </button>
</template>
```

### Achievements Page

Show animation when achievement is unlocked:

```vue
<script setup lang="ts">
import { useAnimations } from '@/composables/useAnimations';

const { showAchievementUnlock } = useAnimations();

async function unlockAchievement(achievement: Achievement) {
  if (achievement.unlocked) return;
  
  await axios.post(`/achievements/${achievement.id}/unlock`);
  
  // Show unlock animation
  showAchievementUnlock(
    achievement.name,
    achievement.description,
    achievement.icon,
    achievement.xp_reward,
    4000,
    true // play sound
  );
  
  // Refresh achievements
  await refreshAchievements();
}
</script>

<template>
  <div 
    v-for="achievement in achievements"
    :key="achievement.id"
    class="achievement-card"
    @click="unlockAchievement(achievement)"
  >
    {{ achievement.name }}
  </div>
</template>
```

### Rewards Page

Use animations for reward claiming:

```vue
<script setup lang="ts">
import { useAnimations } from '@/composables/useAnimations';
import SkeletonLoader from '@/components/animations/SkeletonLoader.vue';

const { showXPPopup, showConfetti, showLevelUp } = useAnimations();
const isLoading = ref(false);
const rewards = ref([]);

async function claimReward(reward: Reward, event: MouseEvent) {
  isLoading.value = true;
  
  try {
    const response = await axios.post(`/rewards/${reward.id}/claim`);
    
    // Animate XP gain
    showXPPopup(
      response.data.xp,
      event.clientX,
      event.clientY
    );
    
    // Animate level up if applicable
    if (response.data.leveledUp) {
      setTimeout(() => {
        showLevelUp(
          response.data.newLevel,
          response.data.bonusXp
        );
        showConfetti();
      }, 500);
    }
    
    // Refresh rewards
    await loadRewards();
  } finally {
    isLoading.value = false;
  }
}
</script>

<template>
  <SkeletonLoader :isLoading="isLoading" :count="3">
    <div class="space-y-4">
      <div 
        v-for="reward in rewards"
        :key="reward.id"
        class="reward-card"
      >
        <h3>{{ reward.name }}</h3>
        <button @click="claimReward(reward, $event)">
          Claim
        </button>
      </div>
    </div>
  </SkeletonLoader>
</template>
```

### Quests Page

Add animations for quest completion:

```vue
<script setup lang="ts">
import { useAnimations } from '@/composables/useAnimations';

const { showXPPopup, showLevelUp, showConfetti, showAchievementUnlock } = 
  useAnimations();

async function completeQuest(quest: Quest, event: MouseEvent) {
  const response = await axios.post(`/quests/${quest.id}/complete`);
  
  // Show XP popup
  showXPPopup(response.data.xp, event.clientX, event.clientY);
  
  // Chain animations
  const delay = 500;
  
  // Level up animation
  if (response.data.leveledUp) {
    setTimeout(() => {
      showLevelUp(response.data.newLevel, response.data.bonusXp);
      showConfetti();
    }, delay);
  }
  
  // Achievement animation
  if (response.data.achievementUnlocked) {
    setTimeout(() => {
      showAchievementUnlock(
        response.data.achievement.name,
        response.data.achievement.description,
        response.data.achievement.icon,
        response.data.achievement.xp_reward
      );
    }, delay + 3000);
  }
  
  // Refresh data
  await refreshQuests();
}
</script>

<template>
  <div v-for="quest in quests" :key="quest.id">
    <h3>{{ quest.title }}</h3>
    <button @click="completeQuest(quest, $event)">
      Complete
    </button>
  </div>
</template>
```

### Courses Page

Use SkeletonLoader for loading states:

```vue
<script setup lang="ts">
import SkeletonLoader from '@/components/animations/SkeletonLoader.vue';

const isLoading = ref(true);
const courses = ref([]);

onMounted(async () => {
  try {
    const response = await axios.get('/courses');
    courses.value = response.data;
  } finally {
    isLoading.value = false;
  }
});
</script>

<template>
  <SkeletonLoader :isLoading="isLoading" :count="4">
    <template #skeleton>
      <div class="h-32 bg-slate-200 dark:bg-slate-700 rounded-lg" />
    </template>
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <div v-for="course in courses" :key="course.id" class="course-card">
        <h3>{{ course.name }}</h3>
      </div>
    </div>
  </SkeletonLoader>
</template>
```

## Daily Bonus Integration

Update DailyBonusModal to show animations:

```vue
<script setup lang="ts">
import { useAnimations } from '@/composables/useAnimations';

const { showXPPopup, showConfetti } = useAnimations();

const props = defineProps<{
  isOpen: boolean;
  xpAmount: number;
}>();

const emit = defineEmits<{
  'claim': [];
  'close': [];
}>();

async function handleClaim() {
  // Calculate center of modal for animation
  const modalX = window.innerWidth / 2;
  const modalY = window.innerHeight / 2;
  
  // Show popup
  showXPPopup(props.xpAmount, modalX, modalY);
  
  // Show celebration
  setTimeout(() => {
    showConfetti(30, 2000, 0.5, 0.5);
  }, 300);
  
  // Emit claim event
  emit('claim');
}
</script>

<template>
  <Dialog :open="isOpen">
    <!-- Modal content -->
    <Button @click="handleClaim">
      Claim Bonus
    </Button>
  </Dialog>
</template>
```

## API Integration Pattern

Here's a reusable pattern for API calls with animations:

```typescript
// Create a helper function
export async function callAnimatedAPI(
  apiCall: () => Promise<any>,
  onSuccess: (response: any) => void,
  onError?: (error: any) => void
) {
  const { showXPPopup, showLevelUp, showConfetti, showAchievementUnlock } =
    useAnimations();

  try {
    const response = await apiCall();

    // Handle XP gains
    if (response.xp) {
      showXPPopup(response.xp, window.innerWidth / 2, window.innerHeight / 2);
    }

    // Handle level up
    if (response.leveledUp) {
      setTimeout(() => {
        showLevelUp(response.newLevel, response.bonusXp);
        showConfetti();
      }, 500);
    }

    // Handle achievement
    if (response.achievementUnlocked) {
      setTimeout(() => {
        showAchievementUnlock(
          response.achievement.name,
          response.achievement.description,
          response.achievement.icon,
          response.achievement.xp_reward
        );
      }, 2000);
    }

    onSuccess(response);
  } catch (error) {
    onError?.(error);
  }
}
```

Usage:
```typescript
callAnimatedAPI(
  () => axios.post('/quests/1/complete'),
  (response) => {
    refreshQuests();
  }
);
```

## Performance Tips

1. **Batch animations**: Chain animations with delays rather than showing all at once
2. **Limit confetti**: Use fewer particles for mobile devices
3. **Disable sounds**: Set `sound: false` on achievement unlock if needed
4. **Lazy load**: Only import animations when needed

```typescript
// Only show confetti on desktop
const isDesktop = window.innerWidth >= 1024;
if (isDesktop) {
  showConfetti();
}
```

## Accessibility Considerations

1. **Respect prefers-reduced-motion**: Users can disable animations in OS settings
2. **Don't rely on animations**: Always provide feedback through other means (toasts, text)
3. **Sound alternatives**: Show visual feedback even when sound is disabled

Add to CSS:
```css
@media (prefers-reduced-motion: reduce) {
    * {
        animation-duration: 0.01ms !important;
        animation-iteration-count: 1 !important;
        transition-duration: 0.01ms !important;
    }
}
```

## Debugging

Check if animations are firing:
```typescript
const { animationContainer } = useAnimations();

// In console:
// console.log(animationContainer.value)
```

Verify AnimationContainer is mounted:
```vue
<DevTools>
  <pre>{{ animationContainer }}</pre>
</DevTools>
```
