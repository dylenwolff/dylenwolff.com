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
        $path = config('mail.sent_maildir');

        if (is_string($path) && is_dir($path)) {
            $counts = collect();
            foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path, \FilesystemIterator::SKIP_DOTS)) as $file) {
                if (! $file->isFile() || ! in_array($file->getPathInfo()->getFilename(), ['cur', 'new'], true)) {
                    continue;
                }

                $sentAt = CarbonImmutable::createFromTimestamp($file->getMTime(), 'Asia/Colombo');
                if ($sentAt->greaterThanOrEqualTo($start)) {
                    $date = $sentAt->toDateString();
                    $counts->put($date, $counts->get($date, 0) + 1);
                }
            }

            return $counts;
        }

        return OutreachEmail::query()->where('sent_at', '>=', $start->utc())->get(['sent_at'])
            ->countBy(fn ($email) => $email->sent_at->timezone('Asia/Colombo')->toDateString());
    }

    public static function total(): int
    {
        $path = config('mail.sent_maildir');
        if (! is_string($path) || ! is_dir($path)) {
            return OutreachEmail::query()->whereNotNull('sent_at')->count();
        }

        $total = 0;
        foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path, \FilesystemIterator::SKIP_DOTS)) as $file) {
            if ($file->isFile() && in_array($file->getPathInfo()->getFilename(), ['cur', 'new'], true)) {
                $total++;
            }
        }

        return $total;
    }
}
