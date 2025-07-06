<?php

namespace App\Filament\Clusters\Laporan\Resources\LaporanResource\Widgets;

use Filament\Widgets\LineChartWidget;
use App\Models\Pesanan;
use Carbon\Carbon;

class LaporanPenjualanMingguanChart extends LineChartWidget
{
    protected static ?string $heading = 'Transaksi Mingguan (Minggu 1 - 4)';

    protected function getData(): array
    {
        $now = now();
        $startOfMonth = $now->copy()->startOfMonth();
        $endOfMonth = $now->copy()->endOfMonth();

        $labels = [];
        $data = [];

        $currentDate = $startOfMonth->copy();
        $weekCounter = 1;

        while ($currentDate->lte($endOfMonth)) {
            if ($weekCounter > 4) {
                break;
            }

            $startOfWeek = $currentDate->copy()->startOfWeek(Carbon::MONDAY);
            $endOfWeek = $currentDate->copy()->endOfWeek(Carbon::SUNDAY);

            if ($endOfWeek->gt($endOfMonth)) {
                $endOfWeek = $endOfMonth->copy();
            }

            $transaksiMinggu = Pesanan::whereBetween('tanggalPemesanan', [
                $startOfWeek->toDateString(),
                $endOfWeek->toDateString()
            ])->count();

            $labels[] = "Minggu ke-$weekCounter";
            $data[] = $transaksiMinggu;

            $currentDate = $endOfWeek->addDay();
            $weekCounter++;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Jumlah Transaksi per Minggu',
                    'data' => $data,
                    'borderColor' => 'rgba(75, 192, 192, 1)',
                    'backgroundColor' => 'rgba(75, 192, 192, 0.2)',
                ],
            ],
            'labels' => $labels,
        ];
    }
}
