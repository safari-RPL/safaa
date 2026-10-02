<?php
require 'config.php';

if (isset($_POST['id'])) {
    $id = $_POST['id'];
    mysqli_query($conn, "DELETE FROM datasiswa WHERE id = $id");
}

header("Location: pageview.php");
exit;
?>