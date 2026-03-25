{{-- resources/views/files/preview/audio.blade.php --}}
@extends('layouts.app')

@section('title', 'Preview - ' . $file->name)

@section('content')
    <div class="row">
        <div class="col-md-6 mx-auto">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Audio Preview: {{ $file->name }}</h5>
                    <div>
                        <a href="{{ route('files.download', $file) }}" class="btn btn-sm btn-success">
                            <i class="bi bi-download"></i> Download
                        </a>
                        <a href="{{ route('files.show', $file) }}" class="btn btn-sm btn-secondary">
                            <i class="bi bi-arrow-left"></i> Back
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        <i class="bi bi-file-music" style="font-size: 5rem; color: var(--primary);"></i>
                    </div>

                    <audio controls style="width: 100%;" controlsList="nodownload">
                        <source src="{{ route('files.preview', $file) }}" type="{{ $file->mime_type }}">
                        Your browser does not support the audio tag.
                    </audio>

                    <div class="mt-3 text-muted text-center">
                        <i class="bi bi-info-circle"></i>
                        File size: {{ $file->size_for_humans }} |
                        Format: {{ strtoupper($file->extension) }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
