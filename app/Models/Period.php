<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Period extends Model
{
    use HasFactory;

    protected $fillable = [
        'group_id',
        'month',
        'year',
        'due_amount',
        'status',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'month' => 'integer',
            'year' => 'integer',
            'due_amount' => 'decimal:2',
        ];
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function incomes(): HasMany
    {
        return $this->hasMany(Income::class);
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }

    public function getEffectiveDueAmountAttribute(): float
    {
        return (float) ($this->due_amount ?? $this->group?->default_due_amount ?? 0);
    }

    public function getTotalIncomeAttribute(): float
    {
        return (float) $this->incomes()->sum('nominal');
    }

    public function getTotalExpenseAttribute(): float
    {
        return (float) $this->expenses()->sum('nominal');
    }

    public function getBalanceAttribute(): float
    {
        return $this->total_income - $this->total_expense;
    }

    public function getPeriodNameAttribute(): string
    {
        $months = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ];

        return ($months[$this->month] ?? (string) $this->month).' '.$this->year;
    }
}
