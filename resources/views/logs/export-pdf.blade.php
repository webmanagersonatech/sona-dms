{{-- resources/views/logs/export-pdf.blade.php --}}
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Activity Logs Report</title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
        }

        h1 {
            text-align: center;
            color: #667eea;
            margin-bottom: 30px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th {
            background: #667eea;
            color: white;
            padding: 10px;
            text-align: left;
            font-size: 11px;
        }

        td {
            padding: 8px;
            border-bottom: 1px solid #ddd;
            font-size: 10px;
        }

        tr:nth-child(even) {
            background: #f8f9fa;
        }

        .footer {
            text-align: center;
            color: #666;
            font-size: 10px;
            margin-top: 30px;
        }

        .header-info {
            margin-bottom: 20px;
            padding: 10px;
            background: #f0f0f0;
            border-radius: 5px;
        }
    </style>
</head>

<body>
    <h1>{{ config('app.name') }} - Activity Logs Report</h1>

    <div class="header-info">
        <p><strong>Generated:</strong> {{ now()->format('F d, Y H:i:s') }}</p>
        <p><strong>Total Records:</strong> {{ $logs->count() }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Date/Time</th>
                <th>User</th>
                <th>Action</th>
                <th>Module</th>
                <th>Description</th>
                <th>IP Address</th>
                <th>Device</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($logs as $log)
                <tr>
                    <td>{{ $log->created_at->format('Y-m-d H:i:s') }}</td>
                    <td>{{ $log->user->name ?? 'System' }}</td>
                    <td>{{ ucfirst(str_replace('_', ' ', $log->action)) }}</td>
                    <td>{{ ucfirst($log->module) }}</td>
                    <td>{{ $log->description }}</td>
                    <td>{{ $log->ip_address ?? 'N/A' }}</td>
                    <td>
                        @if ($log->device_type)
                            {{ ucfirst($log->device_type) }}
                            @if ($log->browser)
                                - {{ $log->browser }}
                            @endif
                        @else
                            N/A
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>This report is generated automatically by {{ config('app.name') }}. For any inquiries, please contact the
            system administrator.</p>
    </div>
</body>

</html>
