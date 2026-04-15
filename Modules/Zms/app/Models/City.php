<?php

namespace Modules\Zms\Models;

use Modules\Base\Trait\ModelHelper;
use Astrotomic\Translatable\Translatable;
use Modules\Base\Models\BaseModel;
use Modules\Base\Trait\Disableable;
use Modules\Zms\Trait\CountryTraitHelper;
use Modules\Zms\Enums\permissions\CountryPermissions;

class City extends BaseModel
{
    use Translatable, ModelHelper, CountryTraitHelper, Disableable;

    // Start Properties

    const VIEW_PATH = 'cities';

    protected $fillable = [
        'state_id',
        'native_name',
        'lat',
        'lng',
        'disabled_at',
    ];

    public $timestamps = false;

    public $translatedAttributes = ['name'];

    protected $with = [
        'translations'
    ];

    // End Properties

    // Start Relationships

    public function state()
    {
        return $this->belongsTo(State::class);
    }

    // End Relationships

    // Start Scopes
    public function scopeSimpleSearch($query, $search)
    {
        return $query->where(function($query) use($search) {
            $query->whereAny(
                ['id', 'native_name'],
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
}
