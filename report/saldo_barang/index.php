<!DOCTYPE html>
<html>

<head>
    <!-- DataTables Library -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">
    <link href="https://cdn.datatables.net/2.0.3/css/dataTables.dataTables.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/datepicker/css/datepicker.css">
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container-fluid">
            <!-- <a class="navbar-brand" href="#">Navbar</a> -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
                <div class="navbar-nav">
                    <a class="nav-link " aria-current="page" href="../../maintenance_stok/index.php">Transaksi</a>
                    <a class="nav-link active" href="index.php">Report Saldo Barang</a>
                    <a class="nav-link" href="../transaksi_history/index.php">Report Transaksi History</a>
                    <!-- <a class="nav-link disabled">Disabled</a> -->
                </div>
            </div>
        </div>
    </nav>
    <div class="container">
        <div id="notification"></div>
        <div class="card mt-5">
            <div class="card-header">
                <h4>Data Karyawan</h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-12">
                    <div class="table-responsive">
                            <center>
                                <table id="karyawanTable" class="table" style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th>NIK</th>
                                            <th>Tanggal Masuk</th>
                                            <th>Nama</th>
                                            <th>Jenis Kelamin</th>
                                            <th>Alamat</th>
                                            <th>Kota</th>
                                            <th>Gelar</th>
                                            <th>Tanggal Keluar</th>
                                            <th>Status</th>
                                            <th style="width: 168px;">
                                                <center>Action</center>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        // Lakukan koneksi ke database atau ambil data dari mana pun yang diperlukan
                                        // Misalnya, kita akan mengasumsikan koneksi ke database telah dibuat sebelumnya
                                        $servername = "localhost";
                                        $username = "root";
                                        $password = "";
                                        $dbname = "db_karyawan";

                                        // Membuat koneksi
                                        $conn = new mysqli($servername, $username, $password, $dbname);

                                        if ($conn->connect_error) {
                                            die("Connection failed: " . $conn->connect_error);
                                        }

                                        // Buat query untuk mengambil data dari database
                                        $query = "SELECT a.*, b.nama_gelar, 
                                                    CASE 
                                                    WHEN a.jenis_kelamin = 'l' THEN 'Laki - Laki'  
                                                    WHEN a.jenis_kelamin = 'p' THEN 'Perempuan'  
                                                    ELSE ''  
                                                    END AS jenis_kelamin_label 
                                                    FROM karyawan a 
                                                    INNER JOIN gelar b ON a.gelar = b.id";

                                        // Tambahkan filter jika tanggal disediakan
                                        if (isset($_GET['tanggal']) && $_GET['tanggal'] != '') {
                                            $tanggal = date('Y-m-d', strtotime($_GET['tanggal']));
                                            $query .= " WHERE (tanggal_masuk <= '$tanggal' AND (tanggal_keluar > '$tanggal' OR tanggal_keluar IS NULL))";
                                        }

                                        // Eksekusi query dan ambil data
                                        $sql_data = mysqli_query($conn, $query);

                                        // Ambil data hasil query
                                        while ($row = mysqli_fetch_assoc($sql_data)) {

                                        ?>

                                            <tr>
                                                <td><?php echo $row['nik']; ?></td>
                                                <!-- <td><?php echo $row['tanggal_masuk']; ?></td> -->
                                                <td><?php echo date('d/m/Y', strtotime($row["tanggal_masuk"])); ?></td>
                                                <td><?php echo $row['nama']; ?></td>
                                                <td><?php echo $row['jenis_kelamin_label']; ?></td>
                                                <td><?php echo $row['alamat']; ?></td>
                                                <td><?php echo $row['kota']; ?></td>
                                                <td><?php echo $row['nama_gelar']; ?></td>
                                                <!-- <td><?php echo $row['tanggal_keluar']; ?></td> -->
                                                <?php if($row['tanggal_keluar'] != null){?>
                                                <td><?php echo date('d/m/Y', strtotime($row["tanggal_keluar"])); ?></td>
                                                <?php } else{
                                                    echo '<td></td>';
                                                }?>
                                                <td><?php
                                                    $today = date("Y-m-d");
                                                    if ($today >= $row['tanggal_masuk'] && ($row['tanggal_keluar'] == null || $today <= $row['tanggal_keluar'])) {
                                                        echo '<span class="badge badge-success">Aktif</span>';
                                                    } else {
                                                        echo '<span class="badge badge-danger">Tidak Aktif</span>';
                                                    }
                                                    ?></td>
                                                <td>
                                                    <?php
                                                    $today = date("Y-m-d");
                                                    if ($today >= $row['tanggal_masuk'] && ($row['tanggal_keluar'] == null || $today <= $row['tanggal_keluar'])) {
                                                        echo '<center>';
                                                        echo '<a href="tambah_karyawan.php?id=' . $row['nik'] . '" class="btn btn-warning btn-sm">Edit</a>';
                                                        echo '&nbsp;';
                                                        // echo '<button class="btn btn-danger btn-sm" onclick="confirmDelete(' . $row['nik'] . ')">Hapus</button>';
                                                        // echo '<a href="hapus_karyawan.php?id=' . $row['nik'] . '" class="btn btn-danger">Hapus</a>';
                                                        echo '</center>';
                                                    }else if($today <= $row['tanggal_masuk'] && $row['tanggal_keluar'] == null ){
                                                        echo '<center>';
                                                        echo '<a href="tambah_karyawan.php?id=' . $row['nik'] . '" class="btn btn-warning btn-sm">Edit</a>';
                                                        echo '&nbsp;';
                                                        echo '<button class="btn btn-danger btn-sm" onclick="confirmDelete(' . $row['nik'] . ')">Hapus</button>';
                                                        // echo '<a href="hapus_karyawan.php?id=' . $row['nik'] . '" class="btn btn-danger">Hapus</a>';
                                                        echo '</center>';
                                                    }
                                                    ?>
                                                </td>
                                            </tr>

                                        <?php
                                        }
                                        ?>

                                    </tbody>
                                </table>
                            </center>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script src="https://cdn.datatables.net/2.0.3/js/dataTables.min.js"></script>
    <script src="../../assets/datepicker/js/bootstrap-datepicker.js"></script>
</body>

</html>