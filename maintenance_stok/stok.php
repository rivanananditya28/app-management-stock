<?php
include '../koneksi.php';
date_default_timezone_set('Asia/Jakarta');

//supaya nama barang keluar sesuai kode barang yg dipilih
if (isset($_POST['kode_barang'])) {
        $kode_barang = $_POST['kode_barang'];

        // Buat query untuk mengambil nama barang berdasarkan kode barang
        $query = "SELECT nama_barang FROM barang WHERE id = '$kode_barang'";
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
        // var_dump($_POST['jenis_transaksi'],$_POST['bukti'],$_POST['lokasi'],$_POST['kode_barang'],$_POST['nama_barang'],$_POST['tgl_transaksi'],$_POST['qty']);
        // die();
        // Ambil data dari form
        $jenis_transaksi        = $_POST['jenis_transaksi'];
        $bukti                  = $_POST['bukti'];
        $lokasi                 = $_POST['lokasi'];
        $kode_barang            = $_POST['kode_barang'];
        $nama_barang            = $_POST['nama_barang'];
        $date                   = date_create_from_format("d/m/Y", $_POST['tgl_transaksi']);
        $tgl_transaksi          = date_format($date, 'Y-m-d');
        // $tgl_transaksi          = date("Y-m-d", strtotime($_POST['tgl_transaksi']));
        // var_dump($tgl_transaksi);
        // die();
        $qty                    = $_POST['qty'];
        $tanggal                = date("Y-m-d H:i:s");
        // var_dump($tanggal);
        // die();
        // $jam                    = date("h:i:s");
        $user                   = "user";

        // Lakukan validasi input kosong
        if (empty($jenis_transaksi) || empty($bukti) || empty($lokasi) || empty($kode_barang) || empty($nama_barang) || empty($qty)) {
                // var_dump(empty($nik),empty($tgl_masuk),empty($nama),empty($alamat),empty($kota),empty($gelar));
                $errors[] = "Semua kolom harus diisi.";
        }

        if ($jenis_transaksi == "masuk") {
                // Ambil tgl masuk terakhir dari saldo barang pada lokasi dan kode barang yang bersangkutan
                $query_last_entry_date = "SELECT MAX(tanggal_masuk) AS last_entry_date FROM stok_barang WHERE kode_barang = '$kode_barang' AND id_lokasi = '$lokasi'";
                $result_last_entry_date = $conn->query($query_last_entry_date);
                $row_last_entry_date = $result_last_entry_date->fetch_assoc();
                $last_entry_date = $row_last_entry_date['last_entry_date'];

                // Konversi tgl transaksi ke format yang sama dengan tgl masuk terakhir
                $date_trans = date_create_from_format("d/m/Y", $_POST['tgl_transaksi']);
                $tgl_transaksi = date_format($date_trans, 'Y-m-d');

                // Bandingkan tgl transaksi dengan tgl masuk terakhir
                if ($tgl_transaksi <= $last_entry_date) {
                        $errors[] = "Tanggal transaksi harus setelah tanggal masuk terakhir.";
                }
                if (empty($tgl_transaksi)) {
                        $errors[] = "Semua kolom harus diisi.";
                }
        }

        if ($jenis_transaksi == "keluar") {
                // Ambil tgl masuk terakhir dari saldo barang pada lokasi dan kode barang yang bersangkutan
                $query_last_entry_date = "SELECT MAX(tanggal_masuk) AS last_entry_date FROM stok_barang WHERE kode_barang = '$kode_barang' AND id_lokasi = '$lokasi'";
                $result_last_entry_date = $conn->query($query_last_entry_date);
                $row_last_entry_date = $result_last_entry_date->fetch_assoc();
                $last_entry_date = $row_last_entry_date['last_entry_date'];

                // Konversi tgl transaksi ke format yang sama dengan tgl masuk terakhir
                $date_trans = date_create_from_format("d/m/Y", $_POST['tgl_transaksi']);
                $tgl_transaksi = date_format($date_trans, 'Y-m-d');

                // Bandingkan tgl transaksi dengan tgl masuk terakhir
                if ($tgl_transaksi < $last_entry_date) {
                        $errors[] = "Tanggal transaksi harus setelah atau sama dengan tanggal masuk terakhir.";
                }
                // Ambil saldo barang pada lokasi dan kode barang yang bersangkutan
                $query_saldo_barang = "SELECT saldo FROM stok_barang WHERE kode_barang = '$kode_barang' AND id_lokasi = '$lokasi'";
                $result_saldo_barang = $conn->query($query_saldo_barang);
                if ($result_saldo_barang->num_rows > 0) {
                        $row_saldo_barang = $result_saldo_barang->fetch_assoc();
                        $saldo_barang = $row_saldo_barang['saldo'];

                        // Bandingkan saldo barang dengan jumlah yang akan ditransaksikan
                        if ($saldo_barang < $qty) {
                                $errors[] = "Saldo barang tidak mencukupi untuk transaksi ini.";
                        }
                } else {
                        $errors[] = "Saldo barang tidak ditemukan.";
                }
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
                $query = "INSERT INTO stok_barang (kode_barang, id_lokasi, saldo, tanggal_masuk) 
                        VALUES ('$kode_barang', '$lokasi', '$qty', '$tgl_transaksi')
                        ON DUPLICATE KEY UPDATE saldo = saldo + $qty";
                $conn->query($query);

                // Masukkan transaksi ke database
                $sql = "INSERT INTO transaksi_history (bukti, tanggal_transaksi, id_lokasi, kode_barang, qty, program, user)
                        VALUES ('$bukti', '$tanggal', '$lokasi', '$kode_barang', '$qty', '$jenis_transaksi', '$user')";
                $conn->query($sql);
        } else {
                // Implementasi FIFO
                $query = "SELECT id, saldo FROM stok_barang WHERE kode_barang = '$kode_barang' ORDER BY id ASC";
                $result = mysqli_query($conn, $query);
                $total_qty = $qty;
                // $row = mysqli_fetch_assoc($result);
                // $saldo = $row['saldo'];
                while ($row = mysqli_fetch_assoc($result)) {
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
