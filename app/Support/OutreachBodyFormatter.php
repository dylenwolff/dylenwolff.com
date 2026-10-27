<?php

namespace App\Support;

final class OutreachBodyFormatter
{
    public static function forDelivery(string $body): string
    {
        $body = trim(str_replace(["\r\n", "\r"], "\n", $body));

        $body = preg_replace(
            '/\n*(Kind regards|Regards),?\s*\n+Dylen Wolff\s*\n+(?:Software Engineer\s*(?:\||and|&)\s*Digital Solutions Developer\s*\n+)?(?:https?:\/\/)?dylenwolff\.com(?:\/work)?\s*\n+connect@dylenwolff\.com(?:\s*\n+\+?[\d\s]+)?\s*$/iu',
            "\n\n$1,\nDylen Wolff",
            $body,
        ) ?? $body;

        return trim($body);
    }
}
