<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Group extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'default_due_amount',
        'is_public',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'default_due_amount' => 'integer',
            'is_public' => 'boolean',
        ];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function members(): HasMany
    {
        return $this->hasMany(Member::class);
    }

    public function periods(): HasMany
    {
        return $this->hasMany(Period::class)->orderBy('year', 'desc')->orderBy('month', 'desc');
    }

    public function incomes(): HasManyThrough
    {
        return $this->hasManyThrough(Income::class, Period::class);
    }

    public function expenses(): HasManyThrough
    {
        return $this->hasManyThrough(Expense::class, Period::class);
    }

    public function getBalanceAttribute(): float
    {
        $totalIncome = (float) $this->incomes()->sum('nominal');
        $totalExpense = (float) $this->expenses()->sum('nominal');

        return $totalIncome - $totalExpense;
    }
}
