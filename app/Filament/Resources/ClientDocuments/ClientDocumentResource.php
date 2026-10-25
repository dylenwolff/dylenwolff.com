<?php

namespace App\Filament\Resources\ClientDocuments;

use App\Filament\Resources\ClientDocuments\Pages\ListClientDocuments;
use App\Models\ClientDocument;
use BackedEnum;
use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class ClientDocumentResource extends Resource
{
    protected static ?string $model = ClientDocument::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedFolderOpen;

    protected static string|UnitEnum|null $navigationGroup = 'Clients';

    protected static ?string $recordTitleAttribute = 'title';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('title')->required()->maxLength(180)->columnSpanFull(),
            Select::make('organization_id')->relationship('organization', 'name')->searchable()->preload()->required(),
            Select::make('contact_id')->relationship('contact', 'first_name')->searchable()->preload(),
            Select::make('client_project_id')->relationship('clientProject', 'name')->searchable()->preload(),
            Select::make('type')->options(['file' => 'File', 'contract' => 'Contract', 'brief' => 'Brief', 'credential' => 'Credential reference', 'link' => 'Important link'])->required(),
            TextInput::make('url')->url()->maxLength(2048)->columnSpanFull(),
            FileUpload::make('file_path')->label('File')->disk('local')->directory('client-documents')->visibility('private')->maxSize(10240)->columnSpanFull(),
            Textarea::make('notes')->rows(4)->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('title')->searchable()->sortable(),
            TextColumn::make('organization.name')->label('Client')->searchable(),
            TextColumn::make('clientProject.name')->label('Project'),
            TextColumn::make('type')->badge(),
            TextColumn::make('updated_at')->since()->sortable(),
        ])->defaultSort('updated_at', 'desc')->recordActions([EditAction::make()])->toolbarActions([CreateAction::make()]);
    }

    public static function getPages(): array
    {
        return ['index' => ListClientDocuments::route('/')];
    }
}
