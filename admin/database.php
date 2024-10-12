<?php
$db_host = "localhost";
$db_user = "root";
$db_pass = "";
$db_name = "kp";

try {
    $db = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Terjadi masalah: " . $e->getMessage());
}

function logged_admin() {
    global $db, $admin_login, $divisi, $id_admin, $nama_admin, $nip_admin, $akses_admin, $divisi_admin, $username_admin;
    
    try {
        $sql = "SELECT admin.id_admin, admin.username, admin.nama, admin.nip, admin.akses, admin.divisi, divisi.nama_divisi 
                FROM admin 
                INNER JOIN divisi ON admin.divisi = divisi.id_divisi 
                WHERE admin.username = :username";
        
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':username', $admin_login);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $divisi = $row['nama_divisi'];
            $id_admin = $row['id_admin'];
            $username_admin = $row['username'];
            $nama_admin = $row['nama'];
            $nip_admin = $row['nip'];
            $akses_admin = $row['akses'];
            $divisi_admin = $row['divisi'];
        } else {
            // Tangani kasus jika admin tidak ditemukan
            echo "Admin tidak ditemukan.";
        }
        
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}

?>
