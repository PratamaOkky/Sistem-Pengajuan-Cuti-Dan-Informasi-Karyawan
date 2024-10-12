<?php
require_once("../admin/database.php");
require_once("../admin/auth.php");
logged_admin(); // Pastikan fungsi ini mengatur variabel global yang diperlukan

// Variabel untuk menyimpan pesan
$message = "";

// Memeriksa apakah formulir dikirimkan
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama = htmlspecialchars($_POST['nama']);
    $nip = htmlspecialchars($_POST['nip']);
    $tujuan = htmlspecialchars($_POST['tujuan']);
    $tglawal = htmlspecialchars($_POST['tglawal']);
    $tglakhir = htmlspecialchars($_POST['tglakhir']);
    $jenis = htmlspecialchars($_POST['jenis']);
    $keterangan = htmlspecialchars($_POST['keterangan']);
    $status = htmlspecialchars($_POST['status']);

    try {
        $query = "INSERT INTO laporan (nama, nip, tujuan, tglawal, tglakhir, jenis, keterangan, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $db->prepare($query);
        $result = $stmt->execute([$nama, $nip, $tujuan, $tglawal, $tglakhir, $jenis, $keterangan, $status]);

        if ($result) {
            // Set pesan dan alihkan ke halaman yang sama dengan parameter status
            header("Location: index.php?status=success");
            exit(); // Pastikan script berhenti setelah pengalihan
        } else {
            $message = "Terjadi kesalahan saat mengajukan cuti. Silakan coba lagi.";
        }
    } catch (PDOException $e) {
        $message = "Terjadi kesalahan: " . $e->getMessage();
    }
}

// Ambil data admin
$nama_admin = htmlspecialchars($nama_admin); // Ganti dengan nama admin yang sesuai
$nip_admin = htmlspecialchars($nip_admin); // Ganti dengan NIP admin yang sesuai
$divisi = htmlspecialchars($divisi); // Ganti dengan divisi admin yang sesuai
$divisi_admin = htmlspecialchars($divisi_admin); // Ganti dengan divisi admin yang sesuai

// Tampilkan pesan jika ada
if (isset($_GET['status']) && $_GET['status'] == 'success') {
    $message = "Pengajuan cuti berhasil diajukan!";
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width">
    <title>Buat | Sistem Pengajuan Cuti Karyawan</title>
    <link rel="shortcut icon" href="images/favicon.png">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="css/bootstrap.css">
    <!-- Font Awesome CSS -->
    <link rel="stylesheet" href="css/font-awesome.min.css">
    <!-- Main Styles CSS -->
    <link href="css/style.css" rel="stylesheet">
</head>

<body>

    <div class="shadow">
        <nav class="navbar navbar-fixed navbar-inverse form-shadow">
            <div class="container-fluid">
                <!-- Brand and toggle get grouped for better mobile display -->
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <a class="navbar-brand" href="admin/login">
                        <img alt="Brand" src="images/Enerrenn.png">
                    </a>
                </div>

                <!-- Collect the nav links, forms, and other content for toggling -->
                <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                    <ul class="nav navbar-nav">
                        <li><a href="index">Pengajuan Cuti</a></li>
                        <li><a href="lihat">Cek Pengajuan</a></li>
                        <li class="navbar-right"><a class="nav-link" data-toggle="modal" data-target="#exampleModal">
                        <i class="fa fa-fw fa-sign-out"></i>Logout
                    </a></li>

                </div><!-- /.navbar-collapse -->
            </div><!-- /.container-fluid -->
        </nav>

        <!-- Content -->
        <div class="main-content">
            <h3>Buat Pengajuan Cuti</h3>
            <hr/>
            <?php if ($message): ?>
                <div class="alert alert-success">
                    <?= $message ?>
                </div>
            <?php endif; ?>
            <div class="row">
                <div class="col-md-8 card-shadow-2 form-custom">
                    <form class="form-horizontal" role="form" method="post" action="">
                        <div class="form-group" style="display:none;">
                            <label for="nomor" class="col-sm-3 control-label">Nomor Pengaduan</label>
                            <div class="col-sm-9">
                                <div class="input-group">
                                    <div class="input-group-addon"><span class="glyphicon glyphicon-exclamation-sign"></span></div>
                                    <input type="text" class="form-control" id="nomor" name="nomor" value="<?php echo htmlspecialchars($max_id); ?>" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="nama" class="col-sm-3 control-label">Nama</label>
                            <div class="col-sm-9">
                                <div class="input-group">
                                    <div class="input-group-addon"><span class="glyphicon glyphicon-user"></span></div>
                                    <input type="text" class="form-control" id="nama" name="nama" value="<?php echo htmlspecialchars($nama_admin); ?>" readonly required>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="nip" class="col-sm-3 control-label">NIP</label>
                            <div class="col-sm-9">
                                <div class="input-group">
                                    <div class="input-group-addon"><span class="glyphicon glyphicon-barcode"></span></div>
                                    <input type="text" class="form-control" id="nip" name="nip" value="<?php echo htmlspecialchars($nip_admin); ?>" readonly required>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="tujuan" class="col-sm-3 control-label">Divisi</label>
                            <div class="col-sm-9">
                                <div class="input-group">
                                    <div class="input-group-addon"><?php echo $divisi; ?></div>
                                    <input type="hidden" class="form-control" id="tujuan" name="tujuan" value="<?php echo htmlspecialchars($divisi_admin); ?>" required>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="tglawal" class="col-sm-3 control-label">Tanggal Awal</label>
                            <div class="col-sm-9">
                                <div class="input-group">
                                    <div class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></div>
                                    <input type="date" class="form-control" id="tglawal" name="tglawal" required>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="tglakhir" class="col-sm-3 control-label">Tanggal Akhir</label>
                            <div class="col-sm-9">
                                <div class="input-group">
                                    <div class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></div>
                                    <input type="date" class="form-control" id="tglakhir" name="tglakhir">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="jenis" class="col-sm-3 control-label">Jenis Pengajuan</label>
                            <div class="col-sm-9">
                                <div class="input-group">
                                    <div class="input-group-addon"><span class="glyphicon glyphicon-pencil"></span></div>
                                    <select class="form-control" name="jenis">
                                        <option value="Cuti Tahunan">Cuti Tahunan</option>
                                        <option value="Cuti /5tahun">Cuti /5tahun</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="keterangan" class="col-sm-3 control-label">Keterangan</label>
                            <div class="col-sm-9">
                                <div class="input-group">
                                    <div class="input-group-addon"><span class="glyphicon glyphicon-info-sign"></span></div>
                                    <input type="text" class="form-control" id="keterangan" name="keterangan" placeholder="Keterangan tambahan opsional">
                                </div>
                            </div>
                        </div>
                        <input type="hidden" class="form-control" id="status" name="status" value="Menunggu">
                        
                        
                        <div class="form-group">
                            <div class="col-sm-10 col-sm-offset-3">
                                <input id="submit" name="submit" type="submit" value="Kirim Pengajuan" class="btn btn-primary form-shadow">
                            </div>
                        </div>
                        
                    </form>
                </div>
                <div class="col-md-4"></div>
            </div>

            <!-- Link to top -->
            <a id="top" href="#" onclick="topFunction()">
                <i class="fa fa-arrow-circle-up"></i>
            </a>
            <script>
            // When the user scrolls down 100px from the top of the document, show the button
            window.onscroll = function() {scrollFunction()};
            function scrollFunction() {
                if (document.body.scrollTop > 100 || document.documentElement.scrollTop > 100) {
                    document.getElementById("top").style.display = "block";
                } else {
                    document.getElementById("top").style.display = "none";
                }
            }
            // When the user clicks on the button, scroll to the top of the document
            function topFunction() {
                document.body.scrollTop = 0;
                document.documentElement.scrollTop = 0;
            }
            </script>
            <!-- Link to top -->

            <!-- Footer -->
            <footer class="footer text-center">
                <div class="row">
                    <div class="col-md-4 mb-5 mb-lg-0">
                        <ul class="list-inline mb-0">
                            <li class="list-inline-item">
                                <i class="fa fa-top fa-map-marker"></i>
                            </li>
                            <li class="list-inline-item">
                                <h4 class="text-uppercase mb-4">Kantor</h4>
                            </li>
                        </ul>
                        <p class="mb-0">
                        Graha Inti Fauzi Lt. 8
                            <br>Jl. Warung Buncit Raya No. 22 Jakarta 12510
                            <br>Administrative Office:
                            <br>Jl Kemang Dalam IV Blok J No. 3 Jakarta 12730
                        </p>
                    </div>
                    <div class="col-md-4 mb-5 mb-lg-0">
                        <ul class="list-inline mb-0">
                            <li class="list-inline-item">
                                <i class="fa fa-top fa-rss"></i>
                            </li>
                            <li class="list-inline-item">
                                <h4 class="text-uppercase mb-4">Sosial Media</h4>
                            </li>
                        </ul>
                        <ul class="list-inline mb-0">
                            <li class="list-inline-item">
                                <a class="btn btn-outline-light btn-social text-center rounded-circle" href="https://www.facebook.com/InovaTrack.GPS/">
                                    <i class="fa fa-fw fa-facebook"></i>
                                </a>
                            </li>
                            <li class="list-inline-item">
                                <a class="btn btn-outline-light btn-social text-center rounded-circle" href="https://x.com/inovatrack_gps/">
                                    <i class="fa fa-fw fa-twitter"></i>
                                </a>
                            </li>
                        </ul>
                    </div>
                    <div class="col-md-4">
                        <ul class="list-inline mb-0">
                            <li class="list-inline-item">
                                <i class="fa fa-top fa-envelope-o"></i>
                            </li>
                            <li class="list-inline-item">
                                <h4 class="text-uppercase mb-4">Kontak</h4>
                            </li>
                        </ul>
                        <p class="mb-0">
                            021-719-8618 <br>
                            luqman@enerren.com <br>
                            info@enerren.com<br>
                            Fax : 021-719-9525
                        </p>
                    </div>
                </div>
            </footer>
            <!-- /footer -->

            <div class="copyright py-4 text-center text-white">
                <small>Copyright &copy; PT. Enerren Technologies 2024</small>
            </div>
            <!-- Shadow -->
        </div>

        <!-- Logout Modal-->
        <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Yakin Ingin Keluar?</h5>
                            <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">×</span>
                            </button>
                        </div>
                        <div class="modal-body">Pilih "Logout" jika anda ingin mengakhiri sesi.</div>
                        <div class="modal-footer">
                            <button class="btn btn-close card-shadow-2 btn-sm" type="button" data-dismiss="modal">Batal</button>
                            <a class="btn btn-primary btn-sm card-shadow-2" href="logout">Logout</a>
                        </div>
                    </div>
                </div>
        </div>

        <!-- jQuery -->
        <script src="js/jquery.min.js"></script>
        <!-- Bootstrap JavaScript -->
        <script src="js/bootstrap.js"></script>

    </div>

</body>

</html>
