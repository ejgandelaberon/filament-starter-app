<?php

declare(strict_types=1);

namespace App\Filament;

use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Tables\Columns\Column;

class FilamentConfigurations
{
    public function boot(): void
    {
        $this->configurePageActions();
        $this->configureTable();
    }

    protected function configurePageActions(): void
    {
        CreateAction::configureUsing(function (CreateAction $action): void {
            $action->label('Create')->icon('heroicon-o-plus');
        }, isImportant: true);
    }

    protected function configureTable(): void
    {
        Action::configureUsing(function (Action $action): void {
            $action->hiddenLabel()->tooltip(fn (Action $action) => $action->getLabel());
        }, isImportant: true);

        Column::configureUsing(function (Column $column): void {
            $column
                ->label(fn (?string $state, Column $column): string => str($column->getName())->headline()->toString())
                ->toggleable()
                ->searchable();
        });
    }
}
