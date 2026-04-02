<?php

namespace Modules\Crm\Models;

use Carbon\Carbon;
use Modules\Base\Models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;

class Subscribe extends BaseModel implements Auditable
{
    use SoftDeletes, HasFactory, AuditableTrait;

    // Start Properties

    const VIEW_PATH = 'subscribes';

    protected $fillable = [
        'email',
        'is_active',
    ];

    public $timestamps = true;

    protected $appends = [
        'created_at_format',
    ];

    // End Properties

    // Start Relationships

    // End Relationships

    // Start Scopes
    public function scopeSimpleSearch($query, $search)
    {
        return $query->whereAny(
            ['id', 'email'],
            'LIKE',
            '%' . $search . '%'
        );
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
            'selected'      => $selected
        ];
    }

    // End Get Data From Model

    // Start Mutators & Accessors

    protected function createdAtFormat() : Attribute
    {
        return Attribute::make(
            get: function($value, $attribute) {
                return Carbon::parse($this->created_at)->format('Y-m-d H:i:s');
            }
        );
    }
    // End Mutators & Accessors
}
