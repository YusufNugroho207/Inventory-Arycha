<?php

namespace App\Filament\Clusters\Laporan\Resources\LaporanResource\Widgets;

use App\Models\Pesanan;
use App\Models\DetailPesanan;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Card;
use Carbon\Carbon;

class laporanPenjualanOverview extends StatsOverviewWidget
{
    protected function getCards(): array
    {
        $now = Carbon::now();
        $weekOfMonth = ceil($now->day / 7);
        $bulanIni = request()->get('bulan', now()->format('m'));
        $tahunIni = request()->get('tahun', now()->format('Y'));
        $namaBulan = Carbon::create()->month($bulanIni)->locale('id')->translatedFormat('F');

        $barangTerlaris = DetailPesanan::whereHas('pesanan', function ($query) use ($bulanIni, $tahunIni) {
            $query->whereMonth('tanggalPemesanan', $bulanIni)
                ->whereYear('tanggalPemesanan', $tahunIni);
        })
            ->select('barang_id')
            ->selectRaw('SUM(jumlah) as total')
            ->groupBy('barang_id')
            ->orderByDesc('total')
            ->with('barang')
            ->first();

        $pendapatanMinggu = Pesanan::whereBetween('tanggalPemesanan', [
            $now->copy()->startOfWeek(),
            $now->copy()->endOfWeek(),
        ])->sum('totalHarga');

        $pendapatanBulan = Pesanan::whereMonth('tanggalPemesanan', $now->month)
            ->whereYear('tanggalPemesanan', $now->year)
            ->sum('totalHarga');

        return [
            Card::make('Barang Terlaris', optional(optional($barangTerlaris)->barang)->nama ?? '-')
                ->description("Bulan $namaBulan $tahunIni")
                ->icon('heroicon-o-archive-box')
                ->chart([17, 16, 14, 15, 14, 13, 12])
                ->color('primary'),

            Card::make('Pendapatan Minggu Ini', 'Rp ' . number_format($pendapatanMinggu, 0, ',', '.'))
                ->description("Minggu ke-$weekOfMonth bulan $namaBulan")
                ->icon('heroicon-o-calendar-days')
                ->chart([12, 7, 12, 9, 10, 4, 15])
                ->color('success'),

            Card::make('Pendapatan Bulan Ini', 'Rp ' . number_format($pendapatanBulan, 0, ',', '.'))
                ->description("Bulan $namaBulan " . $now->year)
                ->icon('heroicon-o-calendar')
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->color('info'),
        ];
    }
}
