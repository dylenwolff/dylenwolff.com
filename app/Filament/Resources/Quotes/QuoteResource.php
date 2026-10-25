<?php

namespace App\Filament\Resources\Quotes;

use App\Filament\Resources\Quotes\Pages\ListQuotes;
use App\Models\Quote;
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

class QuoteResource extends Resource
{
    protected static ?string $model = Quote::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentText;

    protected static string|UnitEnum|null $navigationGroup = 'Billing';

    protected static ?string $recordTitleAttribute = 'number';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('number')->required()->unique(ignoreRecord: true)->maxLength(60),
            Select::make('status')->options(['draft' => 'Draft', 'sent' => 'Sent', 'accepted' => 'Accepted', 'declined' => 'Declined', 'expired' => 'Expired'])->required(),
            Select::make('organization_id')->relationship('organization', 'name')->searchable()->preload()->required(),
            Select::make('contact_id')->relationship('contact', 'first_name')->searchable()->preload(),
            Select::make('opportunity_id')->relationship('opportunity', 'name')->searchable()->preload(),
            Select::make('currency')->options(['LKR' => 'LKR', 'USD' => 'USD'])->required(),
            TextInput::make('subtotal')->numeric()->minValue(0)->required(),
            TextInput::make('tax')->numeric()->minValue(0)->default(0)->required(),
            TextInput::make('total')->numeric()->minValue(0)->required(),
            DatePicker::make('issued_at'),
            DatePicker::make('valid_until'),
            Textarea::make('scope')->rows(6)->columnSpanFull(),
            Textarea::make('notes')->rows(3)->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('number')->searchable()->sortable(),
            TextColumn::make('organization.name')->label('Client')->searchable(),
            TextColumn::make('status')->badge()->sortable(),
            TextColumn::make('total')->money(fn (Quote $record) => $record->currency)->sortable(),
            TextColumn::make('issued_at')->date()->sortable(),
            TextColumn::make('valid_until')->date()->sortable(),
        ])->defaultSort('created_at', 'desc')->recordActions([EditAction::make()])->toolbarActions([CreateAction::make()]);
    }

    public static function getPages(): array
    {
        return ['index' => ListQuotes::route('/')];
    }
}
