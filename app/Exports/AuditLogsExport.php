<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use App\Models\AuditLog;
use Carbon\Carbon;

class AuditLogsExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $startDate;
    protected $endDate;
    
    public function __construct($startDate = null, $endDate = null)
    {
        $this->startDate = $startDate ? Carbon::parse($startDate) : Carbon::now()->subMonth();
        $this->endDate = $endDate ? Carbon::parse($endDate) : Carbon::now();
    }
    
    public function collection()
    {
        return AuditLog::with('user')
            ->whereBetween('created_at', [$this->startDate, $this->endDate])
            ->orderBy('created_at', 'desc')
            ->get();
    }
    
    public function headings(): array
    {
        return [
            'ID',
            'Timestamp',
            'User',
            'Action',
            'Description',
            'IP Address',
            'User Agent',
        ];
    }
    
    public function map($log): array
    {
        return [
            $log->id,
            $log->created_at->format('Y-m-d H:i:s'),
            $log->user ? $log->user->name : 'System',
            $log->action,
            $log->description,
            $log->ip_address,
            $log->user_agent,
        ];
    }
    
    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}