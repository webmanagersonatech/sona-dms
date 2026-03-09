@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4">Export Statistics</h1>
    
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Export Options</h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.stats.export') }}">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="period">Time Period</label>
                            <select class="form-control" id="period" name="period">
                                <option value="daily">Daily (Last 7 days)</option>
                                <option value="weekly" selected>Weekly (Last 30 days)</option>
                                <option value="monthly">Monthly (Last 90 days)</option>
                                <option value="yearly">Yearly (Last 3 years)</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="format">Export Format</label>
                            <select class="form-control" id="format" name="format">
                                <option value="excel">Excel (.xlsx)</option>
                                <option value="csv">CSV (.csv)</option>
                                <option value="pdf">PDF (.pdf)</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="col-md-4 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-download"></i> Export Statistics
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection