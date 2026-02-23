<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="assets/js/theme.js"></script>
    <style>
        :root {
            --primary: #48c1f0;
            --primary-light: #e0f4fd;
            --primary-dark: #1b2a52;
            --text-grey: #808191;
            --bg-body: #f5f7fa;
            --bg-card: #ffffff;
            --text-main: #11142D;
        }

        [data-theme="dark"] {
            --bg-body: #0b0e14;
            --bg-card: #151a23;
            --text-main: #e2e8f0;
            --primary-light: rgba(72, 193, 240, 0.1);
        }

        body {
            background: linear-gradient(135deg, var(--bg-body) 0%, var(--primary-light) 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            font-family: 'Inter', sans-serif;
            transition: 0.3s;
        }
        .login-wrapper {
            display: flex;
            max-width: 900px;
            width: 100%;
            background: var(--bg-card);
            border-radius: 24px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.1);
            overflow: hidden;
            margin: 20px;
            position: relative;
        }
        .theme-toggle {
            position: absolute;
            top: 20px;
            right: 20px;
            width: 40px;
            height: 40px;
            background: var(--primary-light);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            color: var(--primary);
            transition: 0.3s;
            z-index: 10;
        }
        .theme-toggle:hover {
            transform: rotate(15deg) scale(1.1);
        }
        .login-branding {
            flex: 1;
            background: var(--primary-dark);
            padding: 60px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            color: white;
            border-right: 1px solid rgba(255,255,255,0.1);
        }
        .login-branding h1 {
            font-size: 32px;
            font-weight: 800;
            margin-bottom: 20px;
            color: var(--primary);
        }
        .login-branding p {
            font-size: 16px;
            line-height: 1.6;
            opacity: 0.9;
        }
        .login-branding .logo-circle {
            width: 120px;
            height: 120px;
            background: white;
            border-radius: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 40px;
            font-size: 40px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
            padding: 10px;
        }
        .login-form-side {
            flex: 1;
            padding: 60px;
            background: var(--bg-card);
            color: var(--text-main);
        }
        .login-header {
            margin-bottom: 40px;
        }
        .login-header h2 {
            font-size: 24px;
            font-weight: 700;
            color: var(--primary-dark);
            margin-bottom: 8px;
        }
        .login-header p {
            color: var(--text-grey);
            font-size: 14px;
        }
        .form-label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 8px;
            color: var(--text-dark);
        }
        .input-group {
            position: relative;
            margin-bottom: 24px;
        }
        .input-group i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--primary);
            font-size: 14px;
        }
        .input-group input {
            width: 100%;
            padding: 14px 15px 14px 45px;
            border: 2px solid var(--border-color, #f0f0f0);
            background: var(--bg-card);
            color: var(--text-main);
            border-radius: 14px;
            font-size: 14px;
            transition: 0.3s;
            box-sizing: border-box;
        }
        .input-group input:focus {
            border-color: var(--primary);
            outline: none;
            box-shadow: 0 0 0 4px rgba(72, 193, 240, 0.1);
        }
        .btn-submit {
            width: 100%;
            padding: 14px;
            background: var(--primary);
            color: white;
            border: none;
            border-radius: 14px;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            transition: 0.3s;
            margin-top: 10px;
        }
        .btn-submit:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 8px 15px rgba(27, 42, 82, 0.2);
        }
        .error-alert {
            background: #fff5f6;
            color: #ff4757;
            padding: 12px;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 600;
            text-align: center;
            margin-bottom: 24px;
            border: 1px solid #ffe0e6;
        }
        @media (max-width: 768px) {
            .login-wrapper {
                flex-direction: column;
            }
            .login-branding {
                padding: 40px;
                text-align: center;
                align-items: center;
                border-right: none;
                border-bottom: 1px solid rgba(255,255,255,0.1);
            }
            .login-branding .logo-circle {
                margin-bottom: 20px;
                width: 100px;
                height: 100px;
            }
            .login-form-side {
                padding: 40px;
            }
        }
    </style>
</head>
<body>
    <div class="login-wrapper">
        <div class="theme-toggle" onclick="toggleTheme()" title="Toggle Dark Mode">
            <i class="fas fa-moon"></i>
        </div>
        <div class="login-branding">
            <div class="logo-circle">
                <?php if(isset($hospital) && !empty($hospital['image'])): ?>
                    <img src="<?= $hospital['image'] ?>" alt="Logo" style="width: 100%; height: 100%; object-fit: contain; border-radius: 16px;">
                <?php else: ?>
                    <img src="assets/logo.jpeg" alt="Mednoa Logo" style="width: 100%; height: 100%; object-fit: contain; border-radius: 16px;">
                <?php endif; ?>
            </div>
            <h1>Mednoa Admin</h1>
            <p>Welcome back! Securely manage your healthcare facility with our comprehensive administration tools.</p>
        </div>
        <div class="login-form-side">
            <div class="login-header">
                <h2>Secure Login</h2>
                <p>Enter your credentials to access the portal</p>
            </div>

            <?php if(isset($error)): ?>
                <div class="error-alert">
                    <i class="fas fa-exclamation-circle me-2"></i> <?= $error ?>
                </div>
            <?php endif; ?>

            <form action="?route=auth/login<?= isset($_GET['slug']) ? '&slug='.$_GET['slug'] : '' ?>" method="POST">
                <div class="form-group">
                    <label class="form-label">Email Address</label>
                    <div class="input-group">
                        <i class="fas fa-envelope"></i>
                        <input type="email" name="email" placeholder="admin@mednoa.com" required autofocus>
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Password</label>
                    <div class="input-group">
                        <i class="fas fa-lock"></i>
                        <input type="password" name="password" placeholder="••••••••" required>
                    </div>
                </div>
                <button type="submit" class="btn-submit">Sign In</button>
            </form>

            <div style="margin-top: 30px; text-align: center; color: var(--text-grey); font-size: 13px;">
                &copy; <?= date('Y') ?> Mednoa Smart Healthcare System. <br>All rights reserved.
            </div>
        </div>
    </div>
</body>
</html>
