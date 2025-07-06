<?php

namespace App\Filament\Clusters\Laporan\Resources\LaporanResource\Widgets;

use Filament\Widgets\LineChartWidget;
use App\Models\Pesanan;
use Carbon\Carbon;

class LaporanPenjualanChart extends LineChartWidget
{
    protected static ?string $heading = 'Transaksi Harian (Senin - Minggu)';

    protected function getData(): array
    {
        $labels = [];
        $data = [];

        $startOfWeek = now()->startOfWeek(Carbon::MONDAY);

        for ($i = 0; $i < 7; $i++) {
            $date = $startOfWeek->copy()->addDays($i);
            $hari = $date->locale('id')->isoFormat('dddd');
            $labels[] = ucfirst($hari);
            $data[] = Pesanan::whereDate('tanggalPemesanan', $date->format('Y-m-d'))->count();
        }

        return [
            'datasets' => [
                [
                    'label' => 'Jumlah Transaksi',
                    'data' => $data,
                    'borderColor' => 'rgba(54, 162, 235, 1)',
                    'backgroundColor' => 'rgba(54, 162, 235, 0.2)',
                ],
            ],
            'labels' => $labels,
        ];
    }
}
