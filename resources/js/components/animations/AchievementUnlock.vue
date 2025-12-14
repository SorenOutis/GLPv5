<template>
  <Teleport to="body">
    <div
      v-if="isVisible"
      class="pointer-events-none fixed inset-0 z-40 flex items-center justify-center"
    >
      <!-- Background blur -->
      <div
        class="absolute inset-0 bg-black/50 backdrop-blur-sm animate-fade-in"
        :style="{ animationDuration: duration + 'ms' }"
        @click.self="close"
      />

      <!-- Achievement card -->
      <div
        class="relative animate-pop-in"
        :style="{ animationDuration: duration * 0.4 + 'ms' }"
      >
        <!-- Glow effect -->
        <div
          class="absolute -inset-6 rounded-2xl bg-gradient-to-r from-purple-500 via-pink-500 to-red-500 blur-2xl opacity-60 animate-pulse"
        />

        <!-- Main card -->
        <div
          class="relative bg-gradient-to-br from-slate-900 to-slate-800 rounded-xl p-8 border-2 border-purple-500 shadow-2xl w-80 text-center overflow-hidden"
        >
          <!-- Animated background pattern -->
          <div class="absolute inset-0 opacity-10">
            <div
              v-for="i in 20"
              :key="i"
              class="absolute w-px h-px bg-purple-400 rounded-full animate-twinkle"
              :style="{
                left: Math.random() * 100 + '%',
                top: Math.random() * 100 + '%',
                animationDelay: (Math.random() * 2) + 's',
              }"
            />
          </div>

          <!-- Content -->
          <div class="relative z-10">
            <!-- "Achievement Unlocked" text -->
            <p
              class="text-sm font-semibold text-purple-300 mb-3 uppercase tracking-widest animate-bounce"
            >
              🏆 Achievement Unlocked
            </p>

            <!-- Achievement icon -->
            <div
              class="text-6xl mb-4 animate-bounce"
              :style="{ animationDelay: '0.1s' }"
            >
              {{ icon }}
            </div>

            <!-- Achievement name -->
            <h2 class="text-2xl font-bold text-white mb-2">
              {{ name }}
            </h2>

            <!-- Achievement description -->
            <p class="text-slate-300 text-sm mb-4">
              {{ description }}
            </p>

            <!-- XP reward -->
            <div
              v-if="xpReward"
              class="inline-block bg-gradient-to-r from-yellow-400 to-orange-400 text-slate-900 px-4 py-2 rounded-full font-bold text-sm mb-4 animate-bounce"
              :style="{ animationDelay: '0.2s' }"
            >
              +{{ xpReward }} XP
            </div>

            <!-- Progress stars -->
            <div class="flex justify-center gap-2 mt-6">
              <div
                v-for="i in 5"
                :key="i"
                class="w-2 h-2 bg-purple-400 rounded-full animate-pulse"
                :style="{ animationDelay: (i * 100) + 'ms' }"
              />
            </div>
          </div>
        </div>
      </div>

      <!-- Confetti bursts -->
      <div
        v-for="i in 8"
        :key="'burst-' + i"
        class="absolute w-1 h-1 rounded-full animate-burst-out"
        :style="{
          left: 50 + '%',
          top: 50 + '%',
          backgroundColor: ['#a78bfa', '#ec4899', '#f59e0b', '#10b981'][i % 4],
          '--angle': (i / 8) * 360 + 'deg',
          '--distance': 150 + 'px',
          animationDuration: (duration * 0.6) + 'ms',
        } as any"
      />
    </div>
  </Teleport>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';

interface Props {
  name: string;
  description: string;
  icon: string;
  xpReward?: number;
  duration?: number;
  sound?: boolean;
  onClose?: () => void;
}

const props = withDefaults(defineProps<Props>(), {
  duration: 4000,
  sound: true,
});

const isVisible = ref(true);

function playSound() {
  // Create a simple success beep using Web Audio API
  if (!props.sound) return;
  try {
    const audioContext = new (window.AudioContext ||
      (window as any).webkitAudioContext)();
    const now = audioContext.currentTime;

    // Create multiple tones for a "ding" sound
    const notes = [523.25, 659.25, 783.99]; // C, E, G - triumphant chord
    notes.forEach((freq, i) => {
      const osc = audioContext.createOscillator();
      const gain = audioContext.createGain();

      osc.frequency.value = freq;
      osc.connect(gain);
      gain.connect(audioContext.destination);

      gain.gain.setValueAtTime(0.3, now);
      gain.gain.exponentialRampToValueAtTime(0.01, now + 0.5);

      osc.start(now + i * 0.1);
      osc.stop(now + 0.5 + i * 0.1);
    });
  } catch (e) {
    // Audio context not available
  }
}

function close() {
  isVisible.value = false;
  props.onClose?.();
}

onMounted(() => {
  playSound();
  setTimeout(() => {
    close();
  }, props.duration);
});

defineExpose({ close });
</script>

<style scoped>
@keyframes fade-in {
  from {
    opacity: 0;
  }
  to {
    opacity: 1;
  }
}

@keyframes pop-in {
  0% {
    opacity: 0;
    transform: scale(0.5) rotateZ(-10deg);
  }
  70% {
    transform: scale(1.05) rotateZ(5deg);
  }
  100% {
    opacity: 1;
    transform: scale(1) rotateZ(0deg);
  }
}

@keyframes burst-out {
  0% {
    opacity: 1;
    transform: translate(0, 0);
  }
  100% {
    opacity: 0;
    transform: translate(
      calc(cos(var(--angle)) * var(--distance)),
      calc(sin(var(--angle)) * var(--distance))
    );
  }
}

@keyframes twinkle {
  0%,
  100% {
    opacity: 0.3;
  }
  50% {
    opacity: 1;
  }
}

@keyframes bounce {
  0%,
  100% {
    transform: translateY(0);
  }
  50% {
    transform: translateY(-5px);
  }
}

.animate-fade-in {
  animation: fade-in v-bind('duration + "ms"') ease-in-out forwards;
}

.animate-pop-in {
  animation: pop-in v-bind('duration * 0.4 + "ms"') cubic-bezier(0.68, -0.55, 0.265, 1.55) forwards;
}

.animate-burst-out {
  animation: burst-out v-bind('duration * 0.6 + "ms"') ease-out forwards;
}

.animate-twinkle {
  animation: twinkle 1.5s ease-in-out infinite;
}

.animate-pulse {
  animation: pulse 2s ease-in-out infinite;
}

@keyframes pulse {
  0%,
  100% {
    opacity: 1;
  }
  50% {
    opacity: 0.5;
  }
}

.animate-bounce {
  animation: bounce 1s ease-in-out infinite;
}
</style>
