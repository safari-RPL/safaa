<?php
$conn = mysqli_connect('localhost', 'root', '', 'projek');

if (!$conn) {
    throw new RuntimeException('Tidak dapat terhubung ke database.');
}

mysqli_set_charset($conn, 'utf8mb4');
