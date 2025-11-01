<?php

namespace App\Observers;

use App\Models\FeedbackReflection;
use App\Services\Adaptive\MasteryScoringService;

class FeedbackReflectionObserver
{
    public function __construct(
        protected MasteryScoringService $scoring,
    ) {
    }

    public function created(FeedbackReflection $reflection): void
    {
        $this->scoring->updateForReflection($reflection);
    }
}
