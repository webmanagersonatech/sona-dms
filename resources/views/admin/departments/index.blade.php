@extends('layouts.app')

@section('title', 'Department Management')

@section('content')
    <div class="container-fluid">
        <div class="row mb-3">
            <div class="col-12">
                <div class="d-flex justify-content-end align-items-center">

                    <div class="mb-0">
                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#createDepartmentModal">
                            <i class="fas fa-plus"></i> Add Department
                        </button>
                    </div>

                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Departments List</h3>

                    </div>
                    <div class="card-body">
                        @if (session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="fas fa-check-circle"></i> {{ session('success') }}
                                <button type="button" class="close" data-dismiss="alert">
                                    <span>&times;</span>
                                </button>
                            </div>
                        @endif

                        @if ($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="fas fa-exclamation-triangle"></i> Please fix the following errors:
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                                <button type="button" class="close" data-dismiss="alert">
                                    <span>&times;</span>
                                </button>
                            </div>
                        @endif

                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Code</th>
                                        <th>Description</th>
                                        <th>Status</th>
                                        <th>Created</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($departments as $department)
                                        <tr>
                                            <td>
                                                <strong>{{ $department->name }}</strong>
                                            </td>
                                            <td>
                                                <span class="badge badge-info">{{ $department->code }}</span>
                                            </td>
                                            <td>
                                                {{ Str::limit($department->description, 50) }}
                                            </td>

                                            <td>
                                                @if ($department->is_active)
                                                    <span class="badge badge-success">Active</span>
                                                @else
                                                    <span class="badge badge-danger">Inactive</span>
                                                @endif
                                            </td>
                                            <td>
                                                {{ $department->created_at->format('M d, Y') }}
                                            </td>
                                            <td>
                                                <div class="btn-group">
                                                    <button type="button" class="btn btn-sm btn-info" data-toggle="modal"
                                                        data-target="#editDepartmentModal{{ $department->id }}">
                                                        <i class="fas fa-edit"></i>
                                                    </button>

                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center">No departments found.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="d-flex justify-content-between">
                            <div>
                                Showing {{ $departments->firstItem() ?? 0 }} to {{ $departments->lastItem() ?? 0 }} of
                                {{ $departments->total() }} departments
                            </div>
                            <div>
                                {{ $departments->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Create Department Modal -->
    <div class="modal fade" id="createDepartmentModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form action="{{ route('admin.departments.store') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="fas fa-plus"></i> Create New Department
                        </h5>
                        <button type="button" class="close" data-dismiss="modal">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="name">Department Name *</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                                name="name" value="{{ old('name') }}" required>
                            @error('name')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="code">Department Code *</label>
                            <input type="text" class="form-control @error('code') is-invalid @enderror" id="code"
                                name="code" value="{{ old('code') }}" required>
                            <small class="form-text text-muted">Unique code for the department (e.g., IT, HR, FIN)</small>
                            @error('code')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description"
                                rows="3">{{ old('description') }}</textarea>
                            @error('description')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Create Department</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Department Modals -->
    @foreach ($departments as $department)
        <div class="modal fade" id="editDepartmentModal{{ $department->id }}" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <form action="{{ route('admin.departments.update', $department) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="modal-header">
                            <h5 class="modal-title">
                                <i class="fas fa-edit"></i> Edit Department: {{ $department->name }}
                            </h5>
                            <button type="button" class="close" data-dismiss="modal">
                                <span>&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="name{{ $department->id }}">Department Name *</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                    id="name{{ $department->id }}" name="name"
                                    value="{{ old('name', $department->name) }}" required>
                                @error('name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="code{{ $department->id }}">Department Code *</label>
                                <input type="text" class="form-control @error('code') is-invalid @enderror"
                                    id="code{{ $department->id }}" name="code"
                                    value="{{ old('code', $department->code) }}" required>
                                @error('code')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="description{{ $department->id }}">Description</label>
                                <textarea class="form-control @error('description') is-invalid @enderror" id="description{{ $department->id }}"
                                    name="description" rows="3">{{ old('description', $department->description) }}</textarea>
                                @error('description')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input"
                                        id="is_active{{ $department->id }}" name="is_active" value="1"
                                        {{ $department->is_active ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="is_active{{ $department->id }}">
                                        Department Active
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Update Department</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach

    @push('scripts')
        <script>
            // Auto-close alerts after 5 seconds
            $(document).ready(function() {
                setTimeout(function() {
                    $('.alert').alert('close');
                }, 5000);
            });
        </script>
    @endpush
@endsection
