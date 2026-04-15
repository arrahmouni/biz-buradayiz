<?php

namespace Modules\Seo\Models;

use Modules\Base\Models\BaseModel;

class SeoTranslation extends BaseModel
{
    protected $table = 'seo_translations';

    public $timestamps = false;

    protected $fillable = [
        'seo_entry_id',
        'locale',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'og_title',
        'og_description',
        'og_image',
        'robots',
        'canonical_url',
    ];
}
