<?php

namespace App\Exports;

use App\Models\File;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class FilesExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $filters;

    public function __construct($filters = [])
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $query = File::with(['owner', 'department']);

        if (!empty($this->filters['department_id'])) {
            $query->where('department_id', $this->filters['department_id']);
        }

        if (!empty($this->filters['owner_id'])) {
            $query->where('owner_id', $this->filters['owner_id']);
        }

        if (!empty($this->filters['date_from'])) {
            $query->whereDate('created_at', '>=', $this->filters['date_from']);
        }

        if (!empty($this->filters['date_to'])) {
            $query->whereDate('created_at', '<=', $this->filters['date_to']);
        }

        return $query->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Name',
            'Type',
            'Size',
            'Owner',
            'Department',
            'Downloads',
            'Views',
            'Encrypted',
            'Created At'
        ];
    }

    public function map($file): array
    {
        return [
            $file->id,
            $file->name,
            strtoupper($file->extension),
            $this->formatBytes($file->file_size),
            $file->owner->name,
            $file->department->name ?? 'N/A',
            $file->download_count,
            $file->view_count,
            $file->is_encrypted ? 'Yes' : 'No',
            $file->created_at->format('Y-m-d H:i:s'),
        ];
    }

    private function formatBytes($bytes)
    {
        if ($bytes === 0) return '0 Bytes';
        $k = 1024;
        $sizes = ['Bytes', 'KB', 'MB', 'GB'];
        $i = floor(log($bytes) / log($k));
        return round($bytes / pow($k, $i), 2) . ' ' . $sizes[$i];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}