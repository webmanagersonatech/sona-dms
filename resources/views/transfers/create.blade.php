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
    </script>
@endpush
