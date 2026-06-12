<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include "koneksi.php";

$suhu = isset($_GET['suhu']) ? floatval($_GET['suhu']) : 0;
$turbidity = isset($_GET['turbidity']) ? intval($_GET['turbidity']) : 0;
$tds = isset($_GET['tds']) ? floatval($_GET['tds']) : 0;
$status_air = isset($_GET['status_air']) ? $_GET['status_air'] : "-";
$status_tds = isset($_GET['status_tds']) ? $_GET['status_tds'] : "-";
$status_pompa = isset($_GET['status_pompa']) ? $_GET['status_pompa'] : "-";

$sql = "INSERT INTO monitoring 
(suhu, turbidity, tds, status_air, status_tds, status_pompa)
VALUES 
('$suhu', '$turbidity', '$tds', '$status_air', '$status_tds', '$status_pompa')";

if (mysqli_query($conn, $sql)) {
    echo "Data berhasil disimpan";
} else {
    echo "Gagal menyimpan data: " . mysqli_error($conn);
}
?>