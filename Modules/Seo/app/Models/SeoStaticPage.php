<?php

namespace Modules\Seo\Models;

use Modules\Base\Models\BaseModel;

class SeoStaticPage extends BaseModel
{
    protected $fillable = [
        'key',
        'path_hint',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
        ];
    }

    public function seo()
    {
        return $this->morphOne(Seo::class, 'model');
    }

    public function adminLabel(): string
    {
        $labelKey = collect(config('seo.static_pages', []))
            ->firstWhere('key', $this->key)['label'] ?? null;

        if ($labelKey) {
            return trans($labelKey);
        }

        return $this->key;
    }
}
