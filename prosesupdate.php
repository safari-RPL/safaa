<?php
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $nama = $_POST['nama'];
    $kelas = $_POST['kelas'];
    $kehadiran = $_POST['kehadiran'];
    

    // update data di database
    $sql = "UPDATE datasiswa SET Nama='$nama', Kelas='$kelas', Kehadiran='$kehadiran' WHERE ID='$id'";
    $result = mysqli_query($conn, $sql);

    // cek apakah data berhasil diupdate
    if ($result) {
        // redirect ke halaman pageview.php
        header("Location: pageview.php");
        exit();
    } else {
        echo "Data gagal diupdate: " . mysqli_error($conn, $sql);
    }
}