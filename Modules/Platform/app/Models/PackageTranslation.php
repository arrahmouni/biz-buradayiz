<?php

namespace Modules\Platform\Models;

use Modules\Base\Models\BaseModel;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class PackageTranslation extends BaseModel
{
    use HasSlug;

    protected $fillable = [
        'package_id',
        'locale',
        'name',
        'slug',
        'description',
        'features',
    ];

    public $timestamps = false;

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug')
            ->startSlugSuffixFrom(2);
    }
}
