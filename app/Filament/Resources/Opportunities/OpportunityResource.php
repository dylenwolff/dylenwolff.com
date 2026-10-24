<?php

namespace App\Filament\Resources\Opportunities;

use App\Filament\Resources\Opportunities\Pages\ListOpportunities;
use App\Models\Opportunity;
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

class OpportunityResource extends Resource
{
    protected static ?string $model = Opportunity::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCurrencyRupee;

    protected static string|UnitEnum|null $navigationGroup = 'Sales';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')->required()->maxLength(180)->columnSpanFull(),
            Select::make('lead_id')->relationship('lead', 'title')->searchable()->preload(),
            Select::make('contact_id')->relationship('contact', 'first_name')->searchable()->preload(),
            Select::make('organization_id')->relationship('organization', 'name')->searchable()->preload(),
            Select::make('stage')->options(['prospecting' => 'Prospecting', 'qualification' => 'Qualification', 'proposal' => 'Proposal', 'negotiation' => 'Negotiation', 'won' => 'Won', 'lost' => 'Lost'])->required(),
            TextInput::make('amount')->numeric()->prefix('LKR'),
            TextInput::make('probability')->numeric()->minValue(0)->maxValue(100)->suffix('%')->required(),
            DatePicker::make('expected_close_date'),
            Textarea::make('description')->rows(5)->columnSpanFull(),
            Textarea::make('lost_reason')->rows(3)->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('name')->searchable()->sortable(),
            TextColumn::make('organization.name')->label('Organisation'),
            TextColumn::make('stage')->badge()->sortable(),
            TextColumn::make('amount')->money('LKR')->sortable(),
            TextColumn::make('probability')->suffix('%'),
            TextColumn::make('expected_close_date')->date()->sortable(),
        ])->recordActions([EditAction::make()])->toolbarActions([CreateAction::make()]);
    }

    public static function getPages(): array
    {
        return ['index' => ListOpportunities::route('/')];
    }
}
