<?php
include '../koneksi.php';
date_default_timezone_set('Asia/Jakarta');

if (isset($_POST['kode_barang'])) {
        $kode_barang = $_POST['kode_barang'];

        // Buat query untuk mengambil nama barang berdasarkan kode barang
        $query = "SELECT nama_barang FROM barang WHERE kode_barang = '$kode_barang'";
        $result = mysqli_query($conn, $query);

        if (mysqli_num_rows($result) > 0) {
                $row = mysqli_fetch_assoc($result);
                echo $row['nama_barang'];
        } else {
                echo "Nama Barang Tidak Ditemukan"; // Atau tindakan lain sesuai kebutuhan Anda
        }
} else {
        echo "Kode Barang tidak diterima"; // Atau tindakan lain sesuai kebutuhan Anda
}

// Proses transaksi
if (isset($_POST['submit'])) {

        // Ambil data dari form
        $jenis_transaksi        = $_POST['jenis_transaksi'];
        $bukti                  = $_POST['bukti'];
        $lokasi                 = $_POST['lokasi'];
        $kode_barang            = $_POST['kode_barang'];
        $nama_barang            = $_POST['nama_barang'];
        $tgl_transaksi          = $_POST['tgl_transaksi'];
        $qty                    = $_POST['qty'];
        $tanggal                = date("Y-m-d");
        $jam                    = date("h:i:s");
        $user                   = "user";

        // Lakukan validasi input kosong
        if (empty($jenis_transaksi) || empty($bukti) || empty($lokasi) || empty($kode_barang) || empty($nama_barang) || empty($tgl_transaksi) || empty($qty)) {
                // var_dump(empty($nik),empty($tgl_masuk),empty($nama),empty($alamat),empty($kota),empty($gelar));
                $errors[] = "Semua kolom harus diisi.";
        }

        // Jika terdapat error, tampilkan pesan error
        if (!empty($errors)) {
                session_start();
                foreach ($errors as $error) {
                        // ketika di framework sudah ada fitur flash data message (session)
                        // alert hanya muncul sekali ketika di refresh halaman alertnya hilang, tetapi kalau di php native belum ada fitur tersebut (buat sendiri)
                        $_SESSION['validasi'] = "<div class='alert alert-danger' role='alert'>$error</div>";
                        header("Location: index.php");
                        exit();
                }
        }

        if ($jenis_transaksi == "masuk") {
                // Update stok barang
                $query = "INSERT INTO stok_barang (lokasi, kode_barang, nama_barang, saldo, tanggal_masuk) 
                        VALUES ('$lokasi', '$kode_barang', '$nama_barang', '$qty', '$tgl_transaksi')
                        ON DUPLICATE KEY UPDATE saldo = saldo + $qty";
                $conn->query($query);

                // Masukkan transaksi ke database
                $sql = "INSERT INTO transaksi_history (bukti, tanggal, jam, lokasi, kode_barang, tanggal_masuk, qty_transaksi, program, user)
                        VALUES ('$bukti', '$tanggal', '$jam', '$lokasi', '$kode_barang', '$tgl_transaksi', '$qty', '$jenis_transaksi', '$user')";
                $conn->query($sql);
        } else {
                // Implementasi FIFO
                $query = "SELECT id, saldo FROM stok_barang WHERE kode_barang = '$kode_barang' ORDER BY id ASC";
                $result = mysqli_query($conn, $query);
                $total_qty = $qty;
                $row = mysqli_fetch_assoc($result);
                $saldo = $row['saldo'];
                while ($row) {
                        if ($row['saldo'] >= $total_qty) {
                                $query = "UPDATE stok_barang SET saldo = saldo - $total_qty WHERE id = {$row['id']}";
                                mysqli_query($conn, $query);
                                break;
                        } else {
                                $query = "UPDATE stok_barang SET saldo = 0 WHERE id = {$row['id']}";
                                mysqli_query($conn, $query);
                                $total_qty -= $row['saldo'];
                        }
                }
        }
        // Redirect ke halaman lain atau tampilkan pesan sukses
        echo '<script> window.location.href = "index.php";</script>';
}
