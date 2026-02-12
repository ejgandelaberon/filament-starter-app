<?php

declare(strict_types=1);

namespace App\Filament\Resources\Roles\RelationManagers;

use App\Filament\Columns\TimestampColumn;
use Filament\Actions\AttachAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DetachAction;
use Filament\Actions\DetachBulkAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Arr;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionsRelationManager extends RelationManager
{
    protected static string $relationship = 'permissions';

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('name'),
                TextColumn::make('guard_name')->label('Guard'),
                TimestampColumn::make('created_at'),
                TimestampColumn::make('updated_at'),
            ])
            ->headerActions([
                AttachAction::make()
                    ->label('Assign Permissions')
                    ->multiple()
                    ->preloadRecordSelect()
                    ->successNotificationTitle('Permissions assigned')
                    ->using(function (self $livewire, array $data): void {
                        $permissionIds = Arr::map(Arr::collapse($data), fn (string|int $id) => (int) $id);

                        /** @var Role $ownerRecord */
                        $ownerRecord = $livewire->getOwnerRecord();
                        $ownerRecord->givePermissionTo($permissionIds);
                    }),
            ])
            ->recordActions([
                DetachAction::make()
                    ->hiddenLabel()
                    ->successNotificationTitle('Permission removed')
                    ->tooltip('Remove Permission')
                    ->using(function (self $livewire, Permission $record) {
                        /**
                         * @var Role $ownerRecord
                         */
                        $ownerRecord = $livewire->getOwnerRecord();

                        return $ownerRecord->revokePermissionTo($record);
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DetachBulkAction::make()
                        ->label('Remove Permissions')
                        ->successNotificationTitle('Permissions removed')
                        ->using(function (self $livewire, Collection $records) {
                            /** @var Role $ownerRecord */
                            $ownerRecord = $livewire->getOwnerRecord();

                            /** @var Collection<int, Permission> $records */
                            $permissions = $records
                                ->map(fn (Permission $permission): string => $permission->name)
                                ->values()
                                ->all();

                            return $ownerRecord->revokePermissionTo($permissions);
                        }),
                ]),
            ]);
    }
}
