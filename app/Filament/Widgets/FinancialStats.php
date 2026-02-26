<?php

namespace App\Filament\Widgets;

use App\Services\FinancialService;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class FinancialStats extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $financialService = app(FinancialService::class);
        $income = $financialService->getTotalIncome();
        $expense = $financialService->getTotalExpense();
        $balance = $financialService->getCurrentBalance();

        $formatIdr = function ($amount) {
            return 'Rp ' . number_format($amount, 0, ',', '.');
        };

        return [
            Stat::make('Total Pemasukan', $formatIdr($income))
                ->description('Total pemasukan yang tercatat')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),
            Stat::make('Total Pengeluaran', $formatIdr($expense))
                ->description('Total pengeluaran yang tercatat')
                ->descriptionIcon('heroicon-m-arrow-trending-down')
                ->color('danger'),
            Stat::make('Saldo Saat Ini', $formatIdr($balance))
                ->description($balance >= 0 ? 'Keuangan dalam kondisi baik' : 'Saldo negatif, perlu perhatian')
                ->descriptionIcon($balance >= 0 ? 'heroicon-m-scale' : 'heroicon-m-exclamation-triangle')
                ->color($balance >= 0 ? 'primary' : 'warning'),
        ];
    }
}
