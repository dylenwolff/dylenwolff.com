<?php

namespace App\Filament\Resources\Contacts;

use App\Filament\Resources\Contacts\Pages\ListContacts;
use App\Models\Contact;
use BackedEnum;
use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class ContactResource extends Resource
{
    protected static ?string $model = Contact::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUsers;

    protected static string|UnitEnum|null $navigationGroup = 'Sales';

    protected static ?string $recordTitleAttribute = 'full_name';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('organization_id')->relationship('organization', 'name')->searchable()->preload(),
            TextInput::make('first_name')->required()->maxLength(100),
            TextInput::make('last_name')->maxLength(100),
            TextInput::make('email')->email()->maxLength(255),
            TextInput::make('phone')->tel()->maxLength(50),
            TextInput::make('job_title')->maxLength(120),
            Select::make('status')->options(['active' => 'Active', 'inactive' => 'Inactive'])->required(),
            Textarea::make('notes')->rows(5)->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('full_name')->label('Name')->searchable(['first_name', 'last_name'])->sortable(['first_name']),
            TextColumn::make('organization.name')->label('Organisation')->searchable(),
            TextColumn::make('email')->searchable(),
            TextColumn::make('phone'),
            TextColumn::make('status')->badge(),
        ])->recordActions([EditAction::make()])->toolbarActions([CreateAction::make()]);
    }

    public static function getPages(): array
    {
        return ['index' => ListContacts::route('/')];
    }
}
