<?php

namespace Modules\Permission\Models;

use Modules\Base\Trait\ModelHelper;
use Astrotomic\Translatable\Translatable;
use Modules\Permission\Enums\SystemDefaultRoles;
use Silber\Bouncer\Database\Role as DatabaseRole;

class Role extends DatabaseRole
{
    use Translatable, ModelHelper;

    // Start Properties

    const VIEW_PATH = 'roles';

    protected $fillable = [
        'name',
        'title',
        'scope'
    ];

    public $translatedAttributes = ['title', 'description'];

    protected $with = [
        'translations'
    ];

    // End Properties

    // Start Glopal Scope
    public static function boot()
    {
        parent::boot();

        static::addGlobalScope('withoutRoot', function ($query) {
            $query->where('name', '!=', SystemDefaultRoles::ROOT_ROLE);
        });
    }
    // End Glopal Scope

    // Start Relationships

    // End Relationships

    // Start Scopes

    public function scopeSimpleSearch($query, $search)
    {
        return $query->where(function($query) use($search) {
            $query->whereAny(
                ['id', 'name'],
                'LIKE',
                '%' . $search . '%'
            )->orWhereTranslationLike('title', '%' . $search . '%');
        });
    }

    // End Scopes

    public function formAjaxArray($selected = true)
    {
        return [
            'id'            => $this->id,
            'code'          => $this->name,
            'title'         => $this->smartTrans('title'),
            'selected'      => $selected
        ];
    }
}
