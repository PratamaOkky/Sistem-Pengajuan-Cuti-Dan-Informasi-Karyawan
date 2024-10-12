<?php
require_once("../admin/database.php");
require_once("../admin/auth.php");
logged_admin(); // Pastikan fungsi ini mengatur variabel global yang diperlukan

$nip_admin = htmlspecialchars($nip_admin); // Ganti dengan nama admin yang sesuai

// Query untuk mengambil data pengajuan cuti yang sesuai dengan NIP admin
$query = "SELECT * FROM laporan WHERE nip = :nip_admin ORDER BY id DESC";
$stmt = $db->prepare($query);
$stmt->bindParam(':nip_admin', $nip_admin, PDO::PARAM_STR);
$stmt->execute();
$pengajuan = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fungsi untuk menghitung hari kerja di antara dua tanggal
function hitungHariKerja($startDate, $endDate) {
    $startDate = new DateTime($startDate);
    $endDate = new DateTime($endDate);
    $interval = new DateInterval('P1D');
    $daterange = new DatePeriod($startDate, $interval, $endDate->add($interval));

    $workdays = 0;
    foreach($daterange as $date) {
        if ($date->format("N") < 6) {
            $workdays++;
        }
    }
    return $workdays + 0; // Menambahkan satu hari untuk tanggal akhir
}

// Format tanggal dan hitung jumlah hari kerja
foreach ($pengajuan as &$cuti) {
    $tglawal = new DateTime($cuti['tglawal']);
    $cuti['tglawal'] = $tglawal->format('j F Y');
    $tanggal = new DateTime($cuti['tanggal']);
    $cuti['tanggal'] = $tanggal->format('j F Y');

    if (empty($cuti['tglakhir'])) {
        $cuti['tglakhir'] = ' ';
        $cuti['jumlah_hari'] = 1;
    } else {
        $tglakhir = new DateTime($cuti['tglakhir']);
        $cuti['tglakhir'] = '-' . $tglakhir->format('j F Y');
        $cuti['jumlah_hari'] = hitungHariKerja($tglawal->format('Y-m-d'), $tglakhir->format('Y-m-d'));
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width">
    <title>Cek | Sistem Pengajuan Cuti Karyawan</title>
    <link rel="shortcut icon" href="images/favicon.png">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="css/bootstrap.css">
    <!-- font Awesome CSS -->
    <link rel="stylesheet" href="css/font-awesome.min.css">
    <!-- Main Styles CSS -->
    <link href="css/style.css" rel="stylesheet">
    <!-- jQuery -->
    <script src="js/jquery.min.js"></script>
    <!-- Bootstrap JavaScript -->
    <script src="js/bootstrap.js"></script>
    <style>
    .custom-card {
        display: flex;
        border: 1px solid #ddd; /* Border warna abu-abu muda */
        border-radius: 10px; /* Sudut melengkung */
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* Bayangan card */
        transition: transform 0.2s; /* Efek transisi */
        overflow: hidden; /* Menghindari gambar melebihi batas card */
        margin-bottom: 20px; /* Jarak bawah antar card */
    }

    .custom-card:hover {
        transform: translateY(-5px); /* Efek hover */
        box-shadow: 0 6px 12px rgba(0, 0, 0, 0.2); /* Bayangan lebih besar saat hover */
    }

    .card-img-top {
        width: 120px; /* Lebar gambar */
        height: 120px; /* Tinggi gambar */
        object-fit: cover; /* Memastikan gambar mengisi area tanpa distorsi */
        border-top-left-radius: 10px; /* Sudut melengkung gambar */
        border-top-right-radius: 10px; /* Sudut melengkung gambar */
        margin: 10px; /* Jarak gambar dari tepi card */
    }

    .card-body {
        display: flex;
        flex-direction: column;
        justify-content: center;
        padding: 10px;
    }

    .custom-card-footer {
        background-color: #f8f9fa; /* Warna latar belakang footer */
        font-size: 0.9rem; /* Ukuran font footer */
        border-top: 1px solid #ddd; /* Border atas */
        text-align: center;
        
        position: absolute;

    }
    .date-placeholder {
            display: inline-block;
            width: 120px; /* Panjang untuk space kosong */
            text-align: right;
            color: transparent;
        }
</style>
</head>

<body>

    <?php
    // alert pengajuan tidak ditemukan
    if(isset($notFound)) {
        ?>
        <script type="text/javascript">
        $("#failedmodal").modal();
        </script>
        <?php
    }
    ?>

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

                    </ul>
                </div><!-- /.navbar-collapse -->
            </div><!-- /.container-fluid -->
        </nav><!-- /.nav -->

        
        <!-- Content -->
        <div class="main-content">
            <h3>Riwayat Pengajuan</h3>
            <hr/>
            <div class="row">
                <?php if (!empty($pengajuan)): ?>
                    <?php foreach ($pengajuan as $cuti): ?>
                        <div class="col-md-4 mb-4">
                            <div class="card custom-card" data-id="<?php echo htmlspecialchars($cuti['id']); ?>" data-nama="<?php echo htmlspecialchars($cuti['nama']); ?>" data-jenis="<?php echo htmlspecialchars($cuti['jenis']); ?>" data-tgl-mulai="<?php echo htmlspecialchars($cuti['tglawal']); ?>" data-tgl-selesai="<?php echo htmlspecialchars($cuti['tglakhir']); ?>" data-status="<?php echo htmlspecialchars($cuti['status']); ?>" data-keterangan="<?php echo htmlspecialchars($cuti['keterangan']); ?>" data-tanggal="<?php echo htmlspecialchars($cuti['tanggal']); ?>" data-jumlah-hari="<?php echo htmlspecialchars($cuti['jumlah_hari']); ?>">
                                <img src="images/avatar/team.png" class="card-img-top" alt="Image">
                                <div class="card-body">
                                    <h5 class="card-title"><strong><?php echo htmlspecialchars($cuti['nama']); ?><br><?php echo htmlspecialchars($cuti['nip']); ?></strong></h5>
                                    <h5 class="card-title"><?php echo htmlspecialchars($divisi); ?></h5>
                                    <h6 class="card-subtitle mb-2 text-muted"><?php echo htmlspecialchars($cuti['jenis']); ?></h6>
                                    <p class="card-text">
                                        <strong>Tanggal Cuti:</strong><br>
                                        <?php echo htmlspecialchars($cuti['tglawal']); ?> 
                                        <?php echo htmlspecialchars($cuti['tglakhir']); ?>
                                        <br>
                                        <strong>Jumlah Pengajuan:</strong> <?php echo htmlspecialchars($cuti['jumlah_hari']); ?><br>
                                        <strong>Status:</strong> <?php echo htmlspecialchars($cuti['status']); ?>
                                        <br>
                                        </p>
                                    <button class="btn btn-primary btn-block" data-toggle="modal" data-target="#detailModal" onclick="showDetails(this)">Lihat Detail</button>
                                </div>
                                <div class="card-footer text-center custom-card-footer">
                                    Date: <?php echo htmlspecialchars($cuti['tanggal']); ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>Data pengajuan tidak ditemukan.</p>
                <?php endif; ?>
            </div>
        </div>
        <!-- /Content -->


        <hr>

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
        <!-- shadow -->
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


       <!-- Detail Modal -->

<div class="modal fade" id="detailModal" tabindex="-1" role="dialog" aria-labelledby="detailModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h3>Detail Pengajuan</h3>
                    <p class="text-muted">Diajukan Pada: <span id="detail-tanggal"></span></p>
                    
                </div>
                <div class="modal-body">
                    <p><strong>Nama:</strong> <span id="detail-nama"></span></p>
                    <p><strong>Jenis Cuti:</strong> <span id="detail-jenis"></span></p>
                    <p><strong>Tanggal Mulai:</strong> <span id="detail-tgl-mulai"></span></p>
                    <p><strong>Tanggal Selesai:</strong> <span id="detail-tgl-selesai"></span></p>
                    
                    <p><strong>Keterangan:</strong> <span id="detail-keterangan"></span></p>
                    <p><strong>Jumlah Hari:</strong> <span id="detail-jumlah-hari"></span></p>
                    <p><strong>Status:</strong> <span id="detail-status"></span></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

<script>
        function showDetails(button) {
            var card = $(button).closest('.card');
            var modal = $('#detailModal');
            modal.find('.modal-title').text(card.data('nama'));
            modal.find('#detail-nama').text(card.data('nama'));
            modal.find('#detail-jenis').text(card.data('jenis'));
            modal.find('#detail-tgl-mulai').text(card.data('tgl-mulai'));
            modal.find('#detail-tgl-selesai').text(card.data('tgl-selesai'));
            modal.find('#detail-status').text(card.data('status'));
            modal.find('#detail-keterangan').text(card.data('keterangan'));
            modal.find('#detail-tanggal').text(card.data('tanggal'));
            modal.find('#detail-jumlah-hari').text(card.data('jumlah-hari'));
        }
    </script>


    <!-- jQuery -->
    <script src="js/jquery.min.js"></script>
    <!-- Bootstrap JavaScript -->
    <script src="js/bootstrap.js"></script>

</body>

</html>
