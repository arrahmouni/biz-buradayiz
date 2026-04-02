<?php

namespace Modules\Log\Models;

use Carbon\Carbon;
use Modules\Auth\Models\User;
use Modules\Admin\Models\Admin;
use OwenIt\Auditing\Models\Audit as AuditModel;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Audit extends AuditModel
{
    protected $appends = [
        'user_type_name',
        'created_at_format',
        'event_format',
    ];

    // End Properties

    // Start Relationships

    // End Relationships

    // Start Scopes
    public function scopeSimpleSearch($query, $search)
    {
        return $query;
    }

    public function scopeAdvancedSearch($query, $search)
    {
        return $query;
    }
    // End Scopes

    // End Get Data From Model

    // Start Mutators & Accessors

    public function userTypeName() : Attribute
    {
        return Attribute::make(
            get: function ($value, $attributes) {
                $userType = $attributes['user_type'];
                switch ($userType) {
                    case Admin::class:
                        return 'Admin';
                    case User::class:
                        return 'User';
                }
            }
        );
    }

    public function eventFormat() : Attribute
    {
        return Attribute::make(
            get: function ($value, $attributes) {
                $event = $attributes['event'];
                $badge = $this->eventBadge();

                return [
                    'label' => trans("log::strings.audit.events.{$event}"),
                    'color' => $badge[$event] ?? 'info',
                ];
            }
        );
    }

    public function createdAtFormat() : Attribute
    {
        return Attribute::make(
            get: fn ($value, $attributes) => Carbon::parse($attributes['created_at'])->diffForHumans()
        );
    }

    public function eventBadge() : array
    {
        return [
            'created'  => 'success',
            'updated'  => 'info',
            'deleted'  => 'danger',
            'restored' => 'warning',
        ];
    }

    // End Mutators & Accessors
}
