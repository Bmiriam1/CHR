@ -1,463 +0,0 @@
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport"
        content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0" />

    <title>Skills Development Portal - Login</title>
    <link rel="icon" type="image/png" href="https://file.rendit.io/n/8rsfF8X8pQpNnTI0Dd3P.svg" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Poppins:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap"
        rel="stylesheet" />

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #483a8e 0%, #cd3d56 100%);
            color: #1e293b;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .login-container {
            width: 100%;
            max-width: 400px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .logo-section {
            text-align: center;
            margin-bottom: 2rem;
        }

        .logo {
            width: 80px;
            height: 80px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            font-size: 2rem;
            color: white;
        }

        .main-title {
            color: white;
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .subtitle {
            color: rgba(255, 255, 255, 0.8);
            font-size: 0.9rem;
        }

        .role-selector {
            background: white;
            border-radius: 12px;
            padding: 0.5rem;
            margin-bottom: 1.5rem;
            display: flex;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .role-option {
            flex: 1;
            padding: 0.75rem 1rem;
            border-radius: 8px;
            cursor: pointer;
            text-align: center;
            transition: all 0.3s ease;
            border: 2px solid transparent;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.5rem;
        }

        .role-option.active {
            background: #4f46e5;
            color: white;
            border-color: #4f46e5;
        }

        .role-option:not(.active) {
            color: #64748b;
        }

        .role-option:not(.active):hover {
            background: #f1f5f9;
        }

        .role-icon {
            font-size: 1.2rem;
        }

        .role-text {
            font-size: 0.9rem;
            font-weight: 500;
        }

        .login-form {
            background: white;
            border-radius: 16px;
            padding: 2rem;
            width: 100%;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }

        .form-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 0.5rem;
            text-align: center;
        }

        .form-description {
            color: #64748b;
            font-size: 0.9rem;
            text-align: center;
            margin-bottom: 1.5rem;
        }

        .form-group {
            margin-bottom: 1rem;
        }

        .form-label {
            display: block;
            font-size: 0.9rem;
            font-weight: 500;
            color: #374151;
            margin-bottom: 0.5rem;
        }

        .form-input {
            width: 100%;
            padding: 0.75rem 1rem 0.75rem 2.5rem;
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            font-size: 0.9rem;
            transition: all 0.3s ease;
            background: white;
        }

        .form-input:focus {
            outline: none;
            border-color: #4f46e5;
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
        }

        .input-wrapper {
            position: relative;
        }

        .input-icon {
            position: absolute;
            left: 0.75rem;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
            font-size: 0.9rem;
        }

        .password-toggle {
            position: absolute;
            right: 0.75rem;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #9ca3af;
            font-size: 0.9rem;
        }

        .form-options {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin: 1rem 0;
        }

        .checkbox-wrapper {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .form-checkbox {
            width: 1rem;
            height: 1rem;
            accent-color: #4f46e5;
        }

        .forgot-link {
            color: #4f46e5;
            text-decoration: none;
            font-size: 0.9rem;
        }

        .forgot-link:hover {
            text-decoration: underline;
        }

        .btn {
            width: 100%;
            padding: 0.75rem 1rem;
            border: none;
            border-radius: 8px;
            font-size: 0.9rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 1rem;
        }

        .btn-primary {
            background: #4f46e5;
            color: white;
        }

        .btn-primary:hover {
            background: #3730a3;
        }

        .btn-primary:disabled {
            background: #9ca3af;
            cursor: not-allowed;
        }

        .create-account {
            text-align: center;
            margin-top: 1rem;
            font-size: 0.9rem;
            color: #64748b;
        }

        .create-account a {
            color: #4f46e5;
            text-decoration: none;
        }

        .create-account a:hover {
            text-decoration: underline;
        }

        .footer-links {
            display: flex;
            justify-content: center;
            gap: 1rem;
            margin-top: 2rem;
            font-size: 0.8rem;
        }

        .footer-links a {
            color: rgba(255, 255, 255, 0.7);
            text-decoration: none;
        }

        .footer-links a:hover {
            color: white;
            text-decoration: underline;
        }

        .error-message {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #dc2626;
            padding: 0.75rem;
            border-radius: 8px;
            margin-bottom: 1rem;
            font-size: 0.9rem;
        }

        .hidden {
            display: none;
        }
    </style>
</head>

<body>
    <div class="login-container">
        <!-- Logo and Title -->
        <div class="logo-section">
            <div class="logo">
                <img src="{{ asset('assets/images/logo-white.svg') }}" alt="logo" height="80" />
            </div>
        </div>

        <!-- Role Selector -->
        <div class="role-selector">
            <div class="role-option active" data-role="learner">
                <div class="role-icon">🎓</div>
                <div class="role-text">Learner</div>
            </div>
            <div class="role-option" data-role="company">
                <div class="role-icon">🏢</div>
                <div class="role-text">Company</div>
            </div>
        </div>

        <!-- Login Form -->
        <div class="login-form">
            @if ($errors->any())
                <div class="error-message">
                    @foreach ($errors->all() as $error)
                        {{ $error }}
                    @endforeach
                </div>
            @endif

            <!-- Learner Login Form -->
            <form method="POST" action="{{ route('login') }}" id="learner-form">
                @csrf
                <input type="hidden" name="role" value="learner">

                <h3 class="form-title">Learner Login</h3>
                <p class="form-description">Access your training programs and progress</p>

                <div class="form-group">
                    <label class="form-label" for="learner-email">Email:</label>
                    <div class="input-wrapper">
                        <input type="email" id="learner-email" name="email" class="form-input"
                            placeholder="student@example.com" value="{{ old('email') }}" required>
                        <span class="input-icon">📧</span>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="learner-password">Password:</label>
                    <div class="input-wrapper">
                        <input type="password" id="learner-password" name="password" class="form-input"
                            placeholder="Enter Password" required>
                        <span class="input-icon">🔒</span>
                        <span class="password-toggle" onclick="togglePassword('learner-password')">👁️</span>
                    </div>
                </div>

                <div class="form-options">
                    <div class="checkbox-wrapper">
                        <input type="checkbox" id="learner-remember" name="remember" class="form-checkbox">
                        <label for="learner-remember">Remember me</label>
                    </div>
                    <a href="{{ route('password.request') }}" class="forgot-link">Forgot Password?</a>
                </div>

                <button type="submit" class="btn btn-primary">Sign In as Learner</button>
            </form>

            <!-- Company Login Form -->
            <form method="POST" action="{{ route('login') }}" id="company-form" class="hidden">
                @csrf
                <input type="hidden" name="role" value="company">

                <h3 class="form-title">Company Login</h3>
                <p class="form-description">Manage programs and compliance</p>

                <div class="form-group">
                    <label class="form-label" for="company-email">Email:</label>
                    <div class="input-wrapper">
                        <input type="email" id="company-email" name="email" class="form-input"
                            placeholder="admin@company.com" value="{{ old('email') }}" required>
                        <span class="input-icon">📧</span>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="company-password">Password:</label>
                    <div class="input-wrapper">
                        <input type="password" id="company-password" name="password" class="form-input"
                            placeholder="Enter Password" required>
                        <span class="input-icon">🔒</span>
                        <span class="password-toggle" onclick="togglePassword('company-password')">👁️</span>
                    </div>
                </div>

                <div class="form-options">
                    <div class="checkbox-wrapper">
                        <input type="checkbox" id="company-remember" name="remember" class="form-checkbox">
                        <label for="company-remember">Remember me</label>
                    </div>
                    <a href="{{ route('password.request') }}" class="forgot-link">Forgot Password?</a>
                </div>

                <button type="submit" class="btn btn-primary">Sign In to Company Portal</button>
            </form>

            <div class="create-account">
                <span>Don't have an account? </span>
                <a href="{{ route('register') }}">Create account</a>
            </div>
        </div>

        <!-- Footer Links -->
        <div class="footer-links">
            <a href="#">Privacy Notice</a>
            <a href="#">Terms of Service</a>
        </div>
    </div>

    <script>
        // Role selection functionality
        document.addEventListener('DOMContentLoaded', function () {
            const roleOptions = document.querySelectorAll('.role-option');
            const learnerForm = document.getElementById('learner-form');
            const companyForm = document.getElementById('company-form');

            roleOptions.forEach(option => {
                option.addEventListener('click', function () {
                    // Remove active class from all options
                    roleOptions.forEach(opt => opt.classList.remove('active'));

                    // Add active class to clicked option
                    this.classList.add('active');

                    // Show/hide appropriate form
                    const role = this.dataset.role;
                    if (role === 'learner') {
                        learnerForm.classList.remove('hidden');
                        companyForm.classList.add('hidden');
                    } else {
                        learnerForm.classList.add('hidden');
                        companyForm.classList.remove('hidden');
                    }
                });
            });
        });

        // Password toggle functionality
        function togglePassword(inputId) {
            const input = document.getElementById(inputId);
            const toggle = input.nextElementSibling.nextElementSibling;

            if (input.type === 'password') {
                input.type = 'text';
                toggle.textContent = '🙈';
            } else {
                input.type = 'password';
                toggle.textContent = '👁️';
            }
        }
    </script>
</body>

</html>