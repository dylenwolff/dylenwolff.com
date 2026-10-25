<?php

namespace App\Filament\Resources\Leads;

use App\Filament\Resources\Leads\Pages\ListLeads;
use App\Models\Lead;
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
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use UnitEnum;

class LeadResource extends Resource
{
    protected static ?string $model = Lead::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedFunnel;

    protected static string|UnitEnum|null $navigationGroup = 'Sales';

    protected static ?string $recordTitleAttribute = 'title';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('title')->required()->maxLength(180)->columnSpanFull(),
            Select::make('contact_id')->relationship('contact', 'first_name')->searchable()->preload(),
            Select::make('organization_id')->relationship('organization', 'name')->searchable()->preload(),
            Select::make('source')->options(['website' => 'Website', 'referral' => 'Referral', 'upwork' => 'Upwork', 'fiverr' => 'Fiverr', 'outreach' => 'Outreach', 'manual' => 'Manual'])->required(),
            Select::make('status')->options(['new' => 'New', 'contacted' => 'Contacted', 'qualified' => 'Qualified', 'unqualified' => 'Unqualified', 'converted' => 'Converted'])->required(),
            TextInput::make('estimated_value')->numeric()->prefix('LKR'),
            DateTimePicker::make('next_follow_up_at'),
            Textarea::make('description')->rows(6)->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('title')->searchable()->sortable(),
            TextColumn::make('contact.full_name')->label('Contact'),
            TextColumn::make('organization.country_code')->label('Country')->formatStateUsing(fn (?string $state): string => \App\Support\Countries::name($state) ?? 'Not set')->sortable(),
            TextColumn::make('source')->badge(),
            TextColumn::make('status')->badge()->sortable(),
            TextColumn::make('estimated_value')->money('LKR')->sortable(),
            TextColumn::make('next_follow_up_at')->dateTime()->sortable(),
        ])->filters([
            SelectFilter::make('country')->options(\App\Support\Countries::options())->query(fn ($query, array $data) => $query->when($data['value'] ?? null, fn ($query, $country) => $query->whereHas('organization', fn ($query) => $query->where('country_code', $country)))),
        ])->defaultSort('created_at', 'desc')->recordActions([EditAction::make()])->toolbarActions([CreateAction::make()]);
    }

    public static function getPages(): array
    {
        return ['index' => ListLeads::route('/')];
    }
}
