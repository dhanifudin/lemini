<script setup lang="ts">
import { ref, computed } from 'vue';
import { useLearningStyle } from '@/composables/useLearningStyle';

const emit = defineEmits<{
  (e: 'complete', result: { style: string; confidence: number }): void;
  (e: 'close'): void;
}>();

const { quizQuestions, calculateLearningStyle, saveLearningStyle } = useLearningStyle();

const currentQuestionIndex = ref(0);
const answers = ref<string[]>([]);
const isCompleted = ref(false);
const result = ref<any>(null);

const currentQuestion = computed(() => quizQuestions[currentQuestionIndex.value]);
const progress = computed(() => ((currentQuestionIndex.value + 1) / quizQuestions.length) * 100);

const selectAnswer = (answer: string) => {
  answers.value[currentQuestionIndex.value] = answer;
  
  if (currentQuestionIndex.value < quizQuestions.length - 1) {
    currentQuestionIndex.value++;
  } else {
    completeQuiz();
  }
};

const goBack = () => {
  if (currentQuestionIndex.value > 0) {
    currentQuestionIndex.value--;
  }
};

const completeQuiz = async () => {
  result.value = calculateLearningStyle(answers.value);
  isCompleted.value = true;
  
  await saveLearningStyle(
    result.value.primary,
    'quiz',
    { answers: answers.value, breakdown: result.value.breakdown },
    result.value.confidence / 100
  );
  
  emit('complete', {
    style: result.value.primary,
    confidence: result.value.confidence,
  });
};

const getStyleLabel = (style: string) => {
  const labels: Record<string, string> = {
    visual: 'Visual',
    auditory: 'Auditori',
    kinesthetic: 'Kinestetik',
  };
  return labels[style] || style;
};

const getStyleDescription = (style: string) => {
  const descriptions: Record<string, string> = {
    visual: 'Anda belajar lebih baik dengan melihat gambar, diagram, dan visualisasi',
    auditory: 'Anda belajar lebih baik dengan mendengar penjelasan dan diskusi',
    kinesthetic: 'Anda belajar lebih baik dengan praktek langsung dan eksperimen',
  };
  return descriptions[style] || '';
};
</script>

<template>
  <div class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto shadow-2xl">
      <!-- Header -->
      <div class="sticky top-0 bg-white border-b border-gray-200 p-6">
        <div class="flex items-center justify-between mb-4">
          <h2 class="text-2xl font-bold text-gray-900">
            {{ isCompleted ? 'Hasil Gaya Belajar Anda' : 'Temukan Gaya Belajar Anda' }}
          </h2>
          <button
            @click="emit('close')"
            class="text-gray-400 hover:text-gray-600 transition"
          >
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>
        
        <!-- Progress Bar -->
        <div v-if="!isCompleted" class="w-full bg-gray-200 rounded-full h-2">
          <div
            class="bg-gradient-to-r from-blue-500 to-purple-500 h-2 rounded-full transition-all duration-300"
            :style="{ width: `${progress}%` }"
          ></div>
        </div>
        <p v-if="!isCompleted" class="text-sm text-gray-600 mt-2">
          Pertanyaan {{ currentQuestionIndex + 1 }} dari {{ quizQuestions.length }}
        </p>
      </div>

      <!-- Question -->
      <div v-if="!isCompleted" class="p-6">
        <h3 class="text-xl font-semibold text-gray-900 mb-6">
          {{ currentQuestion.question }}
        </h3>

        <div class="space-y-3">
          <button
            v-for="option in currentQuestion.options"
            :key="option.value"
            @click="selectAnswer(option.value)"
            class="w-full p-4 border-2 rounded-xl text-left hover:border-blue-500 hover:bg-blue-50 transition-all group"
            :class="answers[currentQuestionIndex] === option.value ? 'border-blue-500 bg-blue-50' : 'border-gray-200'"
          >
            <div class="flex items-center space-x-4">
              <div class="flex-shrink-0 w-12 h-12 rounded-lg bg-gradient-to-br from-blue-500 to-purple-500 flex items-center justify-center text-white">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path v-if="option.icon === 'eye'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                  <path v-if="option.icon === 'eye'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                  <path v-if="option.icon === 'volume-2'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.536 8.464a5 5 0 010 7.072m2.828-9.9a9 9 0 010 12.728M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z" />
                  <path v-if="option.icon === 'hand'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11.5V14m0-2.5v-6a1.5 1.5 0 113 0m-3 6a1.5 1.5 0 00-3 0v2a7.5 7.5 0 0015 0v-5a1.5 1.5 0 00-3 0m-6-3V11m0-5.5v-1a1.5 1.5 0 013 0v1m0 0V11m0-5.5a1.5 1.5 0 013 0v3m0 0V11" />
                  <path v-if="option.icon === 'image'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                  <path v-if="option.icon === 'headphones'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z" />
                  <path v-if="option.icon === 'monitor'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                  <path v-if="option.icon === 'mic'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z" />
                  <path v-if="option.icon === 'zap'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                  <path v-if="option.icon === 'type'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129" />
                  <path v-if="option.icon === 'message-circle'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                  <path v-if="option.icon === 'edit-3'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                  <path v-if="option.icon === 'map'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                  <path v-if="option.icon === 'message-square'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                  <path v-if="option.icon === 'navigation'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                </svg>
              </div>
              <span class="text-gray-900 font-medium group-hover:text-blue-600 transition">
                {{ option.text }}
              </span>
            </div>
          </button>
        </div>

        <!-- Navigation -->
        <div class="flex justify-between mt-6">
          <button
            v-if="currentQuestionIndex > 0"
            @click="goBack"
            class="px-6 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition"
          >
            Kembali
          </button>
        </div>
      </div>

      <!-- Results -->
      <div v-else class="p-6">
        <div class="text-center mb-6">
          <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-gradient-to-br from-blue-500 to-purple-500 text-white mb-4">
            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
          </div>
          <h3 class="text-2xl font-bold text-gray-900 mb-2">
            Gaya Belajar Anda: {{ getStyleLabel(result.primary) }}
          </h3>
          <p class="text-gray-600">
            {{ getStyleDescription(result.primary) }}
          </p>
        </div>

        <!-- Breakdown -->
        <div class="space-y-3 mb-6">
          <div
            v-for="item in result.breakdown"
            :key="item.style"
            class="flex items-center justify-between"
          >
            <span class="text-gray-700 font-medium capitalize">{{ getStyleLabel(item.style) }}</span>
            <div class="flex items-center space-x-3">
              <div class="w-32 bg-gray-200 rounded-full h-2">
                <div
                  class="bg-gradient-to-r from-blue-500 to-purple-500 h-2 rounded-full"
                  :style="{ width: `${item.percentage}%` }"
                ></div>
              </div>
              <span class="text-gray-600 font-semibold">{{ Math.round(item.percentage) }}%</span>
            </div>
          </div>
        </div>

        <button
          @click="emit('close')"
          class="w-full px-6 py-3 bg-gradient-to-r from-blue-500 to-purple-500 text-white rounded-lg font-semibold hover:shadow-lg transition"
        >
          Mulai Jelajahi Platform
        </button>
      </div>
    </div>
  </div>
</template>
