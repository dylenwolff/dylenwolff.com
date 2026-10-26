<?php

namespace App\Support;

use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Crypt;

final class ContactSpamGuard
{
    public static function formToken(): string
    {
        return Crypt::encryptString((string) now()->timestamp);
    }

    public static function isSpam(array $data): bool
    {
        if (! self::wasCompletedByAHuman($data['form_started_at'] ?? null)) {
            return true;
        }

        $content = mb_strtolower(implode("\n", [
            $data['name'] ?? '',
            $data['email'] ?? '',
            $data['subject'] ?? '',
            $data['message'] ?? '',
        ]));

        $links = preg_match_all('~(?:https?://|www\.)~iu', $content);
        $hasHtmlLink = preg_match('~<\s*a\b|href\s*=~iu', $content) === 1;
        $spamTerms = preg_match(
            '~casino|slots?|vulkan|bonus(?:u|bet)?|xrumer|backlinks?|domain\s+rating|guest\s+post|crypto\s+investment|essay\s+writing~iu',
            $content,
        ) === 1;
        $suspiciousDomain = preg_match('~\.(?:top|ru|click|xyz|it\.com)(?:\b|/)~iu', $content) === 1;

        return $hasHtmlLink || $links >= 3 || ($links >= 1 && ($spamTerms || $suspiciousDomain));
    }

    private static function wasCompletedByAHuman(?string $token): bool
    {
        if (! $token) {
            return false;
        }

        try {
            $startedAt = (int) Crypt::decryptString($token);
        } catch (DecryptException) {
            return false;
        }

        $elapsed = now()->timestamp - $startedAt;

        return $elapsed >= 3 && $elapsed <= 7200;
    }
}
