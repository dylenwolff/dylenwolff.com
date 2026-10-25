<?php

namespace App\Filament\Resources\MaintenancePlans;

use App\Filament\Resources\MaintenancePlans\Pages\ListMaintenancePlans;
use App\Models\MaintenancePlan;
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

class MaintenancePlanResource extends Resource
{
    protected static ?string $model = MaintenancePlan::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedArrowPathRoundedSquare;

    protected static string|UnitEnum|null $navigationGroup = 'Billing';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')->required()->maxLength(180)->columnSpanFull(),
            Select::make('organization_id')->relationship('organization', 'name')->searchable()->preload()->required(),
            Select::make('client_project_id')->relationship('clientProject', 'name')->searchable()->preload(),
            Select::make('status')->options(['active' => 'Active', 'paused' => 'Paused', 'cancelled' => 'Cancelled', 'ended' => 'Ended'])->required(),
            Select::make('currency')->options(['LKR' => 'LKR', 'USD' => 'USD'])->required(),
            TextInput::make('monthly_fee')->numeric()->minValue(0)->required(),
            DatePicker::make('started_at'),
            DatePicker::make('next_billing_date'),
            DatePicker::make('ended_at'),
            Textarea::make('services')->rows(5)->columnSpanFull(),
            Textarea::make('notes')->rows(3)->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('name')->searchable()->sortable(),
            TextColumn::make('organization.name')->label('Client')->searchable(),
            TextColumn::make('status')->badge()->sortable(),
            TextColumn::make('monthly_fee')->money(fn (MaintenancePlan $record) => $record->currency)->sortable(),
            TextColumn::make('next_billing_date')->date()->sortable(),
        ])->defaultSort('next_billing_date')->recordActions([EditAction::make()])->toolbarActions([CreateAction::make()]);
    }

    public static function getPages(): array
    {
        return ['index' => ListMaintenancePlans::route('/')];
    }
}
