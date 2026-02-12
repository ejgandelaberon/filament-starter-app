<?php

declare(strict_types=1);

namespace App\Filament\Resources\Activities;

use App\Filament\Resources\Activities\Pages\ManageActivities;
use BackedEnum;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use Filament\Actions\ViewAction;
use Filament\Infolists\Components\KeyValueEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use Spatie\Activitylog\Models\Activity;
use UnitEnum;

class ActivityResource extends Resource implements HasShieldPermissions
{
    protected static ?string $model = Activity::class;

    protected static string|BackedEnum|null $navigationIcon = 'fluentui-history-48-o';

    protected static string|UnitEnum|null $navigationGroup = 'System Management';

    protected static ?int $navigationSort = 2;

    protected static ?string $label = 'Activity Log';

    public static function canAccess(): bool
    {
        return auth()->user()?->isSuperAdmin() ?? false;
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make([
                    TextEntry::make('event')
                        ->inlineLabel()
                        ->badge(),

                    TextEntry::make('subject_type')
                        ->label('Subject')
                        ->inlineLabel()
                        ->badge()
                        ->formatStateUsing(static fn (Activity $activity): string => Str::afterLast($activity->subject_type ?? '', '\\')),

                    TextEntry::make('causer.name')
                        ->inlineLabel()
                        ->badge(),

                    Grid::make()->schema([
                        TextEntry::make('created_at')
                            ->inlineLabel()
                            ->badge(),

                        TextEntry::make('updated_at')
                            ->inlineLabel()
                            ->badge(),
                    ]),
                ])->columns(),

                KeyValueEntry::make('properties.old')
                    ->label('Old')
                    ->columnSpanFull(),

                KeyValueEntry::make('properties.attributes')
                    ->label('New')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('event')
                    ->toggleable(false)
                    ->badge(),

                TextColumn::make('subject_type')
                    ->toggleable(false)
                    ->label('Subject')
                    ->formatStateUsing(static fn (Activity $activity): string => Str::afterLast($activity->subject_type ?? '', '\\')),

                TextColumn::make('causer.name')
                    ->toggleable(false)
                    ->getStateUsing(static function (Activity $activity): string {
                        $name = $activity->causer?->getAttribute('name');

                        return is_string($name) ? $name : 'System';
                    }),

                TextColumn::make('created_at')
                    ->label('Created At')
                    ->date('Y-m-d H:i:s'),

                TextColumn::make('updated_at')
                    ->label('Updated At')
                    ->date('Y-m-d H:i:s'),
            ])
            ->recordActions([
                ViewAction::make('view')
                    ->label('')
                    ->tooltip('View Changes')
                    ->icon('heroicon-o-eye'),
            ])
            ->modifyQueryUsing(function (Builder $query) {
                return $query->with(['causer', 'subject']);
            });
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageActivities::route('/'),
        ];
    }

    /**
     * @return string[]
     */
    public static function getPermissionPrefixes(): array
    {
        return [];
    }
}
