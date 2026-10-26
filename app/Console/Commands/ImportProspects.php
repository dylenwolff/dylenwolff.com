<?php

namespace App\Console\Commands;

use App\Models\Lead;
use App\Models\Organization;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use JsonException;

class ImportProspects extends Command
{
    protected $signature = 'prospects:import {file : Absolute path to a JSON prospect file}';

    protected $description = 'Import researched organizations and leads without creating outreach emails';

    /** @throws JsonException */
    public function handle(): int
    {
        $path = (string) $this->argument('file');

        if (! is_file($path) || ! is_readable($path)) {
            $this->error('The prospect file could not be read.');

            return self::FAILURE;
        }

        $items = json_decode((string) file_get_contents($path), true, flags: JSON_THROW_ON_ERROR);

        foreach ($items as $index => $item) {
            $validator = Validator::make($item, [
                'organization.name' => ['required', 'string', 'max:255'],
                'organization.industry' => ['nullable', 'string', 'max:255'],
                'organization.country_code' => ['required', 'string', 'size:2'],
                'organization.city' => ['nullable', 'string', 'max:255'],
                'organization.website' => ['required', 'url', 'max:255'],
                'organization.email' => ['nullable', 'email', 'max:255'],
                'organization.phone' => ['nullable', 'string', 'max:255'],
                'organization.notes' => ['required', 'string'],
                'lead.title' => ['required', 'string', 'max:255'],
                'lead.source' => ['nullable', 'string', 'max:255'],
                'lead.status' => ['nullable', 'string', 'max:50'],
                'lead.description' => ['required', 'string'],
            ]);

            if ($validator->fails()) {
                $this->error('Item '.($index + 1).' is invalid: '.$validator->errors()->first());

                return self::FAILURE;
            }

            DB::transaction(function () use ($item): void {
                $organization = Organization::query()->updateOrCreate(
                    ['name' => $item['organization']['name']],
                    array_merge(['status' => 'prospect'], $item['organization']),
                );

                Lead::query()->updateOrCreate(
                    ['organization_id' => $organization->id, 'title' => $item['lead']['title']],
                    array_merge(['status' => 'new', 'source' => 'website'], $item['lead']),
                );
            });
        }

        $this->info('Imported '.count($items).' researched prospect(s).');

        return self::SUCCESS;
    }
}
