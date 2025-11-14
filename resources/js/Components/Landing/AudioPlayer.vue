<script setup lang="ts">
import { ref, watch, onUnmounted } from 'vue';
import { useAudioNarration } from '@/composables/useAudioNarration';

interface Props {
  text: string;
  autoPlay?: boolean;
  showText?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
  autoPlay: false,
  showText: false,
});

const { speak, pause, resume, stop, isPlaying, isPaused, isSupported } = useAudioNarration();

const hasPlayed = ref(false);

watch(() => props.text, () => {
  if (isPlaying.value) {
    stop();
    hasPlayed.value = false;
  }
});

watch(() => props.autoPlay, (newValue) => {
  if (newValue && !hasPlayed.value && isSupported.value) {
    handlePlay();
  }
});

const handlePlay = () => {
  if (isPaused.value) {
    resume();
  } else {
    speak(props.text);
    hasPlayed.value = true;
  }
};

const handlePause = () => {
  pause();
};

const handleStop = () => {
  stop();
  hasPlayed.value = false;
};

onUnmounted(() => {
  stop();
});
</script>

<template>
  <div class="audio-player">
    <div v-if="!isSupported" class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-4">
      <p class="text-yellow-800 text-sm">
        🔇 Browser Anda tidak mendukung narasi audio. Silakan gunakan browser modern seperti Chrome atau Edge.
      </p>
    </div>

    <div v-else class="flex items-center space-x-3 bg-gradient-to-r from-purple-50 to-blue-50 rounded-lg p-4 border border-purple-200">
      <!-- Play/Pause Button -->
      <button
        v-if="!isPlaying || isPaused"
        @click="handlePlay"
        class="flex-shrink-0 w-12 h-12 rounded-full bg-gradient-to-br from-purple-500 to-blue-500 text-white flex items-center justify-center hover:shadow-lg transition-all transform hover:scale-105"
      >
        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
          <path d="M8 5v14l11-7z" />
        </svg>
      </button>

      <button
        v-else
        @click="handlePause"
        class="flex-shrink-0 w-12 h-12 rounded-full bg-gradient-to-br from-purple-500 to-blue-500 text-white flex items-center justify-center hover:shadow-lg transition-all transform hover:scale-105"
      >
        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
          <path d="M6 4h4v16H6V4zm8 0h4v16h-4V4z" />
        </svg>
      </button>

      <!-- Info -->
      <div class="flex-1 min-w-0">
        <div class="flex items-center space-x-2">
          <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.536 8.464a5 5 0 010 7.072m2.828-9.9a9 9 0 010 12.728M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z" />
          </svg>
          <span class="text-purple-900 font-medium text-sm">
            {{ isPlaying && !isPaused ? 'Memutar narasi audio...' : 'Dengarkan narasi audio' }}
          </span>
        </div>
        
        <p v-if="showText" class="text-purple-700 text-xs mt-1 line-clamp-2">
          {{ text }}
        </p>
      </div>

      <!-- Stop Button -->
      <button
        v-if="isPlaying || isPaused"
        @click="handleStop"
        class="flex-shrink-0 w-10 h-10 rounded-full bg-white text-purple-600 flex items-center justify-center hover:bg-purple-50 transition"
      >
        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
          <path d="M6 6h12v12H6z" />
        </svg>
      </button>
    </div>
  </div>
</template>
