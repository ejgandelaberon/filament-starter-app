<?php

declare(strict_types=1);

namespace App\Filament\Columns;

use Filament\Tables\Columns\TextColumn;
use Override;

class TimestampColumn extends TextColumn
{
    #[Override]
    public static function make(?string $name = null): static
    {
        return parent::make($name)
            ->dateTime()
            ->label($name ? str($name)->headline()->value() : null)
            ->toggledHiddenByDefault();
    }
}
