<?php

namespace Modules\Platform\Enums;

use Modules\Platform\Concerns\ProviderDashboardTailwindPillBadge;

enum PackageSubscriptionStatus: string
{
    use ProviderDashboardTailwindPillBadge;
    case PendingPayment = 'pending_payment';
    case Active = 'active';
    case Cancelled = 'cancelled';
    case Expired = 'expired';

    /**
     * @return list<string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * @return array<string, string> Raw enum value => translated label (admin filters, selects).
     */
    public static function adminFilterSelectOptions(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $case) => [
                $case->value => trans('admin::cruds.package_subscriptions.statuses.'.$case->value),
            ])
            ->all();
    }

    /**
     * Metronic `btn-label-*` color key for admin DataTables.
     */
    public function datatableBadgeColor(): string
    {
        return match ($this) {
            self::Active => 'success',
            self::PendingPayment => 'warning',
            self::Cancelled => 'danger',
            self::Expired => 'light',
        };
    }

    /**
     * Tailwind classes for provider dashboard status pills (Front).
     */
    public function providerDashboardTailwindBadgeClass(): string
    {
        return $this->providerDashboardPillBadgeBase().' '.match ($this) {
            self::Active => 'bg-green-100 text-green-800',
            self::PendingPayment => 'bg-amber-100 text-amber-800',
            self::Cancelled => 'bg-red-100 text-red-800',
            self::Expired => 'bg-gray-100 text-gray-700',
        };
    }
}
