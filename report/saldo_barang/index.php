<!DOCTYPE html>
<html>

<head>
    <?php include '../../koneksi.php'; ?>
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
                <h4>Data Stok Barang</h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <p>Filter</p>
                            </div>
                            <div class="card-body">
                                <form id="filterForm" method="get">
                                    <div class="row">
                                        <div class="col-6">
                                            <div class="form-group mx-3">
                                                <label>Lokasi :</label>
                                                <input type="text" id="lokasi" name="lokasi" class="form-control" onchange="change()" value="<?php echo isset($_GET['lokasi']) ? $_GET['lokasi'] : ''; ?>">
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="form-group mx-3">
                                                <label>Kode Barang :</label>
                                                <input type="text" id="kode_barang" name="kode_barang" class="form-control" onchange="change()" value="<?php echo isset($_GET['kode_barang']) ? $_GET['kode_barang'] : ''; ?>">
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-12">
                        <div class="table-responsive">
                            <center>
                                <table id="stokTable" class="table" style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th>Kode Barang</th>
                                            <th>Nama Barang</th>
                                            <th>Lokasi</th>
                                            <th>Deskripsi Lokasi</th>
                                            <th>Saldo</th>
                                            <th>Tanggal Masuk</th>
                                            <!-- <th style="width: 168px;">
                                                <center>Action</center>
                                            </th> -->
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        // Buat query untuk mengambil data dari database
                                        //Kata kunci INNER JOIN memilih record yang memiliki nilai yang cocok di kedua tabel.
                                        $query = "SELECT b.kode_barang, b.nama_barang, l.kode_lokasi, l.deskripsi, sb.saldo, sb.tanggal_masuk 
                                              FROM stok_barang sb
                                              INNER JOIN barang b ON sb.id_barang = b.id
                                              INNER JOIN lokasi l ON sb.id_lokasi = l.id";

                                        // Tambahkan filter jika ada
                                        if (isset($_GET['kode_barang']) && isset($_GET['lokasi'])) {
                                            $kode_barang = $_GET['kode_barang'];
                                            $lokasi = $_GET['lokasi'];
                                            $query .= " WHERE b.kode_barang = '$kode_barang' AND l.kode_lokasi = '$lokasi'";
                                        } else if (isset($_GET['kode_barang'])) {
                                            $kode_barang = $_GET['kode_barang'];
                                            $query .= " WHERE b.kode_barang = '$kode_barang'";
                                        } else if (isset($_GET['lokasi'])) {
                                            $lokasi = $_GET['lokasi'];
                                            $query .= " WHERE l.kode_lokasi = '$lokasi'";
                                        }

                                        // Eksekusi query dan ambil data
                                        $result = mysqli_query($conn, $query);
                                        // var_dump($query);
                                        // die();
                                        // Tambahkan penanganan kesalahan
                                        if (!$result) {
                                            die("Error in SQL query: " . mysqli_error($conn));
                                        }

                                        // Tampilkan data hasil query
                                        while ($row = mysqli_fetch_assoc($result)) {
                                            echo "<tr>";
                                            echo "<td>" . $row['kode_barang'] . "</td>";
                                            echo "<td>" . $row['nama_barang'] . "</td>";
                                            echo "<td>" . $row['kode_lokasi'] . "</td>";
                                            echo "<td>" . $row['deskripsi'] . "</td>";
                                            echo "<td>" . $row['saldo'] . "</td>";
                                            echo"<td>" . date('d/m/Y', strtotime($row["tanggal_masuk"])) . "</td>";
                                            // echo "<td>" . $row['tanggal_masuk'] . "</td>";
                                            echo "</tr>";
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
    <script>
        var table;
        $(document).ready(function() {
            // Initialize DataTable
            table = new DataTable('#stokTable');
        });

        function change() {
            var lokasi = $('#lokasi').val();
            var kode_barang = $('#kode_barang').val();
            if (kode_barang && lokasi) {
                window.location.href = 'index.php?kode_barang=' + kode_barang + '&lokasi=' + lokasi;
            } else if (kode_barang) {
                window.location.href = 'index.php?kode_barang=' + kode_barang;
            } else if (lokasi) {
                window.location.href = 'index.php?lokasi=' + lokasi;
            }
        }
    </script>
</body>

</html>