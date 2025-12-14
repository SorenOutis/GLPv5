<template>
  <div class="space-y-6">
    <div class="rounded-lg border border-border bg-card p-6">
      <h2 class="text-xl font-bold mb-4">Animation Demos</h2>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <!-- XP Popup Demo -->
        <div class="space-y-2">
          <h3 class="font-semibold">XP Popup</h3>
          <p class="text-sm text-muted-foreground">
            Floating text that rises and fades
          </p>
          <RippleButton
            variant="primary"
            @click="showXPPopupDemo"
          >
            Show XP Popup
          </RippleButton>
        </div>

        <!-- Confetti Demo -->
        <div class="space-y-2">
          <h3 class="font-semibold">Confetti Explosion</h3>
          <p class="text-sm text-muted-foreground">
            Celebratory confetti effect
          </p>
          <RippleButton
            variant="primary"
            @click="showConfettiDemo"
          >
            Show Confetti
          </RippleButton>
        </div>

        <!-- Level Up Demo -->
        <div class="space-y-2">
          <h3 class="font-semibold">Level Up</h3>
          <p class="text-sm text-muted-foreground">
            Level up celebration animation
          </p>
          <RippleButton
            variant="primary"
            @click="showLevelUpDemo"
          >
            Show Level Up
          </RippleButton>
        </div>

        <!-- Achievement Unlock Demo -->
        <div class="space-y-2">
          <h3 class="font-semibold">Achievement Unlock</h3>
          <p class="text-sm text-muted-foreground">
            Achievement unlock with sound
          </p>
          <RippleButton
            variant="primary"
            @click="showAchievementDemo"
          >
            Show Achievement
          </RippleButton>
        </div>

        <!-- Card Flip Demo -->
        <div class="space-y-2">
          <h3 class="font-semibold">Card Flip</h3>
          <p class="text-sm text-muted-foreground">
            3D card flip effect (click card)
          </p>
          <CardFlip height="300px">
            <template #front>
              <div
                class="w-full h-full bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center text-white text-2xl font-bold cursor-pointer hover:shadow-lg transition-shadow"
              >
                Click to Flip
              </div>
            </template>
            <template #back>
              <div
                class="w-full h-full bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg flex items-center justify-center text-white text-2xl font-bold cursor-pointer hover:shadow-lg transition-shadow"
              >
                Other Side!
              </div>
            </template>
          </CardFlip>
        </div>

        <!-- Skeleton Loader Demo -->
        <div class="space-y-2">
          <h3 class="font-semibold">Skeleton Loader</h3>
          <p class="text-sm text-muted-foreground">
            Loading state with shimmer effect
          </p>
          <RippleButton
            variant="primary"
            @click="toggleSkeleton"
          >
            {{ showSkeletonLoader ? 'Hide Skeleton' : 'Show Skeleton' }}
          </RippleButton>
          <SkeletonLoader :isLoading="showSkeletonLoader" :count="2">
            <div class="space-y-2">
              <div class="h-12 bg-blue-100 rounded-md flex items-center px-4">
                Loaded Content!
              </div>
            </div>
          </SkeletonLoader>
        </div>
      </div>
    </div>

    <!-- Usage Examples -->
    <div class="rounded-lg border border-border bg-card p-6">
      <h2 class="text-xl font-bold mb-4">Usage Examples</h2>
      <div class="space-y-4 text-sm">
        <div class="bg-slate-50 dark:bg-slate-900 p-4 rounded">
          <p class="font-semibold mb-2">Show XP Popup:</p>
          <code class="text-xs">
            const { showXPPopup } = useAnimations();<br />
            showXPPopup(100, mouseEvent.clientX, mouseEvent.clientY);
          </code>
        </div>

        <div class="bg-slate-50 dark:bg-slate-900 p-4 rounded">
          <p class="font-semibold mb-2">Show Level Up:</p>
          <code class="text-xs">
            const { showLevelUp } = useAnimations();<br />
            showLevelUp(5, 500); // level, xp reward
          </code>
        </div>

        <div class="bg-slate-50 dark:bg-slate-900 p-4 rounded">
          <p class="font-semibold mb-2">Show Achievement:</p>
          <code class="text-xs">
            const { showAchievementUnlock } = useAnimations();<br />
            showAchievementUnlock(<br />
            &nbsp;&nbsp;'Speed Demon',<br />
            &nbsp;&nbsp;'Complete 10 courses',<br />
            &nbsp;&nbsp;'🚀',<br />
            &nbsp;&nbsp;250<br />
            );
          </code>
        </div>

        <div class="bg-slate-50 dark:bg-slate-900 p-4 rounded">
          <p class="font-semibold mb-2">Show Confetti:</p>
          <code class="text-xs">
            const { showConfetti } = useAnimations();<br />
            showConfetti(50, 3000, 0.5, 0.5);
          </code>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import { useAnimations } from '@/composables/useAnimations';
import RippleButton from './RippleButton.vue';
import CardFlip from './CardFlip.vue';
import SkeletonLoader from './SkeletonLoader.vue';

const { showXPPopup, showConfetti, showLevelUp, showAchievementUnlock } =
  useAnimations();
const showSkeletonLoader = ref(false);

function showXPPopupDemo() {
  const randomXP = [50, 100, 150, 200, 250][Math.floor(Math.random() * 5)];
  showXPPopup(
    randomXP,
    window.innerWidth / 2,
    window.innerHeight / 2
  );
}

function showConfettiDemo() {
  showConfetti(50, 3000, 0.5, 0.5);
}

function showLevelUpDemo() {
  showLevelUp(5, 500);
}

function showAchievementDemo() {
  showAchievementUnlock(
    'Speed Demon',
    'Complete 10 courses in a single day',
    '🚀',
    250,
    4000,
    true
  );
}

function toggleSkeleton() {
  showSkeletonLoader.value = !showSkeletonLoader.value;
  if (showSkeletonLoader.value) {
    setTimeout(() => {
      showSkeletonLoader.value = false;
    }, 2000);
  }
}
</script>
