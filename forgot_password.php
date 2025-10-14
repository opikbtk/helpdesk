<?php
include 'includes/database.php';

$success = '';
$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"]);

    if (empty($email)) {
        $error = "Email tidak boleh kosong.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Format email tidak valid.";
    } else {
        // Cek apakah email ada di database
        $stmt = $conn->prepare("SELECT id, username, nama FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();

            // Generate token reset password
            $token = bin2hex(random_bytes(32));
            $token_expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));

            // Simpan token ke database (Anda perlu menambahkan kolom reset_token dan reset_token_expiry di tabel users)
            $stmt = $conn->prepare("UPDATE users SET reset_token = ?, reset_token_expiry = ? WHERE email = ?");
            $stmt->bind_param("sss", $token, $token_expiry, $email);

            if ($stmt->execute()) {
                // Di production, kirim email dengan link reset password
                // Untuk development, tampilkan link reset password
                $reset_link = "http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . "/reset_password.php?token=" . $token;

                $success = "Link reset password telah dibuat! <br><br>";
                $success .= "<strong>Link Reset Password:</strong><br>";
                $success .= "<a href='" . $reset_link . "' style='color: #667eea; word-break: break-all;'>" . $reset_link . "</a><br><br>";
                $success .= "<small style='color: #718096;'>*Dalam aplikasi production, link ini akan dikirim ke email Anda.</small>";
            } else {
                $error = "Gagal membuat token reset password.";
            }
        } else {
            // Untuk keamanan, tampilkan pesan yang sama meskipun email tidak ditemukan
            $success = "Jika email terdaftar, link reset password akan dikirim ke email Anda.";
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
    <title>Lupa Password - Sistem Helpdesk</title>
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

        .forgot-container {
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

        .forgot-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        }

        .forgot-header {
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

        input[type="email"] {
            width: 100%;
            padding: 14px 16px;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            font-size: 15px;
            font-family: 'Poppins', sans-serif;
            transition: all 0.3s ease;
            background: #f7fafc;
        }

        input[type="email"]:focus {
            outline: none;
            border-color: #667eea;
            background: white;
            box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
        }

        input::placeholder {
            color: #a0aec0;
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

        /* Footer Styling */
        .forgot-footer {
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
            .forgot-card {
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

            .forgot-footer {
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
    <div class="forgot-container">
        <div class="forgot-card">
            <div class="forgot-header">
                <div class="logo">
                    <i class="fa-solid fa-key"></i>
                </div>
                <h2>Lupa Password?</h2>
                <p class="subtitle">Masukkan email Anda dan kami akan mengirimkan link untuk reset password</p>
            </div>

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
                    <?php echo $success; ?>
                </div>
                <?php endif; ?>

                <div class="input-group">
                    <label for="email">Email</label>
                    <div class="input-wrapper">
                        <input type="email" id="email" name="email" placeholder="Masukkan email Anda" required>
                    </div>
                </div>

                <button type="submit">
                    <i class="fa-solid fa-paper-plane"></i>
                    Kirim Link Reset Password
                </button>

                <div class="back-to-login">
                    <a href="index.php">
                        <i class="fa-solid fa-arrow-left"></i>
                        Kembali ke Login
                    </a>
                </div>
            </form>
        </div>
    </div>

    <footer class="forgot-footer">
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
