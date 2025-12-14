<template>
  <div class="pointer-events-none">
    <!-- Render all active animations -->
    <template v-for="(animation, id) in animationContainer" :key="id">
      <component
        :is="animation.component"
        v-bind="animation.props"
      />
    </template>
  </div>
</template>

<script setup lang="ts">
import { useAnimations } from '@/composables/useAnimations';
import { provide } from 'vue';

const animations = useAnimations();
const { animationContainer } = animations;

// Provide animations globally so they can be accessed from anywhere
provide('animations', animations);
</script>
