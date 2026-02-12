<?php

declare(strict_types=1);

namespace App\Filament\Resources\Roles;

use App\Filament\Columns\TimestampColumn;
use App\Filament\Resources\Roles\Pages\CreateRole;
use App\Filament\Resources\Roles\Pages\EditRole;
use App\Filament\Resources\Roles\Pages\ListRoles;
use App\Filament\Resources\Roles\RelationManagers\PermissionsRelationManager;
use App\Models\Role;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\RecordActionsPosition;
use Filament\Tables\Table;
use Override;
use UnitEnum;

class RoleResource extends Resource
{
    protected static ?string $model = Role::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedShieldCheck;

    protected static string|UnitEnum|null $navigationGroup = 'User Management';

    protected static ?int $navigationSort = 2;

    protected static ?string $recordTitleAttribute = 'name';

    #[Override]
    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->maxLength(255)
                    ->required(),

                TextInput::make('guard_name')
                    ->helperText('Read-only, defaults to "web"')
                    ->label('Guard')
                    ->readOnly()
                    ->default('web'),
            ]);
    }

    #[Override]
    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('name'),
                TextColumn::make('guard_name')->label('Guard'),
                TextColumn::make('permissions_count')
                    ->badge()
                    ->counts('permissions')
                    ->label('Permissions'),
                TimestampColumn::make('created_at'),
                TimestampColumn::make('updated_at'),
            ])
            ->recordActions([
                EditAction::make()->hiddenLabel(),
                DeleteAction::make()
                    ->hiddenLabel()
                    ->before(function (Role $record, DeleteAction $action): void {
                        if ($record->isSystem()) {
                            Notification::make()
                                ->title('Cannot delete system role')
                                ->body('This role is a system role and cannot be deleted.')
                                ->danger()
                                ->send();

                            $action->cancel();
                        }
                    }),
            ], RecordActionsPosition::BeforeColumns)
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    #[Override]
    public static function getPages(): array
    {
        return [
            'index' => ListRoles::route('/'),
            'create' => CreateRole::route('/create'),
            'edit' => EditRole::route('/{record}/edit'),
        ];
    }

    #[Override]
    public static function getRelations(): array
    {
        return [PermissionsRelationManager::class];
    }
}
