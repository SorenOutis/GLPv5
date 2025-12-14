<template>
  <Teleport to="body">
    <div
      v-if="isVisible"
      class="pointer-events-none fixed z-50"
      :style="{ left: x + 'px', top: y + 'px' }"
    >
      <div
        class="relative flex items-center justify-center animate-float-up"
        :style="{ animationDuration: duration + 'ms' }"
      >
        <!-- XP value text -->
        <div
          class="whitespace-nowrap text-2xl font-bold text-yellow-500 drop-shadow-lg"
          :style="{ animationDuration: duration + 'ms' }"
        >
          +{{ xp }} XP
        </div>
        <!-- Particle effect background -->
        <div
          class="absolute -inset-2 rounded-full bg-yellow-400/20 blur-xl"
          :style="{ animationDuration: duration + 'ms' }"
        />
      </div>
    </div>
  </Teleport>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';

interface Props {
  xp: number;
  x: number;
  y: number;
  duration?: number;
}

const props = withDefaults(defineProps<Props>(), {
  duration: 2000,
});

const isVisible = ref(true);

onMounted(() => {
  setTimeout(() => {
    isVisible.value = false;
  }, props.duration);
});
</script>

<style scoped>
@keyframes float-up {
  0% {
    opacity: 1;
    transform: translateY(0) scale(1);
  }
  50% {
    opacity: 1;
  }
  100% {
    opacity: 0;
    transform: translateY(-60px) scale(0.8);
  }
}

.animate-float-up {
  animation: float-up v-bind(duration) ease-out forwards;
}
</style>
