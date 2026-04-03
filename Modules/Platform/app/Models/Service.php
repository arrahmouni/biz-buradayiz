<?php

namespace Modules\Platform\Models;

use Astrotomic\Translatable\Translatable;
use Modules\Base\Models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Base\Trait\Disableable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Service extends BaseModel
{
    use Translatable, SoftDeletes, Disableable, HasFactory;

    // Start Properties

    const VIEW_PATH = 'services';

    protected $fillable = [
        'show_in_search_filters',
    ];

    public $timestamps = true;

    protected $casts = [
        'show_in_search_filters' => 'boolean',
    ];

    public $translatedAttributes = ['name', 'description'];

    protected $with = [
        'translations'
    ];

    // End Properties

    // Start Relationships

    // End Relationships

    // Start Scopes
    public function scopeSimpleSearch($query, $search)
    {
        return
        $query->where(function($query) use($search) {
            $query->where('id', $search)
            ->orWhereTranslationLike('name', '%' . $search . '%');
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
            'id'       => $this->id,
            'text'     => $this->smartTrans('name') ?? (string) $this->id,
            'selected' => $selected,
        ];
    }

    // End Get Data From Model

    // Start Mutators & Accessors

    // End Mutators & Accessors
}
