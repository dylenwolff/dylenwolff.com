<?php

namespace App\Support;

final class PhoneNumber
{
    private const CALLING_CODES = [
        'AE' => '971',
        'AU' => '61',
        'CA' => '1',
        'GB' => '44',
        'IE' => '353',
        'LK' => '94',
        'NZ' => '64',
        'SG' => '65',
        'US' => '1',
    ];

    public static function international(?string $phone, ?string $countryCode): ?string
    {
        if (! filled($phone)) {
            return null;
        }

        $trimmed = trim($phone);
        $digits = preg_replace('/\D+/', '', $trimmed);

        if (! $digits) {
            return null;
        }

        if (str_starts_with($trimmed, '+')) {
            return '+'.$digits;
        }

        if (str_starts_with($digits, '00')) {
            return '+'.substr($digits, 2);
        }

        $callingCode = self::CALLING_CODES[strtoupper((string) $countryCode)] ?? null;

        return $callingCode ? '+'.$callingCode.ltrim($digits, '0') : $trimmed;
    }
}
