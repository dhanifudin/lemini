import { ref, onMounted } from 'vue';
import axios from 'axios';

export interface QuizQuestion {
  id: number;
  question: string;
  options: Array<{
    value: 'visual' | 'auditory' | 'kinesthetic';
    text: string;
    icon: string;
  }>;
}

export interface LearningStyleResult {
  primary: string;
  confidence: number;
  breakdown: Array<{
    style: string;
    score: number;
    percentage: number;
  }>;
}

export function useLearningStyle() {
  const currentStyle = ref<string>('visual');
  const isDetected = ref(false);

  const quizQuestions: QuizQuestion[] = [
    {
      id: 1,
      question: 'Bagaimana Anda lebih suka mempelajari konsep baru?',
      options: [
        { value: 'visual', text: 'Melihat diagram dan ilustrasi', icon: 'eye' },
        { value: 'auditory', text: 'Mendengar penjelasan', icon: 'volume-2' },
        { value: 'kinesthetic', text: 'Mencoba langsung', icon: 'hand' }
      ]
    },
    {
      id: 2,
      question: 'Saat mengingat informasi, Anda lebih mudah dengan:',
      options: [
        { value: 'visual', text: 'Membayangkan gambar atau grafik', icon: 'image' },
        { value: 'auditory', text: 'Mengulang dalam pikiran', icon: 'headphones' },
        { value: 'kinesthetic', text: 'Mengingat gerakan atau praktik', icon: 'hand' }
      ]
    },
    {
      id: 3,
      question: 'Ketika belajar hal baru, Anda lebih senang:',
      options: [
        { value: 'visual', text: 'Menonton video atau melihat demonstrasi', icon: 'monitor' },
        { value: 'auditory', text: 'Mendengarkan penjelasan atau diskusi', icon: 'mic' },
        { value: 'kinesthetic', text: 'Langsung praktek dan eksperimen', icon: 'zap' }
      ]
    },
    {
      id: 4,
      question: 'Cara terbaik untuk mengingat nomor telepon bagi Anda:',
      options: [
        { value: 'visual', text: 'Membayangkan angkanya tertulis', icon: 'type' },
        { value: 'auditory', text: 'Mengucapkannya berulang kali', icon: 'message-circle' },
        { value: 'kinesthetic', text: 'Mengetiknya beberapa kali', icon: 'edit-3' }
      ]
    },
    {
      id: 5,
      question: 'Saat memberikan petunjuk arah, Anda cenderung:',
      options: [
        { value: 'visual', text: 'Menggambar peta atau sketsa', icon: 'map' },
        { value: 'auditory', text: 'Menjelaskan dengan kata-kata', icon: 'message-square' },
        { value: 'kinesthetic', text: 'Menggunakan gerakan dan menunjuk', icon: 'navigation' }
      ]
    }
  ];

  const calculateLearningStyle = (answers: string[]): LearningStyleResult => {
    const counts = answers.reduce((acc, answer) => {
      acc[answer] = (acc[answer] || 0) + 1;
      return acc;
    }, {} as Record<string, number>);

    const total = answers.length;
    const styles = Object.entries(counts)
      .map(([style, count]) => ({
        style,
        score: count,
        percentage: (count / total) * 100
      }))
      .sort((a, b) => b.score - a.score);

    return {
      primary: styles[0].style,
      confidence: styles[0].percentage,
      breakdown: styles
    };
  };

  const saveLearningStyle = async (
    style: string,
    method: 'quiz' | 'explicit' | 'behavioral' | 'default',
    quizResults?: any,
    confidenceScore?: number
  ) => {
    try {
      await axios.post('/api/landing/learning-style/save', {
        learning_style: style,
        detection_method: method,
        quiz_results: quizResults,
        confidence_score: confidenceScore,
      });

      // Save to localStorage for persistence
      localStorage.setItem('learning_style', style);
      localStorage.setItem('learning_style_detected_at', Date.now().toString());
      
      currentStyle.value = style;
      isDetected.value = true;
    } catch (error) {
      console.error('Failed to save learning style:', error);
    }
  };

  const loadLearningStyle = async () => {
    // Try localStorage first
    const stored = localStorage.getItem('learning_style');
    if (stored) {
      currentStyle.value = stored;
      isDetected.value = true;
      return;
    }

    // Try to load from server
    try {
      const response = await axios.get('/api/landing/learning-style');
      if (response.data.preference) {
        currentStyle.value = response.data.preference.learning_style;
        isDetected.value = true;
        localStorage.setItem('learning_style', response.data.preference.learning_style);
      }
    } catch (error) {
      console.error('Failed to load learning style:', error);
    }
  };

  onMounted(() => {
    loadLearningStyle();
  });

  return {
    currentStyle,
    isDetected,
    quizQuestions,
    calculateLearningStyle,
    saveLearningStyle,
    loadLearningStyle,
  };
}
