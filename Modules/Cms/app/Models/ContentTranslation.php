<?php

namespace Modules\Cms\Models;

use Modules\Base\Models\BaseModel;
use Spatie\Image\Enums\CropPosition;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class ContentTranslation extends BaseModel implements HasMedia
{
    use HasSlug, InteractsWithMedia;

    protected $fillable = [
        'content_id',
        'locale',
        'title',
        'slug',
        'short_description',
        'long_description',
    ];

    public $timestamps = false;

    // Perform crop on image via media library
    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb-100')
            ->crop(100, 100, CropPosition::Center)
            ->performOnCollections(Content::MEDIA_COLLECTION);

        $this->addMediaConversion('thumb-sidebar')
            ->fit(Fit::Max, 192, 192)
            ->performOnCollections(Content::MEDIA_COLLECTION);
    }

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('title')
            ->saveSlugsTo('slug')
            ->startSlugSuffixFrom(2);

    }
}
