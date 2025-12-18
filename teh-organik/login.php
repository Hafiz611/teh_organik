<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Teh Organik</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-green: #2d5016;
            --light-green: #4a7c28;
            --accent-gold: #d4af37;
            --cream: #f8f5f0;
            --white: #ffffff;
        }

        body {
            background: linear-gradient(135deg, var(--cream) 0%, var(--white) 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .login-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .login-card {
            background: var(--white);
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(45, 80, 22, 0.1);
            overflow: hidden;
            max-width: 400px;
            width: 100%;
            border: 1px solid rgba(74, 124, 40, 0.1);
        }

        .login-header {
            background: linear-gradient(135deg, var(--primary-green) 0%, var(--light-green) 100%);
            color: var(--white);
            padding: 30px;
            text-align: center;
        }

        .login-header h2 {
            margin: 0;
            font-weight: 600;
            font-size: 1.8rem;
        }

        .login-header .tea-icon {
            font-size: 3rem;
            margin-bottom: 15px;
            color: var(--accent-gold);
        }

        .login-body {
            padding: 40px 30px;
        }

        .form-control {
            border: 2px solid #e8f0e3;
            border-radius: 10px;
            padding: 12px 15px;
            font-size: 0.95rem;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: var(--light-green);
            box-shadow: 0 0 0 0.2rem rgba(74, 124, 40, 0.1);
        }

        .input-group-text {
            background: var(--cream);
            border: 2px solid #e8f0e3;
            border-right: none;
            color: var(--primary-green);
        }

        .btn-login {
            background: linear-gradient(135deg, var(--primary-green) 0%, var(--light-green) 100%);
            border: none;
            border-radius: 10px;
            padding: 12px;
            font-weight: 600;
            font-size: 1rem;
            transition: all 0.3s ease;
            width: 100%;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(74, 124, 40, 0.3);
        }

        .divider {
            text-align: center;
            margin: 25px 0;
            position: relative;
        }

        .divider::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 1px;
            background: #e8f0e3;
        }

        .divider span {
            background: var(--white);
            padding: 0 15px;
            color: #6c757d;
            font-size: 0.9rem;
        }

        .register-link {
            text-align: center;
            margin-top: 20px;
        }

        .register-link a {
            color: var(--light-green);
            text-decoration: none;
            font-weight: 600;
        }

        .register-link a:hover {
            color: var(--primary-green);
            text-decoration: underline;
        }

        .alert {
            border-radius: 10px;
            border: none;
            margin-bottom: 20px;
        }

        .floating-tea {
            position: fixed;
            font-size: 2rem;
            color: rgba(212, 175, 55, 0.1);
            animation: float 6s ease-in-out infinite;
            z-index: -1;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }

        .tea-1 { top: 10%; left: 10%; animation-delay: 0s; }
        .tea-2 { top: 20%; right: 15%; animation-delay: 2s; }
        .tea-3 { bottom: 20%; left: 15%; animation-delay: 4s; }
        .tea-4 { bottom: 10%; right: 10%; animation-delay: 1s; }
    </style>
</head>
<body>
    <!-- Floating Tea Icons -->
    <i class="fas fa-mug-hot floating-tea tea-1"></i>
    <i class="fas fa-leaf floating-tea tea-2"></i>
    <i class="fas fa-mug-hot floating-tea tea-3"></i>
    <i class="fas fa-leaf floating-tea tea-4"></i>

    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <div class="tea-icon">
                    <i class="fas fa-mug-hot"></i>
                </div>
                <h2>Selamat Datang</h2>
                <p class="mb-0">Masuk ke Akun Teh Organik Anda</p>
            </div>
            
            <div class="login-body">
                <?php
                if (isset($_GET['error'])) {
                    echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-triangle me-2"></i>' . htmlspecialchars($_GET['error']) . '
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                          </div>';
                }
                if (isset($_GET['success'])) {
                    echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i>' . htmlspecialchars($_GET['success']) . '
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                          </div>';
                }
                ?>

                <form action="proses_login.php" method="POST">
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-envelope"></i>
                            </span>
                            <input type="email" class="form-control" id="email" name="email" 
                                   placeholder="Masukkan email Anda" required>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <label for="password" class="form-label">Password</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-lock"></i>
                            </span>
                            <input type="password" class="form-control" id="password" name="password" 
                                   placeholder="Masukkan password Anda" required>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary btn-login">
                        <i class="fas fa-sign-in-alt me-2"></i>Masuk
                    </button>
                </form>
                
                <div class="divider">
                    <span>atau</span>
                </div>
                
                <div class="register-link">
                    <p class="mb-0">Belum punya akun? <a href="register.php">Daftar sekarang</a></p>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>