<template>
  <Teleport to="body">
    <div
      v-if="isVisible"
      class="pointer-events-none fixed inset-0 z-40 flex items-center justify-center"
    >
      <!-- Backdrop -->
      <div
        class="absolute inset-0 bg-black/40 animate-fade-in-out"
        :style="{ animationDuration: duration + 'ms' }"
      />

      <!-- Level up card -->
      <div
        class="relative animate-scale-bounce"
        :style="{ animationDuration: duration + 'ms' }"
      >
        <!-- Aura effect -->
        <div
          class="absolute -inset-8 rounded-lg bg-gradient-to-r from-yellow-400 via-orange-400 to-red-400 blur-xl opacity-60 animate-pulse"
        />

        <!-- Main card -->
        <div
          class="relative bg-gradient-to-br from-slate-900 to-slate-800 rounded-lg p-8 border-2 border-yellow-400 shadow-2xl max-w-md text-center"
        >
          <!-- Stars background animation -->
          <div
            v-for="i in 8"
            :key="i"
            class="absolute w-1 h-1 bg-yellow-300 rounded-full animate-twinkle"
            :style="{
              left: Math.cos((i / 8) * Math.PI * 2) * 80 + 50 + '%',
              top: Math.sin((i / 8) * Math.PI * 2) * 80 + 50 + '%',
              animationDelay: (i * 0.1) + 's',
            }"
          />

          <!-- Content -->
          <div class="relative z-10">
            <!-- Level text -->
            <h2 class="text-5xl font-bold text-yellow-400 mb-2 animate-bounce">
              LEVEL UP!
            </h2>

            <!-- New level -->
            <p class="text-xl text-slate-300 mb-4">You reached</p>
            <div
              class="text-7xl font-black text-transparent bg-clip-text bg-gradient-to-r from-yellow-300 to-orange-400 mb-6"
            >
              {{ level }}
            </div>

            <!-- XP reward -->
            <p
              v-if="xpReward"
              class="text-lg text-green-400 font-semibold animate-pulse"
            >
              +{{ xpReward }} XP Bonus!
            </p>
          </div>
        </div>
      </div>

      <!-- Floating particles -->
      <div
        v-for="i in 12"
        :key="'particle-' + i"
        class="absolute w-3 h-3 bg-yellow-300 rounded-full animate-float-away"
        :style="{
          left: 50 + (Math.cos((i / 12) * Math.PI * 2) * 100) + '%',
          top: 50 + (Math.sin((i / 12) * Math.PI * 2) * 100) + '%',
          animationDelay: (i * 0.05) + 's',
          animationDuration: (duration * 0.8) + 'ms',
        }"
      />
    </div>
  </Teleport>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';

interface Props {
  level: number;
  xpReward?: number;
  duration?: number;
  onComplete?: () => void;
}

const props = withDefaults(defineProps<Props>(), {
  duration: 3000,
});

const isVisible = ref(true);

onMounted(() => {
  setTimeout(() => {
    isVisible.value = false;
    props.onComplete?.();
  }, props.duration);
});
</script>

<style scoped>
@keyframes fade-in-out {
  0% {
    opacity: 0;
  }
  10% {
    opacity: 1;
  }
  90% {
    opacity: 1;
  }
  100% {
    opacity: 0;
  }
}

@keyframes scale-bounce {
  0% {
    transform: scale(0.5);
    opacity: 0;
  }
  50% {
    transform: scale(1.1);
  }
  100% {
    transform: scale(1);
    opacity: 1;
  }
}

@keyframes float-away {
  0% {
    opacity: 1;
    transform: translate(0, 0) scale(1);
  }
  100% {
    opacity: 0;
    transform: translate(0, -200px) scale(0);
  }
}

@keyframes twinkle {
  0%,
  100% {
    opacity: 0;
  }
  50% {
    opacity: 1;
  }
}

.animate-fade-in-out {
  animation: fade-in-out v-bind('duration + "ms"') ease-in-out forwards;
}

.animate-scale-bounce {
  animation: scale-bounce 0.6s cubic-bezier(0.68, -0.55, 0.265, 1.55) forwards;
}

.animate-float-away {
  animation: float-away v-bind('duration + "ms"') ease-out forwards;
}

.animate-twinkle {
  animation: twinkle 2s ease-in-out infinite;
}

.animate-bounce {
  animation: bounce 1s ease-in-out infinite;
}

@keyframes bounce {
  0%,
  100% {
    transform: translateY(0);
  }
  50% {
    transform: translateY(-10px);
  }
}
</style>
