<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login - OBE System</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; }

        body {
            min-height: 100vh;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #dce8ff 0%, #eef2ff 55%, #f0f4ff 100%);
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            padding: 1rem;
        }

        /* ── Main Card ── */
        .login-card {
            width: 100%;
            max-width: 900px;
            display: flex;
            border-radius: 1.5rem;
            overflow: hidden;
            box-shadow: 0 24px 64px rgba(34, 74, 190, 0.14), 0 4px 16px rgba(0,0,0,0.06);
            animation: fadeUp .4s ease both;
        }

        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(18px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        /* ── Left decorative panel ── */
        .login-side {
            width: 42%;
            background: linear-gradient(160deg, #224abe 0%, #1a3a8f 60%, #0f2463 100%);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 3rem 2.25rem;
            position: relative;
            overflow: hidden;
        }

        /* Subtle decorative circles */
        .login-side::before,
        .login-side::after {
            content: "";
            position: absolute;
            border-radius: 50%;
            background: rgba(255,255,255,0.05);
            pointer-events: none;
        }
        .login-side::before { width: 320px; height: 320px; top: -90px; right: -90px; }
        .login-side::after  { width: 220px; height: 220px; bottom: -70px; left: -70px; }

        .login-logo {
            width: 148px;
            height: 148px;
            object-fit: contain;
            border-radius: 1.125rem;
            background: #fff;
            padding: 0.7rem;
            box-shadow: 0 8px 32px rgba(0,0,0,0.22);
            margin-bottom: 1.75rem;
            position: relative;
            z-index: 1;
        }

        .login-uni-name {
            font-size: 1.05rem;
            font-weight: 700;
            color: #fff;
            text-align: center;
            line-height: 1.5;
            letter-spacing: 0.01em;
            position: relative;
            z-index: 1;
            margin-bottom: 1rem;
        }

        .login-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            background: rgba(255,255,255,0.12);
            border: 1px solid rgba(255,255,255,0.22);
            color: rgba(255,255,255,0.9);
            border-radius: 2rem;
            padding: 0.375rem 1rem;
            font-size: 0.78rem;
            font-weight: 600;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            position: relative;
            z-index: 1;
            backdrop-filter: blur(8px);
        }

        /* ── Right form panel ── */
        .login-form-panel {
            flex: 1;
            background: #fff;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 3rem 3rem;
        }

        .login-heading {
            font-size: 1.85rem;
            font-weight: 700;
            color: #1e293b;
            letter-spacing: -0.01em;
            margin-bottom: 0.375rem;
        }

        .login-subheading {
            font-size: 0.9rem;
            color: #64748b;
            margin-bottom: 2rem;
        }

        /* ── Alert ── */
        .login-alert {
            display: flex;
            align-items: center;
            gap: 0.625rem;
            background: #fef2f2;
            border: 1px solid #fecaca;
            border-left: 4px solid #ef4444;
            border-radius: 0.5rem;
            color: #991b1b;
            padding: 0.75rem 1rem;
            font-size: 0.875rem;
            margin-bottom: 1.5rem;
        }

        /* ── Form fields ── */
        .field-label {
            display: block;
            font-size: 0.8125rem;
            font-weight: 600;
            color: #475569;
            margin-bottom: 0.45rem;
            letter-spacing: 0.01em;
        }

        .input-wrap {
            position: relative;
            margin-bottom: 1.25rem;
        }

        .input-wrap .input-icon {
            position: absolute;
            left: 0.9rem;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            font-size: 1rem;
            pointer-events: none;
            transition: color 0.2s;
            z-index: 2;
        }

        .input-wrap:focus-within .input-icon {
            color: #224abe;
        }

        .input-wrap .form-control {
            height: 48px;
            padding-left: 2.7rem;
            padding-right: 1rem;
            border: 1.5px solid #e2e8f0;
            border-radius: 0.625rem;
            background: #f8fafc;
            font-size: 0.9375rem;
            color: #1e293b;
            transition: border-color 0.2s, background 0.2s, box-shadow 0.2s;
        }

        .input-wrap .form-control:focus {
            border-color: #224abe;
            background: #fff;
            box-shadow: 0 0 0 3px rgba(34, 74, 190, 0.08);
            outline: none;
        }

        .input-wrap .form-control.has-toggle {
            padding-right: 3rem;
        }

        .toggle-btn {
            position: absolute;
            right: 0.875rem;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #94a3b8;
            cursor: pointer;
            padding: 0;
            font-size: 1rem;
            line-height: 1;
            transition: color 0.2s;
            z-index: 2;
        }

        .toggle-btn:hover { color: #475569; }

        /* ── Submit button ── */
        .btn-login {
            width: 100%;
            height: 48px;
            background: linear-gradient(135deg, #224abe 0%, #1a3a8f 100%);
            color: #fff;
            border: none;
            border-radius: 0.625rem;
            font-size: 0.9375rem;
            font-weight: 600;
            letter-spacing: 0.01em;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            box-shadow: 0 4px 14px rgba(34, 74, 190, 0.28);
            transition: all 0.2s ease;
            margin-top: 0.5rem;
        }

        .btn-login:hover {
            background: linear-gradient(135deg, #1a3a8f 0%, #0f2463 100%);
            box-shadow: 0 6px 20px rgba(34, 74, 190, 0.38);
            transform: translateY(-1px);
        }

        .btn-login:active {
            transform: translateY(0);
            box-shadow: 0 2px 8px rgba(34, 74, 190, 0.22);
        }

        /* ── Footer ── */
        .login-footer {
            text-align: center;
            font-size: 0.78rem;
            color: #94a3b8;
            margin-top: 2rem;
        }

        /* ── Responsive ── */
        @media (max-width: 700px) {
            .login-card {
                flex-direction: column;
                border-radius: 1.125rem;
            }

            .login-side {
                width: 100%;
                flex-direction: row;
                align-items: center;
                justify-content: flex-start;
                gap: 1.25rem;
                padding: 1.5rem 1.75rem;
            }

            .login-logo {
                width: 64px;
                height: 64px;
                margin-bottom: 0;
                flex-shrink: 0;
            }

            .login-uni-name { text-align: left; font-size: 0.9rem; margin-bottom: 0; }
            .login-badge    { display: none; }

            .login-form-panel { padding: 2rem 1.75rem; }
            .login-heading    { font-size: 1.5rem; }
        }

        @media (max-width: 420px) {
            .login-form-panel { padding: 1.5rem 1.25rem; }
        }
    </style>
</head>
<body>
    <div class="login-card">

        <!-- Left decorative panel -->
        <div class="login-side">
            <img src="<?= base_url('img/image.png') ?>" alt="Logo UPR" class="login-logo">
            <div>
                <div class="login-uni-name">UNIVERSITAS<br>PALANGKA RAYA</div>
                <span class="login-badge">
                    <i class="bi bi-mortarboard-fill"></i>
                    Sistem OBE
                </span>
            </div>
        </div>

        <!-- Right form panel -->
        <div class="login-form-panel">
            <h1 class="login-heading">Selamat Datang</h1>
            <p class="login-subheading">Masuk ke akun Anda untuk melanjutkan</p>

            <?php if (session()->getFlashdata('error')): ?>
                <div class="login-alert">
                    <i class="bi bi-exclamation-circle-fill"></i>
                    <?= session()->getFlashdata('error') ?>
                </div>
            <?php endif; ?>

            <form method="post" action="<?= base_url('auth/login') ?>" autocomplete="on">
                <!-- Username -->
                <div>
                    <label for="username" class="field-label">Username</label>
                    <div class="input-wrap">
                        <i class="bi bi-person input-icon"></i>
                        <input type="text"
                               name="username"
                               id="username"
                               class="form-control"
                               placeholder="Masukkan username"
                               required
                               autofocus
                               autocomplete="username">
                    </div>
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="field-label">Password</label>
                    <div class="input-wrap">
                        <i class="bi bi-lock input-icon"></i>
                        <input type="password"
                               name="password"
                               id="password"
                               class="form-control has-toggle"
                               placeholder="Masukkan password"
                               required
                               autocomplete="current-password">
                        <button type="button" class="toggle-btn" id="togglePassword" tabindex="-1" aria-label="Tampilkan/sembunyikan password">
                            <i class="bi bi-eye" id="toggleIcon"></i>
                        </button>
                    </div>
                </div>

                <button type="submit" class="btn-login">
                    <i class="bi bi-box-arrow-in-right"></i>
                    Masuk
                </button>
            </form>

            <div class="login-footer">
                Sistem OBE &copy; <?= date('Y') ?> Universitas Palangka Raya
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('togglePassword').addEventListener('click', function () {
            const input = document.getElementById('password');
            const icon  = document.getElementById('toggleIcon');
            const show  = input.type === 'password';
            input.type  = show ? 'text' : 'password';
            icon.className = show ? 'bi bi-eye-slash' : 'bi bi-eye';
        });
    </script>
</body>
</html>
