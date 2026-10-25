<?php

namespace App\Filament\Resources\SupportTickets;

use App\Filament\Resources\SupportTickets\Pages\ListSupportTickets;
use App\Models\SupportTicket;
use BackedEnum;
use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class SupportTicketResource extends Resource
{
    protected static ?string $model = SupportTicket::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedLifebuoy;

    protected static string|UnitEnum|null $navigationGroup = 'Clients';

    protected static ?string $recordTitleAttribute = 'subject';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('subject')->required()->maxLength(180)->columnSpanFull(),
            Select::make('organization_id')->relationship('organization', 'name')->searchable()->preload()->required(),
            Select::make('contact_id')->relationship('contact', 'first_name')->searchable()->preload(),
            Select::make('client_project_id')->relationship('clientProject', 'name')->searchable()->preload(),
            Select::make('status')->options(['open' => 'Open', 'in_progress' => 'In progress', 'waiting' => 'Waiting for client', 'resolved' => 'Resolved', 'closed' => 'Closed'])->required(),
            Select::make('priority')->options(['low' => 'Low', 'normal' => 'Normal', 'high' => 'High', 'urgent' => 'Urgent'])->required(),
            DateTimePicker::make('due_at'),
            DateTimePicker::make('resolved_at'),
            Textarea::make('details')->rows(7)->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('subject')->searchable()->sortable(),
            TextColumn::make('organization.name')->label('Client')->searchable(),
            TextColumn::make('priority')->badge()->sortable(),
            TextColumn::make('status')->badge()->sortable(),
            TextColumn::make('due_at')->dateTime()->sortable(),
        ])->defaultSort('created_at', 'desc')->recordActions([EditAction::make()])->toolbarActions([CreateAction::make()]);
    }

    public static function getPages(): array
    {
        return ['index' => ListSupportTickets::route('/')];
    }
}
