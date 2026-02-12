<?php

declare(strict_types=1);

namespace App\Filament;

use Filament\Actions\Action;
use Filament\Actions\AssociateAction;
use Filament\Actions\AttachAction;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DetachAction;
use Filament\Actions\DissociateAction;
use Filament\Actions\EditAction;
use Filament\Actions\ExportAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ImportAction;
use Filament\Actions\ReplicateAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\SelectAction;
use Filament\Actions\ViewAction;
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
            $action->label('');

            match ($action::class) {
                AssociateAction::class => $action->tooltip('Associate'),
                AttachAction::class => $action->tooltip('Attach'),
                CreateAction::class => $action->tooltip('Create'),
                DeleteAction::class => $action->tooltip('Delete'),
                DetachAction::class => $action->tooltip('Detach'),
                DissociateAction::class => $action->tooltip('Dissociate'),
                EditAction::class => $action->tooltip('Edit'),
                ExportAction::class => $action->tooltip('Export'),
                ForceDeleteAction::class => $action->tooltip('Force Delete'),
                ImportAction::class => $action->tooltip('Import'),
                ReplicateAction::class => $action->tooltip('Replicate'),
                RestoreAction::class => $action->tooltip('Restore'),
                SelectAction::class => $action->tooltip('Select'),
                ViewAction::class => $action->tooltip('View'),
                default => $action->tooltip(null),
            };
        }, isImportant: true);

        Column::configureUsing(function (Column $column): void {
            $column
                ->label(fn (?string $state, Column $column): string => str($column->getName())->headline()->toString())
                ->toggleable()
                ->searchable();
        });
    }
}
