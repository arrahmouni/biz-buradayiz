<?php

namespace Modules\Cms\Models;

use Carbon\Carbon;
use Modules\Base\Models\BaseModel;
use Modules\Base\Trait\Disableable;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Cms\Enums\permissions\ContentTagPermissions;

class ContentTag extends BaseModel
{
    use Translatable, SoftDeletes, Disableable, HasFactory;

    // Start Properties

    const VIEW_PATH = 'content_tags';

    protected $fillable = [

    ];

    public $timestamps = true;

    public $translatedAttributes = [
        'title',
    ];

    protected $with = [
        'translations'
    ];

    protected $appends = [
        'created_at_format',
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
            ->orWhereTranslationLike('title', '%' . $search . '%');
        });
    }

    public function scopeAdvancedSearch($query, $search)
    {
        return $query;
    }
    // End Scopes

    // Start Get Data From Model

    public function formAjaxArray($selected = true)
    {
        return [
            'id'            => $this->id,
            'text'          => $this->smartTrans('title'),
            'selected'      => $selected
        ];
    }

    // End Get Data From Model

    // Start Mutators & Accessors

    protected function createdAtFormat() : Attribute
    {
        return Attribute::make(
            get: function($value, $attribute) {
                return Carbon::parse($this->created_at)->format('Y-m-d H:i');
            }
        );
    }
    // End Mutators & Accessors
}
