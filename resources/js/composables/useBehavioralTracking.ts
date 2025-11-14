import { reactive, computed } from 'vue';
import axios from 'axios';

interface BehaviorScores {
  imageHoverCount: number;
  videoPlayCount: number;
  audioPlayCount: number;
  interactionCount: number;
  buttonClickCount: number;
  scrollSpeed: number;
  timeOnVisualContent: number;
  timeOnTextContent: number;
}

export function useBehavioralTracking() {
  const behaviors = reactive<BehaviorScores>({
    imageHoverCount: 0,
    videoPlayCount: 0,
    audioPlayCount: 0,
    interactionCount: 0,
    buttonClickCount: 0,
    scrollSpeed: 0,
    timeOnVisualContent: 0,
    timeOnTextContent: 0,
  });

  const trackImageHover = () => {
    behaviors.imageHoverCount++;
    sendBehaviorEvent('image_hover', { count: behaviors.imageHoverCount });
  };

  const trackVideoPlay = () => {
    behaviors.videoPlayCount++;
    sendBehaviorEvent('video_play', { count: behaviors.videoPlayCount });
  };

  const trackAudioPlay = () => {
    behaviors.audioPlayCount++;
    sendBehaviorEvent('audio_play', { count: behaviors.audioPlayCount });
  };

  const trackInteraction = (type: string, data?: any) => {
    behaviors.interactionCount++;
    sendBehaviorEvent('interaction', { type, ...data });
  };

  const trackButtonClick = (buttonId: string) => {
    behaviors.buttonClickCount++;
    sendBehaviorEvent('button_click', { buttonId, count: behaviors.buttonClickCount });
  };

  const analyzeBehavior = computed(() => {
    const scores = {
      visual: behaviors.imageHoverCount * 2 + behaviors.videoPlayCount * 3,
      auditory: behaviors.videoPlayCount * 2 + behaviors.audioPlayCount * 5,
      kinesthetic: behaviors.interactionCount * 3 + behaviors.buttonClickCount * 1,
    };

    const total = Object.values(scores).reduce((a, b) => a + b, 0);
    
    if (total < 5) return null; // Not enough data

    const entries = Object.entries(scores)
      .map(([style, score]) => ({ 
        style, 
        confidence: total > 0 ? (score / total) * 100 : 0 
      }))
      .sort((a, b) => b.confidence - a.confidence);

    const predicted = entries[0];
    
    return predicted.confidence > 40 ? predicted : null;
  });

  const sendBehaviorEvent = async (eventType: string, data: any) => {
    try {
      await axios.post('/api/landing/track-behavior', {
        event_type: eventType,
        event_data: {
          ...data,
          timestamp: new Date().toISOString(),
        },
      });
    } catch (error) {
      console.error('Failed to track behavior:', error);
    }
  };

  const getAnalysisFromServer = async () => {
    try {
      const response = await axios.get('/api/landing/analyze-behavior');
      return response.data;
    } catch (error) {
      console.error('Failed to get behavior analysis:', error);
      return null;
    }
  };

  return {
    behaviors,
    trackImageHover,
    trackVideoPlay,
    trackAudioPlay,
    trackInteraction,
    trackButtonClick,
    analyzeBehavior,
    getAnalysisFromServer,
  };
}
