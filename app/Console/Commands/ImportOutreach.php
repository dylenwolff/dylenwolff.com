<?php

namespace App\Console\Commands;

use App\Models\Lead;
use App\Models\Organization;
use App\Models\OutreachEmail;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use RuntimeException;

class ImportOutreach extends Command
{
    protected $signature = 'outreach:import {file : Absolute path to a JSON queue file}';

    protected $description = 'Import researched organizations, leads and outreach emails into the CRM queue';

    public function handle(): int
    {
        $path = (string) $this->argument('file');

        if (! is_file($path) || ! is_readable($path)) {
            $this->error('The queue file could not be read.');

            return self::FAILURE;
        }

        $items = json_decode((string) file_get_contents($path), true, flags: JSON_THROW_ON_ERROR);

        if (! is_array($items)) {
            throw new RuntimeException('The queue file must contain a JSON array.');
        }

        $imported = 0;

        foreach ($items as $index => $item) {
            $validator = Validator::make($item, [
                'organization.name' => ['required', 'string', 'max:255'],
                'organization.industry' => ['nullable', 'string', 'max:255'],
                'organization.country_code' => ['nullable', 'string', 'size:2'],
                'organization.city' => ['nullable', 'string', 'max:255'],
                'organization.website' => ['nullable', 'url', 'max:255'],
                'organization.email' => ['nullable', 'email', 'max:255'],
                'organization.phone' => ['nullable', 'string', 'max:255'],
                'organization.status' => ['nullable', 'string', 'max:50'],
                'organization.notes' => ['nullable', 'string'],
                'lead.title' => ['required', 'string', 'max:255'],
                'lead.source' => ['nullable', 'string', 'max:255'],
                'lead.status' => ['nullable', 'string', 'max:50'],
                'lead.description' => ['nullable', 'string'],
                'email.recipient_name' => ['nullable', 'string', 'max:255'],
                'email.recipient_email' => ['required', 'email', 'max:255'],
                'email.subject' => ['required', 'string', 'max:255'],
                'email.body' => ['required', 'string'],
                'email.status' => ['required', 'in:draft,ready,approved,sent,bounced,cancelled'],
                'email.notes' => ['nullable', 'string'],
            ]);

            if ($validator->fails()) {
                $this->error('Item '.($index + 1).' is invalid: '.$validator->errors()->first());

                return self::FAILURE;
            }

            DB::transaction(function () use ($item): void {
                $organizationData = $item['organization'];
                $organization = Organization::query()->firstOrCreate(
                    ['name' => $organizationData['name']],
                    ['status' => 'prospect'],
                );
                $organization->fill(array_filter($organizationData, static fn ($value): bool => $value !== null && $value !== ''));
                $organization->save();

                $leadData = $item['lead'];
                $lead = Lead::query()->firstOrCreate(
                    ['organization_id' => $organization->id, 'title' => $leadData['title']],
                    ['status' => 'new'],
                );
                $lead->fill($leadData);
                $lead->save();

                $emailData = $item['email'];
                OutreachEmail::query()->updateOrCreate(
                    [
                        'recipient_email' => $emailData['recipient_email'],
                        'subject' => $emailData['subject'],
                    ],
                    array_merge($emailData, [
                        'organization_id' => $organization->id,
                        'lead_id' => $lead->id,
                    ]),
                );
            });

            $imported++;
        }

        $this->info("Imported {$imported} outreach queue item(s).");

        return self::SUCCESS;
    }
}
