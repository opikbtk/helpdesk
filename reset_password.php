<?php
include 'includes/database.php';

$success = '';
$error = '';
$token_valid = false;
$token = '';

// Cek apakah token ada di URL
if (isset($_GET['token']) && !empty($_GET['token'])) {
    $token = $_GET['token'];

    // Validasi token
    $stmt = $conn->prepare("SELECT id, username, email FROM users WHERE reset_token = ? AND reset_token_expiry > NOW()");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $token_valid = true;
        $user = $result->fetch_assoc();
    } else {
        $error = "Link reset password tidak valid atau sudah kadaluarsa.";
    }
    $stmt->close();
} else {
    $error = "Token tidak ditemukan.";
}

// Proses reset password
if ($_SERVER["REQUEST_METHOD"] == "POST" && $token_valid) {
    $new_password = $_POST["new_password"];
    $confirm_password = $_POST["confirm_password"];

    if (empty($new_password) || empty($confirm_password)) {
        $error = "Semua field harus diisi.";
    } elseif (strlen($new_password) < 6) {
        $error = "Password minimal 6 karakter.";
    } elseif ($new_password !== $confirm_password) {
        $error = "Password dan konfirmasi password tidak sama.";
    } else {
        // Hash password baru
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

        // Update password dan hapus token
        $stmt = $conn->prepare("UPDATE users SET password = ?, reset_token = NULL, reset_token_expiry = NULL WHERE reset_token = ?");
        $stmt->bind_param("ss", $hashed_password, $token);

        if ($stmt->execute()) {
            $success = "Password berhasil direset! Anda akan diarahkan ke halaman login...";
            header("refresh:3;url=index.php");
        } else {
            $error = "Gagal mereset password: " . $conn->error;
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - Sistem Helpdesk</title>
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
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .reset-container {
            width: 100%;
            max-width: 450px;
            animation: slideUp 0.6s ease-out;
            margin-bottom: 20px;
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

        .reset-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        }

        .reset-header {
            text-align: center;
            margin-bottom: 35px;
        }

        .logo {
            width: 70px;
            height: 70px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.4);
        }

        .logo i {
            font-size: 35px;
            color: white;
        }

        h2 {
            color: #2d3748;
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 8px;
        }

        .subtitle {
            color: #718096;
            font-size: 14px;
            font-weight: 400;
            line-height: 1.6;
        }

        .error-message {
            background: #fee;
            color: #c53030;
            padding: 12px 16px;
            border-radius: 10px;
            margin-bottom: 20px;
            font-size: 14px;
            border-left: 4px solid #fc8181;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .success-message {
            background: #f0fdf4;
            color: #166534;
            padding: 12px 16px;
            border-radius: 10px;
            margin-bottom: 20px;
            font-size: 14px;
            border-left: 4px solid #22c55e;
            line-height: 1.6;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .input-group {
            margin-bottom: 24px;
        }

        label {
            display: block;
            color: #4a5568;
            font-size: 14px;
            font-weight: 500;
            margin-bottom: 8px;
        }

        .input-wrapper {
            position: relative;
        }

        input[type="password"] {
            width: 100%;
            padding: 14px 16px;
            padding-right: 45px;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            font-size: 15px;
            font-family: 'Poppins', sans-serif;
            transition: all 0.3s ease;
            background: #f7fafc;
        }

        input[type="password"]:focus {
            outline: none;
            border-color: #667eea;
            background: white;
            box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
        }

        input::placeholder {
            color: #a0aec0;
        }

        .password-toggle {
            position: absolute;
            right: 16px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #a0aec0;
            transition: color 0.3s ease;
        }

        .password-toggle:hover {
            color: #667eea;
        }

        button[type="submit"] {
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
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        button[type="submit"]:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.6);
        }

        button[type="submit"]:active {
            transform: translateY(0);
        }

        .back-to-login {
            text-align: center;
            margin-top: 25px;
        }

        .back-to-login a {
            color: #667eea;
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            transition: color 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .back-to-login a:hover {
            color: #764ba2;
        }

        small {
            color: #718096;
            font-size: 12px;
        }

        /* Footer Styling */
        .reset-footer {
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

        @media (max-width: 480px) {
            .reset-card {
                padding: 30px 25px;
            }

            h2 {
                font-size: 24px;
            }

            .logo {
                width: 60px;
                height: 60px;
            }

            .logo i {
                font-size: 30px;
            }

            .reset-footer {
                padding: 15px 20px;
            }

            .footer-members {
                font-size: 12px;
                gap: 5px 12px;
            }
        }
    </style>
</head>
<body>
    <div class="reset-container">
        <div class="reset-card">
            <div class="reset-header">
                <div class="logo">
                    <i class="fa-solid fa-lock-open"></i>
                </div>
                <h2>Reset Password</h2>
                <p class="subtitle">Masukkan password baru untuk akun Anda</p>
            </div>

            <?php if (!$token_valid): ?>
                <div class="error-message">
                    <i class="fa-solid fa-circle-exclamation"></i>
                    <span><?php echo $error; ?></span>
                </div>
                <div class="back-to-login">
                    <a href="forgot_password.php">
                        <i class="fa-solid fa-arrow-left"></i>
                        Kirim Link Reset Lagi
                    </a>
                </div>
            <?php else: ?>
                <form method="post" action="">
                    <?php if (!empty($error)): ?>
                    <div class="error-message">
                        <i class="fa-solid fa-circle-exclamation"></i>
                        <span><?php echo $error; ?></span>
                    </div>
                    <?php endif; ?>

                    <?php if (!empty($success)): ?>
                    <div class="success-message">
                        <i class="fa-solid fa-circle-check"></i>
                        <span><?php echo $success; ?></span>
                    </div>
                    <?php endif; ?>

                    <div class="input-group">
                        <label for="new_password">Password Baru</label>
                        <div class="input-wrapper">
                            <input type="password" id="new_password" name="new_password" placeholder="Masukkan password baru" required minlength="6">
                            <span class="password-toggle" onclick="togglePassword('new_password')">
                                <i class="fa-solid fa-eye" id="eye-icon-new"></i>
                            </span>
                        </div>
                        <small>Minimal 6 karakter</small>
                    </div>

                    <div class="input-group">
                        <label for="confirm_password">Konfirmasi Password</label>
                        <div class="input-wrapper">
                            <input type="password" id="confirm_password" name="confirm_password" placeholder="Ulangi password baru" required minlength="6">
                            <span class="password-toggle" onclick="togglePassword('confirm_password')">
                                <i class="fa-solid fa-eye" id="eye-icon-confirm"></i>
                            </span>
                        </div>
                    </div>

                    <button type="submit">
                        <i class="fa-solid fa-check"></i>
                        Reset Password
                    </button>

                    <div class="back-to-login">
                        <a href="index.php">
                            <i class="fa-solid fa-arrow-left"></i>
                            Kembali ke Login
                        </a>
                    </div>
                </form>
            <?php endif; ?>
        </div>
    </div>

    <footer class="reset-footer">
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
        function togglePassword(inputId) {
            const input = document.getElementById(inputId);
            const icon = document.getElementById('eye-icon-' + inputId.split('_')[0]);

            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }

        // Animasi untuk input focus
        const inputs = document.querySelectorAll('input');
        inputs.forEach(input => {
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
