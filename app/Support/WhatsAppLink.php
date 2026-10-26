<?php

namespace App\Support;

use App\Models\Lead;

final class WhatsAppLink
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

    private const MOBILE_PREFIXES = [
        'AE' => ['9715'],
        'AU' => ['614'],
        'GB' => ['447'],
        'IE' => ['3538'],
        'LK' => ['947'],
        'NZ' => ['642'],
        'SG' => ['658', '659'],
    ];

    public static function phone(Lead $lead): ?string
    {
        $raw = $lead->contact?->phone ?: $lead->organization?->phone;
        $country = strtoupper((string) $lead->organization?->country_code);

        if (! filled($raw)) {
            return null;
        }

        $hasInternationalPrefix = str_starts_with(trim((string) $raw), '+') || str_starts_with(trim((string) $raw), '00');
        $digits = preg_replace('/\D+/', '', (string) $raw);

        if ($digits === null || $digits === '') {
            return null;
        }

        if (str_starts_with($digits, '00')) {
            $digits = substr($digits, 2);
        } elseif (! $hasInternationalPrefix) {
            $callingCode = self::CALLING_CODES[$country] ?? null;

            if (! $callingCode) {
                return null;
            }

            $digits = $callingCode.ltrim($digits, '0');
        }

        if (strlen($digits) < 8 || strlen($digits) > 15) {
            return null;
        }

        $mobilePrefixes = self::MOBILE_PREFIXES[$country] ?? null;
        if ($mobilePrefixes && ! collect($mobilePrefixes)->contains(fn (string $prefix): bool => str_starts_with($digits, $prefix))) {
            return null;
        }

        return $digits;
    }

    public static function message(Lead $lead): string
    {
        $organization = $lead->organization?->name ?: 'your team';
        $opportunity = str($lead->title)->lower()->finish('.')->toString();

        return "Hi {$organization} team, I'm Dylen Wolff, a software engineer and digital solutions developer. "
            ."I came across your business while researching organisations that could benefit from a stronger digital presence. I noticed a practical opportunity for your business: {$opportunity} "
            .'I build secure websites and practical digital workflows around how each business actually operates. '
            .'Would you be open to a brief conversation if I shared a focused idea for your current process? '
            .'You can view my work at https://dylenwolff.com';
    }

    public static function url(Lead $lead): ?string
    {
        $phone = self::phone($lead);

        return $phone ? 'https://wa.me/'.$phone.'?text='.rawurlencode(self::message($lead)) : null;
    }
}
