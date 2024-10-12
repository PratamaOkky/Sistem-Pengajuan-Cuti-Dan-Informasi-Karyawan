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

// Fungsi untuk menghitung jumlah hari kerja
function calculate_work_days($start_date, $end_date) {
    $start = new DateTime($start_date);
    
    if (empty($end_date)) {
        // Jika tglakhir kosong, hitung 1 hari
        return 1;
    }
    
    $end = new DateTime($end_date);
    $interval = $start->diff($end);
    $days = $interval->days + 1;
    
    // Hitung jumlah hari kerja
    $work_days = 0;
    $current = $start;
    
    while ($current <= $end) {
        // Cek jika hari adalah Senin sampai Jumat
        if ($current->format('N') < 6) {
            $work_days++;
        }
        $current->modify('+1 day');
    }
    
    return $work_days;
}
function format_date($date) {
    if (empty($date)) {
        return '';
    }
    $dateTime = new DateTime($date);
    return $dateTime->format('d F Y');
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
        .card {
            border: none;
            border-radius: 1rem;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
            transition: transform 0.3s ease-in-out, box-shadow 0.3s ease-in-out;
            margin-bottom: 1.5rem; /* Jarak bawah antar card */
        }
        .card:hover {
            transform: translateY(-15px);
            box-shadow: 0 8px 16px rgba(0,0,0,0.3);
        }
        
        .card-header {
            background-color: #007bff;
            color: white;
            font-weight: bold;
            border-bottom: 2px solid #0056b3;
            padding: 1rem;
            border-radius: 1rem 1rem 0 0;
        }
        .card-body {
            padding: 1.5rem;
        }
        .card-title {
            font-size: 1.5rem;
            color: #343a40;
            margin-bottom: 1rem;
        }
        .card-text {
            margin-bottom: 1rem;
        }
        .card-footer {
            background-color: #f8f9fa;
            border-top: 1px solid #dee2e6;
            padding: 1rem;
            border-radius: 0 0 1rem 1rem;
        }
        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
        }
        .btn-primary:hover {
            background-color: #0056b3;
            border-color: #004085;
        }
        .badge-status {
            font-size: 0.9rem;
            padding: 0.5rem 1rem;
            border-radius: 0.25rem;
        }
        .badge-approved {
            background-color: #28a745;
            color: white;
        }
        .badge-pending {
            background-color: #ffc107;
            color: white;
        }
        .badge-rejected {
            background-color: #dc3545;
            color: white;
        }
        .modal-dialog {
            max-width: 600px; /* Atur lebar modal sesuai kebutuhan */
            margin: 30px auto; /* Center modal secara vertikal dengan margin */
        }

        .modal-content {
            border-radius: 8px; /* Tambahkan border-radius untuk sudut membulat */
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); /* Tambahkan shadow untuk tampilan lebih modern */
        }

        .modal-body {
            max-height: 75vh; /* Batasi tinggi modal-body agar konten dapat digulir jika terlalu panjang */
            overflow-y: auto; /* Tambahkan scroll jika konten melebihi tinggi */
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
 <div class="container">
            <h3 class="my-4">Riwayat Pengajuan</h3>
            <div class="row">
            <?php
                foreach ($pengajuan as $item) {
                    // Status badge class
                    $statusClass = '';
                    switch ($item['status']) {
                        case 'Disetujui':
                            $statusClass = 'badge-approved';
                            break;
                        case 'Pending':
                            $statusClass = 'badge-pending';
                            break;
                        case 'Ditolak':
                            $statusClass = 'badge-rejected';
                            break;
                    }

                    // Hitung selisih hari kerja
                    $jumlah_hari = calculate_work_days($item['tglawal'], $item['tglakhir']);
                    ?>
                    <div class="col-lg-4 col-md-6 mb-4">
    <div class="card">
        <div class="card-header">
            <?php echo htmlspecialchars($item['nama']); ?>
        </div>
        <div class="card-body">
        <h5 class="card-title"><strong>Nama&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:</strong><?php echo htmlspecialchars($item['nama']); ?></h5>
            <h5 class="card-title"><strong>NIP&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:</strong><?php echo htmlspecialchars($item['nip']); ?></h5>
            <p class="card-text"><strong>Divisi&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:</strong> <?php echo $divisi; ?></p>
            <p class="card-text"><strong>Tanggal &nbsp;:</strong> <?php echo format_date($item['tglawal']); ?>&nbsp;- <?php echo format_date($item['tglakhir']); ?></p>
            <p class="card-text"><strong>Jumlah Pengajuan:</strong> <?php echo $jumlah_hari; ?> hari</p>
            <p class="card-text"><strong>Keterangan:</strong> <?php echo htmlspecialchars($item['keterangan']); ?></p>
            <span class="badge badge-status <?php echo $statusClass; ?>">
                <?php echo htmlspecialchars($item['status']); ?>
            </span>
            <small><p class="card-text text-muted">Diajukan pada : <?php echo format_date($item['tanggal']); ?></p></small>

        </div>
        <div class="card-footer text-center">
            <!-- Tambahkan atribut data-* untuk modal -->
            <button class="btn btn-primary btn-detail"
                data-nip="<?php echo htmlspecialchars($item['nip']); ?>"
                data-tujuan="<?php echo htmlspecialchars($item['tujuan']); ?>"
                data-jenis="<?php echo htmlspecialchars($item['jenis']); ?>"
                data-tglawal="<?php echo htmlspecialchars($item['tglawal']); ?>"
                data-tglakhir="<?php echo htmlspecialchars($item['tglakhir']); ?>"
                data-tanggal="<?php echo htmlspecialchars($item['tanggal']); ?>"
                data-keterangan="<?php echo htmlspecialchars($item['keterangan']); ?>"
                data-status="<?php echo htmlspecialchars($item['status']); ?>">
                Detail
            </button>
        </div>
    </div>
</div>

                    <?php
                }
                ?>

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
 
              <!-- Modal -->
<div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="detailModalLabel">Detail Pengajuan Cuti<br></h5><small>Diajukan pada: <span id="modalTanggal"></small><strong><h3><?php echo htmlspecialchars($nama_admin); ?></h3>
                
                
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-6">
                            <!-- Kolom kiri -->
                            <p><strong>NIP:</strong> <span id="modalNip"></span></p>
                            <p><strong>Jenis Cuti:</strong> <span id="modalJenis"></span></p>
                            <p><strong>Tanggal Mulai:</strong> <span id="modalTglawal"></span></p>
                            <p><strong>Tanggal Akhir:</strong> <span id="modalTglakhir"></span></p>
                        </div>
                        <div class="col-md-6">
                            <!-- Kolom kanan -->
                             
                            <p><strong>Divisi:</strong> <?php echo $divisi; ?></p>
                            <p><strong>Jumlah Pengajuan:</strong> <span id="modalJumlahHari"></span> hari</p>
                            <p><strong>Keterangan:</strong> <span id="modalKeterangan"></span></p>
                            <p><strong>Status:</strong> <span id="modalStatus"></span></p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <!-- <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button> -->
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">×</span>
                            </button>
            </div>
        </div>
    </div>
</div>


<script>
    $(document).ready(function(){
        $('.btn-detail').on('click', function(){
            var nip = $(this).data('nip');
            var tujuan = $(this).data('tujuan');
            var jenis = $(this).data('jenis');
            var tglawal = $(this).data('tglawal');
            var tglakhir = $(this).data('tglakhir');
            var tanggal = $(this).data('tanggal');
            var keterangan = $(this).data('keterangan');
            var status = $(this).data('status');

            // Hitung jumlah hari kerja
            function calculateWorkDays(startDate, endDate) {
                var start = new Date(startDate);
                if (!endDate) {
                    return 1;
                }
                var end = new Date(endDate);
                var workDays = 0;
                var current = new Date(start);
                
                while (current <= end) {
                    var day = current.getDay();
                    if (day != 0 && day != 6) {
                        workDays++;
                    }
                    current.setDate(current.getDate() + 1);
                }
                return workDays;
            }

            // Format tanggal
            function formatDate(dateString) {
                if (!dateString) {
                    return '';
                }
                var options = { day: '2-digit', month: 'long', year: 'numeric' };
                return new Date(dateString).toLocaleDateString('en-GB', options);
            }

            var workDays = calculateWorkDays(tglawal, tglakhir);

            // Format tanggal
            var formattedTglawal = formatDate(tglawal);
            var formattedTglakhir = formatDate(tglakhir);
            var formattedTanggal = formatDate(tanggal);

            // Isi konten modal
            $('#modalNip').text(nip);
            $('#modalTujuan').text(tujuan);
            $('#modalJenis').text(jenis);
            $('#modalTglawal').text(formattedTglawal);
            $('#modalTglakhir').text(formattedTglakhir);
            $('#modalJumlahHari').text(workDays);
            $('#modalTanggal').text(formattedTanggal);
            $('#modalKeterangan').text(keterangan);
            $('#modalStatus').text(status);

            // Tampilkan modal
            $('#detailModal').modal('show');
        });
    });
</script>

<script>
    function formatDate(dateString) {
    var options = { day: '2-digit', month: 'long', year: 'numeric' };
    return new Date(dateString).toLocaleDateString('en-GB', options);
}


</script>





    <!-- jQuery -->
    <script src="js/jquery.min.js"></script>
    <!-- Bootstrap JavaScript -->
    <script src="js/bootstrap.js"></script>
    

</body>

</html>
