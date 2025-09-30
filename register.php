<?php
include 'includes/database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $password = password_hash($_POST["password"], PASSWORD_DEFAULT);
    $nama = $_POST["nama"];
    $email = $_POST["email"];

    $sql = "INSERT INTO users (username, password, nama, email, role) 
            VALUES ('$username', '$password', '$nama', '$email', 'user')";

    if ($conn->query($sql) === TRUE) {
        $success = "Registrasi berhasil! Silakan login.";
    } else {
        $error = "Error: " . $sql . "<br>" . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Helpdesk System</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .register-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            width: 100%;
            max-width: 450px;
            position: relative;
            overflow: hidden;
            margin-bottom: 20px;
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

        .register-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #667eea, #764ba2);
        }

        .register-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .register-header h1 {
            color: #2d3748;
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 8px;
        }

        .register-header p {
            color: #718096;
            font-size: 14px;
            font-weight: 400;
        }

        .form-group {
            margin-bottom: 20px;
            position: relative;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #4a5568;
            font-weight: 500;
            font-size: 14px;
        }

        .input-wrapper {
            position: relative;
        }

        .input-wrapper i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #a0aec0;
            font-size: 16px;
        }

        .form-control {
            width: 100%;
            padding: 14px 16px 14px 45px;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            font-size: 15px;
            font-family: 'Poppins', sans-serif;
            transition: all 0.3s ease;
            background: #f7fafc;
        }

        .form-control:focus {
            outline: none;
            border-color: #667eea;
            background: white;
            box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
        }

        .form-control::placeholder {
            color: #a0aec0;
        }

        .btn-register {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 600;
            font-family: 'Poppins', sans-serif;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
            position: relative;
        }

        .btn-register:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.6);
        }

        .btn-register:active {
            transform: translateY(0);
        }

        .login-link {
            text-align: center;
            margin-top: 25px;
            padding-top: 20px;
            border-top: 1px solid #e2e8f0;
        }

        .login-link p {
            color: #718096;
            font-size: 14px;
        }

        .login-link a {
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s ease;
        }

        .login-link a:hover {
            color: #764ba2;
        }

        .alert {
            padding: 14px 16px;
            border-radius: 12px;
            margin-bottom: 20px;
            font-size: 14px;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .alert-success {
            background: #d4edda;
            border: 2px solid #c3e6cb;
            color: #155724;
        }

        .alert-error {
            background: #fee;
            border: 2px solid #fc8181;
            color: #c53030;
        }

        .loading {
            display: none;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }

        .spinner {
            width: 20px;
            height: 20px;
            border: 2px solid #fff;
            border-top: 2px solid transparent;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Footer Styling */
        .register-footer {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            padding: 20px 30px;
            border-radius: 15px;
            text-align: center;
            color: white;
            max-width: 800px;
            width: 100%;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
            margin-top: 20px;
        }

        .footer-group-title {
            font-weight: 600;
            font-size: 15px;
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .footer-members {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 6px 18px;
            font-size: 13px;
            margin-bottom: 15px;
        }

        .footer-members p {
            margin: 0;
        }

        .footer-copyright {
            padding-top: 15px;
            border-top: 1px solid rgba(255, 255, 255, 0.2);
            font-size: 13px;
            opacity: 0.9;
        }

        /* Responsive Design */
        @media (max-width: 480px) {
            .register-container {
                padding: 30px 25px;
            }

            .register-header h1 {
                font-size: 24px;
            }

            .form-control {
                font-size: 15px;
            }

            .register-footer {
                padding: 15px 20px;
            }

            .footer-members {
                font-size: 12px;
                gap: 5px 12px;
            }
        }

        @media (max-width: 360px) {
            .register-container {
                padding: 25px 15px;
            }
        }
    </style>
</head>
<body>
    <div class="register-container">
        <div class="register-header">
            <h1>Buat Akun</h1>
            <p>Silakan lengkapi form untuk mendaftar</p>
        </div>

        <?php if (isset($error)): ?>
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i>
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <?php if (isset($success)): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                <?php echo $success; ?>
            </div>
        <?php endif; ?>

        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" id="registerForm">
            <div class="form-group">
                <label for="username">Username</label>
                <div class="input-wrapper">
                    <i class="fas fa-user"></i>
                    <input type="text" id="username" name="username" class="form-control" placeholder="Masukkan username" required>
                </div>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <div class="input-wrapper">
                    <i class="fas fa-lock"></i>
                    <input type="password" id="password" name="password" class="form-control" placeholder="Masukkan password" required>
                </div>
            </div>

            <div class="form-group">
                <label for="nama">Nama Lengkap</label>
                <div class="input-wrapper">
                    <i class="fas fa-id-card"></i>
                    <input type="text" id="nama" name="nama" class="form-control" placeholder="Masukkan nama lengkap" required>
                </div>
            </div>

            <div class="form-group">
                <label for="email">Email</label>
                <div class="input-wrapper">
                    <i class="fas fa-envelope"></i>
                    <input type="email" id="email" name="email" class="form-control" placeholder="Masukkan email" required>
                </div>
            </div>

            <button type="submit" class="btn-register">
                <span class="btn-text">Daftar Sekarang</span>
                <div class="loading">
                    <div class="spinner"></div>
                </div>
            </button>
        </form>

        <div class="login-link">
            <p>Sudah punya akun? <a href="index.php">Login di sini</a></p>
        </div>
    </div>

    <footer class="register-footer">
        <div class="footer-group-title">
            <i class="fa-solid fa-users"></i> Kelompok 1
        </div>
        <div class="footer-members">
            <p>• Mohamad Taufik Wibowo</p>
            <p>• Fabian Jason Song</p>
            <p>• Ridwan Abdillah</p>
            <p>• Reiksa Azra Octavian</p>
        </div>
        <div class="footer-copyright">
            &copy; <?php echo date('Y'); ?> Helpdesk System. Didesain dengan <i class="fa-solid fa-heart" style="color: #ef4444;"></i>
        </div>
    </footer>

    <script>
        // Add loading animation on form submit
        document.getElementById('registerForm').addEventListener('submit', function() {
            const btn = document.querySelector('.btn-register');
            const btnText = document.querySelector('.btn-text');
            const loading = document.querySelector('.loading');
            
            btnText.style.opacity = '0';
            loading.style.display = 'block';
            btn.disabled = true;
        });

        // Add focus effects
        document.querySelectorAll('.form-control').forEach(input => {
            input.addEventListener('focus', function() {
                this.parentElement.style.transform = 'scale(1.01)';
            });
            
            input.addEventListener('blur', function() {
                this.parentElement.style.transform = 'scale(1)';
            });
        });
    </script>
</body>
</html>