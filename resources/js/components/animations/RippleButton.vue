<template>
  <button
    ref="buttonRef"
    class="relative overflow-hidden transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2"
    :class="[baseClasses, sizeClasses, variantClasses, isPressed && 'scale-95']"
    @mousedown="createRipple"
    @click="emit('click', $event)"
  >
    <!-- Ripple effect container -->
    <div class="absolute inset-0 pointer-events-none">
      <div
        v-for="ripple in ripples"
        :key="ripple.id"
        class="absolute rounded-full bg-white/30 animate-ripple"
        :style="{
          left: ripple.x + 'px',
          top: ripple.y + 'px',
          width: '20px',
          height: '20px',
          transform: 'translate(-50%, -50%)',
        }"
      />
    </div>

    <!-- Button content -->
    <span class="relative z-10 flex items-center justify-center gap-2">
      <slot />
    </span>
  </button>
</template>

<script setup lang="ts">
import { ref } from 'vue';

interface Ripple {
  id: number;
  x: number;
  y: number;
}

type Variant = 'default' | 'primary' | 'secondary' | 'danger' | 'ghost';
type Size = 'sm' | 'md' | 'lg';

interface Props {
  variant?: Variant;
  size?: Size;
}

const props = withDefaults(defineProps<Props>(), {
  variant: 'default',
  size: 'md',
});

const emit = defineEmits<{
  click: [event: MouseEvent];
}>();

const buttonRef = ref<HTMLButtonElement>();
const ripples = ref<Ripple[]>([]);
const isPressed = ref(false);
let rippleId = 0;

const variantClasses: Record<Variant, string> = {
  default:
    'bg-gray-200 text-gray-900 hover:bg-gray-300 dark:bg-gray-700 dark:text-gray-100 dark:hover:bg-gray-600',
  primary:
    'bg-blue-600 text-white hover:bg-blue-700 dark:bg-blue-700 dark:hover:bg-blue-600',
  secondary:
    'bg-gray-600 text-white hover:bg-gray-700 dark:bg-gray-700 dark:hover:bg-gray-600',
  danger:
    'bg-red-600 text-white hover:bg-red-700 dark:bg-red-700 dark:hover:bg-red-600',
  ghost:
    'bg-transparent text-gray-900 hover:bg-gray-100 dark:text-gray-100 dark:hover:bg-gray-800',
};

const sizeClasses: Record<Size, string> = {
  sm: 'px-3 py-1 text-sm rounded',
  md: 'px-4 py-2 text-base rounded-md',
  lg: 'px-6 py-3 text-lg rounded-lg',
};

const baseClasses = 'font-semibold cursor-pointer active:outline-none';

function createRipple(e: MouseEvent) {
  if (!buttonRef.value) return;

  isPressed.value = true;
  setTimeout(() => {
    isPressed.value = false;
  }, 200);

  const rect = buttonRef.value.getBoundingClientRect();
  const x = e.clientX - rect.left;
  const y = e.clientY - rect.top;

  const newRipple: Ripple = {
    id: rippleId++,
    x,
    y,
  };

  ripples.value.push(newRipple);

  // Remove ripple after animation completes
  setTimeout(() => {
    ripples.value = ripples.value.filter(r => r.id !== newRipple.id);
  }, 600);
}
</script>

<style scoped>
@keyframes ripple {
  0% {
    width: 20px;
    height: 20px;
    opacity: 1;
  }
  100% {
    width: 400px;
    height: 400px;
    opacity: 0;
  }
}

.animate-ripple {
  animation: ripple 0.6s ease-out;
}
</style>
