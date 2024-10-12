<?php
session_start();
require_once("../../admin/database.php");
require_once("../../admin/auth.php");
logged_admin();

$nama = $nip = $pengaduan = $is_valid = "";
$namaError = $nipError = $alamatError = $pengaduanError = "";

if (isset($_POST['submit'])) {
    $nomor = $_POST['nomor'];
    $nama = $_POST['nama'];
    $nip = $_POST['nip'];
    $tujuan = $_POST['tujuan'];
    $tglawal = $_POST['tglawal'];
    $tglakhir = $_POST['tglakhir'];
    $pengaduan = $_POST['pengaduan'];
    $keterangan = $_POST['keterangan']; 
    $is_valid = true;
    validate_input();

    if ($is_valid) {
        $sql = "INSERT INTO `laporan` (`id`, `nama`, `nip`, `tujuan`, `tglawal`, `tglakhir`, `jenis`, `keterangan`, `tanggal`, `status`) 
                VALUES (:nomor, :nama, :nip, :tujuan, :tglawal, :tglakhir, :jenis, :keterangan, CURRENT_TIMESTAMP, :status)";
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':nomor', $nomor);
        $stmt->bindValue(':nama', $nama);
        $stmt->bindValue(':nip', $nip);
        $stmt->bindValue(':tujuan', $tujuan);
        $stmt->bindValue(':tglawal', $tglawal);
        $stmt->bindValue(':tglakhir', $tglakhir);
        $stmt->bindValue(':jenis', htmlspecialchars($pengaduan));
        $stmt->bindValue(':keterangan', $keterangan);
        $stmt->bindValue(':status', "Menunggu");

        $stmt->execute();
        header("Location: ../indexa?status=success");
        exit;
    } else {
        header("Location: ../lapor.php?nomor=$nomor&nama=$nama&namaError=$namaError&nip=$nip&nipError=$nipError&pengaduan=$pengaduan&pengaduanError=$pengaduanError");
        exit;
    }
}

// Fungsi Untuk Melakukan Pengecekan Dari Setiap Inputan Di Masing - masing Fungsi
function validate_input() {
    global $nama, $nip, $pengaduan, $is_valid;
    cek_nama($nama);
    cek_nip($nip);
    cek_pengaduan($pengaduan);
}

// validasi nama
function cek_nama($nama) {
    global $nama, $is_valid, $namaError;
    if (!preg_match("/^[a-zA-Z ]*$/", $nama)) { // cek nama hanya huruf dan spasi
        $namaError = "Nama hanya boleh berisi huruf dan spasi";
        $is_valid = false;
    } else { // jika nama valid kosongkan error
        $namaError = "";
    }
}

// validasi nip
function cek_nip($nip) {
    global $nip, $nipError, $is_valid;
    if (!preg_match("/^[0-9]*$/", $nip)) { // cek nip hanya boleh angka
        $nipError = "NIP hanya boleh angka";
        $is_valid = false;
    } elseif (strlen($nip) != 6) { // cek panjang nip harus 6 digit
        $nipError = "Panjang NIP harus 6 digit";
        $is_valid = false;
    } else { // jika nip valid kosongkan error
        $nipError = "";
    }
}

// validasi pengaduan
function cek_pengaduan($pengaduan) {
    global $pengaduan, $is_valid, $pengaduanError;
    if (strlen($pengaduan) > 2048) { // cek panjang pengaduan tidak lebih dari 2048 karakter
        $pengaduanError = "Jenis pengajuan tidak boleh lebih dari 2048 karakter";
        $is_valid = false;
    } else { // jika pengaduan valid kosongkan error
        $pengaduanError = "";
    }
}
?>
