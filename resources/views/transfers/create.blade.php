{{-- resources/views/transfers/create.blade.php --}}
@extends('layouts.app')

@section('title', 'Create Transfer')

@section('content')
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Create New Transfer</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('transfers.store') }}" id="transferForm">
                        @csrf

                        <!-- Receiver Type Selection -->
                        <div class="card mb-3">
                            <div class="card-header bg-light">
                                <h6 class="mb-0">Receiver Information</h6>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label class="form-label">Receiver Type</label>
                                    <div class="d-flex gap-4">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="receiver_type"
                                                id="internalReceiver" value="internal" checked>
                                            <label class="form-check-label" for="internalReceiver">
                                                Internal User
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="receiver_type"
                                                id="externalReceiver" value="external">
                                            <label class="form-check-label" for="externalReceiver">
                                                External Person
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div id="internalReceiverFields">
                                    <div class="mb-3">
                                        <label for="receiver_id" class="form-label">Select User <span
                                                class="text-danger">*</span></label>
                                        <select class="form-select @error('receiver_id') is-invalid @enderror"
                                            name="receiver_id" id="receiver_id">
                                            <option value="">Choose a user...</option>
                                            @foreach ($users as $user)
                                                <option value="{{ $user->id }}"
                                                    {{ old('receiver_id') == $user->id ? 'selected' : '' }}>
                                                    {{ $user->name }} ({{ $user->email }}) -
                                                    {{ $user->department->name ?? 'No Dept' }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('receiver_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div id="externalReceiverFields" style="display: none;">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="receiver_name" class="form-label">Full Name <span
                                                    class="text-danger">*</span></label>
                                            <input type="text"
                                                class="form-control @error('receiver_name') is-invalid @enderror"
                                                id="receiver_name" name="receiver_name" value="{{ old('receiver_name') }}">
                                            @error('receiver_name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="receiver_email" class="form-label">Email <span
                                                    class="text-danger">*</span></label>
                                            <input type="email"
                                                class="form-control @error('receiver_email') is-invalid @enderror"
                                                id="receiver_email" name="receiver_email"
                                                value="{{ old('receiver_email') }}">
                                            @error('receiver_email')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="receiver_phone" class="form-label">Phone</label>
                                            <input type="text"
                                                class="form-control @error('receiver_phone') is-invalid @enderror"
                                                id="receiver_phone" name="receiver_phone"
                                                value="{{ old('receiver_phone') }}">
                                            @error('receiver_phone')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Transfer Details -->
                        <div class="card mb-3">
                            <div class="card-header bg-light">
                                <h6 class="mb-0">Transfer Details</h6>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="file_id" class="form-label">Associated File (Optional)</label>
                                    <select class="form-select @error('file_id') is-invalid @enderror" name="file_id"
                                        id="file_id">
                                        <option value="">No file</option>
                                        @foreach ($files as $file)
                                            <option value="{{ $file->id }}"
                                                {{ old('file_id') == $file->id ? 'selected' : '' }}>
                                                {{ $file->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('file_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="purpose" class="form-label">Purpose <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('purpose') is-invalid @enderror"
                                        id="purpose" name="purpose" value="{{ old('purpose') }}"
                                        placeholder="e.g., Document delivery, Hardware transfer">
                                    @error('purpose')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description"
                                        rows="3" placeholder="Additional details about the transfer...">{{ old('description') }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="expected_delivery_time" class="form-label">Expected Delivery <span
                                                class="text-danger">*</span></label>
                                        <input type="datetime-local"
                                            class="form-control @error('expected_delivery_time') is-invalid @enderror"
                                            id="expected_delivery_time" name="expected_delivery_time"
                                            value="{{ old('expected_delivery_time') }}"
                                            min="{{ now()->format('Y-m-d\TH:i') }}">
                                        @error('expected_delivery_time')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tracking Information -->
                        <div class="card mb-3">
                            <div class="card-header bg-light">
                                <h6 class="mb-0">Tracking Information (Optional)</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="courier_name" class="form-label">Courier/Service</label>
                                        <input type="text"
                                            class="form-control @error('courier_name') is-invalid @enderror"
                                            id="courier_name" name="courier_name" value="{{ old('courier_name') }}"
                                            placeholder="e.g., DHL, FedEx">
                                        @error('courier_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="tracking_number" class="form-label">Tracking Number</label>
                                        <input type="text"
                                            class="form-control @error('tracking_number') is-invalid @enderror"
                                            id="tracking_number" name="tracking_number"
                                            value="{{ old('tracking_number') }}">
                                        @error('tracking_number')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('transfers.index') }}" class="btn btn-secondary">
                                <i class="bi bi-arrow-left"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary" id="submitBtn">
                                <i class="bi bi-send"></i> Create Transfer
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            // Toggle receiver fields based on selection
            $('input[name="receiver_type"]').change(function() {
                if ($(this).val() === 'internal') {
                    $('#internalReceiverFields').show();
                    $('#externalReceiverFields').hide();

                    // Enable/disable fields
                    $('#receiver_id').prop('disabled', false);
                    $('#receiver_name, #receiver_email, #receiver_phone').prop('disabled', true);

                    // Remove validation requirements
                    $('#receiver_name, #receiver_email, #receiver_phone').removeAttr('required');
                    $('#receiver_id').attr('required', true);
                } else {
                    $('#internalReceiverFields').hide();
                    $('#externalReceiverFields').show();

                    // Enable/disable fields
                    $('#receiver_id').prop('disabled', true);
                    $('#receiver_name, #receiver_email, #receiver_phone').prop('disabled', false);

                    // Add validation requirements
                    $('#receiver_name, #receiver_email').attr('required', true);
                    $('#receiver_phone').removeAttr('required');
                    $('#receiver_id').removeAttr('required');
                }
            });

            // Trigger change on page load to set initial state
            $('input[name="receiver_type"]:checked').trigger('change');

            // Form validation
            $('#transferForm').on('submit', function(e) {
                const receiverType = $('input[name="receiver_type"]:checked').val();

                if (receiverType === 'internal' && !$('#receiver_id').val()) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'error',
                        title: 'Validation Error',
                        text: 'Please select a receiver',
                        confirmButtonColor: '#4361ee'
                    });
                } else if (receiverType === 'external') {
                    if (!$('#receiver_name').val() || !$('#receiver_email').val()) {
                        e.preventDefault();
                        Swal.fire({
                            icon: 'error',
                            title: 'Validation Error',
                            text: 'Please fill in receiver name and email',
                            confirmButtonColor: '#4361ee'
                        });
                    } else if (!isValidEmail($('#receiver_email').val())) {
                        e.preventDefault();
                        Swal.fire({
                            icon: 'error',
                            title: 'Validation Error',
                            text: 'Please enter a valid email address',
                            confirmButtonColor: '#4361ee'
                        });
                    }
                }
            });

            function isValidEmail(email) {
                const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                return re.test(email);
            }
        });
    </script>
@endpush
