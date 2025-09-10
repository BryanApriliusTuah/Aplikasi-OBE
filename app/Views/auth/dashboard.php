<h2>Selamat Datang, <?= session()->get('nama') ?>!</h2>
<p>Role: <?= session()->get('role') ?></p>
<a href="/auth/logout">Logout</a>
