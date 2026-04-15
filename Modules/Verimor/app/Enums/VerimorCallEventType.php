<?php

namespace Modules\Verimor\Enums;

enum VerimorCallEventType: string
{
    case Hangup = 'hangup';
    case UserHangup = 'user_hangup';

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
            $options[$case->value] = trans('verimor::filter.event_types.'.$case->value);
        }

        return $options;
    }
}
