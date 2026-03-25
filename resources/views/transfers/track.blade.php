{{-- resources/views/transfers/track.blade.php --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Track Transfer - {{ $transfer->transfer_id }}</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

    <style>
        body {
            background: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .tracking-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .tracking-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }

        .tracking-body {
            padding: 40px;
            background: white;
        }

        .status-badge {
            padding: 10px 20px;
            border-radius: 50px;
            font-weight: 600;
            display: inline-block;
        }

        .timeline {
            position: relative;
            padding: 20px 0;
        }

        .timeline-item {
            position: relative;
            padding-left: 60px;
            margin-bottom: 30px;
        }

        .timeline-badge {
            position: absolute;
            left: 0;
            top: 0;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #e9ecef;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            z-index: 1;
        }

        .timeline-badge.completed {
            background: #28a745;
        }

        .timeline-badge.active {
            background: #007bff;
        }

        .timeline-badge.pending {
            background: #ffc107;
        }

        .timeline-content {
            padding-bottom: 20px;
        }

        .timeline:before {
            content: '';
            position: absolute;
            left: 20px;
            top: 0;
            bottom: 0;
            width: 2px;
            background: #dee2e6;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-top: 30px;
        }

        .info-item {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
        }

        .info-label {
            color: #6c757d;
            font-size: 0.9rem;
            margin-bottom: 5px;
        }

        .info-value {
            font-size: 1.2rem;
            font-weight: 600;
            color: #343a40;
        }

        @media print {
            .no-print {
                display: none;
            }

            body {
                background: white;
            }
        }
    </style>
</head>

<body>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="tracking-card">
                    <div class="tracking-header">
                        <h2><i class="bi bi-truck"></i> Transfer Tracking</h2>
                        <h1 class="display-6 mt-3">{{ $transfer->transfer_id }}</h1>
                        <div class="mt-3">
                            <span class="status-badge bg-white text-dark">
                                Status:
                                @php
                                    $statusColors = [
                                        'pending' => 'warning',
                                        'in_transit' => 'info',
                                        'delivered' => 'success',
                                        'cancelled' => 'danger',
                                    ];
                                @endphp
                                <span class="badge bg-{{ $statusColors[$transfer->status] ?? 'secondary' }} ms-2">
                                    {{ ucfirst(str_replace('_', ' ', $transfer->status)) }}
                                </span>
                            </span>
                        </div>
                    </div>

                    <div class="tracking-body">
                        <!-- Timeline -->
                        <div class="timeline">
                            <div class="timeline-item">
                                <div class="timeline-badge completed">
                                    <i class="bi bi-plus-circle"></i>
                                </div>
                                <div class="timeline-content">
                                    <h6>Transfer Created</h6>
                                    <p class="mb-0 text-muted">{{ $transfer->created_at->format('F d, Y H:i:s') }}</p>
                                    <small>by {{ $transfer->sender->name }}</small>
                                </div>
                            </div>

                            @if ($transfer->courier_name || $transfer->tracking_number)
                                <div class="timeline-item">
                                    <div
                                        class="timeline-badge {{ $transfer->status == 'in_transit' ? 'active' : ($transfer->status == 'delivered' ? 'completed' : 'pending') }}">
                                        <i class="bi bi-truck"></i>
                                    </div>
                                    <div class="timeline-content">
                                        <h6>In Transit</h6>
                                        @if ($transfer->courier_name)
                                            <p class="mb-0">Courier: {{ $transfer->courier_name }}</p>
                                        @endif
                                        @if ($transfer->tracking_number)
                                            <p class="mb-0">Tracking #: {{ $transfer->tracking_number }}</p>
                                        @endif
                                    </div>
                                </div>
                            @endif

                            <div class="timeline-item">
                                <div
                                    class="timeline-badge {{ $transfer->status == 'delivered' ? 'completed' : 'pending' }}">
                                    <i class="bi bi-check-circle"></i>
                                </div>
                                <div class="timeline-content">
                                    <h6>Delivery</h6>
                                    @if ($transfer->status == 'delivered')
                                        <p class="mb-0">{{ $transfer->actual_delivery_time->format('F d, Y H:i:s') }}
                                        </p>
                                        @if ($transfer->received_by)
                                            <small>Received by: {{ $transfer->received_by }}</small>
                                        @endif
                                    @else
                                        <p class="text-muted mb-0">
                                            Expected: {{ $transfer->expected_delivery_time->format('F d, Y H:i') }}
                                        </p>
                                    @endif
                                </div>
                            </div>

                            @if ($transfer->status == 'cancelled')
                                <div class="timeline-item">
                                    <div class="timeline-badge bg-danger">
                                        <i class="bi bi-x-circle"></i>
                                    </div>
                                    <div class="timeline-content">
                                        <h6 class="text-danger">Cancelled</h6>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <!-- Information Grid -->
                        <div class="info-grid">
                            <div class="info-item">
                                <div class="info-label">Sender</div>
                                <div class="info-value">{{ $transfer->sender->name }}</div>
                                <small>{{ $transfer->sender->email }}</small>
                            </div>
                            <div class="info-item">
                                <div class="info-label">Receiver</div>
                                <div class="info-value">
                                    {{ $transfer->receiver->name ?? $transfer->receiver_name }}
                                </div>
                                <small>{{ $transfer->receiver->email ?? $transfer->receiver_email }}</small>
                            </div>
                            <div class="info-item">
                                <div class="info-label">Purpose</div>
                                <div class="info-value">{{ $transfer->purpose }}</div>
                            </div>
                            @if ($transfer->delivery_location)
                                <div class="info-item">
                                    <div class="info-label">Delivery Location</div>
                                    <div class="info-value">{{ $transfer->delivery_location }}</div>
                                </div>
                            @endif
                        </div>

                        @if ($transfer->description)
                            <div class="mt-4 p-3 bg-light rounded">
                                <h6>Description</h6>
                                <p class="mb-0">{{ $transfer->description }}</p>
                            </div>
                        @endif

                        @if ($transfer->notes)
                            <div class="mt-4 p-3 bg-light rounded">
                                <h6>Delivery Notes</h6>
                                <p class="mb-0">{{ $transfer->notes }}</p>
                            </div>
                        @endif

                        <!-- Action Buttons -->
                        <div class="text-center mt-4 no-print">
                            <button onclick="window.print()" class="btn btn-primary">
                                <i class="bi bi-printer"></i> Print Tracking
                            </button>
                            <a href="{{ url()->previous() }}" class="btn btn-secondary">
                                <i class="bi bi-arrow-left"></i> Go Back
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
