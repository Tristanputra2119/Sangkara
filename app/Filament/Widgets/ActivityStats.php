<?php

namespace App\Filament\Widgets;

use App\Models\Meeting;
use App\Models\Report;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ActivityStats extends StatsOverviewWidget
{
    protected static ?int $sort = 2;

    protected function getStats(): array
    {
        $totalReports     = Report::count();
        $reportsBulanIni  = Report::whereMonth('created_at', now()->month)
                                  ->whereYear('created_at', now()->year)
                                  ->count();
        $laporanSelesai   = Report::where('status', 'finalized')->count();
        $totalRapat       = Meeting::count();

        return [
            Stat::make('Total Laporan', $totalReports)
                ->description('Semua laporan yang dibuat')
                ->descriptionIcon('heroicon-m-document-text')
                ->color('primary'),
            Stat::make('Laporan Bulan Ini', $reportsBulanIni)
                ->description('Dibuat pada ' . now()->translatedFormat('F Y'))
                ->descriptionIcon('heroicon-m-calendar-days')
                ->color('info'),
            Stat::make('Laporan Selesai', $laporanSelesai)
                ->description('Status: Selesai / Finalized')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),
            Stat::make('Total Rapat', $totalRapat)
                ->description('Semua rapat yang tercatat')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('warning'),
        ];
    }
}
