<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\DetailPesanan;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class BarangTerlarisChart extends ChartWidget
{
    protected static ?string $heading = 'Barang Terlaris Bulan Ini';

    protected static ?int $sort = 0;

    protected static ?string $maxHeight = '254px';

    protected function getData(): array
    {
        $bulan = Carbon::now()->month;
        $tahun = Carbon::now()->year;

        $terlaris = DetailPesanan::select('barang_id', DB::raw('SUM(jumlah) as total_terjual'))
            ->whereMonth('created_at', $bulan)
            ->whereYear('created_at', $tahun)
            ->groupBy('barang_id')
            ->orderByDesc('total_terjual')
            ->with('barang')
            ->take(5)
            ->get();

        $colors = [
            '#6366F1',
            '#10B981',
            '#F59E0B',
            '#EF4444',
            '#3B82F6',
        ];

        return [
            'datasets' => [
                [
                    'label' => 'Jumlah Terjual',
                    'data' => $terlaris->pluck('total_terjual')->toArray(),
                    'backgroundColor' => array_slice($colors, 0, $terlaris->count()),
                ],
            ],
            'labels' => $terlaris->pluck('barang.nama')->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }

    public static function canView(): bool
    {
        return Auth::user()?->hasRole('Manajer');
    }
}
