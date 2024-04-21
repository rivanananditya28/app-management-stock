<?php
include '../koneksi.php';
date_default_timezone_set('Asia/Jakarta');

function validateJenisTransaksi($str)
{
        // Validasi jenis transaksi
        if ($str != "masuk" || $str != "keluar") {
                return true;
        }
        // return true;
}

function validateBukti($str)
{
        if (strpos($str, ' ') !== false) {
                return true;
        }
        if (bukti_sudah_ada($str)) {
                return false;
        } else {
                return true;
        }
}

function validateQuantity($str)
{
        $str = trim($str);
        if (is_numeric($str) && $str > 0) {
                return true;
        } else {
                return false;
        }
}

// Fungsi untuk memeriksa apakah bukti sudah ada di dalam database
function bukti_sudah_ada($bukti)
{
        // Lakukan koneksi ke database, sesuaikan dengan pengaturan database kamu
        $servername = "localhost";
        $username = "root";
        $password = "";
        $dbname = "db_inventory";

        // Buat koneksi
        $conn = new mysqli($servername, $username, $password, $dbname);

        // Periksa koneksi
        if ($conn->connect_error) {
                die("Koneksi ke database gagal: " . $conn->connect_error);
        }

        // Lakukan kueri untuk memeriksa apakah bukti sudah ada
        $sql = "SELECT COUNT(*) AS count FROM transaksi_history WHERE bukti = '$bukti'";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $count = $row["count"];
                // Jika count > 0, berarti bukti sudah ada di database
                if ($count > 0) {
                        $conn->close();
                        return true;
                }
        }

        $conn->close();
        return false;
}
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
        // Ambil data dari form
        $jenis_transaksi        = $_POST['jenis_transaksi'];
        $bukti                  = $_POST['bukti'];
        $lokasi                 = $_POST['lokasi'];
        $kode_barang            = $_POST['kode_barang'];
        $nama_barang            = $_POST['nama_barang'];
        $date                   = date_create_from_format("d/m/Y", $_POST['tgl_transaksi']);
        $tgl_transaksi          = date_format($date, 'Y-m-d');
        $qty                    = $_POST['qty'];
        $tanggal                = date("Y-m-d H:i:s");
        $user                   = $_POST['user'];

        // Lakukan validasi input kosong
        if (empty($jenis_transaksi) || empty($bukti) || empty($lokasi) || empty($kode_barang) || empty($nama_barang) || empty($qty) || empty($user)) {
                $errors[] = "Semua kolom harus diisi.";
        }

        // Lakukan validasi
        if (!validateJenisTransaksi($jenis_transaksi)) {
                // var_dump($jenis_transaksi);
                // die();
                $errors[] = "Jenis Transaksi Tidak Sesuai";
        }

        if (!validateBukti($bukti)) {
                $errors[] = "Bukti Sudah Ada";
        }

        if (!validateQuantity($qty)) {
                $errors[] = "Qty harus berupa angka non negatif";
        }

        if ($jenis_transaksi == "masuk") {
                //ambil tgl hari ini
                $today = date("Y-m-d");
                // Ambil tgl masuk terakhir dari saldo barang pada lokasi dan kode barang yang bersangkutan
                $query_last_entry_date = "SELECT MAX(tanggal_masuk) AS last_entry_date FROM stok_barang WHERE id_barang = '$kode_barang' AND id_lokasi = '$lokasi'";
                $result_last_entry_date = $conn->query($query_last_entry_date);
                $row_last_entry_date = $result_last_entry_date->fetch_assoc();
                $last_entry_date = $row_last_entry_date['last_entry_date'];

                // Konversi tgl transaksi ke format yang sama dengan tgl masuk terakhir
                $date_trans = date_create_from_format("d/m/Y", $_POST['tgl_transaksi']);
                $tgl_transaksi = date_format($date_trans, 'Y-m-d');

                //Bandingkan tgl transaksi dengan tanggal hari ini
                if ($tgl_transaksi != $today) {
                        $errors[] = "Tanggal transaksi masuk harus sama dengan tgl hari ini.";
                }
                // Bandingkan tgl transaksi dengan tgl masuk terakhir
                if ($tgl_transaksi < $last_entry_date) {
                        $errors[] = "Tanggal transaksi harus setelah tanggal masuk terakhir.";
                }
                if (empty($tgl_transaksi)) {
                        $errors[] = "Semua kolom harus diisi.";
                }
        }

        if ($jenis_transaksi == "keluar") {
                // Ambil tgl masuk terakhir dari saldo barang pada lokasi dan kode barang yang bersangkutan
                $query_last_entry_date = "SELECT MAX(tanggal_masuk) AS last_entry_date FROM stok_barang WHERE id_barang = '$kode_barang' AND id_lokasi = '$lokasi'";
                $result_last_entry_date = $conn->query($query_last_entry_date);
                $row_last_entry_date = $result_last_entry_date->fetch_assoc();
                $last_entry_date = $row_last_entry_date['last_entry_date'];

                // Konversi tgl transaksi ke format yang sama dengan tgl masuk terakhir
                $date_trans = date_create_from_format("d/m/Y", $_POST['tgl_transaksi']);
                $tgl_transaksi = date_format($date_trans, 'Y-m-d');

                // Bandingkan tgl transaksi dengan tgl masuk terakhir
                if ($tgl_transaksi < $last_entry_date) {
                        $errors[] = "Tanggal transaksi harus setelah tanggal masuk transaksi terakhir.";
                }

                // Ambil saldo barang pada lokasi dan kode barang yang bersangkutan
                $query_saldo_barang = "SELECT sum(saldo) as saldo FROM stok_barang WHERE id_barang = '$kode_barang' AND id_lokasi = '$lokasi'";
                // $query_saldo_barang = "SELECT saldo FROM stok_barang WHERE kode_barang = '$kode_barang' AND id_lokasi = '$lokasi'";
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
                // // Update stok barang
                // $query = "INSERT INTO stok_barang (id_barang, id_lokasi, saldo, tanggal_masuk) 
                //         VALUES ('$kode_barang', '$lokasi', '$qty', '$tgl_transaksi')
                //         ON DUPLICATE KEY UPDATE saldo = saldo + $qty";
                // $conn->query($query);

                // // ambil id_stok
                // $query_id_stok = "SELECT id FROM stok_barang WHERE id_barang = '$kode_barang' AND id_lokasi = '$lokasi' AND tanggal_masuk = '$tgl_transaksi'";
                // $result_id_stok = $conn->query($query_id_stok);
                // $row_id_stok = $result_id_stok->fetch_assoc();
                // $query_id_stok = $row_id_stok['id'];

                // // Masukkan transaksi ke database
                // $sql = "INSERT INTO transaksi_history (bukti, tanggal_transaksi, qty, program, id_user, id_stok)
                //         VALUES ('$bukti', '$tanggal', '$qty', '$jenis_transaksi', '$user', '$query_id_stok')";
                // $conn->query($sql);

                // Cek apakah tanggal masuk sudah ada dalam record tabel stok (revisi no.6)
                $query_check = "SELECT id FROM stok_barang WHERE id_barang = '$kode_barang' AND id_lokasi = '$lokasi' AND tanggal_masuk = '$tgl_transaksi'";
                $result_check = $conn->query($query_check);

                if ($result_check->num_rows > 0) {
                        // Jika tanggal masuk sudah ada, lakukan update jumlah stok
                        $query_update = "UPDATE stok_barang SET saldo = saldo + $qty WHERE id_barang = '$kode_barang' AND id_lokasi = '$lokasi' AND tanggal_masuk = '$tgl_transaksi'";
                        $conn->query($query_update);

                        // Ambil id_stok
                        $row_id_stok = $result_check->fetch_assoc();
                        $query_id_stok = $row_id_stok['id'];
                } else {
                        // Jika tanggal masuk belum ada, lakukan insert baru
                        $query_insert = "INSERT INTO stok_barang (id_barang, id_lokasi, saldo, tanggal_masuk) 
                    VALUES ('$kode_barang', '$lokasi', '$qty', '$tgl_transaksi')";
                        $conn->query($query_insert);

                        // Ambil id_stok dari record yang baru saja di-insert
                        $query_id_stok = $conn->insert_id;
                }

                // Masukkan transaksi ke database
                $sql = "INSERT INTO transaksi_history (bukti, tanggal_transaksi, qty, program, id_user, id_stok)
                        VALUES ('$bukti', '$tanggal', '$qty', '$jenis_transaksi', '$user', '$query_id_stok')";
                $conn->query($sql);
        } else {
                // Implementasi FIFO
                $query = "SELECT id, saldo FROM stok_barang WHERE id_barang = '$kode_barang' AND id_lokasi = '$lokasi' AND saldo > 0 ORDER BY tanggal_masuk ASC";
                $result = mysqli_query($conn, $query);
                $total_qty = $qty;
                // $hasil ='';
                while ($row = mysqli_fetch_assoc($result)) {
                        if ($row['saldo'] >= $total_qty) {
                                $query = "UPDATE stok_barang SET saldo = saldo - $total_qty WHERE id = {$row['id']}";
                                mysqli_query($conn, $query);
                                // insert transaksi_history
                                $sql = "INSERT INTO transaksi_history (bukti, tanggal_transaksi, qty, program, id_user, id_stok)
                                        VALUES ('$bukti', '$tanggal', '$total_qty', '$jenis_transaksi', '$user', '{$row['id']}')";
                                $conn->query($sql);
                                break;
                        } else {
                                $query = "UPDATE stok_barang SET saldo = 0 WHERE id = {$row['id']}";
                                mysqli_query($conn, $query);
                                $total_qty -= $row['saldo'];
                                // insert transaksi_history
                                $sql = "INSERT INTO transaksi_history (bukti, tanggal_transaksi, qty, program, id_user, id_stok)
                                        VALUES ('$bukti', '$tanggal', '{$row['saldo']}', '$jenis_transaksi', '$user', '{$row['id']}')";
                                $conn->query($sql);
                        }
                }
        }
        session_start();
        // ketika di framework sudah ada fitur flash data message (session)
        // alert hanya muncul sekali ketika di refresh halaman alertnya hilang, tetapi kalau di php native belum ada fitur tersebut (buat sendiri)
        $_SESSION['message'] = '<div class="alert alert-success" role="alert">Berhasil disimpan!</div>';
        header("Location: index.php");
        exit();
        // echo '<script> window.location.href = "index.php";</script>';
}
