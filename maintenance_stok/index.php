<!DOCTYPE html>
<html>

<head>
    <?php include '../koneksi.php'; ?>
    <style>
        .container {
            flex-wrap: wrap;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }
    </style>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">
    <link rel="stylesheet" href="../assets/datepicker/css/datepicker.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <title>Inventory Management</title>
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
                    <a class="nav-link active" aria-current="page" href="index.php">Transaksi</a>
                    <a class="nav-link" href="../report/saldo_barang/index.php">Report Saldo Barang</a>
                    <a class="nav-link" href="../report/transaksi_history/index.php">Report Transaksi History</a>
                    <!-- <a class="nav-link disabled">Disabled</a> -->
                </div>
            </div>
        </div>
    </nav>
    <div class="container">
        <div id="notification"></div>
        <div class="card mt-5">
            <div class="card-header">
                <h4>Maintenance Stok</h4>
            </div>
            <div class="card-body">
                <!-- notifikasi -->
                <?php
                session_start();
                if (isset($_SESSION['validasi'])) {
                    echo $_SESSION['validasi'];
                    unset($_SESSION['validasi']);
                }
                if (isset($_SESSION['message'])) {
                    echo $_SESSION['message'];
                    unset($_SESSION['message']);
                }
                ?>
                <div class="row">
                    <div class="col-12">
                        <form method="post" action="stok.php">
                            <div class="form-group">
                                <label>Jenis Transaksi :
                                    &nbsp;
                                </label>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="jenis_transaksi" id="masuk" value="masuk">
                                    <label class="form-check-label" for="masuk">Masuk</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="jenis_transaksi" id="keluar" value="keluar">
                                    <label class="form-check-label" for="keluar">Keluar</label>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="bukti">Bukti :</label>
                                <input type="text" class="form-control" id="bukti" name="bukti" required>
                            </div>
                            <div class="form-group">
                                <label for="lokasi">Lokasi :</label>
                                <select class="custom-select" name="lokasi" id="lokasi" required>
                                    <option selected disabled>--Pilih Satu--</option>
                                    <?php
                                    // Buat query untuk mengambil data dari database
                                    $query = "SELECT * from lokasi"; // Eksekusi query dan ambil data
                                    $sql_data = mysqli_query($conn, $query);

                                    // Ambil data hasil query
                                    while ($row = mysqli_fetch_assoc($sql_data)) {
                                        echo '<option value=' . $row['id'] . ($row === $selectedValue ? 'selected="selected"' : '') . '>' . $row['kode_lokasi'] . ' - ' . $row['deskripsi'] . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="lokasi">Kode Barang :</label>
                                <select class="custom-select" name="kode_barang" id="kode_barang" onchange="getNamaBarang()" required>
                                    <option selected disabled>--Pilih Kode Barang--</option>
                                    <?php
                                    // Buat query untuk mengambil data dari database
                                    $query = "SELECT * FROM barang";
                                    $sql_data = mysqli_query($conn, $query);

                                    // Ambil data hasil query
                                    while ($row = mysqli_fetch_assoc($sql_data)) {
                                        echo '<option value=' . $row['id'] . '>' . $row['kode_barang'] . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="lokasi">Nama Barang :</label>
                                <input type="text" class="form-control" id="nama_barang" name="nama_barang" aria-describedby="namaBarangHelp" readonly required>
                                <small id="namaBarangHelp" class="form-text text-muted">*Otomatis Terisi oleh Sistem</small>
                            </div>
                            <div class="form-group">
                                <label for="lokasi">Tgl Transaksi :</label>
                                <input type="text" class="form-control datepicker" id="tgl_transaksi" name="tgl_transaksi" readonly required>
                                <small id="tglHelp" class="form-text text-muted">*Silahkan klik jika akan mengisi</small>

                            </div>
                            <div class="form-group">
                                <label for="lokasi">Quantity :</label>
                                <input type="number" class="form-control" id="qty" name="qty" min="1" required>
                            </div>
                            <div class="form-group">
                                <label for="lokasi">User :</label>
                                <select class="custom-select" name="user" id="user" required>
                                    <option selected disabled>--Pilih User--</option>
                                    <?php
                                    // Buat query untuk mengambil data dari database
                                    $query = "SELECT * FROM user";
                                    $sql_data = mysqli_query($conn, $query);

                                    // Ambil data hasil query
                                    while ($row = mysqli_fetch_assoc($sql_data)) {
                                        echo '<option value=' . $row['id'] . '>' . $row['nama'] . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <input type="submit" class="btn btn-primary mt-3" value="Submit" name="submit">
                                <a class="btn btn-secondary mt-3" href="index.php" role="button">Reset</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="../assets/datepicker/js/bootstrap-datepicker.js"></script>
    <script>
        $(function() {
            $(".datepicker").datepicker({
                format: 'dd/mm/yyyy',
                autoclose: true,
                todayHighlight: true,
            });
        });

        function getNamaBarang() {
            var kodeBarang = document.getElementById("kode_barang").value;

            $.ajax({
                type: 'POST',
                url: 'stok.php',
                data: {
                    kode_barang: kodeBarang
                },
                success: function(response) {
                    document.getElementById("nama_barang").value = response;
                }
            });
        }
    </script>
</body>

</html>