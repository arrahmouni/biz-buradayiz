<?php

namespace Modules\Platform\Enums;

use Modules\Platform\Concerns\ProviderDashboardTailwindPillBadge;

enum PackageSubscriptionPaymentStatus: string
{
    use ProviderDashboardTailwindPillBadge;
    case Pending = 'pending';
    case AwaitingVerification = 'awaiting_verification';
    case Paid = 'paid';
    case Failed = 'failed';
    case Refunded = 'refunded';
    case Cancelled = 'cancelled';

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
                $case->value => trans('admin::cruds.package_subscriptions.payment_statuses.'.$case->value),
            ])
            ->all();
    }

    /**
     * Metronic `btn-label-*` color key for admin DataTables.
     */
    public function datatableBadgeColor(): string
    {
        return match ($this) {
            self::Paid => 'success',
            self::Pending => 'warning',
            self::AwaitingVerification => 'info',
            self::Failed,
            self::Cancelled => 'danger',
            self::Refunded => 'light',
        };
    }

    /**
     * Tailwind classes for provider dashboard payment status pills (Front).
     */
    public function providerDashboardTailwindBadgeClass(): string
    {
        return $this->providerDashboardPillBadgeBase().' '.match ($this) {
            self::Paid => 'bg-green-100 text-green-800',
            self::Pending => 'bg-amber-100 text-amber-800',
            self::AwaitingVerification => 'bg-sky-100 text-sky-800',
            self::Failed => 'bg-red-100 text-red-800',
            self::Cancelled => 'bg-red-100 text-red-800',
            self::Refunded => 'bg-gray-100 text-gray-700',
        };
    }
}
