<?php
$students = [];
$loadError = '';
$counts = ['Hadir' => 0, 'Izin' => 0, 'Sakit' => 0];

try {
    require __DIR__ . '/config.php';
    $result = $conn->query('SELECT id, nama, kelas, kehadiran FROM datasiswa ORDER BY id DESC');
    while ($student = $result->fetch_assoc()) {
        $students[] = $student;
        if (isset($counts[$student['kehadiran']])) {
            $counts[$student['kehadiran']]++;
        }
    }
    $result->free();
} catch (Throwable $error) {
    $loadError = 'Data belum dapat dimuat. Pastikan MySQL aktif dan tabel datasiswa tersedia di database projek.';
}

function escapeHtml($value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#24152d">
    <title>Data Presensi — SMK Negeri 1 Probolinggo</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root{--maroon:#541d40;--plum:#321c39;--navy:#152f50;--blue:#285d8b;--ink:#242235;--muted:#777489;--paper:#fffefa;--line:#e8e5eb}
        *{box-sizing:border-box}
        body{margin:0;min-height:100vh;background:#f4f2f6;color:var(--ink);font-family:'DM Sans',sans-serif}
        .top{position:relative;overflow:hidden;padding:25px clamp(22px,7vw,100px);color:white;background:linear-gradient(112deg,#172f50 0%,#213d62 50%,#4c1f46 100%)}
        .top:after{content:"";position:absolute;width:300px;height:300px;right:8%;top:-210px;border:1px solid #ffffff24;border-radius:50%;box-shadow:0 0 0 38px #ffffff09,0 0 0 78px #ffffff06}
        .nav,.hero{position:relative;z-index:1;max-width:1200px;margin:auto}
        .nav{display:flex;align-items:center;justify-content:space-between}
        .brand{display:flex;align-items:center;gap:12px;color:white;text-decoration:none}
        .brand img{width:48px;height:48px;object-fit:contain;padding:3px;border-radius:50%;background:white}
        .brand-name{font:700 12px/1.45 'Manrope',sans-serif;letter-spacing:.07em;text-transform:uppercase}
        .brand-name span{display:block;color:#d9cbdf;font:500 11px 'DM Sans',sans-serif;letter-spacing:0;text-transform:none}
        .nav-link{padding:10px 15px;border:1px solid #ffffff42;border-radius:9px;color:#fff;text-decoration:none;font-size:12px;font-weight:600;transition:background .2s}
        .nav-link:hover{background:#ffffff18}
        .hero{padding:52px 0 54px}
        .eyebrow{display:flex;align-items:center;gap:9px;color:#f0c8d4;font-size:10px;font-weight:700;letter-spacing:.18em;text-transform:uppercase}
        .eyebrow:before{content:"";width:24px;height:1px;background:#e7abbc}
        h1{margin:14px 0 8px;font:800 clamp(32px,4vw,48px)/1.12 'Manrope',sans-serif;letter-spacing:-.05em}
        .hero p{margin:0;color:#e0deea;font-size:14px}
        .content{max-width:1200px;margin:-25px auto 0;padding:0 24px 60px;position:relative;z-index:2}
        .stats{display:grid;grid-template-columns:repeat(4,1fr);gap:14px;margin-bottom:20px}
        .stat{padding:18px 20px;border:1px solid #e9e6ed;border-radius:14px;background:var(--paper);box-shadow:0 10px 30px #1a18310a}
        .stat-label{color:var(--muted);font-size:11px;font-weight:600}
        .stat-value{margin-top:7px;color:var(--navy);font:800 27px 'Manrope',sans-serif;letter-spacing:-.04em}
        .stat:nth-child(2) .stat-value{color:#2f7a57}.stat:nth-child(3) .stat-value{color:#996722}.stat:nth-child(4) .stat-value{color:#a34355}
        .panel{overflow:hidden;border:1px solid #e9e6ed;border-radius:16px;background:var(--paper);box-shadow:0 18px 50px #1a18310a}
        .panel-head{display:flex;align-items:center;justify-content:space-between;padding:21px 24px;border-bottom:1px solid #eeebf0}
        h2{margin:0;font:700 17px 'Manrope',sans-serif;letter-spacing:-.02em}
        .total{color:var(--muted);font-size:12px}
        .table-wrap{overflow-x:auto}
        table{width:100%;border-collapse:collapse;text-align:left;white-space:nowrap}
        th{padding:12px 24px;background:#faf9fb;color:#8d8998;font-size:10px;font-weight:700;letter-spacing:.1em;text-transform:uppercase}
        td{padding:16px 24px;border-top:1px solid #f0edf2;color:#4a4858;font-size:13px}
        tbody tr:hover{background:#fcfbfd}
        td:first-child{width:70px;color:#a09ca9;font-size:12px}
        .student{font-weight:700;color:#302e3d}
        .badge{display:inline-flex;align-items:center;gap:7px;padding:6px 10px;border-radius:30px;font-size:11px;font-weight:700}
        .badge:before{content:"";width:6px;height:6px;border-radius:50%;background:currentColor}
        .actions{display:flex;align-items:center;gap:8px}.actions form{margin:0}
        .action{display:inline-flex;align-items:center;justify-content:center;padding:7px 11px;border:0;border-radius:7px;text-decoration:none;font:600 11px 'DM Sans',sans-serif;cursor:pointer}
        .edit{background:#eaf1f8;color:#285d8b}.delete{background:#fff0f0;color:#a84651}.action:hover{filter:brightness(.96)}
        @media(max-width:700px){.actions{width:auto;padding-top:9px}}
        .hadir{background:#eaf5ef;color:#34764f}.izin{background:#fff4e5;color:#98661d}.sakit{background:#fff0f0;color:#a84651}.other{background:#f0eff3;color:#656171}
        .empty,.error{padding:54px 24px;text-align:center;color:var(--muted);font-size:13px;line-height:1.7}
        .empty strong{display:block;margin-bottom:6px;color:#39374a;font:700 16px 'Manrope',sans-serif}
        .error{color:#9c3030;background:#fff5f5}
        .notice{margin-bottom:16px;padding:13px 16px;border-radius:9px;font-size:13px}.notice.success{color:#25613f;background:#eaf5ef}.notice.error{color:#9c3030;background:#fff0f0}
        @media(max-width:700px){.top{padding:20px 22px}.hero{padding:40px 0 45px}.content{padding:0 15px 40px}.stats{grid-template-columns:repeat(2,1fr);gap:10px}.stat{padding:15px}.stat-value{font-size:24px}.panel-head{padding:18px}.table-wrap{overflow:visible}table,tbody,tr,td{display:block;width:100%}thead{display:none}tbody{padding:4px 17px}tbody tr{position:relative;padding:13px 0;border-bottom:1px solid #f0edf2}tbody tr:last-child{border-bottom:0}td{padding:4px 0;border:0;white-space:normal}td:first-child{position:absolute;right:0;top:15px;width:auto}td:nth-child(2){padding-right:40px}td:nth-child(3):before{content:'Kelas: ';color:#9995a2}td:nth-child(4){padding-top:7px}.nav-link{padding:9px 11px;font-size:11px}}
    </style>
</head>
<body>
<header class="top">
    <nav class="nav" aria-label="Navigasi utama">
        <a class="brand" href="index.php">
            <img src="assets/logo.gif" alt="Logo SMK Negeri 1 Probolinggo">
            <span class="brand-name">SMK Negeri 1<span>Probolinggo · Jawa Timur</span></span>
        </a>
        <a class="nav-link" href="index.php">← Kembali ke presensi</a>
    </nav>
    <div class="hero">
        <div class="eyebrow">Ruang Presensi Digital</div>
        <h1>Data kehadiran siswa</h1>
        <p>Rekap presensi siswa SMK Negeri 1 Probolinggo.</p>
    </div>
</header>
<main class="content">
    <?php
    $statusMessages = [
        'deleted' => ['Data siswa berhasil dihapus.', 'success'],
        'updated' => ['Perubahan data siswa berhasil disimpan.', 'success'],
        'invalid' => ['Permintaan tidak valid. Data belum diubah.', 'error'],
        'error' => ['Aksi gagal diproses. Periksa koneksi database dan coba lagi.', 'error'],
    ];
    $pageStatus = $_GET['status'] ?? '';
    ?>
    <?php if (isset($statusMessages[$pageStatus])): ?>
        <div class="notice <?= $statusMessages[$pageStatus][1] ?>" role="status"><?= escapeHtml($statusMessages[$pageStatus][0]) ?></div>
    <?php endif; ?>
    <section class="stats" aria-label="Ringkasan kehadiran">
        <article class="stat"><div class="stat-label">Total siswa tercatat</div><div class="stat-value"><?= count($students) ?></div></article>
        <article class="stat"><div class="stat-label">Hadir</div><div class="stat-value"><?= $counts['Hadir'] ?></div></article>
        <article class="stat"><div class="stat-label">Izin</div><div class="stat-value"><?= $counts['Izin'] ?></div></article>
        <article class="stat"><div class="stat-label">Sakit</div><div class="stat-value"><?= $counts['Sakit'] ?></div></article>
    </section>
    <section class="panel" aria-labelledby="data-title">
        <div class="panel-head"><h2 id="data-title">Daftar presensi</h2><span class="total"><?= count($students) ?> data</span></div>
        <?php if ($loadError !== ''): ?>
            <div class="error" role="alert"><?= escapeHtml($loadError) ?></div>
        <?php elseif (count($students) === 0): ?>
            <div class="empty"><strong>Belum ada data presensi</strong>Data siswa yang dikirim melalui formulir akan muncul di sini.</div>
        <?php else: ?>
            <div class="table-wrap">
                <table>
                    <thead><tr><th>No.</th><th>Nama siswa</th><th>Kelas</th><th>Kehadiran</th><th>Aksi</th></tr></thead>
                    <tbody>
                    <?php foreach ($students as $index => $student): ?>
                        <?php $status = (string) $student['kehadiran']; $statusClass = strtolower($status); if (!in_array($statusClass, ['hadir', 'izin', 'sakit'], true)) { $statusClass = 'other'; } ?>
                        <tr>
                            <td><?= $index + 1 ?></td>
                            <td class="student"><?= escapeHtml($student['nama']) ?></td>
                            <td><?= escapeHtml($student['kelas']) ?></td>
                            <td><span class="badge <?= escapeHtml($statusClass) ?>"><?= escapeHtml($status) ?></span></td>
                            <td class="actions">
                                <a class="action edit" href="update.php?id=<?= (int) $student['id'] ?>">Edit</a>
                                <form method="post" action="hapus.php" onsubmit="return confirm('Hapus data <?= escapeHtml($student['nama']) ?>?')">
                                    <input type="hidden" name="id" value="<?= (int) $student['id'] ?>">
                                    <button class="action delete" type="submit">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </section>
</main>
</body>
</html>
