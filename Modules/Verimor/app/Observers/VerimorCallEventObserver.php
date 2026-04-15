<?php

namespace Modules\Verimor\Observers;

use Modules\Platform\Jobs\RecalculateProviderRankingsJob;
use Modules\Verimor\Models\VerimorCallEvent;

class VerimorCallEventObserver
{
    public function created(VerimorCallEvent $event): void
    {
        RecalculateProviderRankingsJob::dispatch();
    }
}
