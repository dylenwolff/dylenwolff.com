<?php

namespace App\Support;

use App\Models\OutreachEmail;
use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

final class SentMailStatistics
{
    public static function dailyCounts(int $days = 7): Collection
    {
        $start = CarbonImmutable::now('Asia/Colombo')->subDays($days - 1)->startOfDay();
        $snapshot = self::snapshot();

        if ($snapshot) {
            return collect($snapshot['daily'] ?? [])->filter(fn ($count, $date) => CarbonImmutable::parse($date, 'Asia/Colombo')->greaterThanOrEqualTo($start));
        }

        return OutreachEmail::query()->where('sent_at', '>=', $start->utc())->get(['sent_at'])
            ->countBy(fn ($email) => $email->sent_at->timezone('Asia/Colombo')->toDateString());
    }

    public static function total(): int
    {
        $snapshot = self::snapshot();
        if ($snapshot) {
            return (int) ($snapshot['total'] ?? 0);
        }

        return OutreachEmail::query()->whereNotNull('sent_at')->count();
    }

    public static function sync(): array
    {
        $path = config('mail.sent_maildir');
        if (! is_string($path) || ! @is_dir($path)) {
            throw new \RuntimeException('The configured Sent mailbox directory is unavailable.');
        }

        $total = 0;
        $daily = [];
        foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path, \FilesystemIterator::SKIP_DOTS)) as $file) {
            if ($file->isFile() && in_array($file->getPathInfo()->getFilename(), ['cur', 'new'], true)) {
                $total++;
                $date = CarbonImmutable::createFromTimestamp($file->getMTime(), 'Asia/Colombo')->toDateString();
                $daily[$date] = ($daily[$date] ?? 0) + 1;
            }
        }

        $snapshot = ['total' => $total, 'daily' => $daily, 'synced_at' => now('Asia/Colombo')->toIso8601String()];
        file_put_contents(self::snapshotPath(), json_encode($snapshot, JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR));

        return $snapshot;
    }

    private static function snapshot(): ?array
    {
        $path = self::snapshotPath();
        if (! is_file($path)) {
            return null;
        }

        $snapshot = json_decode((string) file_get_contents($path), true);

        return is_array($snapshot) ? $snapshot : null;
    }

    private static function snapshotPath(): string
    {
        return storage_path('app/sent-mail-statistics.json');
    }
}
