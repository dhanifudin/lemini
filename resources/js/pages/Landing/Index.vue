<script setup lang="ts">
import { ref, onMounted, computed } from 'vue';
import { Head } from '@inertiajs/vue3';
import { useLearningStyle } from '@/composables/useLearningStyle';
import { useBehavioralTracking } from '@/composables/useBehavioralTracking';
import LearningStyleQuiz from '@/Components/Landing/LearningStyleQuiz.vue';
import StyleSelector from '@/Components/Landing/StyleSelector.vue';
import AdaptiveContainer from '@/Components/Landing/AdaptiveContainer.vue';

const { currentStyle, loadLearningStyle, saveLearningStyle } = useLearningStyle();
const { analyzeBehavior, getAnalysisFromServer } = useBehavioralTracking();

const showQuiz = ref(false);
const isLoading = ref(true);

onMounted(async () => {
  // Load saved learning style
  await loadLearningStyle();
  
  // If no style detected, check behavioral analysis
  if (!currentStyle.value) {
    const serverAnalysis = await getAnalysisFromServer();
    if (serverAnalysis?.predicted_style) {
      await saveLearningStyle(
        serverAnalysis.predicted_style,
        'behavioral',
        serverAnalysis,
        serverAnalysis.confidence
      );
    }
  }
  
  isLoading.value = false;
});

const handleQuizComplete = (result: { style: string; confidence: number }) => {
  showQuiz.value = false;
};

const handleStyleSelect = async (style: string) => {
  await saveLearningStyle(style, 'explicit', {}, 1.0);
};

const handleTakeQuiz = () => {
  showQuiz.value = true;
};

// Computed display style with fallback
const displayStyle = computed(() => currentStyle.value || null);
</script>

<template>
  <Head>
    <title>Platform Pembelajaran Adaptif - Learning X</title>
    <meta name="description" content="Platform pembelajaran yang menyesuaikan dengan gaya belajar Anda: visual, auditori, atau kinestetik." />
  </Head>

  <div class="landing-page min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-purple-50">
    <!-- Loading State -->
    <div v-if="isLoading" class="flex items-center justify-center min-h-screen">
      <div class="text-center">
        <div class="inline-block w-16 h-16 border-4 border-blue-500 border-t-transparent rounded-full animate-spin mb-4"></div>
        <p class="text-gray-600">Memuat pengalaman belajar Anda...</p>
      </div>
    </div>

    <!-- Main Content -->
    <template v-else>
      <!-- Navigation -->
      <nav class="bg-white/80 backdrop-blur-md border-b border-gray-200 sticky top-0 z-40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div class="flex items-center justify-between h-16">
            <!-- Logo -->
            <div class="flex items-center space-x-2">
              <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-purple-500 rounded-lg flex items-center justify-center">
                <span class="text-white font-bold text-xl">L</span>
              </div>
              <span class="text-xl font-bold text-gray-900">Learning X</span>
            </div>

            <!-- Navigation Links -->
            <div class="hidden md:flex items-center space-x-8">
              <a href="#fitur" class="text-gray-700 hover:text-blue-600 transition font-medium">Fitur</a>
              <a href="#testimoni" class="text-gray-700 hover:text-blue-600 transition font-medium">Testimoni</a>
              <a href="#tentang" class="text-gray-700 hover:text-blue-600 transition font-medium">Tentang</a>
            </div>

            <!-- Style Selector & Auth -->
            <div class="flex items-center space-x-4">
              <StyleSelector
                :current-style="displayStyle"
                @select="handleStyleSelect"
                @take-quiz="handleTakeQuiz"
              />
              
              <a
                href="/login"
                class="hidden sm:block px-4 py-2 text-gray-700 hover:text-gray-900 font-medium transition"
              >
                Masuk
              </a>
              
              <a
                href="/register"
                class="px-4 py-2 bg-gradient-to-r from-blue-500 to-purple-500 text-white rounded-lg font-medium hover:shadow-lg transition"
              >
                Daftar Gratis
              </a>
            </div>
          </div>
        </div>
      </nav>

      <!-- Hero Section -->
      <section class="relative overflow-hidden">
        <AdaptiveContainer
          :learning-style="displayStyle"
          tracking-id="hero-section"
        >
          <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 lg:py-32">
            <div class="grid lg:grid-cols-2 gap-12 items-center">
              <!-- Left Content -->
              <div>
                <div class="inline-flex items-center space-x-2 bg-blue-50 px-4 py-2 rounded-full mb-6">
                  <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5" />
                  </svg>
                  <span class="text-blue-600 font-semibold text-sm">Platform Pembelajaran Adaptif</span>
                </div>

                <h1 class="section-title text-5xl lg:text-6xl font-extrabold text-gray-900 mb-6 leading-tight">
                  Belajar Sesuai <span class="bg-gradient-to-r from-blue-500 to-purple-500 text-transparent bg-clip-text">Gaya Anda</span>
                </h1>

                <p class="text-content text-xl text-gray-600 mb-8 leading-relaxed">
                  Platform pembelajaran yang menyesuaikan dengan cara belajar terbaik Anda. 
                  Visual, auditori, atau kinestetik - kami memahami keunikan Anda.
                </p>

                <!-- CTA Buttons -->
                <div class="flex flex-col sm:flex-row gap-4">
                  <a
                    href="/register"
                    class="interactive-btn inline-flex items-center justify-center px-8 py-4 bg-gradient-to-r from-blue-500 to-purple-500 text-white text-lg font-semibold rounded-xl hover:shadow-2xl transition-all transform hover:-translate-y-1"
                  >
                    Mulai Belajar Gratis
                    <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                    </svg>
                  </a>

                  <button
                    v-if="!displayStyle"
                    @click="showQuiz = true"
                    class="inline-flex items-center justify-center px-8 py-4 border-2 border-gray-300 text-gray-700 text-lg font-semibold rounded-xl hover:border-blue-500 hover:text-blue-600 transition"
                  >
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                    Temukan Gaya Belajar Anda
                  </button>
                </div>

                <!-- Stats -->
                <div class="grid grid-cols-3 gap-6 mt-12 pt-12 border-t border-gray-200">
                  <div>
                    <div class="text-3xl font-bold text-gray-900">10K+</div>
                    <div class="text-sm text-gray-600 mt-1">Pelajar Aktif</div>
                  </div>
                  <div>
                    <div class="text-3xl font-bold text-gray-900">500+</div>
                    <div class="text-sm text-gray-600 mt-1">Materi Pembelajaran</div>
                  </div>
                  <div>
                    <div class="text-3xl font-bold text-gray-900">98%</div>
                    <div class="text-sm text-gray-600 mt-1">Tingkat Kepuasan</div>
                  </div>
                </div>
              </div>

              <!-- Right Content - Illustration -->
              <div class="relative">
                <div class="aspect-square bg-gradient-to-br from-blue-100 to-purple-100 rounded-3xl shadow-2xl overflow-hidden">
                  <img
                    src="/images/hero-illustration.svg"
                    alt="Learning Illustration"
                    class="icon w-full h-full object-cover"
                    onerror="this.style.display='none'"
                  />
                  
                  <!-- Fallback illustration -->
                  <div class="absolute inset-0 flex items-center justify-center">
                    <svg class="w-2/3 h-2/3 text-blue-500/20" fill="currentColor" viewBox="0 0 24 24">
                      <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5" />
                    </svg>
                  </div>

                  <!-- Floating elements -->
                  <div class="absolute top-10 right-10 bg-white rounded-2xl shadow-lg p-4 card animate-float">
                    <div class="flex items-center space-x-3">
                      <div class="w-12 h-12 bg-blue-500 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                      </div>
                      <div>
                        <div class="text-sm font-semibold text-gray-900">Materi Selesai</div>
                        <div class="text-2xl font-bold text-blue-600">85%</div>
                      </div>
                    </div>
                  </div>

                  <div class="absolute bottom-10 left-10 bg-white rounded-2xl shadow-lg p-4 card animate-float-delayed">
                    <div class="flex items-center space-x-2">
                      <span class="text-2xl">🎯</span>
                      <div>
                        <div class="text-sm text-gray-600">Gaya Belajar</div>
                        <div class="text-lg font-bold text-purple-600 capitalize">
                          {{ displayStyle || 'Deteksi Otomatis' }}
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </AdaptiveContainer>
      </section>

      <!-- Feature Section Placeholder -->
      <section id="fitur" class="py-20 bg-white">
        <AdaptiveContainer
          :learning-style="displayStyle"
          tracking-id="features-section"
        >
          <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
              <h2 class="section-title text-4xl font-bold text-gray-900 mb-4">
                Fitur yang Menyesuaikan dengan Anda
              </h2>
              <p class="text-content text-xl text-gray-600 max-w-3xl mx-auto">
                Platform kami secara otomatis menyesuaikan konten berdasarkan gaya belajar Anda
              </p>
            </div>

            <div class="grid md:grid-cols-3 gap-8">
              <!-- Feature cards will be added in next step -->
              <div class="card bg-white rounded-2xl p-8 border-2">
                <div class="icon w-16 h-16 bg-gradient-to-br from-blue-500 to-purple-500 rounded-xl flex items-center justify-center mb-6">
                  <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                  </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-3">Konten Visual</h3>
                <p class="text-gray-600">Diagram, infografis, dan visualisasi interaktif untuk pelajar visual</p>
              </div>

              <div class="card bg-white rounded-2xl p-8 border-2">
                <div class="icon w-16 h-16 bg-gradient-to-br from-purple-500 to-pink-500 rounded-xl flex items-center justify-center mb-6">
                  <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.536 8.464a5 5 0 010 7.072m2.828-9.9a9 9 0 010 12.728M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z" />
                  </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-3">Narasi Audio</h3>
                <p class="text-gray-600">Penjelasan audio dan diskusi untuk pelajar auditori</p>
              </div>

              <div class="card bg-white rounded-2xl p-8 border-2">
                <div class="icon w-16 h-16 bg-gradient-to-br from-green-500 to-teal-500 rounded-xl flex items-center justify-center mb-6">
                  <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11.5V14m0-2.5v-6a1.5 1.5 0 113 0m-3 6a1.5 1.5 0 00-3 0v2a7.5 7.5 0 0015 0v-5a1.5 1.5 0 00-3 0m-6-3V11m0-5.5v-1a1.5 1.5 0 013 0v1m0 0V11m0-5.5a1.5 1.5 0 013 0v3m0 0V11" />
                  </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-3">Praktek Interaktif</h3>
                <p class="text-gray-600">Simulasi dan latihan hands-on untuk pelajar kinestetik</p>
              </div>
            </div>
          </div>
        </AdaptiveContainer>
      </section>

      <!-- CTA Section -->
      <section class="py-20 bg-gradient-to-br from-blue-600 to-purple-600 text-white">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
          <h2 class="text-4xl font-bold mb-6">
            Siap Memulai Perjalanan Belajar Anda?
          </h2>
          <p class="text-xl text-blue-100 mb-8">
            Bergabunglah dengan ribuan pelajar yang sudah merasakan pembelajaran yang dipersonalisasi
          </p>
          <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a
              href="/register"
              class="interactive-btn px-8 py-4 bg-white text-blue-600 text-lg font-semibold rounded-xl hover:shadow-2xl transition-all transform hover:-translate-y-1"
            >
              Daftar Sekarang - Gratis!
            </a>
            <button
              v-if="!displayStyle"
              @click="showQuiz = true"
              class="px-8 py-4 border-2 border-white text-white text-lg font-semibold rounded-xl hover:bg-white hover:text-blue-600 transition"
            >
              Coba Kuis Gaya Belajar
            </button>
          </div>
        </div>
      </section>

      <!-- Footer -->
      <footer class="bg-gray-900 text-gray-300 py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div class="grid md:grid-cols-4 gap-8">
            <div>
              <div class="flex items-center space-x-2 mb-4">
                <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-purple-500 rounded-lg flex items-center justify-center">
                  <span class="text-white font-bold text-xl">L</span>
                </div>
                <span class="text-xl font-bold text-white">Learning X</span>
              </div>
              <p class="text-gray-400 text-sm">
                Platform pembelajaran adaptif yang menyesuaikan dengan gaya belajar Anda.
              </p>
            </div>

            <div>
              <h4 class="font-semibold text-white mb-4">Platform</h4>
              <ul class="space-y-2 text-sm">
                <li><a href="#" class="hover:text-white transition">Fitur</a></li>
                <li><a href="#" class="hover:text-white transition">Harga</a></li>
                <li><a href="#" class="hover:text-white transition">Tentang Kami</a></li>
              </ul>
            </div>

            <div>
              <h4 class="font-semibold text-white mb-4">Bantuan</h4>
              <ul class="space-y-2 text-sm">
                <li><a href="#" class="hover:text-white transition">FAQ</a></li>
                <li><a href="#" class="hover:text-white transition">Kontak</a></li>
                <li><a href="#" class="hover:text-white transition">Panduan</a></li>
              </ul>
            </div>

            <div>
              <h4 class="font-semibold text-white mb-4">Ikuti Kami</h4>
              <div class="flex space-x-4">
                <a href="#" class="hover:text-white transition">
                  <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                </a>
                <a href="#" class="hover:text-white transition">
                  <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/></svg>
                </a>
              </div>
            </div>
          </div>

          <div class="border-t border-gray-800 mt-8 pt-8 text-sm text-center text-gray-400">
            <p>&copy; 2025 Learning X. All rights reserved.</p>
          </div>
        </div>
      </footer>
    </template>

    <!-- Learning Style Quiz Modal -->
    <LearningStyleQuiz
      v-if="showQuiz"
      @complete="handleQuizComplete"
      @close="showQuiz = false"
    />
  </div>
</template>

<style scoped>
@keyframes float {
  0%, 100% {
    transform: translateY(0);
  }
  50% {
    transform: translateY(-20px);
  }
}

@keyframes float-delayed {
  0%, 100% {
    transform: translateY(0);
  }
  50% {
    transform: translateY(-15px);
  }
}

.animate-float {
  animation: float 3s ease-in-out infinite;
}

.animate-float-delayed {
  animation: float-delayed 3s ease-in-out infinite;
  animation-delay: 1s;
}
</style>
