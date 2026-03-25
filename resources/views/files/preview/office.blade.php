{{-- resources/views/files/preview/office.blade.php --}}
@extends('layouts.app')

@section('title', 'Preview - ' . $file->name)

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Document Preview: {{ $file->name }}</h5>
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
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i>
                        Using Google Docs Viewer to preview this document. For best results, download the file.
                    </div>

                    <div class="text-center mb-3">
                        <iframe src="https://docs.google.com/gview?url={{ urlencode($fileUrl) }}&embedded=true"
                            style="width: 100%; height: 600px;" frameborder="0">
                        </iframe>
                    </div>

                    <div class="text-muted text-center small">
                        <i class="bi bi-file-earmark"></i>
                        {{ $file->size_for_humans }} |
                        Format: {{ strtoupper($file->extension) }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
