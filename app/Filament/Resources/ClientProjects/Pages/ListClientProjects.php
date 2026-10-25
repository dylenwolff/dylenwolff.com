<?php

namespace App\Filament\Resources\ClientProjects\Pages;

use App\Filament\Resources\ClientProjects\ClientProjectResource;
use Filament\Resources\Pages\ListRecords;

class ListClientProjects extends ListRecords
{
    protected static string $resource = ClientProjectResource::class;
}
