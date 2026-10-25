<?php

namespace App\Support;

use ResourceBundle;

final class Countries
{
    public static function options(): array
    {
        $countries = [];
        $bundle = class_exists(ResourceBundle::class)
            ? ResourceBundle::create('en', 'ICUDATA-region')
            : null;

        $countryBundle = $bundle?->get('Countries');

        if ($countryBundle instanceof ResourceBundle) {
            foreach ($countryBundle as $code => $name) {
                if (preg_match('/^[A-Z]{2}$/', (string) $code) && is_string($name)) {
                    $countries[$code] = $name;
                }
            }
        }

        if ($countries === []) {
            $countries = [
                'AU' => 'Australia',
                'CA' => 'Canada',
                'IE' => 'Ireland',
                'NZ' => 'New Zealand',
                'SG' => 'Singapore',
                'LK' => 'Sri Lanka',
                'AE' => 'United Arab Emirates',
                'GB' => 'United Kingdom',
                'US' => 'United States',
            ];
        }

        asort($countries, SORT_NATURAL | SORT_FLAG_CASE);

        return $countries;
    }

    public static function name(?string $code): ?string
    {
        return $code ? (self::options()[$code] ?? $code) : null;
    }
}
