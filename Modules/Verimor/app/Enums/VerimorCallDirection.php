<?php

namespace Modules\Verimor\Enums;

enum VerimorCallDirection: string
{
    private const PROVIDER_DASHBOARD_PILL_BASE = 'inline-flex items-center font-medium text-xs px-2 py-1 rounded-full';

    case Inbound = 'inbound';
    case Outbound = 'outbound';
    case Internal = 'internal';

    /**
     * Bootstrap / Metronic badge suffix for `badge-light-{color}` (e.g. success, primary).
     */
    public function datatableBadgeColor(): string
    {
        return match ($this) {
            self::Inbound => 'success',
            self::Outbound => 'primary',
            self::Internal => 'info',
        };
    }

    /**
     * @return array<string, string> Stored enum value => datatable badge color suffix
     */
    public static function datatableBadgeColorsByValue(): array
    {
        $map = [];
        foreach (self::cases() as $case) {
            $map[$case->value] = $case->datatableBadgeColor();
        }

        return $map;
    }

    /**
     * @return list<string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * @return array<string, string> Stored value => translated label (filters, datatable)
     */
    public static function filterOptions(): array
    {
        $options = [];
        foreach (self::cases() as $case) {
            $options[$case->value] = trans('verimor::filter.directions.'.$case->value);
        }

        return $options;
    }

    /**
     * Tailwind classes for provider dashboard call direction pills (Front).
     */
    public function providerDashboardTailwindBadgeClass(): string
    {
        return self::PROVIDER_DASHBOARD_PILL_BASE.' '.match ($this) {
            self::Inbound => 'bg-green-100 text-green-800',
            self::Outbound => 'bg-blue-100 text-blue-800',
            self::Internal => 'bg-gray-100 text-gray-700',
        };
    }

    public static function providerDashboardTailwindBadgeClassForNullable(?self $direction): string
    {
        if ($direction === null) {
            return self::PROVIDER_DASHBOARD_PILL_BASE.' bg-gray-100 text-gray-600';
        }

        return $direction->providerDashboardTailwindBadgeClass();
    }
}
