@extends('layouts.app')

@section('title', 'Import / Export')

@section('content')
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5 class="card-title mb-0">Export Data</h5>
                </div>
                <div class="card-body">
                    <div class="list-group">
                        <div class="list-group-item">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-1">Export Users</h6>
                                    <small class="text-muted">Export all users with their details</small>
                                </div>
                                <div class="btn-group">
                                    <a href="{{ route('export.users', ['format' => 'excel']) }}"
                                        class="btn btn-sm btn-success">
                                        <i class="bi bi-file-excel"></i> Excel
                                    </a>
                                    <a href="{{ route('export.users', ['format' => 'csv']) }}" class="btn btn-sm btn-info">
                                        <i class="bi bi-file-text"></i> CSV
                                    </a>
                                    <a href="{{ route('export.users', ['format' => 'pdf']) }}"
                                        class="btn btn-sm btn-danger">
                                        <i class="bi bi-file-pdf"></i> PDF
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div class="list-group-item">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-1">Export Files</h6>
                                    <small class="text-muted">Export all files with metadata</small>
                                </div>
                                <div class="btn-group">
                                    <a href="{{ route('export.files', ['format' => 'excel']) }}"
                                        class="btn btn-sm btn-success">
                                        <i class="bi bi-file-excel"></i> Excel
                                    </a>
                                    <a href="{{ route('export.files', ['format' => 'csv']) }}" class="btn btn-sm btn-info">
                                        <i class="bi bi-file-text"></i> CSV
                                    </a>
                                    <a href="{{ route('export.files', ['format' => 'pdf']) }}"
                                        class="btn btn-sm btn-danger">
                                        <i class="bi bi-file-pdf"></i> PDF
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div class="list-group-item">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-1">Export Transfers</h6>
                                    <small class="text-muted">Export all transfers with tracking info</small>
                                </div>
                                <div class="btn-group">
                                    <a href="{{ route('export.transfers', ['format' => 'excel']) }}"
                                        class="btn btn-sm btn-success">
                                        <i class="bi bi-file-excel"></i> Excel
                                    </a>
                                    <a href="{{ route('export.transfers', ['format' => 'csv']) }}"
                                        class="btn btn-sm btn-info">
                                        <i class="bi bi-file-text"></i> CSV
                                    </a>
                                    <a href="{{ route('export.transfers', ['format' => 'pdf']) }}"
                                        class="btn btn-sm btn-danger">
                                        <i class="bi bi-file-pdf"></i> PDF
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">Import Data</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i>
                        Download templates first, then upload your data.
                    </div>

                    <div class="list-group">
                        <div class="list-group-item">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-1">Import Users</h6>
                                    <small class="text-muted">Bulk import users from Excel/CSV</small>
                                </div>
                                <div>
                                    <a href="{{ route('import.template', 'users') }}" class="btn btn-sm btn-info me-2">
                                        <i class="bi bi-download"></i> Template
                                    </a>
                                    <a href="{{ route('import.form', 'users') }}" class="btn btn-sm btn-primary">
                                        <i class="bi bi-upload"></i> Import
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4">
                        <h6>Import Guidelines:</h6>
                        <ul class="small text-muted">
                            <li>Use the provided template for correct format</li>
                            <li>Maximum file size: 10MB</li>
                            <li>Supported formats: .xlsx, .xls, .csv</li>
                            <li>All required fields must be filled</li>
                            <li>Email addresses must be unique</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
