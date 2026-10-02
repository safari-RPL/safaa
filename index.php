<?php
$saveMessage = '';
$saveSuccess = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nama'] ?? '');
    $kelas = trim($_POST['kelas'] ?? '');
    $kehadiran = $_POST['kehadiran'] ?? '';
    $validStatuses = ['Hadir', 'Izin', 'Sakit'];

    if ($nama === '' || $kelas === '' || !in_array($kehadiran, $validStatuses, true)) {
        $saveMessage = 'Mohon lengkapi nama, kelas, dan status kehadiran dengan benar.';
    } else {
        try {
            require __DIR__ . '/config.php';
            $statement = $conn->prepare('INSERT INTO datasiswa (nama, kelas, kehadiran) VALUES (?, ?, ?)');
            $statement->bind_param('sss', $nama, $kelas, $kehadiran);
            if ($statement->execute()) {
                $statement->close();
                header('Location: pageview.php');
                exit;
            }
            $statement->close();
        } catch (Throwable $error) {
            $saveMessage = 'Presensi belum dapat disimpan. Pastikan MySQL aktif dan tabel datasiswa tersedia di database projek.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#24152d">
    <title>Presensi Siswa — SMK Negeri 1 Probolinggo</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root { --maroon:#541d40; --plum:#321c39; --navy:#152f50; --blue:#285d8b; --ink:#242235; --muted:#777489; --paper:#fffefa; --line:#e8e5eb; }
        * { box-sizing:border-box; }
        body { margin:0; min-height:100vh; color:var(--ink); font-family:'DM Sans',sans-serif; background:#f4f2f6; }
        .page { min-height:100vh; display:grid; grid-template-columns:minmax(370px, .92fr) 1.08fr; }
        .welcome { position:relative; display:flex; flex-direction:column; justify-content:space-between; overflow:hidden; padding:42px clamp(32px,6vw,88px) 36px; color:white; background:linear-gradient(150deg,#172f50 0%,#213d62 43%,#4c1f46 100%); }
        .welcome:before,.welcome:after { content:""; position:absolute; pointer-events:none; border-radius:50%; }
        .welcome:before { width:480px;height:480px;right:-210px;top:23%; border:1px solid #ffffff21; box-shadow:0 0 0 42px #ffffff09,0 0 0 88px #ffffff06; }
        .welcome:after { width:220px;height:220px;left:-135px;bottom:7%;background:#bd82901c; }
        .brand,.intro,.welcome-foot { position:relative; z-index:1; }
        .brand { display:flex; align-items:center; gap:14px; }
        .brand img { width:56px;height:56px;object-fit:contain;border-radius:50%;background:#fff;padding:3px;box-shadow:0 5px 18px #080d2640; }
        .brand-name { font:700 13px/1.45 'Manrope',sans-serif; letter-spacing:.08em;text-transform:uppercase; }
        .brand-name span { display:block;color:#d9cbdf;font:500 11px 'DM Sans',sans-serif;letter-spacing:.02em;text-transform:none; }
        .intro { max-width:510px; padding:48px 0; }
        .eyebrow { display:inline-flex;align-items:center;gap:9px;color:#f0c8d4;font-size:11px;font-weight:700;letter-spacing:.18em;text-transform:uppercase; }
        .eyebrow:before { content:"";width:25px;height:1px;background:#e7abbc; }
        h1 { margin:21px 0 18px;font:800 clamp(42px,5vw,68px)/1.08 'Manrope',sans-serif;letter-spacing:-.055em; }
        h1 em { color:#e8b8c8;font-style:normal; }
        .intro p { max-width:400px;margin:0;color:#e0deea;font-size:15px;line-height:1.8; }
        .welcome-foot { display:flex;justify-content:space-between;align-items:center;color:#d3cedd;font-size:11px; }
        .welcome-foot .mark { display:flex;align-items:center;gap:8px; }
        .mark i { width:7px;height:7px;border-radius:50%;background:#9fe1c1;box-shadow:0 0 0 4px #9fe1c126; }
        .form-side { display:flex;align-items:center;justify-content:center;padding:46px clamp(24px,7vw,104px); }
        .form-wrap { width:min(100%,490px); }
        .form-kicker { color:var(--blue);font-size:11px;font-weight:700;letter-spacing:.16em;text-transform:uppercase; }
        h2 { margin:11px 0 8px;font:800 32px 'Manrope',sans-serif;letter-spacing:-.04em;color:#242235; }
        .sub { margin:0 0 31px;color:var(--muted);font-size:14px;line-height:1.6; }
        .card { padding:32px;background:var(--paper);border:1px solid #e9e6ed;border-radius:20px;box-shadow:0 20px 60px #1a18310d; }
        .field { margin-bottom:20px; }
        label { display:block;margin-bottom:9px;font-size:12px;font-weight:700;color:#39374a; }
        .input-wrap { position:relative; }
        .input-wrap svg { position:absolute;top:50%;left:15px;transform:translateY(-50%);width:18px;height:18px;color:#908b9c;pointer-events:none; }
        input,select { width:100%;height:52px;padding:0 15px 0 45px;border:1px solid var(--line);border-radius:10px;background:#fff;color:#292739;font:500 14px 'DM Sans',sans-serif;outline:none;transition:border-color .2s,box-shadow .2s; }
        input::placeholder { color:#aaa7b2; }
        input:focus,select:focus { border-color:#547da2;box-shadow:0 0 0 4px #285d8b14; }
        select { appearance:none;cursor:pointer;color:#898594; }
        select:valid { color:#292739; }
        .chevron { position:absolute;right:16px;top:50%;transform:translateY(-50%);color:#8b8794;pointer-events:none; }
        .hint { margin:8px 0 0;color:#9a97a4;font-size:11px; }
        .submit { width:100%;height:53px;margin-top:5px;display:flex;align-items:center;justify-content:center;gap:10px;border:0;border-radius:10px;color:#fff;background:linear-gradient(100deg,var(--maroon),#3d315e 54%,var(--navy));font:700 14px 'DM Sans',sans-serif;cursor:pointer;box-shadow:0 9px 18px #3b254022;transition:transform .18s,box-shadow .18s; }
        .submit:hover { transform:translateY(-2px);box-shadow:0 13px 22px #3b254033; }
        .submit:active { transform:translateY(0); }
        .privacy { display:flex;align-items:center;justify-content:center;gap:7px;margin:17px 0 0;color:#9996a3;font-size:11px; }
        .privacy svg { width:13px;height:13px; }
        .toast { display:none;margin-top:14px;padding:12px 14px;border-radius:9px;background:#eaf5ef;color:#25613f;font-size:13px;line-height:1.5; }
        .toast.show { display:block; }
        .toast.error { background:#fff0f0;color:#9c3030; }
        @media(max-width:900px) { .page{grid-template-columns:1fr 1fr}.welcome{padding:32px}.form-side{padding:34px 28px}h1{font-size:52px} }
        @media(max-width:680px) { .page{grid-template-columns:1fr}.welcome{min-height:330px;padding:24px 25px 27px}.brand img{width:48px;height:48px}.intro{padding:35px 0 28px}h1{font-size:44px;margin:14px 0 11px}.intro p{font-size:13px;max-width:370px}.welcome-foot{font-size:10px}.form-side{padding:36px 22px 44px}.form-wrap{max-width:490px}.card{padding:25px 21px}h2{font-size:29px}.sub{margin-bottom:23px} }
        @media(prefers-reduced-motion:reduce) { *,*:before,*:after{scroll-behavior:auto!important;transition:none!important} }
    </style>
</head>
<body>
<main class="page">
    <section class="welcome" aria-label="Sambutan">
        <div class="brand">
            <img src="assets/logo.gif" alt="Logo SMK Negeri 1 Kota Probolinggo">
            <div class="brand-name">SMK Negeri 1 Kota Probolinggo<span>Probolinggo · Jawa Timur</span></div>
        </div>
        <div class="intro">
            <div class="eyebrow">Ruang Presensi Digital</div>
            <h1>Hadir hari ini,<br><em>hebat esok nanti.</em></h1>
            <p>Mulai kegiatan belajar dengan semangat. Isi presensi kehadiranmu dengan mudah dan tepat.</p>
        </div>
        <div class="welcome-foot"><span>Disiplin dimulai dari diri sendiri.</span><span class="mark"><i></i> Sistem presensi</span></div>
    </section>

    <section class="form-side" aria-label="Form presensi">
        <div class="form-wrap">
            <div class="form-kicker">Presensi siswa</div>
            <h2>Selamat datang!</h2>
            <p class="sub">Lengkapi data di bawah untuk mencatat kehadiranmu hari ini.</p>
            <form class="card" id="attendanceForm" method="post" action="index.php">
                <div class="field">
                    <label for="nama">Nama lengkap</label>
                    <div class="input-wrap">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><circle cx="12" cy="8" r="3.5"/><path d="M5 20c.5-3.7 2.8-5.5 7-5.5s6.5 1.8 7 5.5"/></svg>
                        <input id="nama" name="nama" type="text" placeholder="Masukkan nama lengkap" autocomplete="name" required>
                    </div>
                </div>
                <div class="field">
                    <label for="kelas">Kelas</label>
                    <div class="input-wrap">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><path d="M4 5.5A2.5 2.5 0 0 1 6.5 3H20v16H6.5A2.5 2.5 0 0 0 4 21.5z"/><path d="M4 5.5v16M8 7h8M8 11h7"/></svg>
                        <input id="kelas" name="kelas" type="text" placeholder="Contoh: XI RPL 1" required>
                    </div>
                </div>
                <div class="field">
                    <label for="kehadiran">Kehadiran</label>
                    <div class="input-wrap">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><rect x="3.5" y="5" width="17" height="16" rx="2"/><path d="M8 3v4m8-4v4M3.5 10h17m-12 5 2 2 4-4"/></svg>
                        <select id="kehadiran" name="kehadiran" required>
                            <option value="" disabled selected>Pilih status kehadiran</option>
                            <option value="Hadir">Hadir</option>
                            <option value="Izin">Izin</option>
                            <option value="Sakit">Sakit</option>
                        </select>
                        <svg class="chevron" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.7"><path d="m5 7.5 5 5 5-5"/></svg>
                    </div>
                    <p class="hint">Pilih status yang sesuai dengan keadaanmu.</p>
                </div>
                <button class="submit" type="submit">Kirim presensi <span aria-hidden="true">→</span></button>
                <?php if ($saveMessage !== ''): ?>
                    <div class="toast show<?= $saveSuccess ? '' : ' error' ?>" id="confirmation" role="status" aria-live="polite"><?= $saveMessage ?></div>
                <?php endif; ?>
                <div class="privacy"><svg viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="4" y="8" width="12" height="9" rx="2"/><path d="M7 8V6a3 3 0 1 1 6 0v2"/></svg>Data presensi digunakan untuk keperluan sekolah.</div>
            </form>
        </div>
    </section>
</main>
</body>
</html>
