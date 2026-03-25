<?php

namespace App\Exports;

use App\Models\Transfer;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TransfersExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $filters;

    public function __construct($filters = [])
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $query = Transfer::with(['sender', 'receiver']);

        if (!empty($this->filters['status'])) {
            $query->where('status', $this->filters['status']);
        }

        if (!empty($this->filters['sender_id'])) {
            $query->where('sender_id', $this->filters['sender_id']);
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
            'Transfer ID',
            'Sender',
            'Receiver',
            'Purpose',
            'Expected Delivery',
            'Actual Delivery',
            'Status',
            'Cost',
            'Currency',
            'Created At'
        ];
    }

    public function map($transfer): array
    {
        return [
            $transfer->transfer_id,
            $transfer->sender->name,
            $transfer->receiver->name ?? $transfer->receiver_name,
            $transfer->purpose,
            $transfer->expected_delivery_time->format('Y-m-d H:i:s'),
            $transfer->actual_delivery_time ? $transfer->actual_delivery_time->format('Y-m-d H:i:s') : 'Not delivered',
            ucfirst($transfer->status),
            $transfer->cost ? number_format($transfer->cost, 2) : 'N/A',
            $transfer->currency ?? 'USD',
            $transfer->created_at->format('Y-m-d H:i:s'),
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}