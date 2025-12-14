<template>
  <div
    v-if="isLoading"
    class="space-y-4"
  >
    <div
      v-for="i in count"
      :key="i"
      class="animate-pulse"
    >
      <slot name="skeleton" :index="i">
        <!-- Default skeleton -->
        <div class="space-y-2">
          <div
            class="h-12 bg-slate-200 dark:bg-slate-700 rounded-md animate-shimmer"
            :style="{ animationDelay: (i * 100) + 'ms' }"
          />
          <div
            class="h-4 bg-slate-200 dark:bg-slate-700 rounded-md w-5/6 animate-shimmer"
            :style="{ animationDelay: (i * 100) + 150 + 'ms' }"
          />
        </div>
      </slot>
    </div>
  </div>
  <div v-else class="space-y-4 animate-fade-in">
    <slot />
  </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue';

interface Props {
  isLoading?: boolean;
  count?: number;
}

const props = withDefaults(defineProps<Props>(), {
  isLoading: false,
  count: 3,
});

const isLoading = computed(() => props.isLoading);
</script>

<style scoped>
@keyframes shimmer {
  0% {
    background-position: -1000px 0;
  }
  100% {
    background-position: 1000px 0;
  }
}

@keyframes fade-in {
  from {
    opacity: 0;
  }
  to {
    opacity: 1;
  }
}

.animate-shimmer {
  background: linear-gradient(
    90deg,
    #e2e8f0 -1000px,
    #f1f5f9 0px,
    #e2e8f0 1000px
  );
  background-size: 1000px 100%;
  animation: shimmer 2s infinite;
}

.dark .animate-shimmer {
  background: linear-gradient(
    90deg,
    #374151 -1000px,
    #4b5563 0px,
    #374151 1000px
  );
  background-size: 1000px 100%;
  animation: shimmer 2s infinite;
}

.animate-fade-in {
  animation: fade-in 0.3s ease-out;
}
</style>
