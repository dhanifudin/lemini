<?php

namespace App\Observers;

use App\Models\Attempt;
use App\Services\Adaptive\MasteryScoringService;

class AttemptObserver
{
    public function __construct(
        protected MasteryScoringService $scoring,
    ) {
    }

    public function saved(Attempt $attempt): void
    {
        $this->scoring->updateForAttempt($attempt);
    }
}
