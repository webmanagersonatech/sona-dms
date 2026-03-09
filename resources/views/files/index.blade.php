@extends('layouts.app')

@section('title', 'Files')

@section('content')
    <div class="container-fluid">

        <!-- Button Row -->
        <div class="row mb-3">
            <div class="col-12 text-right">
                <a href="{{ route('files.create') }}" class="btn btn-primary">
                    <i class="fas fa-upload"></i> Upload File
                </a>
            </div>
        </div>

        <!-- Table Row -->
        <div class="row">
            <div class="col-12">
                <div class="card">

                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover datatable">
                                <thead>
                                    <tr>
                                        <th>File Name</th>
                                        <th>Type</th>
                                        <th>Size</th>
                                        <th>Owner</th>
                                        <th>Department</th>
                                        <th>Uploaded</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @foreach ($files as $file)
                                        <tr>
                                            <td>
                                                <i
                                                    class="fas fa-file-{{ $file->extension === 'pdf' ? 'pdf' : 'word' }} text-danger"></i>
                                                {{ $file->original_name }}
                                                @if ($file->description)
                                                    <br>
                                                    <small class="text-muted">
                                                        {{ Str::limit($file->description, 50) }}
                                                    </small>
                                                @endif
                                            </td>

                                            <td>{{ strtoupper($file->extension) }}</td>
                                            <td>{{ $file->formatted_size }}</td>
                                            <td>{{ $file->owner->name }}</td>
                                            <td>{{ $file->department->name }}</td>
                                            <td>{{ $file->created_at->format('M d, Y') }}</td>

                                            <td>
                                                @if ($file->is_archived)
                                                    <span class="badge badge-secondary">Archived</span>
                                                @elseif($file->isExpired())
                                                    <span class="badge badge-warning">Expired</span>
                                                @elseif($file->is_shared)
                                                    <span class="badge badge-info">Shared</span>
                                                @else
                                                    <span class="badge badge-success">Active</span>
                                                @endif
                                            </td>

                                            <td>
                                                <div class="btn-group">
                                                    <a href="{{ route('files.show', $file) }}" class="btn btn-sm btn-info">
                                                        <i class="fas fa-eye"></i>
                                                    </a>

                                                    @if ($file->canBeAccessedBy(auth()->user()))
                                                        <a href="{{ route('files.download', $file) }}"
                                                            class="btn btn-sm btn-success">
                                                            <i class="fas fa-download"></i>
                                                        </a>
                                                    @endif

                                                    @if ($file->owner_id === auth()->id())
                                                        <a href="{{ route('files.shares', $file) }}"
                                                            class="btn btn-sm btn-warning">
                                                            <i class="fas fa-share-alt"></i>
                                                        </a>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>

                            </table>
                        </div>
                    </div>

                </div>
            </div>
        </div>

    </div>
@endsection
