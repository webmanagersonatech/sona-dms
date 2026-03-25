@extends('layouts.app')

@section('title', 'Role Permissions')

@section('content')
<!-- Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1">Manage Permissions: {{ $role->name }}</h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('roles.index') }}">Roles</a></li>
                <li class="breadcrumb-item"><a href="{{ route('roles.show', $role) }}">{{ $role->name }}</a></li>
                <li class="breadcrumb-item active">Permissions</li>
            </ol>
        </nav>
    </div>
</div>

<!-- Permissions Form Card -->
<div class="row">
    <div class="col-lg-10 mx-auto">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Role Permissions</h5>
                <span class="badge bg-info">{{ $role->permissions->count() }} permissions assigned</span>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('roles.permissions.update', $role) }}" id="permissionsForm">
                    @csrf
                    @method('PUT')
                    
                    <!-- Quick Actions -->
                    <div class="mb-4">
                        <div class="btn-group">
                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="selectAll()">
                                <i class="bi bi-check-all"></i> Select All
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="deselectAll()">
                                <i class="bi bi-x"></i> Deselect All
                            </button>
                        </div>
                    </div>

                    <!-- Permissions by Module -->
                    @foreach($permissions as $module => $modulePermissions)
                        <div class="card mb-4">
                            <div class="card-header bg-light">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0 fw-bold">{{ ucfirst($module) }}</h6>
                                    <div>
                                        <button type="button" class="btn btn-sm btn-link" onclick="selectModule('{{ $module }}')">
                                            Select All
                                        </button>
                                        <button type="button" class="btn btn-sm btn-link text-muted" onclick="deselectModule('{{ $module }}')">
                                            Deselect
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    @foreach($modulePermissions as $permission)
                                        <div class="col-md-3 mb-3">
                                            <div class="form-check">
                                                <input class="form-check-input module-{{ $module }}" 
                                                       type="checkbox" 
                                                       name="permissions[]" 
                                                       value="{{ $permission->id }}" 
                                                       id="perm_{{ $permission->id }}"
                                                       {{ in_array($permission->id, $rolePermissions) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="perm_{{ $permission->id }}">
                                                    <strong>{{ $permission->name }}</strong>
                                                    <br>
                                                    <small class="text-muted">{{ $permission->slug }}</small>
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endforeach

                    <!-- Form Actions -->
                    <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                        <a href="{{ route('roles.show', $role) }}" class="btn btn-secondary px-4">
                            <i class="bi bi-x me-2"></i>Cancel
                        </a>
                        <button type="submit" class="btn btn-primary px-5">
                            <i class="bi bi-save me-2"></i>Save Permissions
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function selectAll() {
        document.querySelectorAll('input[type="checkbox"]').forEach(checkbox => {
            checkbox.checked = true;
        });
    }

    function deselectAll() {
        document.querySelectorAll('input[type="checkbox"]').forEach(checkbox => {
            checkbox.checked = false;
        });
    }

    function selectModule(module) {
        document.querySelectorAll('.module-' + module).forEach(checkbox => {
            checkbox.checked = true;
        });
    }

    function deselectModule(module) {
        document.querySelectorAll('.module-' + module).forEach(checkbox => {
            checkbox.checked = false;
        });
    }
</script>
@endpush