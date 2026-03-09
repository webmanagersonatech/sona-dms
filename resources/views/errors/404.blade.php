<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>404 - Page Not Found</title>
    
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
            display: flex;
            align-items: center;
        }
        
        .error-container {
            max-width: 520px;
            margin: 0 auto;
        }
        
        .error-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 16px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
            overflow: hidden;
        }
        
        .error-header {
            background: linear-gradient(135deg, #d69e2e 0%, #b7791f 100%);
            color: white;
            padding: 35px 30px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        
        .error-header::before {
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
        
        .error-header h3 {
            font-weight: 600;
            font-size: 24px;
            margin-bottom: 8px;
            position: relative;
        }
        
        .error-header p {
            opacity: 0.9;
            font-size: 15px;
            margin: 0;
        }
        
        .error-body {
            padding: 40px 35px;
        }
        
        .error-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #feebc8 0%, #fffaf0 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 25px;
            border: 3px solid #f6ad55;
        }
        
        .error-icon i {
            color: #d69e2e;
            font-size: 36px;
        }
        
        .error-title {
            font-weight: 600;
            color: #2d3748;
            font-size: 22px;
            margin-bottom: 10px;
        }
        
        .error-description {
            color: #718096;
            font-size: 15px;
            line-height: 1.6;
            margin-bottom: 30px;
        }
        
        .alert-info {
            background: #ebf8ff;
            border: 1px solid #bee3f8;
            border-left: 4px solid #3182ce;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 30px;
        }
        
        .alert-info i {
            color: #3182ce;
            margin-right: 10px;
        }
        
        .btn-action {
            background: linear-gradient(135deg, #3182ce 0%, #2b6cb0 100%);
            border: none;
            color: white;
            padding: 12px 24px;
            border-radius: 10px;
            font-weight: 500;
            font-size: 15px;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            margin: 5px;
        }
        
        .btn-action:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(49, 130, 206, 0.3);
            background: linear-gradient(135deg, #2b6cb0 0%, #2c5282 100%);
            color: white;
        }
        
        .btn-secondary {
            background: #718096;
            border: none;
        }
        
        .btn-secondary:hover {
            background: #4a5568;
        }
        
        .contact-info {
            margin-top: 25px;
            padding: 15px;
            background: #f7fafc;
            border-radius: 8px;
            font-size: 14px;
            color: #4a5568;
            text-align: center;
            border-left: 3px solid #38a169;
        }
        
        .error-footer {
            text-align: center;
            padding: 25px 30px;
            background: #f7fafc;
            border-top: 1px solid #e2e8f0;
            color: #718096;
            font-size: 14px;
        }
        
        .error-footer i {
            color: #3182ce;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="error-container">
                    <div class="error-card">
                        <div class="error-header">
                            <h3><i class="fas fa-map-marker-alt"></i> 404 - Page Not Found</h3>
                            <p>The page you're looking for doesn't exist</p>
                        </div>
                        
                        <div class="error-body">
                            <div class="error-icon">
                                <i class="fas fa-search"></i>
                            </div>
                            
                            <h4 class="error-title text-center">Page Not Found</h4>
                            <p class="error-description text-center">
                                We couldn't find the page you were looking for.
                                It might have been moved, deleted, or never existed.
                            </p>

                            <div class="alert-info">
                                <i class="fas fa-info-circle"></i>
                                <strong>Possible reasons:</strong>
                                <ul class="mb-0 mt-2 ps-3">
                                    <li>The page has been moved or deleted</li>
                                    <li>You typed the wrong URL</li>
                                    <li>The resource is no longer available</li>
                                    <li>You don't have access to this page</li>
                                </ul>
                            </div>

                            <div class="text-center mt-4">
                                <a href="{{ url()->previous() }}" class="btn-action btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Go Back
                                </a>
                                <a href="{{ route('dashboard') }}" class="btn-action">
                                    <i class="fas fa-tachometer-alt"></i> Dashboard
                                </a>
                            </div>
                            
                            <div class="contact-info">
                                <i class="fas fa-headset me-2"></i>
                                If you believe this is an error, contact the system administrator.
                            </div>
                        </div>
                        
                        <div class="error-footer">
                            <p class="mb-0">
                                <i class="fas fa-shield-alt me-1"></i> Secure DMS • Page Not Found • © {{ date('Y') }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>