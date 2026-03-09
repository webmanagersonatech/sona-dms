<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Shared File - Secure DMS</title>
    
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    
    <style>
        * {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
        }
        
        body {
            background: linear-gradient(rgba(26, 54, 93, 0.9), rgba(26, 54, 93, 0.9)), 
                        url('https://images.unsplash.com/photo-1551288049-bebda4e38f71?ixlib=rb-4.0.3&auto=format&fit=crop&w=2070&q=80');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            min-height: 100vh;
            padding: 20px 0;
        }
        
        .shared-container {
            max-width: 850px;
            margin: 0 auto;
        }
        
        .shared-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 16px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
            overflow: hidden;
        }
        
        .shared-header {
            background: linear-gradient(135deg, #1a365d 0%, #2d3748 100%);
            color: white;
            padding: 30px;
            position: relative;
            overflow: hidden;
        }
        
        .shared-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 120" preserveAspectRatio="none"><path d="M1200 120L0 16.48 0 0 1200 0 1200 120z" fill="%23ffffff" opacity="0.1"/></svg>');
            background-size: cover;
            opacity: 0.1;
        }
        
        .header-badge {
            background: rgba(255, 255, 255, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.3);
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 14px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        
        .shared-body {
            padding: 40px;
        }
        
        .file-icon-lg {
            width: 100px;
            height: 100px;
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 25px;
            font-size: 48px;
        }
        
        .file-icon-lg.pdf {
            background: linear-gradient(135deg, #fed7d7 0%, #fff5f5 100%);
            color: #e53e3e;
            border: 3px solid #fc8181;
        }
        
        .file-icon-lg.word {
            background: linear-gradient(135deg, #ebf8ff 0%, #bee3f8 100%);
            color: #3182ce;
            border: 3px solid #90cdf4;
        }
        
        .file-icon-lg.excel {
            background: linear-gradient(135deg, #c6f6d5 0%, #f0fff4 100%);
            color: #38a169;
            border: 3px solid #9ae6b4;
        }
        
        .file-icon-lg.default {
            background: linear-gradient(135deg, #e9d8fd 0%, #faf5ff 100%);
            color: #805ad5;
            border: 3px solid #d6bcfa;
        }
        
        .detail-card {
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 25px;
            margin-bottom: 20px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
        }
        
        .detail-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08);
        }
        
        .detail-card h5 {
            font-weight: 600;
            color: #2d3748;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #e2e8f0;
        }
        
        .badge-permission {
            background: linear-gradient(135deg, #3182ce 0%, #2b6cb0 100%);
            color: white;
            padding: 8px 16px;
            border-radius: 8px;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin: 0 5px 10px 0;
        }
        
        .access-info {
            background: linear-gradient(135deg, #f0fff4 0%, #c6f6d5 100%);
            border: 1px solid #9ae6b4;
            border-left: 4px solid #38a169;
            border-radius: 10px;
            padding: 20px;
            margin: 25px 0;
        }
        
        .btn-action {
            background: linear-gradient(135deg, #3182ce 0%, #2b6cb0 100%);
            border: none;
            color: white;
            padding: 14px 28px;
            border-radius: 10px;
            font-weight: 500;
            font-size: 16px;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin: 0 10px 15px 0;
        }
        
        .btn-action:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(49, 130, 206, 0.3);
            background: linear-gradient(135deg, #2b6cb0 0%, #2c5282 100%);
            color: white;
        }
        
        .btn-success {
            background: linear-gradient(135deg, #38a169 0%, #2f855a 100%);
            border: none;
        }
        
        .btn-success:hover {
            background: linear-gradient(135deg, #2f855a 0%, #276749 100%);
        }
        
        .btn-warning {
            background: linear-gradient(135deg, #d69e2e 0%, #b7791f 100%);
            border: none;
        }
        
        .btn-warning:hover {
            background: linear-gradient(135deg, #b7791f 0%, #975a16 100%);
        }
        
        .alert {
            border-radius: 10px;
            border: none;
            padding: 16px 20px;
            font-size: 14px;
            margin-bottom: 24px;
        }
        
        .alert-danger {
            background: #fff5f5;
            color: #c53030;
            border-left: 4px solid #fc8181;
        }
        
        .alert-success {
            background: #f0fff4;
            color: #38a169;
            border-left: 4px solid #68d391;
        }
        
        .alert-info {
            background: #ebf8ff;
            color: #3182ce;
            border-left: 4px solid #bee3f8;
        }
        
        .shared-footer {
            text-align: center;
            padding: 25px 30px;
            background: #f7fafc;
            border-top: 1px solid #e2e8f0;
            color: #718096;
            font-size: 14px;
        }
        
        .shared-footer i {
            color: #3182ce;
        }
        
        .expiry-badge {
            background: linear-gradient(135deg, #e53e3e 0%, #c53030 100%);
            color: white;
            padding: 8px 16px;
            border-radius: 8px;
            font-weight: 500;
        }
        
        .valid-badge {
            background: linear-gradient(135deg, #38a169 0%, #2f855a 100%);
            color: white;
            padding: 8px 16px;
            border-radius: 8px;
            font-weight: 500;
        }
        
        .table-sm th {
            color: #4a5568;
            font-weight: 500;
            padding: 12px 10px;
        }
        
        .table-sm td {
            padding: 12px 10px;
            color: #2d3748;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="shared-container">
                    <div class="shared-card">
                        <div class="shared-header">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div>
                                    <h1 class="h2 mb-2">
                                        <i class="fas fa-share-alt me-2"></i> Shared File
                                    </h1>
                                    <p class="mb-0 opacity-90">Shared with you via Secure DMS</p>
                                </div>
                                <div class="header-badge">
                                    <i class="fas fa-clock"></i>
                                    Valid until: {{ $share->valid_until->format('M d, Y H:i') }}
                                </div>
                            </div>
                        </div>
                        
                        <div class="shared-body">
                            @if(session('error'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="fas fa-exclamation-circle me-2"></i>
                                {{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                            @endif

                            @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="fas fa-check-circle me-2"></i>
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                            @endif

                            <!-- File Icon and Name -->
                            <div class="text-center mb-4">
                                <div class="file-icon-lg {{ $file->extension === 'pdf' ? 'pdf' : (in_array($file->extension, ['doc', 'docx']) ? 'word' : (in_array($file->extension, ['xls', 'xlsx']) ? 'excel' : 'default')) }}">
                                    @if($file->extension === 'pdf')
                                    <i class="fas fa-file-pdf"></i>
                                    @elseif(in_array($file->extension, ['doc', 'docx']))
                                    <i class="fas fa-file-word"></i>
                                    @elseif(in_array($file->extension, ['xls', 'xlsx']))
                                    <i class="fas fa-file-excel"></i>
                                    @elseif(in_array($file->extension, ['jpg', 'jpeg', 'png', 'gif']))
                                    <i class="fas fa-file-image"></i>
                                    @else
                                    <i class="fas fa-file"></i>
                                    @endif
                                </div>
                                <h2 class="h3 mb-2">{{ $file->original_name }}</h2>
                                <p class="text-muted mb-4">
                                    <i class="fas fa-user me-1"></i> Shared by: {{ $share->sharedBy->name }}
                                    | <i class="fas fa-calendar me-1"></i> {{ $share->created_at->format('M d, Y') }}
                                </p>
                            </div>

                            <!-- File Details -->
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <div class="detail-card">
                                        <h5><i class="fas fa-info-circle me-2"></i> File Details</h5>
                                        <table class="table table-sm mb-0">
                                            <tr>
                                                <th>File Type:</th>
                                                <td>
                                                    <span class="badge bg-primary">
                                                        {{ strtoupper($file->extension) }}
                                                    </span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>File Size:</th>
                                                <td>{{ $file->formatted_size }}</td>
                                            </tr>
                                            <tr>
                                                <th>Uploaded Date:</th>
                                                <td>{{ $file->created_at->format('M d, Y H:i') }}</td>
                                            </tr>
                                            <tr>
                                                <th>Description:</th>
                                                <td>{{ $file->description ?? 'No description provided' }}</td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="detail-card">
                                        <h5><i class="fas fa-shield-alt me-2"></i> Access Information</h5>
                                        <div class="mb-4">
                                            <p class="fw-medium mb-2">Your Permissions:</p>
                                            @foreach($share->permissions as $permission)
                                            <span class="badge-permission">
                                                <i class="fas fa-{{ $permission === 'view' ? 'eye' : ($permission === 'download' ? 'download' : ($permission === 'edit' ? 'edit' : 'print')) }}"></i>
                                                {{ ucfirst($permission) }}
                                            </span>
                                            @endforeach
                                        </div>
                                        
                                        <div class="mb-3">
                                            <p class="fw-medium mb-2">Access Count:</p>
                                            <span class="badge bg-secondary">{{ $share->access_count }} access{{ $share->access_count !== 1 ? 'es' : '' }}</span>
                                        </div>
                                        
                                        <div>
                                            <p class="fw-medium mb-2">Link Validity:</p>
                                            @if($share->valid_until->isPast())
                                            <span class="expiry-badge">
                                                <i class="fas fa-exclamation-triangle me-2"></i>
                                                Expired {{ $share->valid_until->diffForHumans() }}
                                            </span>
                                            @else
                                            <span class="valid-badge">
                                                <i class="fas fa-check-circle me-2"></i>
                                                Valid for {{ $share->valid_until->diffForHumans() }}
                                            </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Security Information -->
                            <div class="access-info">
                                <div class="d-flex align-items-start">
                                    <i class="fas fa-shield-alt fa-2x text-primary mt-1 me-3"></i>
                                    <div>
                                        <h6 class="fw-bold mb-2">Security Information</h6>
                                        <p class="mb-2">This file has been shared with you securely using Secure DMS. All access is encrypted and logged for security purposes.</p>
                                        @if($share->requires_otp_approval)
                                        <div class="alert alert-warning mt-2 mb-0">
                                            <i class="fas fa-lock me-2"></i>
                                            <strong>OTP Protection Enabled:</strong> The file owner will receive an OTP when you attempt to access this file.
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- File Actions -->
                            <div class="text-center my-4">
                                <h5 class="fw-medium mb-3">Available Actions</h5>
                                <div class="d-flex flex-wrap justify-content-center">
                                    @if($share->hasPermission('view') && !$file->is_archived && !$file->isExpired())
                                    <a href="{{ route('files.preview', $file) }}" 
                                       class="btn-action" 
                                       target="_blank">
                                        <i class="fas fa-eye"></i> Preview File
                                    </a>
                                    @endif
                                    
                                    @if($share->hasPermission('download') && !$file->is_archived && !$file->isExpired())
                                    @if($share->requires_otp_approval && !session('otp_verified_file_access'))
                                    <button type="button" 
                                            class="btn-action btn-success"
                                            onclick="requestOTP()">
                                        <i class="fas fa-download"></i> Request Download
                                    </button>
                                    @else
                                    <a href="{{ route('files.download', $file) }}" 
                                       class="btn-action btn-success">
                                        <i class="fas fa-download"></i> Download File
                                    </a>
                                    @endif
                                    @endif
                                    
                                    @if($share->hasPermission('print') && !$file->is_archived && !$file->isExpired())
                                    <button type="button" 
                                            class="btn-action btn-warning"
                                            onclick="printFile()">
                                        <i class="fas fa-print"></i> Print
                                    </button>
                                    @endif
                                </div>
                            </div>

                            <!-- Access History -->
                            <div class="alert alert-info">
                                <div class="d-flex align-items-start">
                                    <i class="fas fa-history me-3 mt-1"></i>
                                    <div>
                                        <strong>Access History:</strong>
                                        <p class="mb-0 mt-1">
                                            This file was last accessed 
                                            @if($share->last_accessed_at)
                                            <strong>{{ $share->last_accessed_at->diffForHumans() }}</strong>
                                            @else
                                            <strong>never</strong>
                                            @endif
                                            • Created {{ $file->created_at->diffForHumans() }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="shared-footer">
                            <p class="mb-0">
                                <i class="fas fa-shield-alt me-1"></i> Secure DMS • Protected File Sharing • © {{ date('Y') }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>
        function requestOTP() {
            Swal.fire({
                title: 'OTP Approval Required',
                text: 'An OTP will be sent to the file owner for approval. Continue?',
                icon: 'info',
                showCancelButton: true,
                confirmButtonText: 'Yes, Request Approval',
                cancelButtonText: 'Cancel',
                confirmButtonColor: '#3182ce',
                cancelButtonColor: '#718096'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch('{{ route("shared.otp.request", $share->share_token) }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'OTP Sent',
                                text: 'An OTP has been sent to the file owner. Please wait for approval.',
                                confirmButtonText: 'OK',
                                confirmButtonColor: '#3182ce'
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Request Failed',
                                text: data.message || 'Failed to send OTP request',
                                confirmButtonText: 'OK',
                                confirmButtonColor: '#e53e3e'
                            });
                        }
                    })
                    .catch(error => {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'An error occurred. Please try again.',
                            confirmButtonText: 'OK',
                            confirmButtonColor: '#e53e3e'
                        });
                    });
                }
            });
        }

        function printFile() {
            Swal.fire({
                title: 'Print File',
                text: 'Are you sure you want to print this file?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Yes, Print',
                cancelButtonText: 'Cancel',
                confirmButtonColor: '#d69e2e',
                cancelButtonColor: '#718096'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.open('{{ route("files.preview", $file) }}', '_blank').print();
                }
            });
        }

        // Auto-dismiss alerts after 5 seconds
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);
    </script>
</body>
</html>