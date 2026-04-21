<?php

namespace Modules\Platform\Observers;

use Modules\Front\Support\FrontPublicServices;
use Modules\Platform\Models\Service;

class ServiceObserver
{
    public function saved(Service $service): void
    {
        $this->flushFrontPublicCaches();
    }

    public function deleted(Service $service): void
    {
        $this->flushFrontPublicCaches();
    }

    public function restored(Service $service): void
    {
        $this->flushFrontPublicCaches();
    }

    private function flushFrontPublicCaches(): void
    {
        if (class_exists(FrontPublicServices::class)) {
            FrontPublicServices::flush();
        }
    }
}
