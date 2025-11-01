<?php

namespace App\Http\Controllers\Student\Api;

use App\Http\Controllers\Controller;
use App\Services\Adaptive\AdaptivePracticeEngine;
use Illuminate\Http\Request;

class PracticeIndexController extends Controller
{
    public function __construct(
        protected AdaptivePracticeEngine $engine,
    ) {
    }

    public function __invoke(Request $request): array
    {
        $student = $request->user();

        return [
            'queue' => $this->engine->recommendationsFor($student),
            'resources' => $this->supplementalResources(),
        ];
    }

    /**
     * @return array<int, array<string, string>>
     */
    protected function supplementalResources(): array
    {
        return [
            [
                'title' => 'Virtual Lab: Photosynthesis Light Intensity',
                'type' => 'simulation',
                'topic' => 'Photosynthesis',
                'url' => 'https://phet.colorado.edu/en/simulations/photorealistic-simulation-photosynthesis',
            ],
            [
                'title' => 'Crash Course Biology #7: DNA Structure and Replication',
                'type' => 'video',
                'topic' => 'Genetics',
                'url' => 'https://www.youtube.com/watch?v=8kK2zwjRV0M',
            ],
            [
                'title' => 'Cellular Respiration Overview',
                'type' => 'article',
                'topic' => 'Cellular Respiration',
                'url' => 'https://www.khanacademy.org/science/ap-biology/cellular-respiration-and-fermentation',
            ],
        ];
    }
}
