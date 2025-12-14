<template>
  <div
    class="relative w-full"
    :style="{ perspective: '1000px', height }"
    @click="toggleFlip"
  >
    <div
      class="relative w-full h-full transition-transform duration-500"
      :style="{
        transformStyle: 'preserve-3d',
        transform: isFlipped ? 'rotateY(180deg)' : 'rotateY(0deg)',
      }"
    >
      <!-- Front side -->
      <div
        class="absolute inset-0 w-full h-full"
        :style="{ backfaceVisibility: 'hidden' }"
      >
        <slot name="front" :isFlipped="isFlipped" />
      </div>

      <!-- Back side -->
      <div
        class="absolute inset-0 w-full h-full"
        :style="{ backfaceVisibility: 'hidden', transform: 'rotateY(180deg)' }"
      >
        <slot name="back" :isFlipped="isFlipped" />
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue';

interface Props {
  height?: string;
  flipOnClick?: boolean;
  initialFlipped?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
  height: 'auto',
  flipOnClick: true,
  initialFlipped: false,
});

const isFlipped = ref(props.initialFlipped);

const toggleFlip = () => {
  if (props.flipOnClick) {
    isFlipped.value = !isFlipped.value;
  }
};

const flip = () => {
  isFlipped.value = !isFlipped.value;
};

defineExpose({ flip, isFlipped });
</script>

<style scoped>
/* Smooth 3D flip effect */
div {
  box-sizing: border-box;
}
</style>
