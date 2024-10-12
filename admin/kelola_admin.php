<?php
require_once("database.php");
require_once("auth.php");
logged_admin();

// Proses Tambah, Edit, dan Hapus Admin
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'];

    if ($action == 'add') {
        // Tambah admin
        $username = $_POST['username'];
        $divisi = $_POST['divisi'];
        $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
        $nama = $_POST['nama'];
        $nip = $_POST['nip'];
        $akses = $_POST['akses'];

        $stmt = $db->prepare("INSERT INTO admin (username, divisi, password, nama, nip, akses) VALUES (:username, :divisi, :password, :nama, :nip, :akses)");
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':divisi', $divisi);
        $stmt->bindParam(':password', $password);
        $stmt->bindParam(':nama', $nama);
        $stmt->bindParam(':nip', $nip);
        $stmt->bindParam(':akses', $akses);
        $stmt->execute();
        header("Location: kelola_admin.php");
        exit;
    } elseif ($action == 'edit') {
        // Edit admin
        $id = $_POST['id_admin'];
        $username = $_POST['username'];
        $divisi = $_POST['divisi'];
        $password = $_POST['password'];
        $nama = $_POST['nama'];
        $nip = $_POST['nip'];
        $akses = $_POST['akses'];

        if (empty($password)) {
            $stmt = $db->prepare("UPDATE admin SET username = :username, divisi = :divisi, nama = :nama, nip = :nip, akses = :akses WHERE id_admin = :id");
        } else {
            $password = password_hash($password, PASSWORD_BCRYPT);
            $stmt = $db->prepare("UPDATE admin SET username = :username, divisi = :divisi, password = :password, nama = :nama, nip = :nip, akses = :akses WHERE id_admin = :id");
            $stmt->bindParam(':password', $password);
        }

        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':divisi', $divisi);
        $stmt->bindParam(':nama', $nama);
        $stmt->bindParam(':nip', $nip);
        $stmt->bindParam(':akses', $akses);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        header("Location: kelola_admin.php");
        exit;
    } elseif ($action == 'delete') {
        // Hapus admin
        $id = $_POST['id_admin'];

        $stmt = $db->prepare("DELETE FROM admin WHERE id_admin = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        header("Location: kelola_admin.php");
        exit;
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
    <title>Kelola User | Sistem Pengajuan Cuti Karyawan</title>
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


    <div class="content-wrapper">
        <div class="container-fluid">

            <!-- Breadcrumbs-->
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="#">Blog</a>
                </li>
                <li class="breadcrumb-item active"><?php echo $divisi; ?></li>
            </ol>
            <!-- Example DataTables Card-->
            <div class="card mb-3">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <i class="fa fa-table">&nbsp;Admin</i>
                    <!-- Tombol untuk  modal tambah admin hanya muncul jika id login adalah 0 -->
                    <?php if ($divisi == "HR") { ?>  
                        <button type="button" class="btn btn-success btn-sm btn-custom card-shadow-2" data-toggle="modal" data-target="#addadminModal">
                         Tambah User
                        </button>  <?php } ?>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="dataTable">
                            <thead>
                                <tr>
                                    <th>Nama</th>
                                    <th>nip</th>
                                    <th>Username</th>
                                    <th>Divisi</th>
                                    <th>Akses</th>
                                    <th class="th-no-border sorting_asc_disabled sorting_desc_disabled" style="text-align:left">Aksi</th>
                                    
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                // Ubah query untuk melakukan join antara admin dan divisi
                                $statement = $db->query("SELECT admin.*, divisi.nama_divisi 
                                                        FROM admin 
                                                        LEFT JOIN divisi ON admin.divisi = divisi.id_divisi 
                                                        ORDER BY admin.id_admin DESC");

                                foreach ($statement as $key) {?>
                                    <tr data-id="<?php echo $key['id_admin']; ?>">
                                        <td><?php echo $key['nama']; ?></td>
                                        <td><?php echo $key['nip']; ?></td>
                                        <td><?php echo $key['username']; ?></td>
                                        <td><?php echo $key['nama_divisi']; ?></td>
                                        <td><?php echo $key['akses']; ?></td>
                                        <td class="td-no-border">
                                            <div class="btn-group" role="group">
                                                
                                                <?php if ($divisi == "HR") { ?>
                                                    <button type="button" class="btn btn-warning btn-sm btn-custom card-shadow-2" 
                                                            data-toggle="modal" 
                                                            data-target="#editadminModal" 
                                                            data-id="<?php echo $key['id_admin']; ?>" 
                                                            data-username="<?php echo $key['username']; ?>" 
                                                            data-nama="<?php echo $key['nama']; ?>"
                                                            data-nip="<?php echo $key['nip']; ?>"
                                                            data-akses="<?php echo $key['akses']; ?>"
                                                            data-divisi="<?php echo $key['divisi']; ?>">
                                                            
                                                        Edit
                                                    </button>
                                                    <button type="button" class="btn btn-danger btn-sm btn-custom card-shadow-2" 
                                                            data-toggle="modal" 
                                                            data-target="#deleteadminModal" 
                                                            data-id="<?php echo $key['id_admin']; ?>">
                                                        Hapus
                                                    </button>
                                                <?php } ?>
                                            </div>
                                        </td>
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

        <!-- /.content-wrapper-->
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


        <!-- Modal Tambah Admin -->
<div class="modal fade" id="addadminModal" tabindex="-1" role="dialog" aria-labelledby="addadminModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addadminModalLabel">Tambah Admin</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="addAdminForm" method="post">
                <div class="modal-body">
                <div class="form-group">
                        <label for="nama">Nama</label>
                        <input type="text" class="form-control" id="nama" name="nama" required>
                    </div>
                    <div class="form-group">
                        <label for="nip">Nip</label>
                        <input type="text" class="form-control" id="nip" name="nip" required>
                    </div>
                    <div class="form-group">
                        <label for="divisi">Divisi</label>
                        <select class="form-control" id="divisi" name="divisi" required>
                            <?php
                            // Ambil data divisi dari database
                            $query_divisi = "SELECT * FROM divisi";
                            $statement_divisi = $db->query($query_divisi);
                            foreach ($statement_divisi as $row_divisi) {
                                echo '<option value="' . $row_divisi['id_divisi'] . '">' . $row_divisi['nama_divisi'] . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="akses">Akses</label>
                        <select class="form-control" id="akses" name="akses" required>
                            <option value="karyawan">Karyawan</option>
                            <option value="spv">Supervisor</option>
                            <option value="hrd">HRD</option>
                            <!-- Add more options as needed -->
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="username">Username</label>
                        <input type="text" class="form-control" id="username" name="username" required>
                    </div>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="action" value="add">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Tambah</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit Admin -->
<div class="modal fade" id="editadminModal" tabindex="-1" role="dialog" aria-labelledby="editadminModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editadminModalLabel">Edit Admin</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="editAdminForm" method="post">
                <div class="modal-body">
                    <input type="hidden" id="edit_id" name="id_admin">
                    <div class="form-group">
                        <label for="edit_nama">Nama</label>
                        <input type="text" class="form-control" id="edit_nama" name="nama" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_nip">nip</label>
                        <input type="text" class="form-control" id="edit_nip" name="nip" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_divisi">Divisi</label>
                        <!-- <input type="text" class="form-control" id="edit_divisi" name="divisi" required> -->
                        <select class="form-control" id="edit_divisi" name="divisi" required>
                            <?php
                            // Ambil data divisi dari database
                            $query_divisi = "SELECT * FROM divisi";
                            $statement_divisi = $db->query($query_divisi);
                            foreach ($statement_divisi as $row_divisi) {
                                echo '<option value="' . $row_divisi['id_divisi'] . '">' . $row_divisi['nama_divisi'] . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="edit_username">Username</label>
                        <input type="text" class="form-control" id="edit_username" name="username" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_password">Password (Biarkan kosong jika tidak ingin mengubah)</label>
                        <input type="password" class="form-control" id="edit_password" name="password">
                    </div>
                    
                    <div class="form-group">
                        <label for="edit_akses">Akses</label>
                        <select class="form-control" id="edit_akses" name="akses" required>
                            <option value="admin">Admin</option>
                            <option value="spv">Supervisor</option>
                            <option value="karyawan">Karyawan</option>
                            <!-- Tambahkan opsi lainnya jika diperlukan -->
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="action" value="edit">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Hapus Admin -->
<div class="modal fade" id="deleteadminModal" tabindex="-1" role="dialog" aria-labelledby="deleteadminModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteadminModalLabel">Hapus Admin</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="deleteAdminForm" method="post">
                <div class="modal-body">
                    <input type="hidden" id="delete_id" name="id_admin">
                    <p>Apakah Anda yakin ingin menghapus admin ini?</p>
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="action" value="delete">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">Hapus</button>
                </div>
            </form>
        </div>
    </div>
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
                        <p style="text-align : center;">Copyright © PT. Enerren Technologies 2024</p>
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
        <script>
            $(document).ready(function(){
                $('#editadminModal').on('show.bs.modal', function (event) {
                    var button = $(event.relatedTarget);
                    var id = button.data('id');
                    var nama = button.data('nama');
                    var nip = button.data('nip');
                    var username = button.data('username');
                    var divisi = button.data('divisi');
                    var akses = button.data('akses');
                    
                    var modal = $(this);
                    modal.find('#edit_id').val(id);
                    modal.find('#edit_username').val(username);
                    modal.find('#edit_divisi').val(divisi);
                    modal.find('#edit_nama').val(nama);
                    modal.find('#edit_nip').val(nip);
                    modal.find('#edit_akses').val(akses);
                });

                $('#deleteadminModal').on('show.bs.modal', function (event) {
                    var button = $(event.relatedTarget);
                    var id = button.data('id');
                    
                    var modal = $(this);
                    modal.find('#delete_id').val(id);
                });
            });
        </script>



    </div>

</body>

</html>
