<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\Period;
use Illuminate\View\View;

class PublicKasController extends Controller
{
    public function show(string $slug, ?int $year = null, ?int $month = null): View
    {
        $group = Group::where('slug', $slug)->first();

        // If group does not exist or is not public, abort with 404
        if (! $group || ! $group->is_public) {
            abort(404, 'Halaman publik kas ini tidak ditemukan atau bersifat privat.');
        }

        // Only closed periods are visible to the public
        $closedPeriods = Period::where('group_id', $group->id)
            ->where('status', 'closed')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get();

        $selectedPeriod = null;

        if ($year && $month) {
            $selectedPeriod = $closedPeriods->first(function ($p) use ($year, $month) {
                return $p->year === (int) $year && $p->month === (int) $month;
            });

            // If requested period is not closed or not found, 404
            if (! $selectedPeriod) {
                abort(404, 'Periode kas yang diminta belum ditutup atau tidak tersedia.');
            }
        } else {
            // Default to the latest closed period
            $selectedPeriod = $closedPeriods->first();
        }

        if ($selectedPeriod) {
            $selectedPeriod->load([
                'incomes.member',
                'expenses.category',
                'expenses.attachments',
            ]);
        }

        return view('public.show', compact('group', 'closedPeriods', 'selectedPeriod', 'year', 'month'));
    }
}
