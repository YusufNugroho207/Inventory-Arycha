<?php

namespace App\Filament\Clusters;

use Filament\Clusters\Cluster;

class Laporan extends Cluster
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $activeNavigationIcon = 'heroicon-o-document-magnifying-glass';

    protected static ?string $navigationLabel = 'Laporan';

    protected static ?string $navigationGroup = 'Laporan';

    protected static ?int $navigationSort = 6;
}
