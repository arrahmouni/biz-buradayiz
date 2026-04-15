<?php

namespace Modules\Seo\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SeoPublicResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        $m = $this->resource;

        return [
            'meta_title' => $m->smartTrans('meta_title'),
            'meta_description' => $m->smartTrans('meta_description'),
            'meta_keywords' => $m->smartTrans('meta_keywords'),
            'og_title' => $m->smartTrans('og_title'),
            'og_description' => $m->smartTrans('og_description'),
            'og_image' => $m->smartTrans('og_image'),
            'robots' => $m->smartTrans('robots'),
            'canonical_url' => $m->smartTrans('canonical_url'),
        ];
    }
}
