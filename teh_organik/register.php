<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi - Teh Organik</title>
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

        .register-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .register-card {
            background: var(--white);
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(45, 80, 22, 0.1);
            overflow: hidden;
            max-width: 500px;
            width: 100%;
            border: 1px solid rgba(74, 124, 40, 0.1);
        }

        .register-header {
            background: linear-gradient(135deg, var(--primary-green) 0%, var(--light-green) 100%);
            color: var(--white);
            padding: 30px;
            text-align: center;
        }

        .register-header h2 {
            margin: 0;
            font-weight: 600;
            font-size: 1.8rem;
        }

        .register-header .tea-icon {
            font-size: 3rem;
            margin-bottom: 15px;
            color: var(--accent-gold);
        }

        .register-body {
            padding: 40px 30px;
            max-height: 60vh;
            overflow-y: auto;
        }

        .form-control, .form-select {
            border: 2px solid #e8f0e3;
            border-radius: 10px;
            padding: 12px 15px;
            font-size: 0.95rem;
            transition: all 0.3s ease;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--light-green);
            box-shadow: 0 0 0 0.2rem rgba(74, 124, 40, 0.1);
        }

        .input-group-text {
            background: var(--cream);
            border: 2px solid #e8f0e3;
            border-right: none;
            color: var(--primary-green);
        }

        .btn-register {
            background: linear-gradient(135deg, var(--primary-green) 0%, var(--light-green) 100%);
            border: none;
            border-radius: 10px;
            padding: 12px;
            font-weight: 600;
            font-size: 1rem;
            transition: all 0.3s ease;
            width: 100%;
        }

        .btn-register:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(74, 124, 40, 0.3);
        }

        .login-link {
            text-align: center;
            margin-top: 20px;
        }

        .login-link a {
            color: var(--light-green);
            text-decoration: none;
            font-weight: 600;
        }

        .login-link a:hover {
            color: var(--primary-green);
            text-decoration: underline;
        }

        .alert {
            border-radius: 10px;
            border: none;
            margin-bottom: 20px;
        }

        .form-label {
            font-weight: 600;
            color: var(--primary-green);
            margin-bottom: 8px;
        }

        .role-selector {
            display: flex;
            gap: 15px;
            margin-top: 10px;
        }

        .role-option {
            flex: 1;
            position: relative;
        }

        .role-option input[type="radio"] {
            position: absolute;
            opacity: 0;
        }

        .role-option label {
            display: block;
            padding: 15px;
            border: 2px solid #e8f0e3;
            border-radius: 10px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
            margin: 0;
        }

        .role-option input[type="radio"]:checked + label {
            background: var(--cream);
            border-color: var(--light-green);
            color: var(--primary-green);
            font-weight: 600;
        }

        .role-option label:hover {
            border-color: var(--light-green);
            background: rgba(74, 124, 40, 0.05);
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

    <div class="register-container">
        <div class="register-card">
            <div class="register-header">
                <div class="tea-icon">
                    <i class="fas fa-leaf"></i>
                </div>
                <h2>Daftar Akun Baru</h2>
                <p class="mb-0">Bergabung dengan Teh Organik</p>
            </div>
            
            <div class="register-body">
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

                <form action="proses_register.php" method="POST">
                    <div class="mb-3">
                        <label for="nama_lengkap" class="form-label">Nama Lengkap</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-user"></i>
                            </span>
                            <input type="text" class="form-control" id="nama_lengkap" name="nama_lengkap" 
                                   placeholder="Masukkan nama lengkap Anda" required>
                        </div>
                    </div>
                    
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
                    
                    <div class="mb-3">
                        <label for="no_telepon" class="form-label">Nomor Telepon</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-phone"></i>
                            </span>
                            <input type="tel" class="form-control" id="no_telepon" name="no_telepon" 
                                   placeholder="Masukkan nomor telepon Anda" required>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="alamat" class="form-label">Alamat</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-map-marker-alt"></i>
                            </span>
                            <textarea class="form-control" id="alamat" name="alamat" rows="3" 
                                      placeholder="Masukkan alamat lengkap Anda" required></textarea>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Pilih Role</label>
                        <div class="role-selector">
                            <div class="role-option">
                                <input type="radio" id="pelanggan" name="role" value="pelanggan" checked>
                                <label for="pelanggan">
                                    <i class="fas fa-shopping-cart d-block mb-2"></i>
                                    Pelanggan
                                </label>
                            </div>
                            <div class="role-option">
                                <input type="radio" id="admin" name="role" value="admin">
                                <label for="admin">
                                    <i class="fas fa-user-shield d-block mb-2"></i>
                                    Admin
                                </label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-lock"></i>
                            </span>
                            <input type="password" class="form-control" id="password" name="password" 
                                   placeholder="Masukkan password Anda" required>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <label for="konfirmasi_password" class="form-label">Konfirmasi Password</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-lock"></i>
                            </span>
                            <input type="password" class="form-control" id="konfirmasi_password" name="konfirmasi_password" 
                                   placeholder="Konfirmasi password Anda" required>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary btn-register">
                        <i class="fas fa-user-plus me-2"></i>Daftar Sekarang
                    </button>
                </form>
                
                <div class="login-link">
                    <p class="mb-0">Sudah punya akun? <a href="login.php">Masuk di sini</a></p>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Validasi password konfirmasi
        document.getElementById('konfirmasi_password').addEventListener('input', function() {
            const password = document.getElementById('password').value;
            const konfirmasi = this.value;
            
            if (password !== konfirmasi) {
                this.setCustomValidity('Password tidak cocok!');
            } else {
                this.setCustomValidity('');
            }
        });
    </script>
</body>
</html>