<?php

namespace Modules\Platform\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Modules\Admin\Enums\AdminStatus;
use Modules\Auth\Enums\UserType;
use Modules\Config\Constatnt;

class RecalculateProviderRankingsJob implements ShouldBeUnique, ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $uniqueFor = 60;

    public function handle(): void
    {
        $wRating = max(0, (int) getSetting(Constatnt::RANKING_WEIGHT_RATING, 50));
        $wActivity = max(0, (int) getSetting(Constatnt::RANKING_WEIGHT_ACTIVITY, 30));
        $wExperience = max(0, (int) getSetting(Constatnt::RANKING_WEIGHT_EXPERIENCE, 20));

        $totalWeight = $wRating + $wActivity + $wExperience;
        if ($totalWeight === 0) {
            $totalWeight = 100;
            $wRating = 50;
            $wActivity = 30;
            $wExperience = 20;
        }

        $nRating = $wRating / $totalWeight;
        $nActivity = $wActivity / $totalWeight;
        $nExperience = $wExperience / $totalWeight;

        $providers = DB::table('users')
            ->where('type', UserType::ServiceProvider->value)
            ->where('status', AdminStatus::ACTIVE)
            ->select([
                'users.id',
                'users.review_rating_average',
                'users.approved_at',
                DB::raw('(SELECT COUNT(*) FROM verimor_call_events WHERE verimor_call_events.user_id = users.id) as call_count'),
                DB::raw('COALESCE(DATEDIFF(NOW(), users.approved_at), 0) as days_registered'),
            ])
            ->get();

        if ($providers->isEmpty()) {
            return;
        }

        $ratings = $providers->pluck('review_rating_average')->map(fn ($v) => (float) $v);
        $calls = $providers->pluck('call_count')->map(fn ($v) => (int) $v);
        $days = $providers->pluck('days_registered')->map(fn ($v) => (int) $v);

        $ratingMin = $ratings->min();
        $ratingMax = $ratings->max();
        $ratingRange = $ratingMax - $ratingMin;

        $callMin = $calls->min();
        $callMax = $calls->max();
        $callRange = $callMax - $callMin;

        $dayMin = $days->min();
        $dayMax = $days->max();
        $dayRange = $dayMax - $dayMin;

        $updates = [];

        foreach ($providers as $provider) {
            $normRating = $ratingRange > 0
                ? ((float) $provider->review_rating_average - $ratingMin) / $ratingRange
                : 0;

            $normActivity = $callRange > 0
                ? ((int) $provider->call_count - $callMin) / $callRange
                : 0;

            $normExperience = $dayRange > 0
                ? ((int) $provider->days_registered - $dayMin) / $dayRange
                : 0;

            $score = round(
                ($nRating * $normRating) + ($nActivity * $normActivity) + ($nExperience * $normExperience),
                4
            );

            $updates[$provider->id] = $score;
        }

        $chunks = array_chunk($updates, 500, true);

        foreach ($chunks as $chunk) {
            $cases = [];
            $ids = [];

            foreach ($chunk as $id => $score) {
                $cases[] = "WHEN {$id} THEN {$score}";
                $ids[] = $id;
            }

            $casesSql = implode(' ', $cases);
            $idsList = implode(',', $ids);

            DB::statement("UPDATE users SET ranking_score = CASE id {$casesSql} ELSE ranking_score END WHERE id IN ({$idsList})");
        }

        DB::table('users')
            ->where('type', UserType::ServiceProvider->value)
            ->where('status', '!=', AdminStatus::ACTIVE)
            ->where('ranking_score', '!=', 0)
            ->update(['ranking_score' => 0]);
    }
}
