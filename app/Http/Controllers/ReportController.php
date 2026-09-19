<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\Period;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        $groups = Group::orderBy('name')->get();
        $selectedGroupId = $request->filled('group_id') ? (int) $request->group_id : $groups->first()?->id;

        $periods = Period::where('group_id', $selectedGroupId)
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get();

        $selectedPeriodId = $request->filled('period_id') ? (int) $request->period_id : $periods->first()?->id;
        $period = null;

        if ($selectedPeriodId) {
            $period = Period::with(['group', 'incomes.member', 'expenses.category', 'expenses.attachments'])
                ->find($selectedPeriodId);
        }

        return view('reports.index', compact('groups', 'periods', 'selectedGroupId', 'selectedPeriodId', 'period'));
    }

    public function exportExcel(Period $period): StreamedResponse
    {
        $period->load(['group', 'incomes.member', 'expenses.category']);

        $filename = 'Laporan_Kas_'.str_replace(' ', '_', $period->group->name).'_'.$period->year.'_'.sprintf('%02d', $period->month).'.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        return response()->stream(function () use ($period) {
            $handle = fopen('php://output', 'w');
            // Write UTF-8 BOM so Excel opens it with correct encoding
            fwrite($handle, "\xEF\xBB\xBF");

            // Title
            fputcsv($handle, ['LAPORAN KAS BULANAN', $period->group->name]);
            fputcsv($handle, ['Periode', $period->period_name]);
            fputcsv($handle, ['Status', strtoupper($period->status)]);
            fputcsv($handle, []);

            // 1. PEMASUKAN
            fputcsv($handle, ['DAFTAR PEMASUKAN (IURAN ANGGOTA)']);
            fputcsv($handle, ['No', 'Tanggal', 'Nama Anggota', 'Nominal (Rp)', 'Keterangan']);

            $totalIncome = 0;
            $no = 1;
            foreach ($period->incomes as $inc) {
                $totalIncome += $inc->nominal;
                fputcsv($handle, [
                    $no++,
                    $inc->transaction_date->format('d/m/Y'),
                    $inc->member->name ?? '-',
                    number_format($inc->nominal, 0, ',', '.'),
                    $inc->note ?? '',
                ]);
            }
            fputcsv($handle, ['', '', 'TOTAL PEMASUKAN', number_format($totalIncome, 0, ',', '.')]);
            fputcsv($handle, []);

            // 2. PENGELUARAN
            fputcsv($handle, ['DAFTAR PENGELUARAN']);
            fputcsv($handle, ['No', 'Tanggal', 'Keperluan / Barang', 'Kategori', 'Nominal (Rp)', 'Keterangan']);

            $totalExpense = 0;
            $no = 1;
            foreach ($period->expenses as $exp) {
                $totalExpense += $exp->nominal;
                fputcsv($handle, [
                    $no++,
                    $exp->transaction_date->format('d/m/Y'),
                    $exp->item_name,
                    $exp->category->name ?? 'Umum',
                    number_format($exp->nominal, 0, ',', '.'),
                    $exp->note ?? '',
                ]);
            }
            fputcsv($handle, ['', '', '', 'TOTAL PENGELUARAN', number_format($totalExpense, 0, ',', '.')]);
            fputcsv($handle, []);

            // 3. RINGKASAN SALDO
            $balance = $totalIncome - $totalExpense;
            fputcsv($handle, ['RINGKASAN SALDO PERIODE INI']);
            fputcsv($handle, ['Total Pemasukan', number_format($totalIncome, 0, ',', '.')]);
            fputcsv($handle, ['Total Pengeluaran', number_format($totalExpense, 0, ',', '.')]);
            fputcsv($handle, ['SISA / SALDO AKHIR', number_format($balance, 0, ',', '.')]);

            fclose($handle);
        }, 200, $headers);
    }

    public function printPdf(Period $period): View
    {
        $period->load(['group', 'incomes.member', 'expenses.category', 'expenses.attachments']);

        return view('reports.print', compact('period'));
    }
}
