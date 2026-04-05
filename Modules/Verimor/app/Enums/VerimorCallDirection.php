<?php

namespace Modules\Verimor\Enums;

enum VerimorCallDirection: string
{
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
}
