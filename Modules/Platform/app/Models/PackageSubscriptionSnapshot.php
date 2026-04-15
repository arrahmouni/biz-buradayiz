<?php

namespace Modules\Platform\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Base\Models\BaseModel;

class PackageSubscriptionSnapshot extends BaseModel
{
    protected $fillable = [
        'package_subscription_id',
        'source_package_id',
        'name_translations',
        'price',
        'currency',
        'billing_period',
        'connections_count',
    ];

    protected function casts(): array
    {
        return [
            'name_translations' => 'array',
            'price' => 'decimal:2',
            'connections_count' => 'integer',
        ];
    }

    public function packageSubscription(): BelongsTo
    {
        return $this->belongsTo(PackageSubscription::class);
    }

    public function sourcePackage(): BelongsTo
    {
        return $this->belongsTo(Package::class, 'source_package_id');
    }

    /**
     * Build snapshot attributes from a catalog package (immutable order line).
     *
     * @return array<string, mixed>
     */
    public static function attributesFromPackage(Package $package): array
    {
        $package->loadMissing('translations');

        $names = [];
        foreach ($package->translations as $translation) {
            if (! empty($translation->name)) {
                $names[$translation->locale] = $translation->name;
            }
        }

        if ($names === []) {
            $names[app()->getLocale()] = (string) $package->id;
        }

        $billingPeriod = $package->billing_period;
        $billingPeriodValue = $billingPeriod instanceof \BackedEnum
            ? $billingPeriod->value
            : (string) $billingPeriod;

        return [
            'source_package_id' => $package->id,
            'name_translations' => $names,
            'price' => $package->price,
            'currency' => $package->currency,
            'billing_period' => $billingPeriodValue,
            'connections_count' => $package->connections_count,
        ];
    }

    public function smartTransName(?string $locale = null): ?string
    {
        $locale ??= app()->getLocale();
        $translations = $this->name_translations ?? [];

        if (isset($translations[$locale]) && $translations[$locale] !== '') {
            return $translations[$locale];
        }

        $fallback = config('app.fallback_locale');
        if ($fallback && isset($translations[$fallback]) && $translations[$fallback] !== '') {
            return $translations[$fallback];
        }

        $first = reset($translations);

        return $first !== false ? (string) $first : null;
    }

    public function priceDisplay(): string
    {
        return number_format((float) $this->price, 2).' '.$this->currency;
    }
}
