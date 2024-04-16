<?php
include 'koneksi.php';

// Ambil data dari form
$kode_barang = $_POST['kode_barang'];
$qty = $_POST['qty'];

// Kurangi stok barang dengan FIFO
$sql = "SELECT id, saldo FROM stok_barang 
        WHERE kode_barang = '$kode_barang' 
        ORDER BY tanggal_masuk ASC";
$result = $conn->query($sql);

$sisa_qty = $qty;
while ($row = $result->fetch_assoc()) {
    $id = $row['id'];
    $saldo = $row['saldo'];

    if ($saldo <= $sisa_qty) {
        $sisa_qty -= $saldo;
        $update_sql = "UPDATE stok_barang SET saldo = 0 WHERE id = $id";
    } else {
        $update_sql = "UPDATE stok_barang SET saldo = saldo - $sisa_qty WHERE id = $id";
        $sisa_qty = 0;
    }

    $conn->query($update_sql);

    if ($sisa_qty == 0) {
        break;
    }
}

$conn->close();
?>