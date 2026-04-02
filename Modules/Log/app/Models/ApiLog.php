<?php

namespace Modules\Log\Models;

use Illuminate\Support\Str;
use Modules\Base\Models\BaseModel;
use Modules\Log\Classes\ServiceName;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Modules\Log\Enums\ApiLogStatuses;
use Modules\Log\Enums\permissions\ApiLogPermissions;

class ApiLog extends BaseModel
{
    use SoftDeletes;

    // Start Properties

    const VIEW_PATH = 'api_logs';

    protected $fillable = [
        'user_type',
        'user_id',
        'service_name',
        'method',
        'endpoint',
        'request',
        'response',
        'status',
        'status_code',
        'error',
    ];

    public $timestamps = true;

    protected $appends = [
        'service_name_format',
        'endpoint_format',
        'status_format',
        'created_at_format',
    ];

    // End Properties

    // Start Relationships
    public function user()
    {
        return $this->morphTo();
    }
    // End Relationships

    // Start Scopes
    public function scopeSimpleSearch($query, $search)
    {
        return $query->whereAny(
            ['id', 'endpoint'],
            'LIKE',
            '%' . $search . '%'
        );
    }

    public function scopeAdvancedSearch($query, $search)
    {
        return $query
            ->when(!empty($search['service_name'])  , fn($q) => $q->where('service_name', $search['service_name']))
            ->when(!empty($search['method'])        , fn($q) => $q->where('method', 'like', '%' . $search['method'] . '%'))
            ->when(!empty($search['status'])        , fn($q) => $q->where('status', $search['status']))
            ->when(!empty($search['status_code'])   , fn($q) => $q->where('status_code', $search['status_code']));
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

    public function statusFormat() : Attribute
    {
        return Attribute::make(
            get: fn ($value, $attributes) => [
                'label' => ApiLogStatuses::getStatuses()[$attributes['status']],
                'color' => ApiLogStatuses::getStatusColor($attributes['status']),
            ],
        );
    }

    public function serviceNameFormat() : Attribute
    {
        return Attribute::make(
            get: fn ($value, $attributes) => ServiceName::getServiceNames()[$attributes['service_name']],
        );
    }

    public function endpointFormat() : Attribute
    {
        return Attribute::make(
            get: fn ($value, $attributes) => Str::limit($attributes['endpoint'], config('base.datatable_max_characters'), '...')
        );
    }

    public function createdAtFormat() : Attribute
    {
        return Attribute::make(
            get: fn ($value, $attributes) => getFormattedDate($attributes['created_at'])
        );
    }

    // End Mutators & Accessors
}
