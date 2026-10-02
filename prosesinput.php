<!-- koneksi database -->
<?php
include 'config.php';
?>


<!-- menyimpan inputan -->
<?php
// get data dari form
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama = $_POST['nama'];
    $kelas = $_POST['kelas'];
    $kehadiran = $_POST['kehadiran'];

    // insert data ke database
    $sql = "INSERT INTO datasiswa (nama, kelas, kehadiran) VALUES ('$nama', '$kelas', '$kehadiran')";
    $result = mysqli_query($conn, $sql);

    // cek apakah data berhasil disimpan
    if ($result) {
        
        // redirect ke halaman index.php
        header("Location: pageview.php");
        exit();
    } else {
        echo "Data gagal disimpan: " . mysqli_error($conn, $sql);
    }
}
?>