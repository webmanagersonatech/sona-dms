{{-- resources/views/files/preview/video.blade.php --}}
@extends('layouts.app')

@section('title', 'Preview - ' . $file->name)

@section('content')
<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Video Preview: {{ $file->name }}</h5>
                <div>
                    <a href="{{ route('files.download', $file) }}" class="btn btn-sm btn-success">
                        <i class="bi bi-download"></i> Download
                    </a>
                    <a href="{{ route('files.show', $file) }}" class="btn btn-sm btn-secondary">
                        <i class="bi bi-arrow-left"></i> Back
                    </a>
                </div>
            </div>
            <div class="card-body text-center">
                <video controls style="max-width: 100%; max-height: 500px;" controlsList="nodownload">
                    <source src="{{ route('files.preview', $file) }}" type="{{ $file->mime_type }}">
                    Your browser does not support the video tag.
                </video>
                
                <div class="mt-3 text-muted">
                    <i class="bi bi-info-circle"></i> 
                    File size: {{ $file->size_for_humans }} | 
                    Format: {{ strtoupper($file->extension) }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection