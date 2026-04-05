<?php

namespace Modules\Verimor\Support;

final class VerimorPhoneNormalizer
{
    /**
     * Canonical Turkish-style E.164 without leading + (e.g. 905321234567).
     */
    public static function canonicalize(?string $phone): string
    {
        if ($phone === null || $phone === '') {
            return '';
        }

        $digits = preg_replace('/\D+/', '', $phone) ?? '';

        if ($digits === '') {
            return '';
        }

        if (str_starts_with($digits, '90') && strlen($digits) >= 12) {
            return $digits;
        }

        if (str_starts_with($digits, '0')) {
            return '90'.substr($digits, 1);
        }

        if (str_starts_with($digits, '5') && strlen($digits) === 10) {
            return '90'.$digits;
        }

        return $digits;
    }
}
