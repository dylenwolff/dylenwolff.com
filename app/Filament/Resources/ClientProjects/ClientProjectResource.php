<?php

namespace App\Filament\Resources\ClientProjects;

use App\Filament\Resources\ClientProjects\Pages\ListClientProjects;
use App\Models\ClientProject;
use BackedEnum;
use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class ClientProjectResource extends Resource
{
    protected static ?string $model = ClientProject::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentList;

    protected static string|UnitEnum|null $navigationGroup = 'Clients';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')->required()->maxLength(180)->columnSpanFull(),
            Select::make('organization_id')->relationship('organization', 'name')->searchable()->preload()->required(),
            Select::make('contact_id')->relationship('contact', 'first_name')->searchable()->preload(),
            Select::make('opportunity_id')->relationship('opportunity', 'name')->searchable()->preload(),
            Select::make('status')->options(['planned' => 'Planned', 'active' => 'Active', 'on_hold' => 'On hold', 'completed' => 'Completed', 'cancelled' => 'Cancelled'])->required(),
            Select::make('billing_type')->options(['fixed' => 'Fixed price', 'hourly' => 'Hourly', 'recurring' => 'Recurring'])->required(),
            Select::make('currency')->options(['LKR' => 'LKR', 'USD' => 'USD'])->required(),
            TextInput::make('fee')->numeric()->minValue(0),
            DatePicker::make('start_date'),
            DatePicker::make('deadline'),
            Textarea::make('notes')->rows(5)->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('name')->searchable()->sortable(),
            TextColumn::make('organization.name')->label('Client')->searchable(),
            TextColumn::make('status')->badge()->sortable(),
            TextColumn::make('billing_type')->badge(),
            TextColumn::make('fee')->money(fn (ClientProject $record) => $record->currency)->sortable(),
            TextColumn::make('deadline')->date()->sortable(),
        ])->defaultSort('deadline')->recordActions([EditAction::make()])->toolbarActions([CreateAction::make()]);
    }

    public static function getPages(): array
    {
        return ['index' => ListClientProjects::route('/')];
    }
}
