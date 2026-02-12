<?php

declare(strict_types=1);

namespace App\Filament\Resources\Users;

use App\Filament\Resources\Users\Pages\CreateUser;
use App\Filament\Resources\Users\Pages\EditUser;
use App\Filament\Resources\Users\Pages\ListUsers;
use App\Models\User;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use Exception;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use STS\FilamentImpersonate\Actions\Impersonate;

class UserResource extends Resource implements HasShieldPermissions
{
    protected static ?string $model = User::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-user-group';

    protected static string|\UnitEnum|null $navigationGroup = 'User Management';

    protected static ?int $navigationSort = 0;

    /**
     * @return array<string>
     */
    public static function getPermissionPrefixes(): array
    {
        return [
            'view_any',
            'create',
            'update',
            'delete',
            'delete_any',
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return strval(static::getEloquentQuery()->count());
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components(UserResourceForm::make());
    }

    /**
     * @throws Exception
     */
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->toggleable(false)
                    ->sortable()
                    ->searchable(),

                TextColumn::make('email')
                    ->toggleable(false)
                    ->sortable()
                    ->searchable(),

                TextColumn::make('roles.name')
                    ->listWithLineBreaks()
                    ->badge()
                    ->sortable()
                    ->searchable(),

                TextColumn::make('email_verified_at')
                    ->dateTime()
                    ->sortable(),

                IconColumn::make('system')
                    ->boolean()
                    ->trueColor('info')
                    ->falseColor('warning')
                    ->alignCenter()
                    ->label('System')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('role')
                    ->relationship('roles', 'name')
                    ->multiple()
                    ->preload()
                    ->searchable(),

                TernaryFilter::make('email_verified_at')
                    ->label('Email Verified')
                    ->native(false)
                    ->nullable(),
            ])
            ->recordActions([
                Impersonate::make()
                    ->visible(fn (): bool => (bool) auth()->user()?->isSuperAdmin())
                    ->tooltip('Impersonate User'),
                EditAction::make(),
                DeleteAction::make()
                    ->color(fn (User $record) => $record->isSuperAdmin() || $record->is(auth()->user()) || $record->system ? 'gray' : 'danger')
                    ->disabled(fn (User $record) => $record->isSuperAdmin() || $record->is(auth()->user()) || $record->system),
            ])
            ->groupedBulkActions([
                DeleteBulkAction::make()
                    ->using(static function (Collection $records): void {
                        /** @var Collection<int, User> $users */
                        $users = $records;

                        $users
                            ->reject(fn (User $record): bool => $record->isSuperAdmin() || $record->is(auth()->user()))
                            ->each(fn (User $record): ?bool => $record->delete());
                    }),
            ]);
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
            'index' => ListUsers::route('/'),
            'create' => CreateUser::route('/create'),
            'edit' => EditUser::route('/{record}/edit'),
        ];
    }

    public static function getRecordTitleAttribute(): ?string
    {
        return 'name';
    }

    /**
     * @param  User  $record
     */
    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            'Email' => $record->email,
            'Verified' => filled($record->email_verified_at) ? 'Yes' : 'No',
            'Roles' => $record->roles->pluck('name')->implode(', '),
        ];
    }

    /**
     * @return Builder<User>
     */
    public static function getGlobalSearchEloquentQuery(): Builder
    {
        return parent::getGlobalSearchEloquentQuery()->with(['roles']);
    }
}
