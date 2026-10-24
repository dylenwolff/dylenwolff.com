<?php

namespace App\Filament\Resources\CrmTasks;

use App\Filament\Resources\CrmTasks\Pages\ListCrmTasks;
use App\Models\CrmTask;
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

class CrmTaskResource extends Resource
{
    protected static ?string $model = CrmTask::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCheckCircle;

    protected static string|UnitEnum|null $navigationGroup = 'Work';

    protected static ?string $navigationLabel = 'Tasks';

    protected static ?string $modelLabel = 'task';

    protected static ?string $pluralModelLabel = 'tasks';

    protected static ?string $recordTitleAttribute = 'title';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('title')->required()->maxLength(180)->columnSpanFull(),
            Select::make('status')->options(['todo' => 'To do', 'in_progress' => 'In progress', 'done' => 'Done', 'cancelled' => 'Cancelled'])->required(),
            Select::make('priority')->options(['low' => 'Low', 'normal' => 'Normal', 'high' => 'High', 'urgent' => 'Urgent'])->required(),
            DateTimePicker::make('due_at'),
            DateTimePicker::make('completed_at'),
            Select::make('contact_id')->relationship('contact', 'first_name')->searchable()->preload(),
            Select::make('lead_id')->relationship('lead', 'title')->searchable()->preload(),
            Select::make('opportunity_id')->relationship('opportunity', 'name')->searchable()->preload(),
            Textarea::make('description')->rows(5)->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('title')->searchable()->sortable(),
            TextColumn::make('status')->badge()->sortable(),
            TextColumn::make('priority')->badge()->sortable(),
            TextColumn::make('due_at')->dateTime()->sortable(),
            TextColumn::make('lead.title')->label('Lead'),
            TextColumn::make('opportunity.name')->label('Opportunity'),
        ])->defaultSort('due_at')->recordActions([EditAction::make()])->toolbarActions([CreateAction::make()]);
    }

    public static function getPages(): array
    {
        return ['index' => ListCrmTasks::route('/')];
    }
}
