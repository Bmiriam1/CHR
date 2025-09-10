<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <!-- Meta tags  -->
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport"
        content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Connect HR') }}</title>
    <link rel="icon" type="image/png" href="images/favicon.png" />

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Poppins:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet" />

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .auth-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            width: 100%;
            max-width: 1000px;
            margin: 2rem;
        }
        
        .brand-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 3rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
        }
        
        .brand-logo {
            font-size: 3rem;
            margin-bottom: 1rem;
        }
        
        .brand-title {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
        }
        
        .brand-subtitle {
            font-size: 1.1rem;
            opacity: 0.9;
            margin-bottom: 2rem;
        }
        
        .features-list {
            list-style: none;
            padding: 0;
        }
        
        .features-list li {
            display: flex;
            align-items: center;
            margin-bottom: 0.75rem;
            font-size: 0.95rem;
        }
        
        .features-list i {
            margin-right: 0.75rem;
            color: #4ade80;
        }
        
        .auth-section {
            padding: 3rem;
        }
        
        .btn-gradient {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            color: white;
            font-weight: 600;
            padding: 0.75rem 2rem;
            border-radius: 10px;
            transition: all 0.3s ease;
        }
        
        .btn-gradient:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
            color: white;
        }
        
        .form-control {
            border: 2px solid #e5e7eb;
            border-radius: 10px;
            padding: 0.75rem 1rem;
            transition: all 0.3s ease;
        }
        
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        
        @media (max-width: 768px) {
            .auth-container {
                margin: 1rem;
            }
            
            .brand-section,
            .auth-section {
                padding: 2rem;
            }
            
            .brand-title {
                font-size: 2rem;
            }
        }
    </style>
</head>

<body>
    <div class="auth-container">
        <div class="row g-0">
            <div class="col-lg-5">
                <div class="brand-section h-100">
                    <div class="brand-logo">
                        <i class="fas fa-users-cog"></i>
                    </div>
                    <h1 class="brand-title">Connect HR</h1>
                    <p class="brand-subtitle">South African Workforce Development & Compliance Platform</p>
                    
                    <ul class="features-list">
                        <li>
                            <i class="fas fa-check-circle"></i>
                            SARS Compliance & Reporting
                        </li>
                        <li>
                            <i class="fas fa-check-circle"></i>
                            Skills Development Programs
                        </li>
                        <li>
                            <i class="fas fa-check-circle"></i>
                            ETI & Section 12H Management
                        </li>
                        <li>
                            <i class="fas fa-check-circle"></i>
                            Payroll & Attendance Tracking
                        </li>
                        <li>
                            <i class="fas fa-check-circle"></i>
                            UIF & SDL Administration
                        </li>
                    </ul>
                </div>
            </div>
            <div class="col-lg-7">
                <div class="auth-section">
                    @yield('content')
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    @stack('scripts')
</body>

</html>