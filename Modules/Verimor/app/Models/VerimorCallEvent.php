<?php

namespace Modules\Verimor\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Auth\Models\User;
use Modules\Base\Models\BaseModel;
use Modules\Platform\Models\PackageSubscription;
use Modules\Verimor\Enums\VerimorCallDirection;
use Modules\Verimor\Enums\VerimorCallEventType;

class VerimorCallEvent extends BaseModel
{
    const VIEW_PATH = 'verimor_call_events';

    protected $appends = [
        'provider',
        'created_at_format',
    ];

    protected $fillable = [
        'call_uuid',
        'event_type',
        'direction',
        'destination_number_normalized',
        'user_id',
        'package_subscription_id',
        'answered',
        'consumed_quota',
        'raw_payload',
    ];

    protected function casts(): array
    {
        return [
            'event_type' => VerimorCallEventType::class,
            'direction' => VerimorCallDirection::class,
            'answered' => 'boolean',
            'consumed_quota' => 'boolean',
            'raw_payload' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function packageSubscription(): BelongsTo
    {
        return $this->belongsTo(PackageSubscription::class);
    }

    protected function provider(): Attribute
    {
        return Attribute::make(
            get: function () {
                $this->loadMissing('user');
                if ($this->user === null) {
                    return '—';
                }

                return e($this->user->full_name).' ('.e($this->user->email).')';
            },
        );
    }

    protected function createdAtFormat(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->created_at?->format('Y-m-d H:i:s') ?? '—',
        );
    }

    public function scopeSimpleSearch($query, string $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('id', $search)
                ->orWhere('call_uuid', 'like', '%'.$search.'%')
                ->orWhere('direction', 'like', '%'.$search.'%')
                ->orWhere('destination_number_normalized', 'like', '%'.$search.'%')
                ->orWhere('event_type', 'like', '%'.$search.'%')
                ->orWhereHas('user', function ($uq) use ($search) {
                    $uq->where('email', 'like', '%'.$search.'%')
                        ->orWhere('first_name', 'like', '%'.$search.'%')
                        ->orWhere('last_name', 'like', '%'.$search.'%')
                        ->orWhere('central_phone', 'like', '%'.$search.'%');
                });
        });
    }

    public function scopeAdvancedSearch($query, $search)
    {
        return $query
            ->when(
                ! empty($search['direction']) && in_array($search['direction'], VerimorCallDirection::values(), true),
                fn ($q) => $q->where('direction', $search['direction'])
            )
            ->when(
                ! empty($search['event_type']) && in_array($search['event_type'], VerimorCallEventType::values(), true),
                fn ($q) => $q->where('event_type', $search['event_type'])
            )
            ->when(
                ! empty($search['user_id']) && (int) $search['user_id'] > 0,
                fn ($q) => $q->where('user_id', (int) $search['user_id'])
            );
    }
}
