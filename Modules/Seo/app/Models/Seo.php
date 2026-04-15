<?php

namespace Modules\Seo\Models;

use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Base\Models\BaseModel;
use Modules\Cms\Models\Content;

class Seo extends BaseModel
{
    use SoftDeletes, Translatable;

    const VIEW_PATH = 'seo_entries';

    protected $table = 'seo_entries';

    public $translationForeignKey = 'seo_entry_id';

    protected $fillable = [];

    public $translatedAttributes = [
        'meta_title',
        'meta_description',
        'meta_keywords',
        'og_title',
        'og_description',
        'og_image',
        'robots',
        'canonical_url',
    ];

    protected $with = [
        'translations',
    ];

    public function scopeSimpleSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('id', $search)
                ->orWhereTranslationLike('meta_title', '%'.$search.'%')
                ->orWhereTranslationLike('meta_description', '%'.$search.'%');
        });
    }

    public function model()
    {
        return $this->morphTo();
    }

    public function adminTargetLabel(): string
    {
        $subject = $this->model;

        if ($subject instanceof SeoStaticPage) {
            return $subject->adminLabel();
        }

        if ($subject instanceof Content) {
            return $subject->smartTrans('title').' ('.$subject->type.')';
        }

        return class_basename($this->model_type).' #'.$this->model_id;
    }
}
