<!DOCTYPE html>
<html>
<?php include '../../koneksi.php';
// date_default_timezone_set('Asia/Jakarta');
?>

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
                    <a class="nav-link" aria-current="page" href="../../maintenance_stok/index.php">Transaksi</a>
                    <a class="nav-link" href="../saldo_barang/index.php">Report Saldo Barang</a>
                    <a class="nav-link active" href="index.php">Report Transaksi History</a>
                    <!-- <a class="nav-link disabled">Disabled</a> -->
                </div>
            </div>
        </div>
    </nav>
    <div class="container">
        <div class="card mt-5">
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
                                        <div class="col-3">
                                            <div class="form-group mx-3">
                                                <label>Bukti :</label>
                                                <input type="text" id="bukti" name="bukti" class="form-control" onchange="change()" value="<?php echo isset($_GET['bukti']) ? $_GET['bukti'] : ''; ?>">
                                            </div>
                                        </div>
                                        <div class="col-3">
                                            <div class="form-group mx-3">
                                                <label>Tanggal Transaksi :</label>
                                                <input type="text" id="tgl_transaksi" name="tgl_transaksi" class="form-control datepicker" onchange="change()" value="<?php echo isset($_GET['tgl_transaksi']) ? $_GET['tgl_transaksi'] : ''; ?>" autocomplete="off">
                                            </div>
                                        </div>
                                        <div class="col-3">
                                            <div class="form-group mx-3">
                                                <label>Lokasi :</label>
                                                <input type="text" id="lokasi" name="lokasi" class="form-control" onchange="change()" value="<?php echo isset($_GET['lokasi']) ? $_GET['lokasi'] : ''; ?>">
                                            </div>
                                        </div>
                                        <div class="col-3">
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
                        <table id="table_id" class="datatable table table-striped">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Bukti</th>
                                    <th>Tanggal Transaksi</th>
                                    <th>Jam Transaksi</th>
                                    <th>Lokasi</th>
                                    <th>Kode Barang</th>
                                    <th>Tgl Masuk</th>
                                    <th>Qty</th>
                                    <th>Program</th>
                                    <th>User</th>
                                    <!-- <th style="width: 168px;">
                                        <center>Action</center>
                                    </th> -->
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                //mengembalikan semua catatan dari tabel kiri (tabel1), dan catatan yang cocok dari tabel kanan 
                                $query = "SELECT
                                        * 
                                    FROM
                                        transaksi_history
                                        INNER JOIN stok_barang ON transaksi_history.id_stok = stok_barang.id
                                        INNER JOIN lokasi ON stok_barang.id_lokasi = lokasi.id
                                        INNER JOIN barang ON stok_barang.id_barang = barang.id
                                        INNER JOIN user ON transaksi_history.id_user = user.id";

                                // // Ambil nilai parameter dari JavaScript
                                $kode_barang = isset($_GET['kode_barang']) ? $_GET['kode_barang'] : '';
                                $lokasi = isset($_GET['lokasi']) ? $_GET['lokasi'] : '';
                                $bukti = isset($_GET['bukti']) ? $_GET['bukti'] : '';
                                // $tgl_transaksi = isset($_GET['tgl_transaksi']) ? $_GET['tgl_transaksi'] :'';
                                if (!empty($_GET["tgl_transaksi"])) {
                                    $tgl_transaksi = DateTime::createFromFormat("d/m/Y", $_GET["tgl_transaksi"]);
                                    if ($tgl_transaksi) {
                                        $tgl_transaksi_formatted = $tgl_transaksi->format("Y-m-d"); // Konversi ke format yang sesuai dengan tipe data timestamp di database
                                        $query .= " AND DATE(tanggal_transaksi) = '$tgl_transaksi_formatted'";
                                    } else {
                                        echo "Format Tanggal Salah";
                                    }
                                }

                                if (!empty($kode_barang)) {
                                    $query .= " AND barang.id_barang = '$kode_barang'";
                                }
                                if (!empty($lokasi)) {
                                    $query .= " AND lokasi.id_lokasi = '$lokasi'";
                                }
                                // if (!empty($tgl_transaksi)) {
                                //     $query .= " AND tanggal_transaksi = '$tgl_transaksi'";
                                // }
                                if (!empty($bukti)) {
                                    $query .= " AND transaksi_history.bukti = '$bukti'";
                                }

                                $result = $conn->query($query);
                                if ($result === false) {
                                    die("Error dalam eksekusi query: " . $conn->error);
                                }
                                // var_dump($result->fetch_assoc());
                                // die();
                                $no = 1;
                                while ($row = $result->fetch_assoc()) {
                                ?>
                                    <tr>
                                        <td><?= $no++; ?></td>
                                        <td><?= $row['bukti']; ?></td>
                                        <td><?= date('d/m/Y', strtotime($row['tanggal_transaksi'])); ?></td>
                                        <td><?= date('H:i:s', strtotime($row['tanggal_transaksi'])); ?></td>
                                        <td><?= $row['kode_lokasi']; ?></td>
                                        <td><?= $row['kode_barang']; ?></td>
                                        <td><?= date('d/m/Y', strtotime($row['tanggal_masuk'])); ?></td>
                                        <!-- <td><?= $row['tanggal_masuk']; ?></td> -->
                                        <td>
                                            <?php
                                            if ($row['program'] == "keluar") {
                                                echo "-";
                                            }
                                            ?>
                                            <?= $row['qty']; ?>
                                        </td>
                                        <td><?= $row['program']; ?></td>
                                        <td><?= $row['nama']; ?></td>
                                        <!-- <td><?php
                                                    if ($row['program'] == "masuk") {
                                                        echo '<center>';
                                                        echo '<a href="../../maintenance_stok/index.php?id=' . $row['id'] . '" class="btn btn-warning btn-sm">Edit</a>';
                                                        echo '&nbsp;';
                                                        echo '<button class="btn btn-danger btn-sm" onclick="confirmDelete(' . $row['id'] . ')">Hapus</button>';
                                                        echo '</center>';
                                                    } ?></td> -->

                                    </tr>
                                <?php
                                }
                                ?>
                            </tbody>
                        </table>
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
        $(function() {
            $(".datepicker").datepicker({
                format: 'dd/mm/yyyy',
                autoclose: true,
                todayHighlight: true,
            });
            // Initialize DataTable
            table = new DataTable('#table_id', {
                columnDefs: [{
                    targets: [0],
                    orderable: false,
                    searchable: false
                }, ]
            });
        });

        ////cara panjang
        // function change() {
        //     var lokasi = $('#lokasi').val();
        //     var kode_barang = $('#kode_barang').val();
        //     var tgl_transaksi = $('#tgl_transaksi').val();
        //     var bukti = $('#bukti').val();

        //     if (kode_barang && lokasi && tgl_transaksi && bukti) {
        //         window.location.href = 'index.php?kode_barang=' + kode_barang + '&lokasi=' + lokasi + '&tgl_transaksi=' + tgl_transaksi + '&bukti=' + bukti;
        //     } else if (kode_barang && lokasi && tgl_transaksi) {
        //         window.location.href = 'index.php?kode_barang=' + kode_barang + '&lokasi=' + lokasi + '&tgl_transaksi=' + tgl_transaksi;
        //     } else if (kode_barang && lokasi && bukti) {
        //         window.location.href = 'index.php?kode_barang=' + kode_barang + '&lokasi=' + lokasi + '&bukti=' + bukti;
        //     } else if (kode_barang && tgl_transaksi && bukti) {
        //         window.location.href = 'index.php?kode_barang=' + kode_barang + '&tgl_transaksi=' + tgl_transaksi + '&bukti=' + bukti;
        //     } else if (lokasi && tgl_transaksi && bukti) {
        //         window.location.href = 'index.php?lokasi=' + lokasi + '&tgl_transaksi=' + tgl_transaksi + '&bukti=' + bukti;
        //     } else if (kode_barang && lokasi) {
        //         window.location.href = 'index.php?kode_barang=' + kode_barang + '&lokasi=' + lokasi;
        //     } else if (kode_barang && tgl_transaksi) {
        //         window.location.href = 'index.php?kode_barang=' + kode_barang + '&tgl_transaksi=' + tgl_transaksi;
        //     } else if (kode_barang && bukti) {
        //         window.location.href = 'index.php?kode_barang=' + kode_barang + '&bukti=' + bukti;
        //     } else if (lokasi && tgl_transaksi) {
        //         window.location.href = 'index.php?lokasi=' + lokasi + '&tgl_transaksi=' + tgl_transaksi;
        //     } else if (lokasi && bukti) {
        //         window.location.href = 'index.php?lokasi=' + lokasi + '&bukti=' + bukti;
        //     } else if (tgl_transaksi && bukti) {
        //         window.location.href = 'index.php?tgl_transaksi=' + tgl_transaksi + '&bukti=' + bukti;
        //     } else if (kode_barang) {
        //         window.location.href = 'index.php?kode_barang=' + kode_barang;
        //     } else if (lokasi) {
        //         window.location.href = 'index.php?lokasi=' + lokasi;
        //     } else if (tgl_transaksi) {
        //         window.location.href = 'index.php?tgl_transaksi=' + tgl_transaksi;
        //     } else if (bukti) {
        //         window.location.href = 'index.php?bukti=' + bukti;
        //     } else {
        //         window.location.href = 'index.php';
        //     }
        // }

        //cara sederhana
        function change() {
            var lokasi = $('#lokasi').val();
            var kode_barang = $('#kode_barang').val();
            var tgl_transaksi = $('#tgl_transaksi').val();
            var bukti = $('#bukti').val();

            var url = 'index.php';
            if (kode_barang) url += '?kode_barang=' + kode_barang;
            if (lokasi) url += (url.includes('?') ? '&' : '?') + 'lokasi=' + lokasi;
            if (tgl_transaksi) url += (url.includes('?') ? '&' : '?') + 'tgl_transaksi=' + tgl_transaksi;
            if (bukti) url += (url.includes('?') ? '&' : '?') + 'bukti=' + bukti;

            window.location.href = url;
        }
    </script>
</body>

</html>