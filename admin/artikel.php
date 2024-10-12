<?php
require_once("database.php");
require_once("auth.php");
logged_admin();

// Fungsi untuk validasi gambar
function validate_image($temp, $path) {
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
    $detected_type = mime_content_type($temp);
    if (in_array($detected_type, $allowed_types)) {
        return move_uploaded_file($temp, $path);
    }
    return false;
}

// Proses pengajuan artikel
handle_article_submission($db);

// Fungsi untuk menangani pengajuan artikel
function handle_article_submission($db) {
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (isset($_POST['add'])) {
            // Proses tambah artikel
            $judul = filter_input(INPUT_POST, 'judul', FILTER_SANITIZE_STRING);
            $isi = filter_input(INPUT_POST, 'isi', FILTER_SANITIZE_STRING);

            $gambar = $_FILES['gambar']['name'];
            $gambar_temp = $_FILES['gambar']['tmp_name'];
            $folder = "images/artikel/";

            if (validate_image($gambar_temp, $folder . $gambar)) {
                $query = "INSERT INTO artikel (judul, isi, gambar) VALUES (:judul, :isi, :gambar)";
                $stmt = $db->prepare($query);
                $stmt->bindParam(':judul', $judul);
                $stmt->bindParam(':isi', $isi);
                $stmt->bindParam(':gambar', $gambar);

                if ($stmt->execute()) {
                    header("Location: " . $_SERVER['PHP_SELF']);
                    exit();
                } else {
                    echo "Gagal menambahkan artikel.";
                }
            } else {
                echo "File gambar tidak valid.";
            }
        } elseif (isset($_POST['update'])) {
            // Proses edit artikel
            $id = filter_input(INPUT_POST, 'id_artikel', FILTER_SANITIZE_NUMBER_INT);
            $judul = filter_input(INPUT_POST, 'judul', FILTER_SANITIZE_STRING);
            $isi = filter_input(INPUT_POST, 'isi', FILTER_SANITIZE_STRING);

            $query = "UPDATE artikel SET judul = :judul, isi = :isi WHERE id_artikel = :id";

            if (!empty($_FILES['gambar']['name'])) {
                $gambar = $_FILES['gambar']['name'];
                $gambar_temp = $_FILES['gambar']['tmp_name'];
                $folder = "images/artikel/";

                if (validate_image($gambar_temp, $folder . $gambar)) {
                    $query = "UPDATE artikel SET judul = :judul, isi = :isi, gambar = :gambar WHERE id_artikel = :id";
                } else {
                    echo "File gambar tidak valid.";
                    exit();
                }
            }

            $stmt = $db->prepare($query);
            $stmt->bindParam(':judul', $judul);
            $stmt->bindParam(':isi', $isi);
            $stmt->bindParam(':id', $id);
            if (isset($gambar)) {
                $stmt->bindParam(':gambar', $gambar);
            }

            if ($stmt->execute()) {
                header("Location: " . $_SERVER['PHP_SELF']);
                exit();
            } else {
                echo "Gagal mengupdate artikel.";
            }
        } elseif (isset($_POST['delete'])) {
            // Proses delete artikel
            $id = filter_input(INPUT_POST, 'id_artikel', FILTER_SANITIZE_NUMBER_INT);

            $stmt = $db->prepare("DELETE FROM artikel WHERE id_artikel = :id");
            $stmt->bindParam(':id', $id);

            if ($stmt->execute()) {
                header("Location: " . $_SERVER['PHP_SELF']);
                exit();
            } else {
                echo "Gagal menghapus artikel.";
            }
        }
    }
}
?>
<?php
if (isset($_POST['action']) && $_POST['action'] == 'get_article_details') {
    $id_artikel = $_POST['id_artikel'];

    // Lakukan pengambilan detail artikel dari database berdasarkan $id_artikel
    $stmt = $db->prepare("SELECT * FROM artikel WHERE id_artikel = :id");
    $stmt->bindParam(':id', $id_artikel);
    $stmt->execute();
    $detail_artikel = $stmt->fetch(PDO::FETCH_ASSOC);

    // Tampilkan detail artikel dalam format yang sesuai
    echo '<h5>' . $detail_artikel['judul'] . '</h5>';
    echo '<p>' . $detail_artikel['isi'] . '</p>';
    echo '<img src="images/artikel/' . $detail_artikel['gambar'] . '" alt="Gambar Artikel" style="max-width: 100%;">';
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
    <title>Artikel | Sistem Pengajuan Cuti Karyawan</title>
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
                    <i class="fa fa-table">&nbsp;Artikel</i> 
                    <!-- Tombol untuk  modal tambah artikel hanya muncul jika id login adalah 0 -->
                    <?php if ($divisi == 'HR') { ?>  
                        <button type="button" class="btn btn-success btn-sm btn-custom card-shadow-2" data-toggle="modal" data-target="#addArtikelModal">
                         Tambah Artikel
                        </button>  <?php } ?>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="dataTable">
                            <thead>
                                <tr>
                                    <th>Judul</th>
                                    <th>Isi</th>
                                    <th>Gambar</th>
                                    <th class="th-no-border sorting_asc_disabled sorting_desc_disabled" style="text-align:left">Aksi</th>
                                    
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $statement = $db->query("SELECT * FROM artikel ORDER BY artikel.id_artikel DESC");
                                foreach ($statement as $key) {
                                    $short_content = strlen($key['isi']) > 70 ? substr($key['isi'], 0, 70) . '...' : $key['isi'];
                                ?>
                                    <tr data-id="<?php echo $key['id_artikel']; ?>">
                                        <td><?php echo $key['judul']; ?></td>
                                        <td data-full-content="<?php echo htmlspecialchars($key['isi']); ?>"><?php echo $short_content; ?></td>
                                        <td><img src="images/artikel/<?php echo $key['gambar']; ?>" alt="Gambar Artikel" style="max-width: 100px;"></td>
                                        <td class="td-no-border">
                                        <div class="btn-group" role="group">
                                            <button type="button" class="btn btn-info btn-sm btn-custom card-shadow-2" 
                                                    data-toggle="modal" 
                                                    data-target="#detailArtikelModal" 
                                                    data-id="<?php echo $key['id_artikel']; ?>"
                                                    onclick="loadArticleDetails(<?php echo $key['id_artikel']; ?>)">
                                                Detail
                                            </button>
                                            <?php if ($divisi == "HR") { ?>
                                                <button type="button" class="btn btn-warning btn-sm btn-custom card-shadow-2" 
                                                        data-toggle="modal" 
                                                        data-target="#editArtikelModal" 
                                                        data-id="<?php echo $key['id_artikel']; ?>" 
                                                        data-judul="<?php echo $key['judul']; ?>" 
                                                        data-isi="<?php echo $key['isi']; ?>">
                                                    Edit
                                                </button>
                                                <button type="button" class="btn btn-danger btn-sm btn-custom card-shadow-2" 
                                                        data-toggle="modal" 
                                                        data-target="#deleteArtikelModal" 
                                                        data-id="<?php echo $key['id_artikel']; ?>">
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


<!-- Modal Detail Artikel -->
<div class="modal fade" id="detailArtikelModal" tabindex="-1" role="dialog" aria-labelledby="detailArtikelModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="detailArtikelModalLabel">Detail Artikel</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="detailArtikelContent">
                <!-- Konten Detail Artikel akan dimuat secara dinamis di sini -->
            </div>
        </div>
    </div>
</div>
<script>
// Fungsi untuk memuat detail artikel ke dalam modal
function loadArticleDetails(id_artikel) {
    // Mengambil detail artikel melalui AJAX atau menggunakan data yang sudah ada di tabel
    var artikelRow = $('#dataTable').find('tr[data-id="' + id_artikel + '"]');
    var judul = artikelRow.find('td:eq(0)').text();
    var isi = artikelRow.find('td:eq(1)').data('full-content'); // Ambil isi lengkap dari atribut data
    var gambarSrc = artikelRow.find('td:eq(2) img').attr('src');

    var modalContent = `
        <p><b>Judul:</b></p>
        <p>${judul}</p>
        <hr>
        <p><b>Isi:</b></p>
        <p>${isi}</p>
        <hr>
        <p><b>Gambar:</b></p>
        <img src="${gambarSrc}" alt="Gambar Artikel" style="max-width: 100%;">
    `;
    $('#detailArtikelContent').html(modalContent);
}
</script>



        <!-- Tambah Artikel Modal -->
<div class="modal fade" id="addArtikelModal" tabindex="-1" role="dialog" aria-labelledby="addArtikelModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addArtikelModalLabel">Tambah Artikel</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="addForm" method="POST" action="" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="add_judul">Judul</label>
                        <input type="text" class="form-control" id="add_judul" name="judul" required>
                    </div>
                    <div class="form-group">
                        <label for="add_isi">Isi</label>
                        <textarea class="form-control" id="add_isi" name="isi" rows="5" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="add_gambar">Gambar</label>
                        <input type="file" class="form-control-file" id="add_gambar" name="gambar" accept="image/*" required>
                    </div>
                    <button type="submit" class="btn btn-primary" name="add">Tambah</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Edit Artikel Modal -->
<div class="modal fade" id="editArtikelModal" tabindex="-1" role="dialog" aria-labelledby="editArtikelModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editArtikelModalLabel">Edit Artikel</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="editForm" method="POST" action="" enctype="multipart/form-data">
                    <input type="hidden" id="edit_id" name="id_artikel">
                    <div class="form-group">
                        <label for="edit_judul">Judul</label>
                        <input type="text" class="form-control" id="edit_judul" name="judul" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_isi">Isi</label>
                        <textarea class="form-control" id="edit_isi" name="isi" rows="5" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="edit_gambar">Gambar</label>
                        <input type="file" class="form-control-file" id="edit_gambar" name="gambar" accept="image/*">
                        <small id="gambarHelp" class="form-text text-muted">Kosongkan jika tidak ingin mengubah gambar.</small>
                    </div>
                    <button type="submit" class="btn btn-primary" name="update">Simpan</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Delete Artikel Modal -->
<div class="modal fade" id="deleteArtikelModal" tabindex="-1" role="dialog" aria-labelledby="deleteArtikelModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteArtikelModalLabel">Hapus Artikel</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menghapus artikel ini?</p>
                <form id="deleteForm" method="POST" action="">
                    <input type="hidden" id="delete_id" name="id_artikel">
                    <button type="submit" class="btn btn-danger" name="delete">Hapus</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                </form>
            </div>
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
    $('#editArtikelModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var id = button.data('id');
        var judul = button.data('judul');
        var isi = button.data('isi');
        
        var modal = $(this);
        modal.find('#edit_id').val(id);
        modal.find('#edit_judul').val(judul);
        modal.find('#edit_isi').val(isi);
    });

    $('#deleteArtikelModal').on('show.bs.modal', function (event) {
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
