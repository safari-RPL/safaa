<?php
$student = null;
$errorMessage = '';
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id || $id < 1) {
    $errorMessage = 'ID data tidak valid.';
} else {
    try {
        require __DIR__ . '/config.php';
        $statement = $conn->prepare('SELECT id, nama, kelas, kehadiran FROM datasiswa WHERE id = ?');
        $statement->bind_param('i', $id);
        $statement->execute();
        $result = $statement->get_result();
        $student = $result->fetch_assoc();
        $statement->close();
        if (!$student) {
            $errorMessage = 'Data siswa tidak ditemukan.';
        }
    } catch (Throwable $error) {
        $errorMessage = 'Data tidak dapat dimuat. Pastikan MySQL aktif dan tabel datasiswa tersedia.';
    }
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
    <title>Edit Presensi — SMK Negeri 1 Probolinggo</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root{--maroon:#541d40;--navy:#152f50;--blue:#285d8b;--ink:#242235;--muted:#777489;--paper:#fffefa;--line:#e8e5eb}
        *{box-sizing:border-box}body{margin:0;min-height:100vh;background:#f4f2f6;color:var(--ink);font-family:'DM Sans',sans-serif}
        header{padding:22px clamp(22px,7vw,100px);color:white;background:linear-gradient(112deg,#172f50,#213d62 50%,#4c1f46)}
        .brand{display:flex;align-items:center;gap:12px;max-width:1200px;margin:auto;color:white;text-decoration:none}.brand img{width:48px;height:48px;object-fit:contain;padding:3px;border-radius:50%;background:white}.brand-name{font:700 12px/1.45 'Manrope',sans-serif;letter-spacing:.07em;text-transform:uppercase}.brand-name span{display:block;color:#d9cbdf;font:500 11px 'DM Sans',sans-serif;letter-spacing:0;text-transform:none}
        main{width:min(100% - 32px,560px);margin:58px auto}.kicker{color:var(--blue);font-size:10px;font-weight:700;letter-spacing:.16em;text-transform:uppercase}h1{margin:10px 0 8px;font:800 34px 'Manrope',sans-serif;letter-spacing:-.04em}.sub{margin:0 0 25px;color:var(--muted);font-size:14px}
        .card{padding:30px;background:var(--paper);border:1px solid #e9e6ed;border-radius:17px;box-shadow:0 18px 50px #1a18310a}.field{margin-bottom:19px}label{display:block;margin-bottom:8px;font-size:12px;font-weight:700}.input-wrap{position:relative}input,select{width:100%;height:50px;padding:0 14px;border:1px solid var(--line);border-radius:9px;background:white;color:#292739;font:500 14px 'DM Sans',sans-serif;outline:none}input:focus,select:focus{border-color:#547da2;box-shadow:0 0 0 4px #285d8b14}select{cursor:pointer}.buttons{display:flex;gap:10px;margin-top:25px}.button{display:inline-flex;align-items:center;justify-content:center;min-height:46px;padding:0 17px;border:0;border-radius:9px;text-decoration:none;font:700 13px 'DM Sans',sans-serif;cursor:pointer}.save{flex:1;color:white;background:linear-gradient(100deg,var(--maroon),#3d315e,var(--navy))}.cancel{color:#514e5e;background:#efedf2}.message{padding:16px 18px;border-radius:10px;background:#fff0f0;color:#9c3030;font-size:13px}
        @media(max-width:520px){main{margin:36px auto}.card{padding:23px 19px}h1{font-size:29px}}
    </style>
</head>
<body>
<header><a class="brand" href="pageview.php"><img src="assets/logo.gif" alt="Logo SMK Negeri 1 Probolinggo"><span class="brand-name">SMK Negeri 1<span>Probolinggo · Jawa Timur</span></span></a></header>
<main>
    <div class="kicker">Kelola presensi</div>
    <h1>Edit data siswa</h1>
    <p class="sub">Perbarui nama, kelas, atau status kehadiran siswa.</p>
    <?php if ($errorMessage !== ''): ?>
        <div class="message" role="alert"><?= escapeHtml($errorMessage) ?><p><a href="pageview.php">Kembali ke daftar presensi</a></p></div>
    <?php else: ?>
        <form class="card" method="post" action="prosesupdate.php">
            <input type="hidden" name="id" value="<?= (int) $student['id'] ?>">
            <div class="field"><label for="nama">Nama lengkap</label><input id="nama" name="nama" type="text" value="<?= escapeHtml($student['nama']) ?>" required maxlength="150"></div>
            <div class="field"><label for="kelas">Kelas</label><input id="kelas" name="kelas" type="text" value="<?= escapeHtml($student['kelas']) ?>" required maxlength="100"></div>
            <div class="field"><label for="kehadiran">Kehadiran</label><select id="kehadiran" name="kehadiran" required>
                <?php foreach (['Hadir', 'Izin', 'Sakit'] as $status): ?>
                    <option value="<?= escapeHtml($status) ?>" <?= $student['kehadiran'] === $status ? 'selected' : '' ?>><?= escapeHtml($status) ?></option>
                <?php endforeach; ?>
            </select></div>
            <div class="buttons"><a class="button cancel" href="pageview.php">Batal</a><button class="button save" type="submit">Simpan perubahan</button></div>
        </form>
    <?php endif; ?>
</main>
</body>
</html>
