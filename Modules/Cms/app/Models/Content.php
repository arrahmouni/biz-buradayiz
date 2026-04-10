<?php

namespace Modules\Cms\Models;

use Astrotomic\Translatable\Translatable;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Arr;
use Modules\Base\Models\BaseModel;
use Modules\Base\Trait\Disableable;
use Modules\Cms\Database\Factories\ContentFactory;
use Modules\Cms\Traits\ContentTrait;
use Modules\Seo\Models\Seo;
use OwenIt\Auditing\Auditable as AuditableTrait;
use OwenIt\Auditing\Contracts\Auditable;

class Content extends BaseModel implements Auditable
{
    use AuditableTrait, ContentTrait, Disableable, HasFactory, SoftDeletes, Translatable;

    // Start Properties

    const VIEW_PATH = 'contents';

    protected $fillable = [
        'type',
        'sub_type',
        'slug',
        'link',
        'custom_properties',
        'can_be_deleted',
        'published_at',
        'placement',
        'value',
    ];

    public $timestamps = true;

    public $translatedAttributes = [
        'title',
        'slug',
        'short_description',
        'long_description',
    ];

    protected $with = [
        'translations',
    ];

    protected $appends = [
        'can_be_deleted_format',
        'placement_position',
        'created_at_format',
        'published_at_format',
    ];

    /**
     * Attributes to exclude from the Audit.
     *
     * @var array
     */
    protected $auditExclude = [];

    public const MEDIA_COLLECTION = 'content';

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'custom_properties' => 'array',
        ];
    }

    /**
     * Create a new factory instance for the model.
     *
     * @return Factory
     */
    protected static function newFactory()
    {
        return ContentFactory::new();
    }

    /**
     * Transform the model for auditing.
     */
    public function transformAudit(array $data): array
    {
        if (Arr::has($data, 'new_values.can_be_deleted')) {
            $data['old_values']['can_be_deleted'] = (bool) $data['old_values']['can_be_deleted'];
            $data['new_values']['can_be_deleted'] = (bool) $data['new_values']['can_be_deleted'];
        }

        return $data;
    }

    // End Properties

    // Start Relationships
    public function mainCategoriesParent()
    {
        return $this->belongsToMany(ContentCategory::class)->wherePivot('relation_type', 'main_category');
    }

    public function subCategoriesParent()
    {
        return $this->belongsToMany(ContentCategory::class)->wherePivot('relation_type', 'subcategory');
    }

    public function tags()
    {
        return $this->belongsToMany(ContentTag::class, 'tag_content');
    }

    public function seo()
    {
        return $this->morphOne(Seo::class, 'model');
    }

    // End Relationships

    // Start Scopes
    public function scopeSimpleSearch($query, $search)
    {
        return $query->byType(e(request()->type) ?? null)
            ->where(function ($query) use ($search) {
                $query->where('id', $search)
                    ->orWhereTranslationLike('title', '%'.$search.'%');
            });
    }

    public function scopeAdvancedSearch($query, $search, $type)
    {
        return $query->byType($type);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }
    // End Scopes

    // Start Get Data From Model

    public function formAjaxArray($selected = true)
    {
        return [
            'id' => $this->id,
            'text' => $this->smartTrans('title'),
            'selected' => $selected,
        ];
    }

    public static function findBySlug($slug, $locale = null)
    {
        $locale ??= config('cms.slug_default_locale');

        return static::whereTranslationLike('slug', '%'.$slug.'%')->first();
    }

    /**
     * Public URL segment for this page (translation slug, or legacy sub_type).
     */
    public function publicPageSlug(?string $locale = null): ?string
    {
        $locale ??= app()->getLocale();
        $slug = $this->translate($locale)?->slug;
        if (is_string($slug) && !empty($slug)) {
            return $slug;
        }
        if (is_string($this->sub_type) && !empty($this->sub_type)) {
            return $this->sub_type;
        }

        return null;
    }

    // End Get Data From Model

    // Start Mutators & Accessors

    protected function canBeDeletedFormat(): Attribute
    {
        return Attribute::make(
            get: fn ($value, $attribute) => trans('base::base.yes_no_boolean.'.$attribute['can_be_deleted']),
        );
    }

    protected function placementPosition(): Attribute
    {
        return Attribute::make(
            get: function ($value, $attribute) {
                $key = null;

                if ($this->typeHasField($this->type, 'placement')) {
                    $key = $this->custom_properties['placement'] ?? null;
                }

                return $key ? trans('cms::contents.sliders.placement.'.$key) : '--';
            }
        );
    }

    protected function appearInFooter(): Attribute
    {
        return Attribute::make(
            get: fn () => filter_var(
                Arr::get($this->custom_properties, 'appear_in_footer', false),
                FILTER_VALIDATE_BOOL
            ),
        );
    }

    protected function createdAtFormat(): Attribute
    {
        return Attribute::make(
            get: function ($value, $attribute) {
                return Carbon::parse($this->created_at)->locale(app()->getLocale())->diffForHumans();
            }
        );
    }

    protected function publishedAtFormat(): Attribute
    {
        return Attribute::make(
            get: function ($value, $attribute) {
                return Carbon::parse($this->published_at)->format('Y-m-d H:i:s');
            }
        );
    }
    // End Mutators & Accessors
}
