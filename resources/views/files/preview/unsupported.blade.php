{{-- resources/views/files/preview/unsupported.blade.php --}}
@extends('layouts.app')

@section('title', 'Preview - ' . $file->name)

@section('content')
    <div class="row">
        <div class="col-md-6 mx-auto">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Preview Unavailable</h5>
                    <a href="{{ route('files.show', $file) }}" class="btn btn-sm btn-secondary">
                        <i class="bi bi-arrow-left"></i> Back
                    </a>
                </div>
                <div class="card-body text-center py-5">
                    <i class="bi {{ $file->icon }}" style="font-size: 5rem; color: var(--gray);"></i>

                    <h5 class="mt-3">{{ $file->name }}</h5>

                    <div class="mt-3">
                        <span class="badge bg-info">Size: {{ $file->size_for_humans }}</span>
                        <span class="badge bg-secondary">Format: {{ strtoupper($file->extension) }}</span>
                    </div>

                    <p class="text-muted mt-4">
                        This file type cannot be previewed in the browser.
                        Please download the file to view its contents.
                    </p>

                    <a href="{{ route('files.download', $file) }}" class="btn btn-primary mt-3">
                        <i class="bi bi-download"></i> Download File
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
