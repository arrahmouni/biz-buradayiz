<?php

namespace Modules\Crm\Models;

use Carbon\Carbon;
use Illuminate\Support\Str;
use Modules\Base\Models\BaseModel;
use Modules\Crm\Enums\ContactusStatuses;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;

class Contactus extends BaseModel implements Auditable
{
    use SoftDeletes, HasFactory, AuditableTrait;

    // Start Properties

    const VIEW_PATH = 'contactuses';

    protected $fillable = [
        'type',
        'first_name',
        'last_name',
        'email',
        'phone',
        'message',
        'reply',
        'ip_address',
        'user_agent',
        'status',
        'locale',
    ];

    public $timestamps = true;

    protected $appends = [
        'full_name',
        'message_text',
        'status_format',
        'created_at_format',
    ];

    /**
     * Create a new factory instance for the model.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    protected static function newFactory()
    {
        return \Modules\Crm\Database\Factories\ContactusFactory::new();
    }

    // End Properties

    // Start Relationships

    // End Relationships

    // Start Scopes
    public function scopeSimpleSearch($query, $search)
    {
        return $query->whereAny(
            ['id', 'first_name', 'last_name', 'email', 'phone', 'message', 'reply', 'ip_address', 'user_agent'],
            'LIKE',
            '%' . $search . '%'
        );
    }

    public function scopeAdvancedSearch($query, $search)
    {
        return $query
            ->when(!empty($search['search']), fn($q) => $q->simpleSearch($search['search']));
    }

    public function canReply() : bool
    {
        return in_array($this->status, [ContactusStatuses::PENDING, ContactusStatuses::SEEN]);
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
    public function fullName() : Attribute
    {
        return Attribute::make(
            get: fn ($value, $attributes) => $attributes['first_name'] . ' ' . $attributes['last_name'],
        );
    }

    public function messageText() : Attribute
    {
        return Attribute::make(
            get: fn ($value, $attributes) => Str::limit($attributes['message'], config('base.datatable_max_characters'), '...'),
        );
    }

    public function statusFormat() : Attribute
    {
        return Attribute::make(
            get: fn ($value, $attributes) => [
                'label' => ContactusStatuses::getStatuses()[$attributes['status']],
                'color' => ContactusStatuses::getStatusColor($attributes['status']),
            ],
        );
    }

    protected function createdAtFormat() : Attribute
    {
        return Attribute::make(
            get: function($value, $attribute) {
                return Carbon::parse($this->created_at)->locale(app()->getLocale())->diffForHumans();
            }
        );
    }
    // End Mutators & Accessors
}
