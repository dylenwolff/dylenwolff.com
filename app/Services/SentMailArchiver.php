<?php

namespace App\Services;

use Illuminate\Mail\SentMessage;
use RuntimeException;

class SentMailArchiver
{
    /** @var resource|null */
    private $connection = null;

    private int $tag = 0;

    public function archive(SentMessage $message): void
    {
        if (! config('mail.sent_copy.enabled', false)) {
            return;
        }

        $host = (string) config('mail.sent_copy.host');
        $port = (int) config('mail.sent_copy.port', 993);
        $username = (string) config('mail.sent_copy.username');
        $password = (string) config('mail.sent_copy.password');
        $mailbox = (string) config('mail.sent_copy.mailbox', 'Sent');
        $timeout = (int) config('mail.sent_copy.timeout', 10);

        if ($host === '' || $username === '' || $password === '') {
            throw new RuntimeException('Sent-mail archiving is enabled but its IMAP settings are incomplete.');
        }

        $target = sprintf('tls://%s:%d', $host, $port);
        $errorCode = 0;
        $errorMessage = '';

        $this->connection = stream_socket_client(
            $target,
            $errorCode,
            $errorMessage,
            $timeout,
            STREAM_CLIENT_CONNECT
        );

        if (! is_resource($this->connection)) {
            throw new RuntimeException("Could not connect to the IMAP server: {$errorMessage} ({$errorCode}).");
        }

        stream_set_timeout($this->connection, $timeout);

        try {
            $greeting = $this->readLine();
            if (! str_starts_with($greeting, '* OK')) {
                throw new RuntimeException('The IMAP server did not return a ready greeting.');
            }

            $this->command(sprintf('LOGIN %s %s', $this->quote($username), $this->quote($password)));

            $rawMessage = $message->getOriginalMessage()->toString();
            $tag = $this->nextTag();
            $command = sprintf(
                '%s APPEND %s (\\Seen) {%d}\r\n',
                $tag,
                $this->quote($mailbox),
                strlen($rawMessage)
            );

            $this->write($command);
            $continuation = $this->readLine();

            if (! str_starts_with($continuation, '+')) {
                throw new RuntimeException('The IMAP server did not accept the Sent message upload.');
            }

            $this->write($rawMessage."\r\n");
            $this->readTaggedResponse($tag);
            $this->command('LOGOUT');
        } finally {
            if (is_resource($this->connection)) {
                fclose($this->connection);
            }

            $this->connection = null;
        }
    }

    private function command(string $command): void
    {
        $tag = $this->nextTag();
        $this->write("{$tag} {$command}\r\n");
        $this->readTaggedResponse($tag);
    }

    private function readTaggedResponse(string $tag): void
    {
        do {
            $line = $this->readLine();
        } while (! str_starts_with($line, $tag.' '));

        if (! str_starts_with($line, $tag.' OK')) {
            throw new RuntimeException('The IMAP server rejected a command: '.$this->sanitize($line));
        }
    }

    private function readLine(): string
    {
        if (! is_resource($this->connection)) {
            throw new RuntimeException('The IMAP connection is not available.');
        }

        $line = fgets($this->connection);

        if ($line === false) {
            $metadata = stream_get_meta_data($this->connection);
            $reason = ($metadata['timed_out'] ?? false) ? 'timed out' : 'closed unexpectedly';
            throw new RuntimeException("The IMAP connection {$reason}.");
        }

        return rtrim($line, "\r\n");
    }

    private function write(string $data): void
    {
        if (! is_resource($this->connection) || fwrite($this->connection, $data) === false) {
            throw new RuntimeException('Could not write to the IMAP connection.');
        }
    }

    private function nextTag(): string
    {
        return 'A'.str_pad((string) ++$this->tag, 4, '0', STR_PAD_LEFT);
    }

    private function quote(string $value): string
    {
        if (str_contains($value, "\r") || str_contains($value, "\n")) {
            throw new RuntimeException('Invalid IMAP setting.');
        }

        return '"'.addcslashes($value, '\\"').'"';
    }

    private function sanitize(string $message): string
    {
        return mb_substr(preg_replace('/[^[:print:]\t]/u', '', $message) ?? '', 0, 300);
    }
}
