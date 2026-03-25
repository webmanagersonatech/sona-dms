<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Link Expired - Secure DMS</title>
    
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            height: 100vh;
            display: flex;
            align-items: center;
        }
        .expired-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
            overflow: hidden;
            max-width: 500px;
            margin: 0 auto;
        }
        .expired-header {
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
            color: white;
            padding: 25px;
            text-align: center;
        }
        .expired-body {
            padding: 40px;
        }
        .btn-contact {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            color: white;
            padding: 12px 24px;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s;
        }
        .btn-contact:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="expired-card">
            <div class="expired-header">
                <h3><i class="fas fa-exclamation-triangle"></i> Link Expired</h3>
                <p class="mb-0">This shared link is no longer available</p>
            </div>
            
            <div class="expired-body">
                <div class="text-center mb-4">
                    <div class="mb-3">
                        <i class="fas fa-link-slash fa-4x text-danger"></i>
                    </div>
                    <h4>Access Denied</h4>
                    <p class="text-muted">
                        This shared file link has expired or been revoked by the owner.
                    </p>
                </div>

                <div class="alert alert-warning">
                    <i class="fas fa-info-circle"></i>
                    <strong>Possible reasons:</strong>
                    <ul class="mb-0 mt-2">
                        <li>The link has expired (usually after 7 days)</li>
                        <li>The file owner has revoked access</li>
                        <li>The maximum number of accesses has been reached</li>
                        <li>The file has been archived or deleted</li>
                    </ul>
                </div>

                <div class="text-center mt-4">
                    <p class="mb-3">If you need access to this file, please:</p>
                    <a href="mailto:{{ session('owner_email') ?? 'administrator@yourcompany.com' }}" 
                       class="btn btn-contact mb-2">
                        <i class="fas fa-envelope"></i> Contact File Owner
                    </a>
                    <br>
                    <small class="text-muted">
                        Or return to the login page if you have an account
                    </small>
                    <br>
                    <a href="{{ route('login') }}" class="btn btn-link mt-2">
                        <i class="fas fa-sign-in-alt"></i> Go to Login
                    </a>
                </div>
            </div>
            
            <div class="card-footer text-center py-3">
                <small class="text-muted">
                    <i class="fas fa-shield-alt"></i> Secure DMS • Protected File Sharing • © {{ date('Y') }}
                </small>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>