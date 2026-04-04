<?php

namespace Modules\Platform\Enums;

enum PackageSubscriptionPaymentMethod: string
{
    case BankTransfer = 'bank_transfer';
    case Cash = 'cash';
    case Other = 'other';
    case OnlineGateway = 'online_gateway';

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
                $case->value => trans('admin::cruds.package_subscriptions.payment_methods.'.$case->value),
            ])
            ->all();
    }

    /**
     * Icon URL for admin subscription views (Metronic demo assets).
     */
    public function adminDisplayIconUrl(): string
    {
        return match ($this) {
            self::BankTransfer => asset('modules/admin/metronic/demo/media/icons/duotune/finance/fin006.svg'),
            self::Cash => asset('modules/admin/metronic/demo/media/icons/duotune/finance/fin008.svg'),
            self::Other => asset('modules/admin/metronic/demo/media/icons/duotune/general/gen046.svg'),
            self::OnlineGateway => asset('modules/admin/metronic/demo/media/svg/payment-methods/paypal.svg'),
        };
    }
}
