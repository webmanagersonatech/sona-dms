@extends('layouts.app')

@section('title', 'Upload File')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-10 offset-md-1">
                <div class="card">

                    <div class="card-header">
                        <h3 class="card-title">Upload New File</h3>
                    </div>

                    <form action="{{ route('files.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="card-body">

                            <!-- Row 1 -->
                            <div class="row">

                                <!-- File -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="file">Select File *</label>
                                        <div class="custom-file">
                                            <input type="file"
                                                class="custom-file-input @error('file') is-invalid @enderror" id="file"
                                                name="file" required>

                                            <label class="custom-file-label" for="file">Choose file</label>

                                            @error('file')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <small class="form-text text-muted">
                                            Allowed: PDF, DOC, DOCX, JPG, PNG, GIF, ZIP(Max: 100MB)
                                        </small>
                                    </div>
                                </div>

                                <!-- Description -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="description">Description</label>
                                        <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description"
                                            rows="3">{{ old('description') }}</textarea>

                                        @error('description')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                            </div>


                            <!-- Row 2 -->
                            <div class="row">

                                <!-- Tags -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="tags">Tags</label>
                                        <input type="text" class="form-control @error('tags') is-invalid @enderror"
                                            id="tags" name="tags" value="{{ old('tags') }}"
                                            placeholder="tag1, tag2">

                                        @error('tags')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Expiry Date -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="expires_at">Expiry Date</label>
                                        <input type="datetime-local"
                                            class="form-control @error('expires_at') is-invalid @enderror" id="expires_at"
                                            name="expires_at" value="{{ old('expires_at') }}">

                                        @error('expires_at')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                            </div>

                        </div>

                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-upload"></i> Upload File
                            </button>

                            <a href="{{ route('files.index') }}" class="btn btn-default">Cancel</a>
                        </div>

                    </form>

                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        // Update custom file label
        document.querySelector('.custom-file-input').addEventListener('change', function(e) {
            var fileName = document.getElementById("file").files[0].name;
            var nextSibling = e.target.nextElementSibling;
            nextSibling.innerText = fileName;
        });
    </script>
@endpush
