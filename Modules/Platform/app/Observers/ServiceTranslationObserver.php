<?php

namespace Modules\Platform\Observers;

use Modules\Front\Support\FrontPublicServices;
use Modules\Platform\Models\ServiceTranslation;

class ServiceTranslationObserver
{
    public function saved(ServiceTranslation $serviceTranslation): void
    {
        $this->flushFrontPublicCaches();
    }

    public function deleted(ServiceTranslation $serviceTranslation): void
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
