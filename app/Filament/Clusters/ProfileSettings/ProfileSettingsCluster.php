<?php

declare(strict_types=1);

namespace App\Filament\Clusters\ProfileSettings;

use BackedEnum;
use Filament\Clusters\Cluster;

class ProfileSettingsCluster extends Cluster
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-squares-2x2';
}
