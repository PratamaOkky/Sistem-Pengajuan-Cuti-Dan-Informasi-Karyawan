<?php
require_once("database.php");
require_once("auth.php"); // Session
logged_admin ();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="shortcut icon" href="images/favicon.png">
    <title>Export | Sistem Pengajuan Cuti Karyawan</title>
    <!-- Bootstrap core CSS-->
    <link href="vendor/bootstrap/css/bootstrap.css" rel="stylesheet">
    <!-- Custom fonts for this template-->
    <link href="vendor/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
    <!-- Page level plugin CSS-->
    <link href="vendor/datatables/dataTables.bootstrap4.css" rel="stylesheet">
    <!-- Custom styles for this template-->
    <link href="css/admin.css" rel="stylesheet">
    <!-- Page level plugin CSS-->
    <link rel="stylesheet" type="text/css" href="vendor/datatables/extra/jquery.dataTables.min.css">
    <link rel="stylesheet" type="text/css" href="vendor/datatables/extra/buttons.dataTables.min.css">

    <!-- Bootstrap core JavaScript-->
    <script src="vendor/jquery/jquery.min.js"></script>

    <!-- export plugin JavaScript-->
    <script src="vendor/datatables/jquery.dataTables.js"></script>
    <script src="vendor/datatables/extra/dataTables.buttons.min.js"></script>
    <script src="vendor/datatables/extra/buttons.print.min.js"></script>
    <script src="vendor/datatables/extra/jszip.min.js"></script>
    <script src="vendor/datatables/extra/pdfmake.min.js"></script>
    <script src="vendor/datatables/extra/vfs_fonts.js"></script>
    <script src="vendor/datatables/extra/buttons.html5.min.js"></script>
    <script type="text/javascript"  class="init">
    $(document).ready(function() {
        $('#example').DataTable( {
            dom: 'Bfrtip',
            buttons: [
                {
                    extend: 'print',
                    title: 'Data Pengajuan Cuti',
                    customize: function ( win ) {
                        $(win.document.body).find( 'table' )
                        .addClass( 'compact' )
                        .css( 'font-size', 'inherit' );
                        $(win.document.body)
                        .css( 'font-size', '10pt' )
                        .prepend(
                            '<img src="images/Enerrenn.png" style="opacity: 0.5; display:block;margin-left: auto; margin-top: auto; margin-right: auto; width: 100px;" />'
                        );
                    }
                },
                {
                    extend: 'pdf',
                    orientation: 'portrait',
                    pageSize: 'A4',
                    title: 'Data Pengajuan Cuti'
                },
                {
                    extend: 'excel',
                    title: 'Data Pengajuan Cut'
                }
            ]
        } );
    } );
    </script>

</head>

<body class="fixed-nav sticky-footer" id="page-top">
    <!-- Navigation-->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top" id="mainNav">
        <a class="navbar-brand" href="index">Sistem Pengajuan Cuti Karyawan</a>
        <button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarResponsive">
            <ul class="navbar-nav navbar-sidenav sidebar-menu" id="exampleAccordion">

            <li class="sidebar-profile nav-item" data-toggle="tooltip" data-placement="right" title="Admin">
                    <div class="profile-main">
                        <p class="image">
                            <?php if ($akses_admin == "admin") { ?>
                                <img alt="image" src="images/management.png" width="80">
                            <?php } else { ?>
                                <img alt="image" src="images/office-man.png" width="80">
                            <?php } ?>
                            <span class="status"><i class="fa fa-circle text-success"></i></span>
                        </p>
                        <p>
                            <span class=""><?php echo $nama_admin; ?></span><br>
                            <span class="user" style="font-family: monospace;"><?php echo $divisi; ?></span><br>
                            <span class="user" style="font-family: monospace;"><?php echo $akses_admin; ?></span>
                        </p>
                    </div>
                </li>
                <!-- Menu Kelola User, hanya terlihat untuk HR -->
                <?php if ($akses_admin == "admin"): ?>
                <li class="nav-item" data-toggle="tooltip" data-placement="right" title="Export">
                    <a class="nav-link" href="kelola_admin">
                        <i class="fa fa-fw fa-user"></i>
                        <span class="nav-link-text">Kelola User</span>
                    </a>
                </li>
                <li class="nav-item" data-toggle="tooltip" data-placement="right" title="Dashboard">
                    <a class="nav-link" href="artikel">
                        <i class="fa fa-fw fa-dashboard"></i>
                        <span class="nav-link-text">Kelola Artikel</span>
                    </a>
                </li>
                <?php endif; ?>

                <li class="nav-item" data-toggle="tooltip" data-placement="right" title="Tables">
                    <a class="nav-link" href="index">
                        <i class="fa fa-fw fa-table"></i>
                        <span class="nav-link-text">Persetujuan Cuti</span>
                    </a>
                </li>
                <li class="nav-item" data-toggle="tooltip" data-placement="right" title="Export">
                    <a class="nav-link" href="export">
                        <i class="fa fa-fw fa-print"></i>
                        <span class="nav-link-text">Cetak Data</span>
                    </a>
                </li>
                
            </ul>
            <ul class="navbar-nav sidenav-toggler">
                <li class="nav-item">
                    <a class="nav-link text-center" id="sidenavToggler">
                        <i class="fa fa-fw fa-angle-left"></i>
                    </a>
                </li>
            </ul>
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a class="nav-link" data-toggle="modal" data-target="#exampleModal">
                        <i class="fa fa-fw fa-sign-out"></i>Logout
                    </a>
                </li>
            </ul>
        </div>
    </nav>


    <!-- Body -->
    <div class="content-wrapper">
        <div class="container-fluid">

            <!-- Breadcrumbs-->
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="#">Ekspor</a>
                </li>
                <li class="breadcrumb-item active"><?php echo $divisi; ?></li>
            </ol>

            <!-- DataTables Card-->
            <div class="card mb-3">
                <div class="card-header">
                    <i class="fa fa-table"></i> Cetak Laporan Masuk
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered" id="example" width="100%">
                            <thead>
                                <tr>
                                    <th>Nama</th>
                                    <th>NIP</th>
                                    <th>Divisi</th>
                                    <th>Jenis</th>
                                    <th>Pengajuan</th>
                                    <th>Tanggal Awal</th>
                                    <th>Tanggal Akhir</th>
                                    <th class="sorting_asc_disabled sorting_desc_disabled">Keterangan</th>
                                    <th class="sorting_asc_disabled sorting_desc_disabled">Status</th>
                                </tr>
                            </thead>
                            <tbody>
    <?php
    // Ambil semua record dari tabel laporan
    if ($divisi == "HR") {
        // Jika admin dengan divisi 0, tampilkan semua data
        $query = "SELECT laporan.*, divisi.nama_divisi 
                  FROM laporan 
                  INNER JOIN divisi ON laporan.tujuan = divisi.id_divisi 
                  ORDER BY laporan.id DESC";
        $statement = $db->prepare($query);
    } else {
        // Jika bukan admin dengan divisi 0, sesuaikan dengan divisi admin yang login
        $query = "SELECT laporan.*, divisi.nama_divisi 
                  FROM laporan 
                  INNER JOIN divisi ON laporan.tujuan = divisi.id_divisi 
                  WHERE divisi.nama_divisi = :divisi 
                  ORDER BY laporan.id DESC";
        $statement = $db->prepare($query);
        $statement->bindParam(':divisi', $divisi);
    }
    
    
    try {
        $statement->execute();
        $result = $statement->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($result as $key ) {
            $mysqldate = $key['tanggal'];
            $phpdate = strtotime($mysqldate);
            $tanggal = date('d/m/Y', $phpdate);
            $status  = $key['status'];
            if($status == "Disetujui") {
                $style_status = "<p style=\"background-color:#009688;color:#fff;padding-left:2px;padding-right:2px;padding-bottom:2px;margin-top:16px;font-size:15px;font-style:italic;\">Disetujui</p>";
            } 
            else if($status == "Ditolak") {
                $style_status = "<p style=\"background-color:#c51f1a;color:#fff;padding-left:2px;padding-right:2px;padding-bottom:2px;margin-top:16px;font-size:15px;font-style:italic;\">Ditolak</p>";
            }
            else {
                $style_status = "<p style=\"background-color:#FF9800;color:#fff;padding-left:2px;padding-right:2px;padding-bottom:2px;margin-top:16px;font-size:15px;font-style:italic;\">Menunggu</p>";
            }
            ?>
            <tr>
                <td><?php echo $key['nama']; ?></td>
                <td><?php echo $key['nip']; ?></td>
                <td><?php echo $key['nama_divisi']; ?></td> <!-- Pastikan ini sesuai dengan nama kolom yang ada di tabel divisi -->
                <td><?php echo $key['jenis']; ?></td>
                <td>
                <?php
                // Hitung selisih hari jika tglawal dan tglakhir tidak kosong
                if (!empty($key['tglawal']) && !empty($key['tglakhir'])) {
                    $tgl_awal = new DateTime($key['tglawal']);
                    $tgl_akhir = new DateTime($key['tglakhir']);
                    
                    // Tambahkan 1 hari ke dalam tanggal akhir
                    $tgl_akhir->modify('+1 day');

                    $interval = new DateInterval('P1D');
                    $period = new DatePeriod($tgl_awal, $interval, $tgl_akhir);
                    
                    $hari_kerja = 0;
                    foreach ($period as $dt) {
                        $dayOfWeek = $dt->format('N');
                        if ($dayOfWeek < 6) { // 1 = Senin, ..., 5 = Jumat
                            $hari_kerja++;
                        }
                    }
                    
                    echo $hari_kerja . " hari";
                } else {
                    echo '1 hari';
                }
                ?>
            </td>
                <td><?php echo date('d F Y', strtotime($key['tglawal'])); ?></td>
                <td>
                    <?php
                    if (!empty($key['tglakhir'])) {
                        echo date('d F Y', strtotime($key['tglakhir']));
                    } else {
                        echo '-';
                    }
                    ?>
                </td>
                <td><?php echo $key['keterangan']; ?></td>
                <td><?php echo $style_status; ?></td>
            </tr>
            <?php
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
    ?>
</tbody>



                        </table>
                    </div>
                </div>
                <div class="card-footer small text-muted"> </div>
            </div>
        </div>
        <!-- /.container-fluid-->

        <footer class="sticky-footer">
            <div class="container">
                <div class="text-center">
                    <small>Copyright ©  PT. Enerren Technologies 2024</small>
                </div>
            </div>
        </footer>


        <!-- Scroll to Top Button-->
        <a class="scroll-to-top rounded" href="#page-top">
            <i class="fa fa-angle-up"></i>
        </a>


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
        
        <!-- Version Info Modal -->
        <!-- Modal -->
        <div class="modal fade" id="VersionModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Admin Versi</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <h5 style="text-align : center;">V-6.0</h5>
                        <p style="text-align : center;">Copyright ©  PT. Enerren Technologies 2024</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-close card-shadow-2 btn-sm" data-dismiss="modal">Tutup</button>
                    </div>
                </div>
            </div>
        </div>

        <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
        <!-- Core plugin JavaScript-->
        <script src="vendor/jquery-easing/jquery.easing.min.js"></script>
        <script src="vendor/datatables/dataTables.bootstrap4.js"></script>
        <!-- Custom scripts for all pages-->
        <script src="js/admin.js"></script>
        <!-- Custom scripts for this page-->
        <script src="js/admin-datatables.js"></script>

    </div>
    <!-- /.content-wrapper-->

</body>

</html>
