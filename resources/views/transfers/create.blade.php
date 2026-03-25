<<<<<<< HEAD
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
=======
@extends('layouts.app')

@section('title', 'Create New Transfer')

@section('content')
    <div class="container-fluid">

        <div class="row mb-3">
            <div class="col-12 text-right">
                <a href="{{ route('transfers.index') }}" class="btn btn-primary">
                    <i class="fas fa-arrow-left"></i> Back to Transfers
                </a>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">

                <div class="card">

                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-edit"></i> Transfer Details
                        </h3>
                    </div>

                    <form action="{{ route('transfers.store') }}" method="POST">
                        @csrf

                        <div class="card-body">

                            <!-- Row 1 -->
                            <div class="row">

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Select File *</label>
                                        <select class="form-control @error('file_id') is-invalid @enderror" name="file_id"
                                            required>

                                            <option value="">Select File</option>

                                            @foreach ($files as $file)
                                                <option value="{{ $file->id }}">
                                                    {{ $file->original_name }} ({{ $file->formatted_size }})
                                                </option>
                                            @endforeach

                                        </select>

                                        @error('file_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror

                                    </div>
                                </div>


                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Select Receiver *</label>

                                        <select class="form-control @error('receiver_id') is-invalid @enderror"
                                            name="receiver_id" required>

                                            <option value="">Select Receiver</option>

                                            @foreach ($receivers as $receiver)
                                                <option value="{{ $receiver->id }}">
                                                    {{ $receiver->name }} ({{ $receiver->email }})
                                                </option>
                                            @endforeach

                                        </select>

                                        @error('receiver_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror

                                    </div>
                                </div>


                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Expected Delivery *</label>

                                        <input type="datetime-local" class="form-control" name="expected_delivery_time"
                                            value="{{ old('expected_delivery_time') ?? now()->addHours(24)->format('Y-m-d\TH:i') }}"
                                            required>

                                    </div>
                                </div>

                            </div>


                            <!-- Row 2 -->
                            <div class="row">

                                <div class="col-md-4">
                                    <div class="form-group">

                                        <label>Transfer Type *</label>

                                        <div class="custom-control custom-radio">
                                            <input type="radio" class="custom-control-input" id="physical"
                                                name="transfer_type" value="physical"
                                                {{ old('transfer_type', 'physical') == 'physical' ? 'checked' : '' }}>

                                            <label class="custom-control-label" for="physical">
                                                <i class="fas fa-truck"></i> Physical
                                            </label>
                                        </div>

                                        <div class="custom-control custom-radio">
                                            <input type="radio" class="custom-control-input" id="cloud"
                                                name="transfer_type" value="cloud"
                                                {{ old('transfer_type') == 'cloud' ? 'checked' : '' }}>

                                            <label class="custom-control-label" for="cloud">
                                                <i class="fas fa-cloud"></i> Cloud
                                            </label>
                                        </div>

                                    </div>
                                </div>


                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Purpose *</label>

                                        <textarea class="form-control @error('purpose') is-invalid @enderror" name="purpose" rows="2" required>{{ old('purpose') }}</textarea>

                                        @error('purpose')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror

                                    </div>
                                </div>


                                <div class="col-md-4">
                                    <div class="form-group mt-4">

                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" id="third_party_involved"
                                                name="third_party_involved" value="1">

                                            <label class="custom-control-label" for="third_party_involved">
                                                <i class="fas fa-user-friends"></i> Third Party Involved
                                            </label>
                                        </div>

                                    </div>
                                </div>

                            </div>


                            <!-- Third Party Details -->
                            <div id="third_party_details" style="display:none">

                                <div class="card mt-3">
                                    <div class="card-body bg-light">

                                        <h6><i class="fas fa-info-circle"></i> Third Party Information</h6>

                                        <div class="row">

                                            <div class="col-md-6">
                                                <div class="form-group">

                                                    <label>Third Party Name *</label>

                                                    <input type="text" class="form-control" id="third_party_name"
                                                        name="third_party_name">

                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="form-group">

                                                    <label>Third Party Email *</label>

                                                    <input type="email" class="form-control" id="third_party_email"
                                                        name="third_party_email">

                                                </div>
                                            </div>

                                        </div>

                                    </div>
                                </div>

                            </div>


                            <!-- Notes -->
                            <div class="form-group mt-3">

                                <label>Additional Notes</label>

                                <textarea class="form-control" name="notes" rows="2"></textarea>

                            </div>


                            <!-- Security -->
                            <div class="alert alert-info mt-4">

                                <i class="fas fa-shield-alt"></i>
                                <strong>Security Information</strong>

                                <ul class="mt-2 mb-0">
                                    <li>All transfers are logged</li>
                                    <li>OTP approval required</li>
                                    <li>Third party needs verification</li>
                                    <li>You can cancel anytime</li>
                                </ul>

                            </div>


                        </div>


                        <div class="card-footer">

                            <button class="btn btn-primary">
                                <i class="fas fa-paper-plane"></i> Create Transfer
                            </button>

                            <a href="{{ route('transfers.index') }}" class="btn btn-secondary">
                                Cancel
                            </a>

                        </div>

                    </form>

                </div>

            </div>
        </div>

    </div>
@endsection


@push('scripts')
    <script>
        document.getElementById('third_party_involved').addEventListener('change', function() {

            let div = document.getElementById('third_party_details')

            if (this.checked) {

                div.style.display = 'block'

                document.getElementById('third_party_name').required = true
                document.getElementById('third_party_email').required = true

            } else {

                div.style.display = 'none'

                document.getElementById('third_party_name').required = false
                document.getElementById('third_party_email').required = false

            }

        })
>>>>>>> 0d0e6d232ac65287743e92e7c7778391eab60c9f
    </script>
@endpush
