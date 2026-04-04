<?php

namespace Modules\Platform\Enums;

enum BillingPeriod: string
{
    case Monthly = 'monthly';
    case Yearly  = 'yearly';
    case OneTime = 'one_time';

    /**
     * @return list<string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function getBillingPeriods()
    {
        return [
            self::Monthly => trans('admin::cruds.packages.billing_periods.monthly'),
            self::Yearly  => trans('admin::cruds.packages.billing_periods.yearly'),
            self::OneTime => trans('admin::cruds.packages.billing_periods.one_time'),
        ];
    }

    public static function getBillingPeriodsSelectOptions()
    {
        return collect(self::cases())->mapWithKeys(
            fn ($case) => [$case->value => trans('admin::cruds.packages.billing_periods.' . $case->value)]
        )->all();
    }
}
