<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login dulu</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html, body {
            height: 100%;
            width: 100%;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            overflow: hidden;
        }

        .login-wrapper {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            position: relative;
        }

        /* Background dengan overlay */
        .login-wrapper::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(
                135deg,
                rgba(44, 62, 80, 0.85) 0%,
                rgba(52, 73, 94, 0.85) 50%,
                rgba(127, 140, 141, 0.85) 100%
            );
            z-index: 1;
        }

        /* Animated background particles */
        .particles {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            z-index: 0;
            overflow: hidden;
        }

        .particle {
            position: absolute;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            animation: float 6s infinite ease-in-out;
        }

        @keyframes float {
            0%, 100% {
                transform: translateY(0px) translateX(0px);
                opacity: 0;
            }
            50% {
                opacity: 1;
            }
        }

        /* Background image */
        .bg-image {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1920 1080"><defs><linearGradient id="grad1" x1="0%25" y1="0%25" x2="100%25" y2="100%25"><stop offset="0%25" style="stop-color:rgb(102,126,234);stop-opacity:0.3" /><stop offset="100%25" style="stop-color:rgb(118,75,162);stop-opacity:0.3" /></linearGradient></defs><rect width="1920" height="1080" fill="url(%23grad1)"/><circle cx="100" cy="100" r="80" fill="rgba(52,152,219,0.1)"/><circle cx="1820" cy="980" r="100" fill="rgba(39,174,96,0.1)"/><path d="M0,540 Q480,450 960,540 T1920,540" stroke="rgba(255,255,255,0.05)" stroke-width="2" fill="none"/></svg>') center/cover no-repeat;
            z-index: 0;
        }

        .login-container {
            width: 100%;
            max-width: 450px;
            z-index: 2;
            position: relative;
        }

        .login-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.2);
            animation: slideUp 0.6s ease-out;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .login-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 50px 30px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .login-header::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200px;
            height: 200px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            animation: float 8s infinite ease-in-out;
        }

        .login-header::after {
            content: '';
            position: absolute;
            bottom: -50%;
            left: -50%;
            width: 150px;
            height: 150px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            animation: float 6s infinite ease-in-out reverse;
        }

        .login-header h1 {
            font-size: 32px;
            margin-bottom: 10px;
            font-weight: 700;
            position: relative;
            z-index: 1;
        }

        .login-header .icon {
            font-size: 48px;
            margin-bottom: 15px;
            display: block;
            position: relative;
            z-index: 1;
        }

        .login-header p {
            opacity: 0.95;
            font-size: 14px;
            position: relative;
            z-index: 1;
        }

        .login-body {
            padding: 45px 35px;
        }

        .form-group {
            margin-bottom: 25px;
        }

        .form-label {
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .form-label i {
            margin-right: 8px;
            color: #667eea;
        }

        .input-group {
            position: relative;
        }

        .form-control {
            border: 2px solid #ecf0f1;
            padding: 14px 16px;
            border-radius: 10px;
            font-size: 14px;
            transition: all 0.3s ease;
            background: rgba(255, 255, 255, 0.9);
        }

        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
            outline: none;
            background: white;
        }

        .form-control::placeholder {
            color: #bdc3c7;
        }

        .form-control.is-invalid {
            border-color: #e74c3c;
        }

        .form-control.is-invalid:focus {
            box-shadow: 0 0 0 4px rgba(231, 76, 60, 0.1);
        }

        .invalid-feedback {
            color: #e74c3c;
            font-size: 12px;
            margin-top: 8px;
            display: block;
            animation: shake 0.3s ease;
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
        }

        .btn-login {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 14px 20px;
            border-radius: 10px;
            font-weight: 600;
            width: 100%;
            transition: all 0.3s ease;
            font-size: 16px;
            text-transform: uppercase;
            letter-spacing: 1px;
            cursor: pointer;
        }

        .btn-login:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.4);
            color: white;
        }

        .btn-login:active {
            transform: translateY(-1px);
        }

        .btn-login:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        .remember-me {
            display: flex;
            align-items: center;
            margin-bottom: 25px;
            gap: 8px;
        }

        .remember-me input[type="checkbox"] {
            width: 18px;
            height: 18px;
            cursor: pointer;
            accent-color: #667eea;
        }

        .remember-me label {
            color: #7f8c8d;
            font-size: 13px;
            cursor: pointer;
            margin: 0;
        }

        .alert {
            border-radius: 10px;
            border: none;
            margin-bottom: 20px;
            animation: slideDown 0.4s ease;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .alert-danger {
            background-color: #fadbd8;
            color: #c0392b;
            border-left: 4px solid #e74c3c;
        }

        .alert-danger strong {
            color: #922b21;
        }

        .alert-danger ul {
            margin: 10px 0 0 20px;
        }

        .alert-danger li {
            font-size: 12px;
            margin: 5px 0;
        }

        .footer-text {
            text-align: center;
            color: #95a5a6;
            font-size: 12px;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #ecf0f1;
        }

        .footer-text p {
            margin: 0;
        }

        .divider {
            text-align: center;
            color: #95a5a6;
            font-size: 12px;
            margin: 25px 0;
        }

        .info-box {
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);
            border-left: 4px solid #667eea;
            padding: 12px 15px;
            border-radius: 8px;
            font-size: 12px;
            color: #2c3e50;
            margin-bottom: 20px;
        }

        .info-box strong {
            color: #667eea;
        }

        /* Loading state */
        .btn-login.loading {
            pointer-events: none;
            opacity: 0.7;
        }

        .spinner-border {
            width: 16px;
            height: 16px;
            margin-right: 8px;
        }

        @media (max-width: 576px) {
            .login-container {
                margin: 20px;
            }

            .login-header {
                padding: 35px 25px;
            }

            .login-header h1 {
                font-size: 26px;
            }

            .login-header .icon {
                font-size: 40px;
            }

            .login-body {
                padding: 30px 25px;
            }

            .form-control {
                padding: 12px 14px;
                font-size: 13px;
            }
        }
    </style>
</head>
<body>
    <!-- Background -->
    <div class="login-wrapper">
        <div class="bg-image"></div>
        <div class="particles" id="particles"></div>

        <!-- Login Card -->
        <div class="login-container">
            <div class="login-card">
                <!-- Body -->
                <div class="login-body">
                    <!-- Alert Error -->
                    @if ($errors->any())
                        <div class="alert alert-danger" role="alert">
                            <strong><i class="fas fa-exclamation-circle"></i> Login Gagal!</strong>
                            <ul class="mb-0 mt-2">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <!-- Form Login -->
                    <form method="POST" action="{{ route('login') }}" id="loginForm">
                        @csrf

                        <!-- Email -->
                        <div class="form-group">
                            <label for="email" class="form-label"> Username
                            </label>
                            <div class="input-group">
                                <input 
                                    type="email" 
                                    class="form-control @error('email') is-invalid @enderror" 
                                    id="email" 
                                    name="email" 
                                    value="{{ old('email') }}"
                                    placeholder="Masukkan email Anda"
                                    required 
                                    autofocus
                                >
                            </div>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Password -->
                        <div class="form-group">
                            <label for="password" class="form-label">Password
                            </label>
                            <div class="input-group">
                                <input 
                                    type="password" 
                                    class="form-control @error('password') is-invalid @enderror" 
                                    id="password" 
                                    name="password" 
                                    placeholder="Masukkan password Anda"
                                    required
                                >
                            </div>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Remember Me -->
                        <div class="remember-me">
                            <input 
                                type="checkbox" 
                                id="remember" 
                                name="remember"
                                value="1"
                            >
                            <label for="remember">Ingat saya di perangkat ini</label>
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" class="btn btn-login" id="submitBtn">
                            <i class="fas fa-sign-in-alt"></i> Masuk
                        </button>
                    </form>

                    <!-- Footer -->
                    <div class="footer-text">
                        <p style="font-size: 13px;">© 2026 All Right Reserved</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
    
    <script>
        // Create floating particles
        function createParticles() {
            const particlesContainer = document.getElementById('particles');
            const particleCount = 30;

            for (let i = 0; i < particleCount; i++) {
                const particle = document.createElement('div');
                particle.className = 'particle';
                
                const size = Math.random() * 80 + 20;
                const duration = Math.random() * 4 + 3;
                const delay = Math.random() * 2;
                const left = Math.random() * 100;
                const top = Math.random() * 100;

                particle.style.width = size + 'px';
                particle.style.height = size + 'px';
                particle.style.left = left + '%';
                particle.style.top = top + '%';
                particle.style.animationDuration = duration + 's';
                particle.style.animationDelay = delay + 's';

                particlesContainer.appendChild(particle);
            }
        }

        // Initialize particles on load
        document.addEventListener('DOMContentLoaded', createParticles);

        // Form submission loading state
        document.getElementById('loginForm').addEventListener('submit', function() {
            const submitBtn = document.getElementById('submitBtn');
            submitBtn.classList.add('loading');
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>Memproses...';
        });

        // Auto-clear error after 5 seconds
        const alertElement = document.querySelector('.alert-danger');
        if (alertElement) {
            setTimeout(function() {
                alertElement.style.transition = 'opacity 0.5s ease';
                alertElement.style.opacity = '0';
                setTimeout(function() {
                    alertElement.remove();
                }, 500);
            }, 5000);
        }
    </script>
</body>
</html>