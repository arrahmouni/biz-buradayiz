<?php

namespace Modules\Verimor\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Auth\Models\User;
use Modules\Base\Models\BaseModel;
use Modules\Platform\Models\PackageSubscription;

class VerimorCallEvent extends BaseModel
{
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
}
