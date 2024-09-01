<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Clusters\UserManagement;
use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\UserResourceForm;
use App\Models\User;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use Exception;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;

class UserResource extends Resource implements HasShieldPermissions
{
    protected static ?string $cluster = UserManagement::class;

    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

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

    public static function form(Form $form): Form
    {
        return $form->schema(UserResourceForm::make());
    }

    /**
     * @throws Exception
     */
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->toggleable(false)
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('email')
                    ->toggleable(false)
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('roles.name')
                    ->listWithLineBreaks()
                    ->badge()
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('email_verified_at')
                    ->dateTime()
                    ->sortable(),

                Tables\Columns\IconColumn::make('system')
                    ->boolean()
                    ->trueColor('info')
                    ->falseColor('warning')
                    ->alignCenter()
                    ->label('System')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('role')
                    ->relationship('roles', 'name')
                    ->multiple()
                    ->preload()
                    ->searchable(),

                Tables\Filters\TernaryFilter::make('email_verified_at')
                    ->label('Email Verified')
                    ->native(false)
                    ->nullable(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->color(fn ($record) => $record->isSuperAdmin() || $record->is(auth()->user()) || $record->system ? 'gray' : 'danger')
                    ->disabled(fn ($record) => $record->isSuperAdmin() || $record->is(auth()->user()) || $record->system),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->using(static function (Collection $records): void {
                            /** @var Collection<int, User> $users */
                            $users = $records;

                            $users
                                ->reject(fn (User $record): bool => $record->isSuperAdmin() || $record->is(auth()->user()))
                                ->each(fn (User $record): ?bool => $record->delete());
                        }),
                ]),
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
