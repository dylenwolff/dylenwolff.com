<?php

namespace App\Filament\Resources\Invoices;

use App\Filament\Resources\Invoices\Pages\ListInvoices;
use App\Models\Invoice;
use BackedEnum;
use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
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

class InvoiceResource extends Resource
{
    protected static ?string $model = Invoice::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBanknotes;

    protected static string|UnitEnum|null $navigationGroup = 'Billing';

    protected static ?string $recordTitleAttribute = 'number';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('number')->required()->unique(ignoreRecord: true)->maxLength(60),
            Select::make('status')->options(['draft' => 'Draft', 'sent' => 'Sent', 'part_paid' => 'Part paid', 'paid' => 'Paid', 'overdue' => 'Overdue', 'void' => 'Void'])->required(),
            Select::make('organization_id')->relationship('organization', 'name')->searchable()->preload()->required(),
            Select::make('contact_id')->relationship('contact', 'first_name')->searchable()->preload(),
            Select::make('client_project_id')->relationship('clientProject', 'name')->searchable()->preload(),
            Select::make('currency')->options(['LKR' => 'LKR', 'USD' => 'USD'])->required(),
            TextInput::make('total')->numeric()->minValue(0)->required(),
            TextInput::make('amount_paid')->numeric()->minValue(0)->default(0)->required(),
            DatePicker::make('issued_at'),
            DatePicker::make('due_at'),
            DateTimePicker::make('paid_at'),
            Textarea::make('notes')->rows(4)->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('number')->searchable()->sortable(),
            TextColumn::make('organization.name')->label('Client')->searchable(),
            TextColumn::make('status')->badge()->sortable(),
            TextColumn::make('total')->money(fn (Invoice $record) => $record->currency)->sortable(),
            TextColumn::make('amount_paid')->money(fn (Invoice $record) => $record->currency),
            TextColumn::make('due_at')->date()->sortable(),
        ])->defaultSort('due_at', 'desc')->recordActions([EditAction::make()])->toolbarActions([CreateAction::make()]);
    }

    public static function getPages(): array
    {
        return ['index' => ListInvoices::route('/')];
    }
}
