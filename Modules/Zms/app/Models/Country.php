<?php

namespace Modules\Zms\Models;

use Modules\Base\Models\BaseModel;
use Modules\Base\Trait\Disableable;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Zms\Trait\CountryTraitHelper;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;

class Country extends BaseModel implements Auditable
{
    use Translatable, SoftDeletes, CountryTraitHelper, Disableable, AuditableTrait;

    // Start Properties

    const VIEW_PATH  = 'countries';

    protected $fillable = [
        'native_name',
        'iso3',
        'iso2',
        'phone_code',
        'currency',
        'currency_symbol',
        'lat',
        'lng',
    ];

    public $timestamps = false;

    public $translatedAttributes = [
        'name',
    ];

    protected $with = [
        'translations',
        'states.cities'
    ];

    // End Properties

    // Start Relationships

    public function states()
    {
        return $this->hasMany(State::class);
    }

    // End Relationships

    // Start Scopes
    public function scopeSimpleSearch($query, $search)
    {
        return $query->where(function($query) use($search) {
            $query->whereAny(
                ['id', 'native_name', 'iso3', 'phone_code', 'currency'],
                'LIKE',
                '%' . $search . '%'
            )->orWhereTranslationLike('name', '%' . $search . '%');
        });
    }
    // End Scopes

    public function formAjaxArray($selected = true): array
    {
        return [
            'id'       => $this->id,
            'text'     => $this->smartTrans('name') ?? $this->native_name,
            'selected' => $selected,
        ];
    }

    // End Get Data From Model

    // Start Mutators & Accessors


    // End Mutators & Accessors
}
