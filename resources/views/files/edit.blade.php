{{-- resources/views/files/edit.blade.php --}}
@extends('layouts.app')

@section('title', 'Edit File - ' . $file->name)

@section('content')
<div class="row">
    <div class="col-md-8 mx-auto">
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('files.index') }}">Files</a></li>
                <li class="breadcrumb-item"><a href="{{ route('files.show', $file) }}">{{ $file->name }}</a></li>
                <li class="breadcrumb-item active">Edit</li>
            </ol>
        </nav>

        <div class="card overflow-hidden">
            <div class="card-header bg-white py-3">
                <h5 class="card-title mb-0">
                    <i class="bi bi-pencil-square text-primary me-2"></i> Edit File Metadata
                </h5>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('files.update', $file) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-4">
                        <label for="name" class="form-label fw-bold">File Display Name</label>
                        <input type="text" name="name" id="name" 
                            class="form-control @error('name') is-invalid @enderror" 
                            value="{{ old('name', $file->name) }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">This is the name shown in the file lists.</small>
                    </div>

                    <div class="mb-4">
                        <label for="description" class="form-label fw-bold">Description</label>
                        <textarea name="description" id="description" rows="4" 
                            class="form-control @error('description') is-invalid @enderror"
                            placeholder="Provide details about the file content or purpose...">{{ old('description', $file->description) }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    @if(Auth::user()->isSuperAdmin())
                        <div class="mb-4">
                            <label for="department_id" class="form-label fw-bold">Department Assignment</label>
                            <select name="department_id" id="department_id" 
                                class="form-select @error('department_id') is-invalid @enderror" required>
                                @foreach($departments as $dept)
                                    <option value="{{ $dept->id }}" 
                                        {{ old('department_id', $file->department_id) == $dept->id ? 'selected' : '' }}>
                                        {{ $dept->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('department_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text mt-2 text-warning">
                                <i class="bi bi-exclamation-triangle"></i> Reassigning a department will change who can see this file via department broad access.
                            </div>
                        </div>
                    @endif

                    <hr class="my-4 opacity-10">

                    <div class="d-flex justify-content-between align-items-center">
                        <a href="{{ route('files.show', $file) }}" class="btn btn-light px-4">
                            <i class="bi bi-x-circle me-1"></i> Cancel
                        </a>
                        <button type="submit" class="btn btn-primary px-5 btn-soft-fill">
                            <i class="bi bi-check-circle me-1"></i> Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="mt-4 p-3 bg-light rounded text-muted small">
            <i class="bi bi-info-circle me-1"></i> Note: File contents (the physical file) cannot be replaced here. If you need to upload a new version, please delete this file and upload a new one.
        </div>
    </div>
</div>
@endsection
