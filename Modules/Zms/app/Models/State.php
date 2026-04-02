<?php

namespace Modules\Zms\Models;

use Modules\Base\Trait\ModelHelper;
use Astrotomic\Translatable\Translatable;
use Modules\Base\Models\BaseModel;
use Modules\Zms\Trait\CountryTraitHelper;


class State extends BaseModel
{
    use Translatable, ModelHelper, CountryTraitHelper;

    // Start Properties

    const VIEW_PATH  = 'states';

    protected $fillable = [
        'country_id',
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

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function cities()
    {
        return $this->hasMany(City::class);
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

    // Start Mutators & Accessors

    // End Mutators & Accessors
}
