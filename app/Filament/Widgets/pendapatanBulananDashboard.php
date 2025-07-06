<?php

namespace App\Filament\Widgets;

use App\Models\Pesanan;
use Carbon\Carbon;
use Filament\Widgets\LineChartWidget;
use Illuminate\Support\Facades\Auth;

class PendapatanBulananDashboard extends LineChartWidget
{
    protected static ?string $heading = 'Pendapatan Bulanan';

    protected static ?int $sort = 0;

    protected function getData(): array
    {
        $data = Pesanan::with('detailPesanan')
            ->whereYear('tanggalPemesanan', now()->year)
            ->get()
            ->groupBy(fn ($item) => Carbon::parse($item->tanggalPemesanan)->month);

        $labels = [];
        $values = [];

        for ($i = 1; $i <= 12; $i++) {
            $labels[] = Carbon::create()->month($i)->translatedFormat('F');

            $bulanan = $data[$i] ?? collect();
            $total = 0;

            foreach ($bulanan as $pesanan) {
                $total += $pesanan->detailPesanan->sum('subtotal');
            }

            $values[] = $total;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Pendapatan',
                    'data' => $values,
                    'backgroundColor' => '#3b82f6',
                    'borderColor' => '#3b82f6',
                ],
            ],
            'labels' => $labels,
        ];
    }

    public static function canView(): bool
    {
        return Auth::user()?->hasRole('Manajer');
    }
}
