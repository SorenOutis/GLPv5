import { reactive, inject } from 'vue';
import XPPopup from '@/components/animations/XPPopup.vue';
import ConfettiExplosion from '@/components/animations/ConfettiExplosion.vue';
import LevelUpAnimation from '@/components/animations/LevelUpAnimation.vue';
import AchievementUnlock from '@/components/animations/AchievementUnlock.vue';

// Global state for animations
const globalAnimationContainer = reactive<Map<string, any>>(new Map());
let globalAnimationId = 0;

export function useAnimations() {
  const animationContainer = globalAnimationContainer;
  let animationId = globalAnimationId;

  /**
   * Show XP popup animation at specific coordinates
   */
  function showXPPopup(xp: number, x: number, y: number, duration = 2000) {
    const id = `xp-${animationId++}`;
    globalAnimationId = animationId;

    const instance = {
      component: XPPopup,
      props: { xp, x, y, duration },
    };

    animationContainer.set(id, instance);

    setTimeout(() => {
      animationContainer.delete(id);
    }, duration + 100);

    return id;
  }

  /**
   * Show confetti explosion at center or custom position
   */
  function showConfetti(
    count = 50,
    duration = 3000,
    x = 0.5,
    y = 0.5
  ) {
    const id = `confetti-${animationId++}`;
    globalAnimationId = animationId;

    const instance = {
      component: ConfettiExplosion,
      props: { count, duration, x, y },
    };

    animationContainer.set(id, instance);

    setTimeout(() => {
      animationContainer.delete(id);
    }, duration + 100);

    return id;
  }

  /**
   * Show level up celebration animation
   */
  function showLevelUp(
    level: number,
    xpReward?: number,
    duration = 3000
  ) {
    const id = `levelup-${animationId++}`;
    globalAnimationId = animationId;

    const instance = {
      component: LevelUpAnimation,
      props: { level, xpReward, duration },
    };

    animationContainer.set(id, instance);

    setTimeout(() => {
      animationContainer.delete(id);
    }, duration + 100);

    return id;
  }

  /**
   * Show achievement unlock animation with sound
   */
  function showAchievementUnlock(
    name: string,
    description: string,
    icon: string,
    xpReward?: number,
    duration = 4000,
    sound = true
  ) {
    const id = `achievement-${animationId++}`;
    globalAnimationId = animationId;

    const instance = {
      component: AchievementUnlock,
      props: { name, description, icon, xpReward, duration, sound },
    };

    animationContainer.set(id, instance);

    setTimeout(() => {
      animationContainer.delete(id);
    }, duration + 100);

    return id;
  }

  /**
   * Clear all active animations
   */
  function clearAnimations() {
    animationContainer.clear();
  }

  /**
   * Remove specific animation
   */
  function removeAnimation(id: string) {
    animationContainer.delete(id);
  }

  return {
    animationContainer,
    showXPPopup,
    showConfetti,
    showLevelUp,
    showAchievementUnlock,
    clearAnimations,
    removeAnimation,
  };
}
