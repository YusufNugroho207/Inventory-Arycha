<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Card;
use App\Models\DetailPesanan;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class PendapatanTahunanOverview extends BaseWidget
{
    protected function getCards(): array
    {
        $now = Carbon::now();
        $tahunIni = $now->year;
        $bulanIni = $now->month;

        $totalPendapatanTahun = DetailPesanan::whereHas('pesanan', function ($query) use ($tahunIni) {
            $query->whereYear('tanggalPemesanan', $tahunIni);
        })->sum('subtotal');

        $totalPesananBulan = DetailPesanan::whereHas('pesanan', function ($query) use ($tahunIni, $bulanIni) {
            $query->whereYear('tanggalPemesanan', $tahunIni)
                ->whereMonth('tanggalPemesanan', $bulanIni);
        })->distinct('pesanan_id')->count('pesanan_id');

        $totalPesananTahun = DetailPesanan::whereHas('pesanan', function ($query) use ($tahunIni) {
            $query->whereYear('tanggalPemesanan', $tahunIni);
        })->distinct('pesanan_id')->count('pesanan_id');

        $bulanBerjalan = max($bulanIni, 1);
        $rataRataPendapatanBulan = $totalPendapatanTahun / $bulanBerjalan;

        return [
            Card::make('Total Pendapatan Tahun Ini', 'Rp ' . number_format($totalPendapatanTahun, 0, ',', '.'))
                ->description("Tahun $tahunIni")
                ->descriptionColor('success')
                ->icon('heroicon-o-banknotes')
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->color('success'),

            Card::make('Avg. Pendapatan Bulanan', 'Rp ' . number_format($rataRataPendapatanBulan, 0, ',', '.'))
                ->description("Sampai bulan ke-$bulanIni")
                ->descriptionColor('info')
                ->icon('heroicon-o-chart-bar')
                ->chart([17, 16, 14, 15, 14, 13, 12])
                ->color('info'),

            Card::make('Total Pesanan Tahun Ini', $totalPesananTahun)
                ->description("Tahun $tahunIni")
                ->descriptionColor('warning')
                ->icon('heroicon-o-clipboard-document-list')
                ->chart([15, 4, 10, 2, 12, 4, 12])
                ->color('warning'),

            Card::make('Total Pesanan Bulan Ini', $totalPesananBulan)
                ->description("Bulan " . \Carbon\Carbon::create()->month($bulanIni)->locale('id')->translatedFormat('F'))
                ->descriptionColor('primary')
                ->icon('heroicon-o-shopping-cart')
                ->chart([12, 7, 12, 9, 10, 4, 15])
                ->color('primary'),

        ];
    }

    public static function canView(): bool
    {
        return Auth::user()?->hasRole('Manajer');
    }
}
