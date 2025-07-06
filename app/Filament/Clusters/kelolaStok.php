<?php

namespace App\Filament\Clusters;

use Filament\Clusters\Cluster;

class kelolaStok extends Cluster
{
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document';

    protected static ?string $activeNavigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?string $navigationLabel = 'Kelola Stok';

    protected static ?string $navigationGroup = 'Manajemen Barang';
}
