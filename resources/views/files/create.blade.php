@extends('layouts.app')

@section('title', 'Upload File')

@section('content')
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Upload New File</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('files.store') }}" enctype="multipart/form-data">
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
                            @error('file')
                                <div class="text-danger mt-2">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description (Optional)</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description"
                                rows="3">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="encrypt" name="encrypt" value="1"
                                    {{ old('encrypt') ? 'checked' : '' }}>
                                <label class="form-check-label" for="encrypt">
                                    Encrypt file (AES-256)
                                </label>
                            </div>
                            <small class="text-muted">
                                Encrypted files require OTP verification for download
                            </small>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('files.index') }}" class="btn btn-secondary">
                                <i class="bi bi-arrow-left"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-upload"></i> Upload File
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

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

        // Prevent default drag behaviors
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            dropZone.addEventListener(eventName, preventDefaults, false);
            document.body.addEventListener(eventName, preventDefaults, false);
        });

        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }

        // Highlight drop zone when dragging over
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

        // Handle dropped files
        dropZone.addEventListener('drop', (e) => {
            const files = e.dataTransfer.files;
            if (files.length) {
                fileInput.files = files;
                displayFileInfo(files[0]);
            }
        });

        // Handle file selection via browse button
        fileInput.addEventListener('change', (e) => {
            if (this.files.length) {
                displayFileInfo(this.files[0]);
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

        // Click on drop zone triggers file selection
        dropZone.addEventListener('click', () => {
            fileInput.click();
        });
    </script>
@endpush
