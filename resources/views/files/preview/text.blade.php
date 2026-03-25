{{-- resources/views/files/preview/text.blade.php --}}
@extends('layouts.app')

@section('title', 'Preview - ' . $file->name)

@section('content')
    <div class="row">
        <div class="col-md-10 mx-auto">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Text Preview: {{ $file->name }}</h5>
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
                    <div class="mb-3">
                        <span class="badge bg-info">File size: {{ $file->size_for_humans }}</span>
                        <span class="badge bg-secondary">Format: {{ strtoupper($file->extension) }}</span>
                    </div>

                    <pre class="p-3 bg-light rounded"
                        style="max-height: 500px; overflow-y: auto; font-family: monospace; white-space: pre-wrap; word-wrap: break-word;"><code>{{ $content }}</code></pre>
                </div>
            </div>
        </div>
    </div>
@endsection
