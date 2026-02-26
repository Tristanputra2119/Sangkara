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
            Stat::make('Total Income', $formatIdr($income))
                ->description('Total income recorded')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),
            Stat::make('Total Expense', $formatIdr($expense))
                ->description('Total expenses recorded')
                ->descriptionIcon('heroicon-m-arrow-trending-down')
                ->color('danger'),
            Stat::make('Current Balance', $formatIdr($balance))
                ->description('Maintained in Redis cache (10m)')
                ->descriptionIcon($balance >= 0 ? 'heroicon-m-scale' : 'heroicon-m-exclamation-triangle')
                ->color($balance >= 0 ? 'primary' : 'warning'),
        ];
    }
}
