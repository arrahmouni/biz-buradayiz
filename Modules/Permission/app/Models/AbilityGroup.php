<?php

namespace Modules\Permission\Models;

use Modules\Base\Trait\ModelHelper;
use Astrotomic\Translatable\Translatable;
use Modules\Base\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\HasMany;
class AbilityGroup extends BaseModel
{
    use Translatable, ModelHelper;

    // Start Properties

    const VIEW_PATH = 'permissions';

    protected $fillable = [
        'code',
        'icon',
    ];

    public $translatedAttributes = ['title', 'description'];

    protected $with = [
        'translations'
    ];

    // End Properties

    // Start Relationships

    public function abilities(): HasMany
    {
        return $this->hasMany(Ability::class);
    }

    // End Relationships

    // Start Scopes

    public function scopeSimpleSearch($query, $search)
    {
        return $query->where(function($query) use($search) {
            $query->whereAny(
                ['id', 'code'],
                'LIKE',
                '%' . $search . '%'
            )->orWhereTranslationLike('title', '%' . $search . '%');
        });
    }

    public function scopeWithAbilities($query)
    {
        return $query->with('abilities');
    }

    // End Scopes
}
