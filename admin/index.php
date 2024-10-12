<?php
    // database
    require_once("database.php");
    require_once("auth.php"); // Session
    logged_admin ();
    // global var
    global $nomor, $foundreply;
    // hapus Setujuan laporan berdasarkan id Setujuan laporan
    if (isset($_POST['HapusTanggapan'])) {
        $id_hapus_tanggapan = $_POST['id_tanggapan'];
        $id_hapus_tanggapan_laporan = $_POST['id_hapus_tanggapan_laporan'];
        // hapus tanggapan dari tabel tanggapan
        $statement = $db->query("DELETE FROM `tanggapan` WHERE `tanggapan`.`id_tanggapan` = $id_hapus_tanggapan");
        $statt = $db->query("SELECT * FROM `tanggapan` WHERE id_laporan = $id_hapus_tanggapan_laporan");
        $cek = $statt->fetch(PDO::FETCH_ASSOC);
        // jika user terdaftar
        if(!$cek){
            $update = $db->query("UPDATE `laporan` SET `status` = 'Menunggu' WHERE `laporan`.`id` = $id_hapus_tanggapan_laporan");
        }
    }
    // Menanggapi Laporan
    if(isset($_POST['simpanEditStatus'])) {
        $id = $_POST['editStatusId'];
        $status = $_POST['editStatus'];
    
        // Lakukan pembaruan status di dalam database
        $statement = $db->prepare("UPDATE laporan SET status = :status WHERE id = :id");
        $statement->execute(array(':status' => $status, ':id' => $id));
    
        // Redirect atau lakukan tindakan lain yang sesuai
    }


    global $total_laporan_masuk, $total_laporan_menunggu, $total_laporan_ditanggapi;

    // Pastikan untuk memeriksa apakah $id_admin adalah angka yang valid sebelum menjalankan kueri
    
    if ($divisi !== 'HR') {
        // Mengambil divisi admin berdasarkan ID admin
        $query_divisi = $db->prepare("SELECT divisi FROM admin WHERE id_admin = ?");
        $query_divisi->execute([$id_admin]);
        $result_divisi = $query_divisi->fetch(PDO::FETCH_ASSOC);
    
        if ($result_divisi) {
            $divisi_admin = $result_divisi['divisi'];
    
            // Menghitung jumlah laporan berdasarkan tujuan yang sesuai dengan divisi admin yang sedang login
            foreach($db->query("SELECT COUNT(*) FROM laporan WHERE tujuan = '$divisi_admin'") as $row) {
                $total_laporan_masuk = $row['COUNT(*)'];
            }
    
            foreach($db->query("SELECT COUNT(*) FROM laporan WHERE status = 'Ditanggapi' AND tujuan = '$divisi_admin'") as $row) {
                $total_laporan_ditanggapi = $row['COUNT(*)'];
            }
    
            foreach($db->query("SELECT COUNT(*) FROM laporan WHERE status = 'Menunggu' AND tujuan = '$divisi_admin'") as $row) {
                $total_laporan_menunggu = $row['COUNT(*)'];
            }
        } else {
            // Handle jika ID admin tidak ditemukan atau tidak memiliki divisi yang valid
            // Misalnya, menetapkan nilai default untuk laporan jika tidak ada divisi yang cocok
            $total_laporan_masuk = 0;
            $total_laporan_ditanggapi = 0;
            $total_laporan_menunggu = 0;
        }
    } else {
        // Default case if $id_admin is not valid or not greater than 0
        foreach($db->query("SELECT COUNT(*) FROM laporan") as $row) {
            $total_laporan_masuk = $row['COUNT(*)'];
        }
    
        foreach($db->query("SELECT COUNT(*) FROM laporan WHERE status = 'Ditanggapi'") as $row) {
            $total_laporan_ditanggapi = $row['COUNT(*)'];
        }
    
        foreach($db->query("SELECT COUNT(*) FROM laporan WHERE status = 'Menunggu'") as $row) {
            $total_laporan_menunggu = $row['COUNT(*)'];
        }
    }

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
    <title>Index | Sistem Pengajuan Cuti Karyawan</title>
    <!-- Bootstrap core CSS-->
    <link href="vendor/bootstrap/css/bootstrap.css" rel="stylesheet">
    <!-- Custom fonts for this template-->
    <link href="vendor/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
    <!-- Page level plugin CSS-->
    <link href="vendor/datatables/dataTables.bootstrap4.css" rel="stylesheet">
    <!-- Custom styles for this template-->
    <link href="css/admin.css" rel="stylesheet">
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
                            <?php if ($akses_admin == 'admin') { ?>
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
                    <a href="#">Kelola Pengajuan</a>
                </li>
                <li class="breadcrumb-item active"><?php echo $divisi; ?></li>
            </ol>
             <!-- Icon Cards-->
             <div class="row">

<div class="col-xl-3 col-sm-6 mb-3">
    <div class="card text-white bg-primary o-hidden h-100">
        <div class="card-body">
            <div class="card-body-icon">
                <i class="fa fa-fw fa-comments-o"></i>
            </div>
            <div class="mr-5"><?php echo $total_laporan_masuk; ?> Pengajuan</div>
        </div>
        <a class="card-footer text-white clearfix small z-1" href="tables">
            <span class="float-left">Total Pengajuan</span>
            <span class="float-right">
                <i class="fa fa-angle-right"></i>
            </span>
        </a>
    </div>
</div>

<div class="col-xl-3 col-sm-6 mb-3">
    <div class="card text-white bg-danger o-hidden h-100">
        <div class="card-body">
            <div class="card-body-icon">
                <i class="fa fa-fw fa-minus-circle"></i>
            </div>
            <div class="mr-5"><?php echo $total_laporan_menunggu; ?> Belum Ditanggapi</div>
        </div>
        <a class="card-footer text-white clearfix small z-1" href="#">
            <span class="float-left">Belum Ditanggapi</span>
            <span class="float-right">
                <i class="fa fa-angle-right"></i>
            </span>
        </a>
    </div>
</div>
</div>
<!-- ./Icon Cards-->
            <!-- DataTables Card-->
            <div class="card mb-3">
                <div class="card-header">
                    <i class="fa fa-table"></i> Pengajuan
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="dataTable">
                            <thead>
                                <tr>
                                    <th class="sorting_asc_disabled sorting_desc_disabled">Nama</th>
                                    <th class="sorting_asc_disabled sorting_desc_disabled">NIP</th>
                                    <th>Divisi</th>
                                    <th>Jenis</th>
                                    <th>Jumlah Pegajuan</th>
                                    <th>Tanggal Pengajuan</th>
                                    <th >Status</th>
                                    <th class="th-no-border sorting_asc_disabled sorting_desc_disabled"></th>
                                    <th class="th-no-border sorting_asc_disabled sorting_desc_disabled" style="text-align:left">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                // Ambil semua record dari tabel laporan berdasarkan divisi admin yang login atau semua jika divisi admin adalah "HR"
                                if ($divisi == "HR") {
                                    $query = "SELECT laporan.*, divisi.nama_divisi 
                                            FROM laporan 
                                            INNER JOIN divisi ON laporan.tujuan = divisi.id_divisi 
                                            ORDER BY laporan.id DESC";

                                    $statement = $db->query($query);
                                } else {
                                    $query = "SELECT laporan.*, divisi.nama_divisi 
                                            FROM laporan 
                                            INNER JOIN divisi ON laporan.tujuan = divisi.id_divisi 
                                            WHERE divisi.id_divisi = (
                                                SELECT divisi 
                                                FROM admin 
                                                WHERE id_admin = :id_admin
                                            )
                                            ORDER BY laporan.id DESC";

                                    $statement = $db->prepare($query);
                                    $statement->bindParam(':id_admin', $id_admin);
                                    $statement->execute();
                                }

                                foreach ($statement as $key) {
                                    $mysqldate = $key['tanggal'];
                                    $phpdate = strtotime($mysqldate);
                                    $tanggal = date('d/m/Y', $phpdate);
                                    $status = $key['status'];

                                    // Tentukan gaya status berdasarkan nilai status
                                    if ($status == "Disetujui") {
                                        $style_status = "<p style=\"background-color:#009688;color:#fff;padding-left:2px;padding-right:2px;padding-bottom:2px;margin-top:16px;font-size:15px;font-style:italic;\">Disetujui</p>";
                                    } else if ($status == "Ditolak") {
                                        $style_status = "<p style=\"background-color:#c51f1a;color:#fff;padding-left:2px;padding-right:2px;padding-bottom:2px;margin-top:16px;font-size:15px;font-style:italic;\">Ditolak</p>";
                                    } else {
                                        $style_status = "<p style=\"background-color:#FF9800;color:#fff;padding-left:2px;padding-right:2px;padding-bottom:2px;margin-top:16px;font-size:15px;font-style:italic;\">Menunggu</p>";
                                    }
                                ?>
                                    <tr>
                                        <td><?php echo $key['nama']; ?></td>
                                        <td><?php echo $key['nip']; ?></td>
                                        <td><?php echo $key['nama_divisi']; ?></td>
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
                                        <td><?php echo $tanggal; ?></td>
                                        <td><?php echo $style_status; ?></td>
                                        <td class="td-no-border">
                                            <button type="button" class="btn btn-info btn-sm btn-custom card-shadow-2" data-toggle="modal" data-target="#ModalDetail<?php echo $key['id']; ?>">
                                                Detail
                                            </button>
                                        </td>
                                        <td class="td-no-border">
                                            <button type="button" style="background-color:#009688;" class="btn btn-success btn-sm btn-custom card-shadow-2" data-toggle="modal" data-target="#editStatusModal" data-id="<?php echo $key['id']; ?>" data-status="<?php echo $key['status']; ?>" data-nama="<?php echo $key['nama']; ?>" data-nip="<?php echo $key['nip']; ?>" data-divisi="<?php echo $key['nama_divisi']; ?>" data-tglawal="<?php echo $key['tglawal']; ?>" data-tglakhir="<?php echo $key['tglakhir']; ?>">
                                                Tanggapi
                                            </button>
                                        </td>
                                        <!-- <td class="td-no-border">
                                            <button type="button" class="btn btn-danger btn-sm btn-custom card-shadow-2" data-toggle="modal" data-target="#ModalHapus<?php echo $key['id']; ?>">
                                                Hapus
                                            </button>
                                        </td> -->
                                    </tr>
                                <?php
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

        <!-- Isi masing2 modal, detail, Setuju dan hapus -->
        <?php
            $statement = $db->query("SELECT * FROM laporan, divisi WHERE laporan.tujuan = divisi.id_divisi ORDER BY laporan.id DESC");
            foreach ($statement as $key ) {
                // cek apakah laporan sudah Disetujui atau belum
                $nomor = $key['id'];
                $stat = $db->query("SELECT * FROM `laporan` WHERE id = $nomor");
                if ($stat->rowCount() > 0) {
                    // jika laporan sudah Disetujui, maka tampilkan tanggapan di modal detail laporan
                    $foundreply = true;
                }
        ?>

        <!-- Modal Detail -->
<div class="modal fade" id="ModalDetail<?php echo $key['id']; ?>" tabindex="-1" role="dialog" aria-labelledby="modalTitle<?php echo $key['id']; ?>" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle<?php echo $key['id']; ?>">Detail Laporan</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="container">
                    <!-- Informasi Pribadi -->
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="nama">Nama:</label>
                            <input type="text" class="form-control" id="nama" value="<?php echo $key['nama']; ?>" readonly>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="divisi">Divisi:</label>
                            <input type="text" class="form-control" id="divisi" value="<?php echo $key['nama_divisi']; ?>" readonly>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="nip">NIP:</label>
                            <input type="text" class="form-control" id="nip" value="<?php echo $key['nip']; ?>" readonly>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="tanggal">Diajukan Pada:</label>
                            <input type="text" class="form-control" id="tanggal" value="<?php echo date('d F Y H:i', strtotime($key['tanggal'])); ?>" readonly>
                        </div>
                    </div>
                    <hr>
                    <!-- Informasi Pengajuan -->
                    <div class="form-group">
                        <label for="jenis">Jenis Pengajuan:</label>
                        <input type="text" class="form-control" id="jenis" value="<?php echo $key['jenis']; ?>" readonly>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="tglawal">Pengajuan Cuti Pada Tanggal:</label>
                            <input type="text" class="form-control" id="tglawal" value="<?php echo date('d F Y', strtotime($key['tglawal'])); ?>" readonly>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="tglakhir">Sampai Tanggal:</label>
                            <input type="text" class="form-control" id="tglakhir" value="<?php echo !empty($key['tglakhir']) ? date('d F Y', strtotime($key['tglakhir'])) : ''; ?>" readonly>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="jumlahPengajuan">Jumlah Pengajuan:</label>
                        <input type="text" class="form-control" id="jumlahPengajuan" value="<?php
                                if (!empty($key['tglawal']) && !empty($key['tglakhir'])) {
                                    $tgl_awal = new DateTime($key['tglawal']);
                                    $tgl_akhir = new DateTime($key['tglakhir']);
                                    $tgl_akhir->modify('+1 day'); // Tambahkan 1 hari untuk menghitung dengan benar

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
                            ?>" readonly>
                    </div>
                    <div class="form-group">
                        <label for="keterangan">Keterangan:</label>
                        <textarea class="form-control" id="keterangan" rows="3" readonly><?php echo $key['keterangan']; ?></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
            <button type="button" class="btn btn-close btn-sm card-shadow-2" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
<!-- ./Modal Detail -->


       <!-- Modal Tanggapi -->
<div class="modal fade" id="editStatusModal" tabindex="-1" role="dialog" aria-labelledby="editStatusModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editStatusModalLabel">Tanggapi Laporan</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form method="post" id="editStatusForm">
                    <input type="hidden" id="editStatusId" name="editStatusId"> <!-- Menyimpan ID data yang akan diubah statusnya -->
                    
                   
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="nama">Nama:</label>
                            <input type="text" class="form-control" id="nama" name="nama" readonly>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="nip">NIP:</label>
                            <input type="text" class="form-control" id="nip" name="nip" readonly>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="divisi">Divisi:</label>
                        <input type="text" class="form-control" id="divisi" name="divisi" readonly>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="tglawal">Tanggal Awal:</label>
                            <input type="text" class="form-control" id="tglawal" name="tglawal" readonly>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="tglakhir">Tanggal Akhir:</label>
                            <input type="text" class="form-control" id="tglakhir" name="tglakhir" readonly>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="editStatus">Status:</label>
                        <select class="form-control" id="editStatus" name="editStatus" required>
                            <option value="Menunggu">Menunggu</option>
                            <option value="Disetujui">Disetujui</option>
                            <option value="Ditolak">Ditolak</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary" form="editStatusForm" name="simpanEditStatus">Simpan</button>
            </div>
        </div>
    </div>
</div>

        <!-- /Modal Tanggapi -->
        <!--Modal Hapus-->
        <div class="modal fade" id="ModalHapus<?php echo $key['id']; ?>" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-sm " role="document">
                <div class="modal-content">
                    <div class="modal-header ">
                        <h5 class="modal-title text-center">Hapus Laporan</h5>
                    </div>
                    <div class="modal-body">
                        <p class="text-center">Hapus Pengaduan</p>
                        <p class="text-center">Dari <b><?php echo $key['nama']; ?></b> ?</p>
                    </div>
                    <div class="modal-footer">
                        <form method="post">
                            <input type="hidden" name="id_laporan" value="<?php echo $key['id']; ?>">
                            <input type="submit" class="btn btn-danger btn-sm card-shadow-2" name="Hapus" value="Hapus">
                            <button type="button" class="btn btn-close btn-sm card-shadow-2" data-dismiss="modal">Batal</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- ./Modal Hapus-->
        <?php
            }
        ?>

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

        <!-- Bootstrap core JavaScript-->
        <script src="vendor/jquery/jquery.min.js"></script>
        <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
        <!-- Core plugin JavaScript-->
        <script src="vendor/jquery-easing/jquery.easing.min.js"></script>
        <!-- Page level plugin JavaScript-->
        <script src="vendor/datatables/jquery.dataTables.js"></script>
        <script src="vendor/datatables/dataTables.bootstrap4.js"></script>
        <!-- Custom scripts for all pages-->
        <script src="js/admin.js"></script>
        <!-- Custom scripts for this page-->
        <script src="js/admin-datatables.js"></script>

        <!-- Script untuk modal edit status -->
<script>
    function formatDate(dateString) {
    var options = { day: 'numeric', month: 'long', year: 'numeric' };
    var date = new Date(dateString);
    return date.toLocaleDateString('en-GB', options); // Format: 20 July 2024
}

$('#editStatusModal').on('show.bs.modal', function(event) {
    var button = $(event.relatedTarget); // Tombol yang memicu modal
    var id = button.data('id'); // Ekstrak data-id dari tombol
    var status = button.data('status'); // Ekstrak data-status dari tombol
    var nama = button.data('nama'); // Ekstrak data-nama dari tombol
    var nip = button.data('nip'); // Ekstrak data-nip dari tombol
    var divisi = button.data('divisi'); // Ekstrak data-divisi dari tombol
    var tglawal = button.data('tglawal'); // Ekstrak data-tglawal dari tombol
    var tglakhir = button.data('tglakhir'); // Ekstrak data-tglakhir dari tombol

    // Format tanggal menggunakan JavaScript standar
    var formattedTglawal = formatDate(tglawal); // Format tanggal sesuai dengan format yang diinginkan
    var formattedTglakhir = formatDate(tglakhir); // Format tanggal sesuai dengan format yang diinginkan

    var modal = $(this);
    modal.find('#editStatusId').val(id); // Set value dari input hidden untuk ID
    modal.find('#editStatus').val(status); // Set value dari dropdown untuk status
    modal.find('#nama').val(nama); // Set value dari input untuk nama
    modal.find('#nip').val(nip); // Set value dari input untuk nip
    modal.find('#divisi').val(divisi); // Set value dari input untuk divisi
    modal.find('#tglawal').val(formattedTglawal); // Set value dari input untuk tglawal dengan format yang sudah diubah
    modal.find('#tglakhir').val(formattedTglakhir); // Set value dari input untuk tglakhir dengan format yang sudah diubah
});

</script>


    </div>
    <!-- /.content-wrapper-->
</body>

</html>
