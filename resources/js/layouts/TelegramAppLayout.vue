<script setup lang="ts">
import { onMounted, onBeforeUnmount, ref, computed } from 'vue';
import { viewport } from '@telegram-apps/sdk';

interface Props {
  /** Adds safe-area paddings around the content */
  padded?: boolean;
  /** Optional: force full-bleed background for Telegram theme */
  fill?: boolean;
  /** Optional extra classes for the root */
  class?: string;
}

const props = withDefaults(defineProps<Props>(), {
  padded: true,
  fill: true,
  class: '',
});

// Reactive CSS vars map for container style
const styleVars = ref<Record<string, string>>({
  // Defaults use CSS env fallbacks where available, otherwise 0px
  '--tma-safe-top': 'env(safe-area-inset-top, 0px)',
  '--tma-safe-bottom': 'env(safe-area-inset-bottom, 0px)',
  '--tma-safe-left': 'env(safe-area-inset-left, 0px)',
  '--tma-safe-right': 'env(safe-area-inset-right, 0px)',
  // Min height prefers Telegram viewport CSS var, then dynamic viewport units
  '--tma-viewport-h': 'var(--tg-viewport-height, 100dvh)',
});

function applySafeAreaFromSDK(): void {
  try {
    // Prefer content-safe area to avoid Telegram UI overlays
    const c = viewport.contentSafeAreaInsets?.();
    if (c) {
      styleVars.value['--tma-safe-top'] = `${Math.max(0, c.top)}px`;
      styleVars.value['--tma-safe-bottom'] = `${Math.max(0, c.bottom)}px`;
      styleVars.value['--tma-safe-left'] = `${Math.max(0, c.left)}px`;
      styleVars.value['--tma-safe-right'] = `${Math.max(0, c.right)}px`;
    }
  } catch {
    // Fall back to env() defaults already set
  }

  try {
    // If SDK bound CSS vars, use its viewport height token
    if (viewport.bindCssVars?.isAvailable?.()) {
      styleVars.value['--tma-viewport-h'] = 'var(--tg-viewport-height, 100dvh)';
    }
  } catch {
    // keep default
  }
}

let interval: number | undefined;

onMounted(async () => {
  // Attempt to mount only if not already mounting/mounted
  try {
    const canMount = viewport.mount?.isAvailable?.();
    const mounting = viewport.isMounting?.();
    const mounted = viewport.isMounted?.();
    if (canMount && !mounting && !mounted) {
      viewport.mount?.();
    }
  } catch {}
  // Expanding is idempotent; only call if available
  try { if (viewport.expand?.isAvailable?.()) viewport.expand?.(); } catch {}

  // Apply initial values from SDK if available
  applySafeAreaFromSDK();

  // Light polling as a fallback to update insets on changes (e.g., rotate)
  // Avoids deep event wiring; SDK also updates env tokens where supported.
  interval = window.setInterval(applySafeAreaFromSDK, 600);

  // Also react on resize
  window.addEventListener('resize', applySafeAreaFromSDK);
});

onBeforeUnmount(() => {
  if (interval) window.clearInterval(interval);
  window.removeEventListener('resize', applySafeAreaFromSDK);
});

const rootClasses = computed(() => [
  'tma-root',
  props.fill ? 'tma-fill' : '',
  props.padded ? 'tma-padded' : '',
  props.class,
]);
</script>

<template>
  <div :class="rootClasses" :style="styleVars">
    <!-- Optional top spacer to avoid underlapping Telegram UI -->
    <div v-if="padded" aria-hidden="true" class="tma-spacer tma-spacer-top" />

    <main class="tma-content">
      <slot />
    </main>

    <!-- Optional bottom spacer to keep sticky actions above system areas -->
    <div v-if="padded" aria-hidden="true" class="tma-spacer tma-spacer-bottom" />
  </div>

  <!-- Sticky action slot rendered above the bottom safe-area -->
  <div v-if="$slots.actions" class="tma-actions">
    <slot name="actions" />
  </div>
</template>

<style scoped>
/* Root fills the Telegram viewport height and adapts to theme */
.tma-root {
  min-height: var(--tma-viewport-h);
  display: flex;
  flex-direction: column;
  align-items: stretch;
}

/* When fill is enabled, use Telegram theme background and text */
.tma-fill {
  background: var(--color-tg-bg, var(--background));
  color: var(--color-tg-text, var(--foreground));
}

/* Padded layout reserves safe areas using CSS variables with env() fallbacks */
.tma-padded {
  padding-left: max(var(--tma-safe-left), 15px);
  padding-right: max(var(--tma-safe-right), 15px);
}

.tma-content {
  flex: 1 1 auto;
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
}

.tma-spacer {
  width: 100%;
  flex: 0 0 auto;
}

.tma-spacer-top {
  height: max(var(--tma-safe-top), 15px);
}

.tma-spacer-bottom {
  height: max(var(--tma-safe-bottom), 15px);
}

/* Sticky actions bar that stays above bottom safe area */
.tma-actions {
  position: sticky;
  bottom: 0;
  left: 0;
  right: 0;
  padding: 0.5rem clamp(0.75rem, 2vw, 1rem);
  padding-bottom: calc(0.5rem + max(var(--tma-safe-bottom), 0px));
  background: linear-gradient(
      to top,
      color-mix(in oklab, var(--color-tg-bg, var(--background)) 100%, transparent),
      transparent 60%
  );
  display: flex;
  gap: 0.5rem;
  justify-content: stretch;
}
</style>
