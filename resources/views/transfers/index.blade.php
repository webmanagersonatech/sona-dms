{{-- resources/views/transfers/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Transfers')

@section('content')
    <!-- Header Actions -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">Transfer Management</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Transfers</li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('transfers.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-2"></i> New Transfer
        </a>
    </div>

    <!-- Stats Cards -->
    <div class="row g-4 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="stat-card">
                <div class="stat-info">
                    <h3>{{ number_format($stats['total'] ?? 0) }}</h3>
                    <p>Total Transfers</p>
                </div>
                <div class="stat-icon primary">
                    <i class="bi bi-arrow-left-right"></i>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="stat-card">
                <div class="stat-info">
                    <h3>{{ number_format($stats['pending'] ?? 0) }}</h3>
                    <p>Pending</p>
                </div>
                <div class="stat-icon warning">
                    <i class="bi bi-hourglass-split"></i>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="stat-card">
                <div class="stat-info">
                    <h3>{{ number_format($stats['delivered'] ?? 0) }}</h3>
                    <p>Delivered</p>
                </div>
                <div class="stat-icon success">
                    <i class="bi bi-check-circle"></i>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="stat-card">
                <div class="stat-info">
                    <h3>{{ number_format($stats['overdue'] ?? 0) }}</h3>
                    <p>Overdue</p>
                </div>
                <div class="stat-icon danger">
                    <i class="bi bi-exclamation-triangle"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Tabs -->
    <div class="card mb-4">
        <div class="card-body">
            <ul class="nav nav-tabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link {{ !request()->has('status') || request('status') == '' ? 'active' : '' }}"
                        href="{{ route('transfers.index') }}">
                        <i class="bi bi-list-ul me-1"></i> All Transfers
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request('status') == 'pending' ? 'active' : '' }}"
                        href="{{ route('transfers.index', ['status' => 'pending']) }}">
                        <i class="bi bi-hourglass-split me-1"></i> Pending
                        @if (($stats['pending'] ?? 0) > 0)
                            <span class="badge bg-warning ms-1">{{ $stats['pending'] }}</span>
                        @endif
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request('status') == 'in_transit' ? 'active' : '' }}"
                        href="{{ route('transfers.index', ['status' => 'in_transit']) }}">
                        <i class="bi bi-truck me-1"></i> In Transit
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request('status') == 'delivered' ? 'active' : '' }}"
                        href="{{ route('transfers.index', ['status' => 'delivered']) }}">
                        <i class="bi bi-check-circle me-1"></i> Delivered
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request('status') == 'cancelled' ? 'active' : '' }}"
                        href="{{ route('transfers.index', ['status' => 'cancelled']) }}">
                        <i class="bi bi-x-circle me-1"></i> Cancelled
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <!-- Transfers Table Card -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">
                @if (request('status'))
                    {{ ucfirst(str_replace('_', ' ', request('status'))) }} Transfers
                @else
                    All Transfers
                @endif
            </h5>
            <div class="btn-group">
                <button type="button" class="btn btn-sm btn-outline-primary" onclick="window.print()">
                    <i class="bi bi-printer"></i> Print
                </button>
                <button type="button" class="btn btn-sm btn-outline-success" onclick="exportTable()">
                    <i class="bi bi-download"></i> Export
                </button>
            </div>
        </div>
        <div class="card-body">
            <!-- Filters -->
            <form method="GET" action="{{ route('transfers.index') }}" class="row g-3 mb-4">
                @if (request('status'))
                    <input type="hidden" name="status" value="{{ request('status') }}">
                @endif
                <div class="col-md-3">
                    <div class="input-group">
                        <input type="text" name="search" class="form-control"
                            placeholder="Search by ID, purpose, receiver..." value="{{ request('search') }}">
                        <button class="btn btn-primary" type="submit">
                            <i class="bi bi-search"></i>
                        </button>
                    </div>
                </div>
                <div class="col-md-2">
                    <input type="date" name="date_from" class="form-control" placeholder="From"
                        value="{{ request('date_from') }}">
                </div>
                <div class="col-md-2">
                    <input type="date" name="date_to" class="form-control" placeholder="To"
                        value="{{ request('date_to') }}">
                </div>
                <div class="col-md-2">
                    <select name="sort" class="form-select">
                        <option value="desc" {{ request('sort', 'desc') == 'desc' ? 'selected' : '' }}>Newest First
                        </option>
                        <option value="asc" {{ request('sort') == 'asc' ? 'selected' : '' }}>Oldest First</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <a href="{{ route('transfers.index') }}" class="btn btn-secondary">
                        <i class="bi bi-eraser me-1"></i> Clear Filters
                    </a>
                </div>
            </form>

            <!-- Transfers Table -->
            <div class="table-responsive">
                <table class="table table-hover align-middle" id="transfers-table">
                    <thead>
                        有机
                            <th>Transfer ID</th>
                            <th>Sender</th>
                            <th>Receiver</th>
                            <th>Purpose</th>
                            <th>Expected</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transfers as $transfer)
                            <tr>
                                <td>
                                    <a href="{{ route('transfers.show', $transfer) }}"
                                        class="text-decoration-none fw-medium">
                                        <code>{{ $transfer->transfer_id }}</code>
                                    </a>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        @if ($transfer->sender->avatar)
                                            <img src="{{ Storage::url($transfer->sender->avatar) }}"
                                                alt="{{ $transfer->sender->name }}" class="rounded-circle me-2"
                                                width="32" height="32" style="object-fit: cover;">
                                        @else
                                            <div class="rounded-circle bg-info d-flex align-items-center justify-content-center me-2"
                                                style="width: 32px; height: 32px;">
                                                <span
                                                    class="text-white small">{{ strtoupper(substr($transfer->sender->name, 0, 1)) }}</span>
                                            </div>
                                        @endif
                                        <div>
                                            <span class="fw-medium">{{ $transfer->sender->name }}</span>
                                            <br>
                                            <small class="text-muted">{{ $transfer->sender->email }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @if ($transfer->receiver)
                                        <div class="d-flex align-items-center">
                                            @if ($transfer->receiver->avatar)
                                                <img src="{{ Storage::url($transfer->receiver->avatar) }}"
                                                    alt="{{ $transfer->receiver->name }}" class="rounded-circle me-2"
                                                    width="32" height="32" style="object-fit: cover;">
                                            @else
                                                <div class="rounded-circle bg-success d-flex align-items-center justify-content-center me-2"
                                                    style="width: 32px; height: 32px;">
                                                    <span
                                                        class="text-white small">{{ strtoupper(substr($transfer->receiver->name, 0, 1)) }}</span>
                                                </div>
                                            @endif
                                            <div>
                                                <span class="fw-medium">{{ $transfer->receiver->name }}</span>
                                                <br>
                                                <small class="text-muted">{{ $transfer->receiver->email }}</small>
                                            </div>
                                        </div>
                                    @else
                                        <div>
                                            <span class="fw-medium">{{ $transfer->receiver_name }}</span>
                                            <br>
                                            <small class="text-muted">{{ $transfer->receiver_email }}</small>
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <span title="{{ $transfer->purpose }}">
                                        {{ Str::limit($transfer->purpose, 30) }}
                                    </span>
                                </td>
                                <td>
                                    @if ($transfer->expected_delivery_time && method_exists($transfer->expected_delivery_time, 'format'))
                                        <span
                                            class="fw-medium">{{ $transfer->expected_delivery_time->format('M d, Y') }}</span>
                                        <br>
                                        <small
                                            class="text-muted">{{ $transfer->expected_delivery_time->format('h:i A') }}</small>
                                        @if ($transfer->isOverdue())
                                            <br>
                                            <span class="badge bg-danger mt-1">Overdue</span>
                                        @endif
                                    @else
                                        N/A
                                    @endif
                                </td>
                                <td>
                                    @php
                                        $statusColors = [
                                            'pending' => 'warning',
                                            'in_transit' => 'info',
                                            'delivered' => 'success',
                                            'cancelled' => 'danger',
                                            'failed' => 'danger',
                                        ];
                                        $statusIcons = [
                                            'pending' => 'hourglass-split',
                                            'in_transit' => 'truck',
                                            'delivered' => 'check-circle',
                                            'cancelled' => 'x-circle',
                                            'failed' => 'exclamation-circle',
                                        ];
                                    @endphp
                                    <span
                                        class="badge bg-{{ $statusColors[$transfer->status] ?? 'secondary' }} py-2 px-3">
                                        <i class="bi bi-{{ $statusIcons[$transfer->status] ?? 'question' }} me-1"></i>
                                        {{ ucfirst(str_replace('_', ' ', $transfer->status)) }}
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <a href="{{ route('transfers.show', $transfer) }}"
                                            class="btn btn-sm btn-outline-primary" title="View Details">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        @if ($transfer->status === 'pending')
                                            @if ($transfer->sender_id === Auth::id() || Auth::user()->isSuperAdmin())
                                                <button type="button" class="btn btn-sm btn-outline-danger"
                                                    onclick="cancelTransfer({{ $transfer->id }})"
                                                    title="Cancel Transfer">
                                                    <i class="bi bi-x-circle"></i>
                                                </button>
                                            @endif
                                            @if ($transfer->receiver_id === Auth::id())
                                                <button type="button" class="btn btn-sm btn-outline-success"
                                                    onclick="confirmDelivery({{ $transfer->id }})"
                                                    title="Confirm Delivery">
                                                    <i class="bi bi-check-circle"></i>
                                                </button>
                                            @endif
                                        @endif
                                        @if ($transfer->status === 'in_transit' && $transfer->sender_id === Auth::id())
                                            <button type="button" class="btn btn-sm btn-outline-info"
                                                onclick="markDelivered({{ $transfer->id }})" title="Mark as Delivered">
                                                <i class="bi bi-truck"></i>
                                            </button>
                                        @endif
                                        <a href="{{ route('transfers.track', $transfer->transfer_id) }}"
                                            class="btn btn-sm btn-outline-secondary" title="Track Transfer">
                                            <i class="bi bi-geo-alt"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <i class="bi bi-truck display-1 text-muted"></i>
                                    <h5 class="mt-3">No Transfers Found</h5>
                                    <p class="text-muted mb-3">Get started by creating your first transfer</p>
                                    <a href="{{ route('transfers.create') }}" class="btn btn-primary">
                                        <i class="bi bi-plus-circle me-2"></i> Create New Transfer
                                    </a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if ($transfers->hasPages())
                <div class="d-flex justify-content-between align-items-center mt-4">
                    <div class="text-muted">
                        Showing {{ $transfers->firstItem() }} to {{ $transfers->lastItem() }} of
                        {{ $transfers->total() }} entries
                    </div>
                    <div>
                        {{ $transfers->appends(request()->query())->links() }}
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Cancel Transfer Modal -->
    <div class="modal fade" id="cancelModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Cancel Transfer</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="" id="cancelForm">
                    @csrf
                    @method('POST')
                    <div class="modal-body">
                        <p class="text-muted mb-3">Are you sure you want to cancel this transfer?</p>
                        <div class="mb-3">
                            <label for="cancel_reason" class="form-label">Reason for cancellation (Optional)</label>
                            <textarea class="form-control" id="cancel_reason" name="reason" rows="3"
                                placeholder="Please provide a reason..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-danger">
                            <i class="bi bi-x-circle me-2"></i> Cancel Transfer
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Confirm Delivery Modal -->
    <div class="modal fade" id="confirmModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirm Delivery</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="" id="confirmForm">
                    @csrf
                    @method('POST')
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="delivery_location" class="form-label">Delivery Location <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="delivery_location" name="delivery_location"
                                placeholder="e.g., Main Office, Warehouse, etc." required>
                        </div>
                        <div class="mb-3">
                            <label for="received_by" class="form-label">Received By <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="received_by" name="received_by"
                                placeholder="Name of person who received" value="{{ Auth::user()->name }}" required>
                        </div>
                        <div class="mb-3">
                            <label for="signature" class="form-label">Digital Signature (Optional)</label>
                            <div id="signature-pad" class="signature-pad border rounded p-2 bg-light">
                                <canvas id="signature-canvas" width="400" height="150"
                                    style="width: 100%; height: auto; cursor: crosshair;"></canvas>
                                <input type="hidden" name="signature" id="signature-input">
                            </div>
                            <div class="mt-2">
                                <button type="button" class="btn btn-sm btn-outline-secondary"
                                    onclick="clearSignature()">
                                    <i class="bi bi-eraser me-1"></i> Clear Signature
                                </button>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="notes" class="form-label">Additional Notes</label>
                            <textarea class="form-control" id="notes" name="notes" rows="2"
                                placeholder="Any additional information..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-check-circle me-2"></i> Confirm Delivery
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Mark as Delivered Modal -->
    <div class="modal fade" id="deliveredModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Mark as Delivered</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="" id="deliveredForm">
                    @csrf
                    @method('POST')
                    <div class="modal-body">
                        <p class="text-muted">Mark this transfer as delivered?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-info">
                            <i class="bi bi-truck me-2"></i> Mark as Delivered
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .signature-pad {
            border: 2px dashed var(--gray-light);
            border-radius: var(--border-radius);
            background: #fff;
        }

        .signature-pad canvas {
            width: 100%;
            height: auto;
            cursor: crosshair;
        }

        [data-theme="dark"] .signature-pad {
            background: var(--dark);
            border-color: var(--gray-light);
        }

        .stat-card {
            cursor: pointer;
        }

        .nav-tabs .nav-link {
            color: var(--gray);
            border: none;
            padding: 10px 20px;
            font-weight: 500;
        }

        .nav-tabs .nav-link:hover {
            color: var(--primary);
            border: none;
        }

        .nav-tabs .nav-link.active {
            color: var(--primary);
            background: transparent;
            border-bottom: 3px solid var(--primary);
        }
        
        /* Simple table sorting styles (optional) */
        .sortable-header {
            cursor: pointer;
            user-select: none;
        }
        
        .sortable-header:hover {
            background-color: rgba(0, 0, 0, 0.05);
        }
        
        .sortable-header i {
            font-size: 0.8rem;
            margin-left: 5px;
            opacity: 0.5;
        }
        
        .sortable-header.sort-asc i {
            opacity: 1;
            transform: rotate(180deg);
        }
        
        .sortable-header.sort-desc i {
            opacity: 1;
        }
    </style>
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>
    <script>
        let signaturePad;

        function initSignaturePad() {
            const canvas = document.getElementById('signature-canvas');
            if (!canvas) return;

            // Set canvas dimensions
            const container = canvas.parentElement;
            canvas.width = container.offsetWidth;
            canvas.height = 150;

            signaturePad = new SignaturePad(canvas, {
                penColor: '#4361ee',
                backgroundColor: 'rgb(255, 255, 255)'
            });
        }

        function clearSignature() {
            if (signaturePad) {
                signaturePad.clear();
                document.getElementById('signature-input').value = '';
            }
        }

        function cancelTransfer(transferId) {
            const form = document.getElementById('cancelForm');
            form.action = '{{ url('transfers') }}/' + transferId + '/cancel';
            $('#cancelModal').modal('show');
        }

        function confirmDelivery(transferId) {
            const form = document.getElementById('confirmForm');
            form.action = '{{ url('transfers') }}/' + transferId + '/confirm';
            $('#confirmModal').modal('show');
            setTimeout(initSignaturePad, 500);
        }

        function markDelivered(transferId) {
            const form = document.getElementById('deliveredForm');
            form.action = '{{ url('transfers') }}/' + transferId + '/transit';
            $('#deliveredModal').modal('show');
        }

        $('#confirmForm').on('submit', function() {
            if (signaturePad && !signaturePad.isEmpty()) {
                document.getElementById('signature-input').value = signaturePad.toDataURL();
            }
        });

        // Export table function
        function exportTable() {
            const table = document.getElementById('transfers-table');
            const rows = table.querySelectorAll('tbody tr');
            let csv = [];

            // Get headers
            const headers = [];
            table.querySelectorAll('thead th').forEach(th => {
                headers.push(th.innerText);
            });
            csv.push(headers.join(','));

            // Get data
            rows.forEach(row => {
                const rowData = [];
                row.querySelectorAll('td').forEach((td, index) => {
                    if (index < 6) { // Skip actions column (index 6)
                        // Clean the data
                        let text = td.innerText.replace(/,/g, ';').replace(/\n/g, ' ').trim();
                        rowData.push(text);
                    }
                });
                if (rowData.length > 0 && !row.classList.contains('empty-state-row')) {
                    csv.push(rowData.join(','));
                }
            });

            // Download CSV
            const csvContent = csv.join('\n');
            const blob = new Blob([csvContent], {
                type: 'text/csv'
            });
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'transfers-{{ date('Y-m-d') }}.csv';
            a.click();
            window.URL.revokeObjectURL(url);
        }

        // Optional: Add simple sorting functionality without DataTables
        $(document).ready(function() {
            // Add sortable functionality to table headers (except Actions column)
            $('#transfers-table thead th').each(function(index) {
                if (index !== 6) { // Skip Actions column
                    $(this).addClass('sortable-header').append('<i class="bi bi-arrow-down-up"></i>');
                    
                    $(this).on('click', function() {
                        const table = $('#transfers-table tbody');
                        const rows = table.find('tr').get();
                        const column = index;
                        const isAsc = $(this).hasClass('sort-asc');
                        
                        // Remove sort classes from all headers
                        $('#transfers-table thead th').removeClass('sort-asc sort-desc');
                        
                        // Sort rows
                        rows.sort(function(a, b) {
                            let aValue = $(a).find('td').eq(column).text().trim();
                            let bValue = $(b).find('td').eq(column).text().trim();
                            
                            // Try to parse as date if it looks like a date
                            if (aValue.match(/\w{3} \d{1,2}, \d{4}/) && bValue.match(/\w{3} \d{1,2}, \d{4}/)) {
                                aValue = new Date(aValue);
                                bValue = new Date(bValue);
                            }
                            
                            if (isAsc) {
                                return aValue > bValue ? -1 : (aValue < bValue ? 1 : 0);
                            } else {
                                return aValue < bValue ? -1 : (aValue > bValue ? 1 : 0);
                            }
                        });
                        
                        // Update table
                        table.empty().append(rows);
                        
                        // Add sort class
                        $(this).addClass(isAsc ? 'sort-desc' : 'sort-asc');
                    });
                }
            });
        });
    </script>
@endpush