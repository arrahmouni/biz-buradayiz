<?php

namespace Modules\Zms\Models;

use Modules\Base\Trait\ModelHelper;
use Astrotomic\Translatable\Translatable;
use Modules\Base\Models\BaseModel;
use Modules\Zms\Trait\CountryTraitHelper;
use Modules\Zms\Enums\permissions\CountryPermissions;

class City extends BaseModel
{
    use Translatable, ModelHelper, CountryTraitHelper;

    // Start Properties

    const VIEW_PATH = 'cities';

    protected $fillable = [
        'state_id',
        'native_name',
        'lat',
        'lng',
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

    // End Get Data From Model
}
