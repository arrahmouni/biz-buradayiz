<?php

namespace Modules\Platform\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Auth\Models\User;
use Modules\Base\Models\BaseModel;
use Modules\Platform\Enums\PackageSubscriptionStatus;

class PackageSubscription extends BaseModel
{
    protected $fillable = [
        'user_id',
        'package_id',
        'status',
        'starts_at',
        'ends_at',
        'cancelled_at',
    ];

    protected function casts(): array
    {
        return [
            'status'    => PackageSubscriptionStatus::class,
            'starts_at' => 'datetime',
            'ends_at'   => 'datetime',
            'cancelled_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class);
    }
}
