import { ref } from 'vue';

export function useAudioNarration() {
  const isPlaying = ref(false);
  const isPaused = ref(false);
  const isSupported = ref('speechSynthesis' in window);
  let utterance: SpeechSynthesisUtterance | null = null;

  // Check browser support
  if (!isSupported.value) {
    console.warn('Speech Synthesis API is not supported in this browser.');
    return {
      isPlaying,
      isPaused,
      isSupported,
      speak: () => {},
      pause: () => {},
      resume: () => {},
      stop: () => {},
      toggle: () => {},
    };
  }

  const speak = (
    text: string,
    options: {
      rate?: number;
      pitch?: number;
      volume?: number;
      lang?: string;
    } = {}
  ) => {
    // Stop any ongoing speech
    if (window.speechSynthesis.speaking) {
      window.speechSynthesis.cancel();
    }

    utterance = new SpeechSynthesisUtterance(text);
    
    // Set options
    utterance.rate = options.rate || 1;
    utterance.pitch = options.pitch || 1;
    utterance.volume = options.volume || 1;
    utterance.lang = options.lang || 'id-ID'; // Indonesian by default

    // Event listeners
    utterance.onstart = () => {
      isPlaying.value = true;
      isPaused.value = false;
    };

    utterance.onend = () => {
      isPlaying.value = false;
      isPaused.value = false;
    };

    utterance.onerror = (event) => {
      console.error('Speech synthesis error:', event);
      isPlaying.value = false;
      isPaused.value = false;
    };

    window.speechSynthesis.speak(utterance);
  };

  const pause = () => {
    if (window.speechSynthesis.speaking && !window.speechSynthesis.paused) {
      window.speechSynthesis.pause();
      isPaused.value = true;
      isPlaying.value = false;
    }
  };

  const resume = () => {
    if (window.speechSynthesis.paused) {
      window.speechSynthesis.resume();
      isPaused.value = false;
      isPlaying.value = true;
    }
  };

  const stop = () => {
    if (window.speechSynthesis.speaking) {
      window.speechSynthesis.cancel();
      isPlaying.value = false;
      isPaused.value = false;
    }
  };

  const toggle = () => {
    if (isPlaying.value) {
      pause();
    } else if (isPaused.value) {
      resume();
    }
  };

  return {
    isPlaying,
    isPaused,
    isSupported,
    speak,
    pause,
    resume,
    stop,
    toggle,
  };
}
