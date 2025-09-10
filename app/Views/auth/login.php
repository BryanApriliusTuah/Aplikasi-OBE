<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login - OBE System</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('css/custom.css') ?>">
</head>
<body style="background: #f4f6fb;">
    <div class="container d-flex align-items-center justify-content-center" style="min-height: 100vh;">
        <div class="card login-card shadow-sm">
            <div class="login-flex-row">
                <!-- Logo + Label -->
                <div class="login-left">
                    <img src="<?= base_url('img/image.png') ?>" alt="Logo UPR" class="login-logo">
                    <div class="login-label mt-3">
                        <strong>SISTEM OBE<br>UNIVERSITAS PALANGKA RAYA</strong>
                    </div>
                </div>
                <!-- Form -->
                <div class="login-form-col">
                    <div class="login-title text-center mb-4">Login</div>
                    <?php if(session()->getFlashdata('error')): ?>
                        <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
                    <?php endif; ?>
                    <form method="post" action="<?= base_url('auth/login') ?>" class="login-form">
                        <div class="mb-3">
                            <label for="username" class="form-label">Username:</label>
                            <input type="text" name="username" id="username" class="form-control" required autofocus>
                        </div>
                        <div class="mb-4">
                            <label for="password" class="form-label">Password:</label>
                            <input type="password" name="password" id="password" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-login">
                            <i class="bi bi-box-arrow-in-right"></i> Login
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
