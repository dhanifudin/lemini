<script setup lang="ts">
import { computed, onMounted } from 'vue';
import { useBehavioralTracking } from '@/composables/useBehavioralTracking';

interface Props {
  learningStyle: string | null;
  trackingId?: string;
}

const props = defineProps<Props>();

const { trackInteraction } = useBehavioralTracking();

const containerClasses = computed(() => {
  const baseClasses = 'adaptive-container transition-all duration-300';
  
  switch (props.learningStyle) {
    case 'visual':
      return `${baseClasses} visual-style`;
    case 'auditory':
      return `${baseClasses} auditory-style`;
    case 'kinesthetic':
      return `${baseClasses} kinesthetic-style`;
    default:
      return `${baseClasses} default-style`;
  }
});

onMounted(() => {
  if (props.trackingId) {
    trackInteraction('section_view', {
      section_id: props.trackingId,
      learning_style: props.learningStyle,
    });
  }
});
</script>

<template>
  <div :class="containerClasses">
    <slot />
  </div>
</template>

<style scoped>
/* Visual Style: Image-heavy, bright colors, clear hierarchy */
.visual-style {
  --primary-color: #3b82f6;
  --secondary-color: #60a5fa;
  --accent-color: #dbeafe;
  --text-primary: #1e293b;
  --text-secondary: #64748b;
}

.visual-style :deep(.section-title) {
  font-size: 2.5rem;
  font-weight: 800;
  background: linear-gradient(135deg, #3b82f6 0%, #8b5cf6 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
  margin-bottom: 1.5rem;
}

.visual-style :deep(.card) {
  border: 2px solid #e0e7ff;
  box-shadow: 0 4px 6px -1px rgba(59, 130, 246, 0.1);
  transition: transform 0.2s, box-shadow 0.2s;
}

.visual-style :deep(.card:hover) {
  transform: translateY(-4px);
  box-shadow: 0 20px 25px -5px rgba(59, 130, 246, 0.2);
}

.visual-style :deep(.icon) {
  filter: drop-shadow(0 4px 6px rgba(59, 130, 246, 0.3));
}

/* Auditory Style: Focus on text, audio cues, subtle animations */
.auditory-style {
  --primary-color: #8b5cf6;
  --secondary-color: #a78bfa;
  --accent-color: #ede9fe;
  --text-primary: #1e293b;
  --text-secondary: #64748b;
}

.auditory-style :deep(.section-title) {
  font-size: 2rem;
  font-weight: 700;
  color: #8b5cf6;
  margin-bottom: 1rem;
  position: relative;
  padding-left: 1.5rem;
}

.auditory-style :deep(.section-title::before) {
  content: '🔊';
  position: absolute;
  left: 0;
  font-size: 1.5rem;
}

.auditory-style :deep(.card) {
  border: 1px solid #e9d5ff;
  box-shadow: 0 1px 3px 0 rgba(139, 92, 246, 0.1);
  background: linear-gradient(135deg, #ffffff 0%, #faf5ff 100%);
}

.auditory-style :deep(.text-content) {
  line-height: 1.8;
  font-size: 1.1rem;
}

.auditory-style :deep(.audio-indicator) {
  display: inline-block;
  animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

@keyframes pulse {
  0%, 100% {
    opacity: 1;
  }
  50% {
    opacity: 0.5;
  }
}

/* Kinesthetic Style: Interactive, button-heavy, tactile feedback */
.kinesthetic-style {
  --primary-color: #10b981;
  --secondary-color: #34d399;
  --accent-color: #d1fae5;
  --text-primary: #1e293b;
  --text-secondary: #64748b;
}

.kinesthetic-style :deep(.section-title) {
  font-size: 2.25rem;
  font-weight: 700;
  color: #10b981;
  margin-bottom: 1.25rem;
  cursor: pointer;
  transition: transform 0.2s;
}

.kinesthetic-style :deep(.section-title:hover) {
  transform: scale(1.02);
}

.kinesthetic-style :deep(.card) {
  border: 2px solid #d1fae5;
  box-shadow: 0 4px 6px -1px rgba(16, 185, 129, 0.1);
  cursor: pointer;
  transition: all 0.2s;
  position: relative;
}

.kinesthetic-style :deep(.card::after) {
  content: '👆 Click to Explore';
  position: absolute;
  top: 0.5rem;
  right: 0.5rem;
  font-size: 0.75rem;
  color: #10b981;
  background: #d1fae5;
  padding: 0.25rem 0.5rem;
  border-radius: 0.25rem;
  opacity: 0;
  transition: opacity 0.2s;
}

.kinesthetic-style :deep(.card:hover::after) {
  opacity: 1;
}

.kinesthetic-style :deep(.card:hover) {
  transform: scale(1.02);
  box-shadow: 0 10px 15px -3px rgba(16, 185, 129, 0.2);
  border-color: #10b981;
}

.kinesthetic-style :deep(.card:active) {
  transform: scale(0.98);
}

.kinesthetic-style :deep(.interactive-btn) {
  background: linear-gradient(135deg, #10b981 0%, #34d399 100%);
  color: white;
  font-weight: 600;
  padding: 0.75rem 1.5rem;
  border-radius: 0.5rem;
  transition: all 0.2s;
  box-shadow: 0 4px 6px -1px rgba(16, 185, 129, 0.3);
}

.kinesthetic-style :deep(.interactive-btn:hover) {
  transform: translateY(-2px);
  box-shadow: 0 10px 15px -3px rgba(16, 185, 129, 0.4);
}

.kinesthetic-style :deep(.interactive-btn:active) {
  transform: translateY(0);
  box-shadow: 0 2px 4px -1px rgba(16, 185, 129, 0.3);
}

/* Default Style: Neutral, balanced */
.default-style {
  --primary-color: #6366f1;
  --secondary-color: #818cf8;
  --accent-color: #e0e7ff;
  --text-primary: #1e293b;
  --text-secondary: #64748b;
}

.default-style :deep(.section-title) {
  font-size: 2.25rem;
  font-weight: 700;
  color: #1e293b;
  margin-bottom: 1.25rem;
}

.default-style :deep(.card) {
  border: 1px solid #e2e8f0;
  box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
  transition: box-shadow 0.2s;
}

.default-style :deep(.card:hover) {
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
}
</style>
