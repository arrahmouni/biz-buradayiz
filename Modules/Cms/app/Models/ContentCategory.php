<?php

namespace Modules\Cms\Models;

use Carbon\Carbon;
use Modules\Base\Models\BaseModel;
use Modules\Base\Trait\Disableable;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Cms\Enums\permissions\ContentCategoryPermissions;

class ContentCategory extends BaseModel
{
    use Translatable, SoftDeletes, Disableable, HasFactory;

    // Start Properties

    const VIEW_PATH = 'content_categories';

    protected $fillable = [
        'slug',
        'parent_id',
        'can_be_deleted',
    ];

    public $timestamps = true;

    public $translatedAttributes = [
        'title',
    ];

    protected $with = [
        'translations'
    ];

    protected $appends = [
        'can_be_deleted_format',
        'category_parents_name',
        'created_at_format',
    ];

    // End Properties

    // Start Relationships
    public function parent(): BelongsTo
    {
        return $this->belongsTo(ContentCategory::class, 'parent_id')->withDefault([
            'title' => '----'
        ]);
    }

    public function children(): HasMany
    {
        return $this->hasMany(ContentCategory::class, 'parent_id');
    }

    // End Relationships

    // Start Scopes
    public function scopeSimpleSearch($query, $search)
    {
        return
        $query->where(function($query) use($search) {
            $query->where('id', $search)
                ->orWhereTranslationLike('title', '%' . $search . '%');
        });
    }

    public function scopeAdvancedSearch($query, $search)
    {
        return $query;
    }

    public function scopeOnlyLastLevel($query)
    {
        return $query->whereDoesntHave('children');
    }

    public function scopeOnlyFirstParent($query)
    {
        return $query->whereNull('parent_id');
    }
    // End Scopes

    // Start Get Data From Model

    public function formAjaxArray($selected = true)
    {
        return [
            'id'            => $this->id,
            'text'          => $this->smartTrans('title'),
            'selected'      => $selected
        ];
    }


    // End Get Data From Model

    // Start Mutators & Accessors

    protected function canBeDeletedFormat() : Attribute
    {
        return Attribute::make(
            get: fn($value, $attribute) => trans('base::base.yes_no_boolean.' . $attribute['can_be_deleted']),
        );
    }

    protected function createdAtFormat() : Attribute
    {
        return Attribute::make(
            get: function($value, $attribute) {
                return Carbon::parse($this->created_at)->locale(app()->getLocale())->diffForHumans();
            }
        );
    }

    protected function categoryParentsName() : Attribute
    {
        $names = $this->allCategoryParents()->pluck('title')->implode(' > ');

        return Attribute::make(
            get: fn($value, $attribute) => empty($names) ? '----' : $names,
        );
    }

    public function allCategoryParents()
    {
        $parents  = [];
        $category = $this;

        while($category->parent_id) {
            $category  = $category->parent;
            $parents[] = $category;
        }

        return collect($parents)->reverse();
    }

    // End Mutators & Accessors
}
