<?php

namespace App\Filament\Clusters;

use Filament\Clusters\Cluster;

class Barangs extends Cluster
{
    protected static ?string $navigationIcon = 'heroicon-o-folder';

    protected static ?string $activeNavigationIcon = 'heroicon-o-folder-open';

    protected static ?string $navigationLabel = 'Barang';

    protected static ?string $navigationGroup = 'Manajemen Barang';
}
