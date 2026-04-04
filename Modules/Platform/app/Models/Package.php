<?php

namespace Modules\Platform\Models;

use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Base\Models\BaseModel;
use Modules\Base\Trait\Disableable;
use Modules\Platform\Database\Factories\PackageFactory;
use Modules\Platform\Enums\BillingPeriod;

class Package extends BaseModel
{
    use Disableable, HasFactory, SoftDeletes, Translatable;

    const VIEW_PATH = 'packages';

    protected $fillable = [
        'price',
        'currency',
        'billing_period',
        'sort_order',
        'connections_count',
        'is_free_tier',
    ];

    public $timestamps = true;

    public $translatedAttributes = ['name', 'slug', 'description', 'features'];

    protected $with = [
        'translations',
    ];

    protected $appends = [
        'created_at_format',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'billing_period' => BillingPeriod::class,
            'connections_count' => 'integer',
            'is_free_tier' => 'boolean',
        ];
    }

    protected static function newFactory()
    {
        return PackageFactory::new();
    }

    public function services(): BelongsToMany
    {
        return $this->belongsToMany(Service::class, 'package_service')
            ->withTimestamps();
    }

    public function scopeSimpleSearch($query, $search)
    {
        return $query->where(function ($query) use ($search) {
            $query->where('id', $search)
                ->orWhereTranslationLike('slug', '%'.$search.'%')
                ->orWhereTranslationLike('name', '%'.$search.'%');
        });
    }

    public function scopeAdvancedSearch($query, $search)
    {
        return $query;
    }

    public function formAjaxArray($selected = true)
    {
        return [
            'id' => $this->id,
            'text' => $this->smartTrans('name') ?? (string) $this->id,
            'selected' => $selected,
        ];
    }

    public function createdAtFormat(): Attribute
    {
        return Attribute::make(
            get: fn ($value, $attributes) => getFormattedDate($attributes['created_at'], 'd M Y'),
        );
    }
}
