<?php

namespace App\Services;

use App\Models\Transaction;
use Illuminate\Support\Facades\Cache;

class FinancialService
{
    /**
     * Get the total income.
     *
     * @return int
     */
    public function getTotalIncome(): int
    {
        return Transaction::whereHas('category', function ($query) {
            $query->where('type', 'income');
        })->sum('amount');
    }

    /**
     * Get the total expense.
     *
     * @return int
     */
    public function getTotalExpense(): int
    {
        return Transaction::whereHas('category', function ($query) {
            $query->where('type', 'expense');
        })->sum('amount');
    }

    /**
     * Get the current balance.
     * Uses Redis cache for 10 minutes (600 seconds).
     *
     * @return int
     */
    public function getCurrentBalance(): int
    {
        return Cache::store('redis')->remember('financial_current_balance', 600, function () {
            return $this->getTotalIncome() - $this->getTotalExpense();
        });
    }

    /**
     * Clear the cached balance. Useful for observers when a transaction is created/updated/deleted.
     *
     * @return void
     */
    public function clearBalanceCache(): void
    {
        Cache::store('redis')->forget('financial_current_balance');
    }
}
