<template>
  <Teleport to="body">
    <div
      v-if="isVisible"
      class="pointer-events-none fixed inset-0 z-40 overflow-hidden"
    >
      <!-- Individual confetti pieces -->
      <div
        v-for="(confetti, index) in confettiPieces"
        :key="index"
        class="absolute w-2 h-2 rounded-full animate-confetti"
        :style="{
          left: confetti.x + 'px',
          top: confetti.y + 'px',
          backgroundColor: confetti.color,
          '--tx': confetti.tx + 'px',
          '--ty': confetti.ty + 'px',
          '--rotation': confetti.rotation + 'deg',
          animationDuration: confetti.duration + 'ms',
          animationDelay: confetti.delay + 'ms',
        } as any"
      />
    </div>
  </Teleport>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';

interface Confetti {
  x: number;
  y: number;
  tx: number;
  ty: number;
  color: string;
  rotation: number;
  duration: number;
  delay: number;
}

interface Props {
  count?: number;
  duration?: number;
  x?: number;
  y?: number;
}

const props = withDefaults(defineProps<Props>(), {
  count: 50,
  duration: 3000,
  x: 0.5,
  y: 0.5,
});

const isVisible = ref(true);
const confettiPieces = ref<Confetti[]>([]);

const colors = [
  '#FFD700', // gold
  '#FF6B6B', // red
  '#4ECDC4', // teal
  '#45B7D1', // blue
  '#FFA07A', // salmon
  '#98D8C8', // mint
  '#F7DC6F', // yellow
  '#BB8FCE', // purple
];

function generateConfetti() {
  const centerX = window.innerWidth * props.x;
  const centerY = window.innerHeight * props.y;

  for (let i = 0; i < props.count; i++) {
    const angle = (Math.random() * Math.PI * 2);
    const velocity = 5 + Math.random() * 8;
    const tx = Math.cos(angle) * velocity * 100;
    const ty = Math.sin(angle) * velocity * 100 - 50;

    confettiPieces.value.push({
      x: centerX,
      y: centerY,
      tx: tx,
      ty: ty,
      color: colors[Math.floor(Math.random() * colors.length)],
      rotation: Math.random() * 360,
      duration: props.duration,
      delay: Math.random() * 100,
    });
  }
}

onMounted(() => {
  generateConfetti();
  setTimeout(() => {
    isVisible.value = false;
  }, props.duration + 100);
});
</script>

<style scoped>
@keyframes confetti {
  0% {
    transform:
      translate(0, 0) rotate(0deg) rotateZ(0deg);
    opacity: 1;
  }
  100% {
    transform:
      translate(var(--tx), var(--ty)) rotate(var(--rotation)) rotateZ(720deg);
    opacity: 0;
  }
}

.animate-confetti {
  animation: confetti v-bind('duration + "ms"') ease-out forwards;
}
</style>
