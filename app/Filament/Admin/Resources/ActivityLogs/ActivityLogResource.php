<?php

namespace App\Filament\Admin\Resources\ActivityLogs;

use App\Filament\Admin\Resources\ActivityLogs\Pages\CreateActivityLog;
use App\Filament\Admin\Resources\ActivityLogs\Pages\EditActivityLog;
use App\Filament\Admin\Resources\ActivityLogs\Pages\ListActivityLogs;
use App\Filament\Admin\Resources\ActivityLogs\Pages\ViewActivityLog;
use App\Filament\Admin\Resources\ActivityLogs\Schemas\ActivityLogForm;
use App\Filament\Admin\Resources\ActivityLogs\Schemas\ActivityLogInfolist;
use App\Filament\Admin\Resources\ActivityLogs\Tables\ActivityLogsTable;
use App\HasRolePermission;
use App\Models\ActivityLog;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ActivityLogResource extends Resource
{
    use HasRolePermission;

    protected static array $allowedroles = ['admin'];

    protected static ?string $model = ActivityLog::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::FingerPrint;

    protected static ?int $navigationSort = 99;

    protected static ?string $recordTitleAttribute = 'log_name';

    public static function form(Schema $schema): Schema
    {
        return ActivityLogForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ActivityLogInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ActivityLogsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListActivityLogs::route('/'),
            // 'create' => CreateActivityLog::route('/create'),
            'view' => ViewActivityLog::route('/{record}'),
            // 'edit' => EditActivityLog::route('/{record}/edit'),
        ];
    }
}
