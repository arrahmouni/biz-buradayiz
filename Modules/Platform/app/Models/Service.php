<?php

namespace Modules\Platform\Models;

use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Auth\Enums\UserType;
use Modules\Auth\Models\User;
use Modules\Base\Models\BaseModel;
use Modules\Base\Trait\Disableable;

class Service extends BaseModel
{
    use Disableable, HasFactory, SoftDeletes, Translatable;

    // Start Properties

    const VIEW_PATH = 'services';

    protected $fillable = [
        'show_in_search_filters',
        'icon',
    ];

    public $timestamps = true;

    protected $casts = [
        'show_in_search_filters' => 'boolean',
    ];

    public $translatedAttributes = ['name', 'description'];

    protected $with = [
        'translations',
    ];

    // End Properties

    // Start Relationships

    public function packages(): BelongsToMany
    {
        return $this->belongsToMany(Package::class, 'package_service')
            ->withTimestamps();
    }

    /**
     * Users of type service provider assigned to this service (users.service_id).
     */
    public function serviceProviders(): HasMany
    {
        return $this->hasMany(User::class, 'service_id')
            ->where('type', UserType::ServiceProvider->value);
    }

    // End Relationships

    // Start Scopes
    public function scopeSimpleSearch($query, $search)
    {
        return
        $query->where(function ($query) use ($search) {
            $query->where('id', $search)
                ->orWhereTranslationLike('name', '%'.$search.'%');
        });
    }

    public function scopeAdvancedSearch($query, $search)
    {
        return $query;
    }

    /**
     * Limit to services offered as options in public website search filters.
     */
    public function scopeForSearchFilters($query)
    {
        return $query->where('show_in_search_filters', true);
    }
    // End Scopes

    // Start Get Data From Model

    public function formAjaxArray($selected = true)
    {
        return [
            'id' => $this->id,
            'text' => $this->smartTrans('name') ?? (string) $this->id,
            'selected' => $selected,
        ];
    }

    // End Get Data From Model

    // Start Mutators & Accessors

    // End Mutators & Accessors
}
