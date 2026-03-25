@extends('layouts.app')

@section('title', 'Upload File')

@section('content')
<<<<<<< HEAD
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Upload New File</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('files.store') }}" enctype="multipart/form-data" id="uploadForm">
                        @csrf

                        <div class="mb-4">
                            <label class="form-label">Select File</label>
                            <div class="upload-area" id="dropZone">
                                <i class="bi bi-cloud-upload display-1 text-primary"></i>
                                <h5 class="mt-3">Drag & Drop file here</h5>
                                <p class="text-muted">or</p>
                                <button type="button" class="btn btn-primary"
                                    onclick="document.getElementById('fileInput').click()">
                                    Browse Files
                                </button>
                                <input type="file" class="d-none" id="fileInput" name="file" required>
                                <div id="fileInfo" class="mt-3 d-none">
                                    <div class="alert alert-info">
                                        <strong>Selected:</strong> <span id="fileName"></span>
                                        (<span id="fileSize"></span>)
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description (Optional)</label>
                            <textarea class="form-control" id="description" name="description" rows="3">{{ old('description') }}</textarea>
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="encrypt" name="encrypt"
                                    value="1">
                                <label class="form-check-label" for="encrypt">
                                    Encrypt file (AES-256)
                                </label>
                            </div>
                            <small class="text-muted">
                                Encrypted files require OTP verification for download
                            </small>
                        </div>

                        <div class="progress mb-3 d-none" id="uploadProgress">
                            <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar"
                                style="width: 0%"></div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('files.index') }}" class="btn btn-secondary">
                                <i class="bi bi-arrow-left"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary" id="uploadBtn">
                                <i class="bi bi-upload"></i> Upload File
                            </button>
                        </div>
                    </form>
=======
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

>>>>>>> 0d0e6d232ac65287743e92e7c7778391eab60c9f
                </div>
            </div>
        </div>
    </div>
@endsection
<<<<<<< HEAD

@push('styles')
    <style>
        .upload-area {
            border: 2px dashed #ccc;
            border-radius: 10px;
            padding: 40px;
            text-align: center;
            background: #f8f9fa;
            transition: all 0.3s;
            cursor: pointer;
        }

        .upload-area:hover {
            border-color: #667eea;
            background: #f1f3f5;
        }

        .upload-area.dragover {
            border-color: #28a745;
            background: #e8f5e9;
        }
    </style>
@endpush

@push('scripts')
    <script>
        const dropZone = document.getElementById('dropZone');
        const fileInput = document.getElementById('fileInput');
        const fileInfo = document.getElementById('fileInfo');
        const fileName = document.getElementById('fileName');
        const fileSize = document.getElementById('fileSize');
        const uploadProgress = document.getElementById('uploadProgress');
        const progressBar = uploadProgress.querySelector('.progress-bar');
        const uploadBtn = document.getElementById('uploadBtn');

        // Drag & Drop handlers
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            dropZone.addEventListener(eventName, preventDefaults, false);
        });

        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }

        ['dragenter', 'dragover'].forEach(eventName => {
            dropZone.addEventListener(eventName, () => {
                dropZone.classList.add('dragover');
            });
        });

        ['dragleave', 'drop'].forEach(eventName => {
            dropZone.addEventListener(eventName, () => {
                dropZone.classList.remove('dragover');
            });
        });

        dropZone.addEventListener('drop', (e) => {
            const file = e.dataTransfer.files[0];
            fileInput.files = e.dataTransfer.files;
            displayFileInfo(file);
        });

        fileInput.addEventListener('change', (e) => {
            if (fileInput.files.length) {
                displayFileInfo(fileInput.files[0]);
            }
        });

        function displayFileInfo(file) {
            fileName.textContent = file.name;
            fileSize.textContent = formatFileSize(file.size);
            fileInfo.classList.remove('d-none');
        }

        function formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        }

        // Upload progress simulation (in production, use actual XHR upload progress)
        document.getElementById('uploadForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);

            // Simulate upload progress
            uploadProgress.classList.remove('d-none');
            uploadBtn.disabled = true;

            let progress = 0;
            const interval = setInterval(() => {
                progress += 10;
                progressBar.style.width = progress + '%';

                if (progress >= 100) {
                    clearInterval(interval);
                    // Submit form after progress completes
                    fetch(this.action, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    }).then(response => {
                        if (response.redirected) {
                            window.location.href = response.url;
                        }
                    });
                }
            }, 200);
=======
@push('scripts')
    <script>
        // Update custom file label
        document.querySelector('.custom-file-input').addEventListener('change', function(e) {
            var fileName = document.getElementById("file").files[0].name;
            var nextSibling = e.target.nextElementSibling;
            nextSibling.innerText = fileName;
>>>>>>> 0d0e6d232ac65287743e92e7c7778391eab60c9f
        });
    </script>
@endpush
