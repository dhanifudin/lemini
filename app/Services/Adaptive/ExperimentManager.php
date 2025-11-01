<?php

namespace App\Services\Adaptive;

use App\Models\AdaptiveExperimentAssignment;
use App\Models\User;
use Illuminate\Support\Arr;

class ExperimentManager
{
    /**
     * Retrieve or create an experiment assignment for the user.
     *
     * @param  array<int, string>  $variants
     */
    public function assign(User $user, string $experimentKey, array $variants = ['control', 'variant_a']): AdaptiveExperimentAssignment
    {
        $assignment = AdaptiveExperimentAssignment::firstOrNew([
            'user_id' => $user->id,
            'experiment_key' => $experimentKey,
        ]);

        if ($assignment->exists) {
            return $assignment;
        }

        $variant = $this->deterministicVariant($user, $experimentKey, $variants);

        $assignment->fill([
            'variant' => $variant,
            'assigned_at' => now(),
        ])->save();

        return $assignment;
    }

    protected function deterministicVariant(User $user, string $experimentKey, array $variants): string
    {
        $hash = crc32($user->id.'|'.$experimentKey);
        $index = $hash % max(1, count($variants));

        return Arr::get(array_values($variants), $index, $variants[0]);
    }
}
