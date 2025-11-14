<script setup lang="ts">
import { ref } from 'vue';

interface Props {
  currentStyle: string | null;
}

const props = defineProps<Props>();

const emit = defineEmits<{
  (e: 'select', style: string): void;
  (e: 'take-quiz'): void;
}>();

const styles = [
  {
    value: 'visual',
    label: 'Visual',
    icon: 'eye',
    color: 'blue',
    description: 'Saya lebih suka belajar dengan melihat gambar dan diagram',
  },
  {
    value: 'auditory',
    label: 'Auditori',
    icon: 'volume-2',
    color: 'purple',
    description: 'Saya lebih suka belajar dengan mendengar penjelasan',
  },
  {
    value: 'kinesthetic',
    label: 'Kinestetik',
    icon: 'hand',
    color: 'green',
    description: 'Saya lebih suka belajar dengan praktek langsung',
  },
];

const showDropdown = ref(false);

const getColorClasses = (color: string, isActive: boolean) => {
  const colors: Record<string, { bg: string; border: string; text: string; hoverBg: string }> = {
    blue: {
      bg: isActive ? 'bg-blue-500' : 'bg-blue-50',
      border: isActive ? 'border-blue-500' : 'border-blue-200',
      text: isActive ? 'text-white' : 'text-blue-700',
      hoverBg: 'hover:bg-blue-100',
    },
    purple: {
      bg: isActive ? 'bg-purple-500' : 'bg-purple-50',
      border: isActive ? 'border-purple-500' : 'border-purple-200',
      text: isActive ? 'text-white' : 'text-purple-700',
      hoverBg: 'hover:bg-purple-100',
    },
    green: {
      bg: isActive ? 'bg-green-500' : 'bg-green-50',
      border: isActive ? 'border-green-500' : 'border-green-200',
      text: isActive ? 'text-white' : 'text-green-700',
      hoverBg: 'hover:bg-green-100',
    },
  };
  return colors[color] || colors.blue;
};
</script>

<template>
  <div class="relative">
    <button
      @click="showDropdown = !showDropdown"
      class="flex items-center space-x-2 px-4 py-2 bg-white border-2 border-gray-200 rounded-lg hover:border-gray-300 transition"
    >
      <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
      </svg>
      <span class="font-medium text-gray-700">
        {{ currentStyle ? styles.find(s => s.value === currentStyle)?.label : 'Pilih Gaya Belajar' }}
      </span>
      <svg class="w-4 h-4 text-gray-500 transition-transform" :class="{ 'rotate-180': showDropdown }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
      </svg>
    </button>

    <transition
      enter-active-class="transition ease-out duration-200"
      enter-from-class="opacity-0 translate-y-1"
      enter-to-class="opacity-100 translate-y-0"
      leave-active-class="transition ease-in duration-150"
      leave-from-class="opacity-100 translate-y-0"
      leave-to-class="opacity-0 translate-y-1"
    >
      <div
        v-if="showDropdown"
        class="absolute right-0 mt-2 w-80 bg-white rounded-xl shadow-xl border border-gray-200 z-50"
      >
        <div class="p-4">
          <h3 class="text-sm font-semibold text-gray-900 mb-3">Pilih Gaya Belajar Anda</h3>
          
          <div class="space-y-2 mb-4">
            <button
              v-for="style in styles"
              :key="style.value"
              @click="emit('select', style.value); showDropdown = false"
              class="w-full p-3 border-2 rounded-lg text-left transition-all"
              :class="[
                getColorClasses(style.color, currentStyle === style.value).bg,
                getColorClasses(style.color, currentStyle === style.value).border,
                getColorClasses(style.color, currentStyle === style.value).text,
                currentStyle !== style.value ? getColorClasses(style.color, false).hoverBg : '',
              ]"
            >
              <div class="flex items-start space-x-3">
                <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path v-if="style.icon === 'eye'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                  <path v-if="style.icon === 'eye'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                  <path v-if="style.icon === 'volume-2'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.536 8.464a5 5 0 010 7.072m2.828-9.9a9 9 0 010 12.728M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z" />
                  <path v-if="style.icon === 'hand'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11.5V14m0-2.5v-6a1.5 1.5 0 113 0m-3 6a1.5 1.5 0 00-3 0v2a7.5 7.5 0 0015 0v-5a1.5 1.5 0 00-3 0m-6-3V11m0-5.5v-1a1.5 1.5 0 013 0v1m0 0V11m0-5.5a1.5 1.5 0 013 0v3m0 0V11" />
                </svg>
                <div>
                  <div class="font-semibold">{{ style.label }}</div>
                  <div class="text-xs opacity-90 mt-1">{{ style.description }}</div>
                </div>
                <svg v-if="currentStyle === style.value" class="w-5 h-5 ml-auto flex-shrink-0" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z" />
                </svg>
              </div>
            </button>
          </div>

          <div class="border-t border-gray-200 pt-3">
            <button
              @click="emit('take-quiz'); showDropdown = false"
              class="w-full px-4 py-2 bg-gradient-to-r from-blue-500 to-purple-500 text-white rounded-lg font-medium hover:shadow-lg transition"
            >
              🧪 Ikuti Kuis untuk Mengetahui Gaya Belajar Anda
            </button>
          </div>
        </div>
      </div>
    </transition>

    <!-- Backdrop -->
    <div
      v-if="showDropdown"
      @click="showDropdown = false"
      class="fixed inset-0 z-40"
    ></div>
  </div>
</template>
