<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class StatsExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $period;
    
    public function __construct($period = 'monthly')
    {
        $this->period = $period;
    }
    
    public function collection()
    {
        $stats = [];
        
        switch ($this->period) {
            case 'daily':
                $dateFormat = '%Y-%m-%d';
                $days = 7;
                break;
            case 'weekly':
                $dateFormat = '%Y-%W';
                $days = 30;
                break;
            case 'yearly':
                $dateFormat = '%Y';
                $days = 365 * 3;
                break;
            default: // monthly
                $dateFormat = '%Y-%m';
                $days = 90;
        }
        
        $endDate = Carbon::now();
        $startDate = Carbon::now()->subDays($days);
        
        // Get user statistics
        $userStats = DB::table('users')
            ->select(
                DB::raw("DATE_FORMAT(created_at, '{$dateFormat}') as period"),
                DB::raw('COUNT(*) as new_users')
            )
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('period')
            ->orderBy('period')
            ->get();
            
        // Get file statistics
        $fileStats = DB::table('files')
            ->select(
                DB::raw("DATE_FORMAT(created_at, '{$dateFormat}') as period"),
                DB::raw('COUNT(*) as new_files'),
                DB::raw('SUM(size) as total_size')
            )
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('period')
            ->orderBy('period')
            ->get();
            
        // Get transfer statistics
        $transferStats = DB::table('transfers')
            ->select(
                DB::raw("DATE_FORMAT(created_at, '{$dateFormat}') as period"),
                DB::raw('COUNT(*) as new_transfers'),
                DB::raw("SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed_transfers")
            )
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('period')
            ->orderBy('period')
            ->get();
        
        // Combine stats
        $periods = collect([])
            ->merge($userStats->pluck('period'))
            ->merge($fileStats->pluck('period'))
            ->merge($transferStats->pluck('period'))
            ->unique()
            ->sort();
            
        foreach ($periods as $period) {
            $userStat = $userStats->where('period', $period)->first();
            $fileStat = $fileStats->where('period', $period)->first();
            $transferStat = $transferStats->where('period', $period)->first();
            
            $stats[] = [
                'period' => $period,
                'new_users' => $userStat->new_users ?? 0,
                'new_files' => $fileStat->new_files ?? 0,
                'total_size_mb' => isset($fileStat->total_size) ? round($fileStat->total_size / 1024 / 1024, 2) : 0,
                'new_transfers' => $transferStat->new_transfers ?? 0,
                'completed_transfers' => $transferStat->completed_transfers ?? 0,
            ];
        }
        
        return collect($stats);
    }
    
    public function headings(): array
    {
        return [
            'Period',
            'New Users',
            'New Files',
            'Total Size (MB)',
            'New Transfers',
            'Completed Transfers',
        ];
    }
    
    public function map($stat): array
    {
        return [
            $stat['period'],
            $stat['new_users'],
            $stat['new_files'],
            $stat['total_size_mb'],
            $stat['new_transfers'],
            $stat['completed_transfers'],
        ];
    }
    
    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}