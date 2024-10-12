<?php
require_once("admin/database.php");
$message = "";
if (isset($_POST['login']) && $_POST['login'] == "Login") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM admin WHERE username = :username";
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':username', $username);
    $stmt->execute();
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($admin) {
        // Verifikasi password menggunakan password_verify()
        if (password_verify($password, $admin['password'])) {
            // Password benar, buat session admin
            session_start();
            $_SESSION['admin'] = $admin['username'];

            // Periksa level akses dan arahkan sesuai
            if ($admin['akses'] === 'karyawan') {
                header("Location: karyawan/index.php");
            } else {
                header("Location: admin/index.php");
            }
            exit;
        } else {
            // Password salah
            $message = "Username atau Password Salah";
        }
    } else {
        // Username tidak ditemukan
        $message = "Username atau Password Salah";
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
    <title>Login - Kelola Pengajuan Cuti</title>
    <!-- Bootstrap core CSS-->
    <link href="vendor/bootstrap/css/bootstrap.css" rel="stylesheet">
    <!-- Custom fonts for this template-->
    <link href="vendor/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
    <!-- Custom styles for this template-->
    <link href="css/admin.css" rel="stylesheet">
    <style>
        .bodylogin {
            background-image: url('images/bglogin.jpg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            padding: 0;
        }

        .card-login {
            background: rgba(255, 255, 255, 0.8);
            border-radius: 10px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            width: 350px;
            padding: 20px;
        }

        .btn-orange {
            background-color: #FF8800 !important;
            border-color: #FF8800 !important;
            color: white !important;
        }

        .btn-orange:hover {
            background-color: #FF7700 !important;
            border-color: #FF7700 !important;
        }

    </style>
</head>

<body class="bodylogin">
    <div class="container">
        <div class="card container card-login mx-auto mt-5">
            <img src="images/logo1.png" alt="Logo" class="logo" style="margin-top:100px">
            <div class="card-body">
                <form method="post">
                    <div class="form-group">
                        <label for="exampleInputEmail1">Username</label>
                        <input class="form-control" id="username" type="text" name="username" aria-describedby="userlHelp"
                            placeholder="Enter Username" required>
                    </div>
                    <div class="form-group">
                        <label for="exampleInputPassword1">Password</label>
                        <input class="form-control" id="password" name="password" type="password" placeholder="Password"
                            required>
                    </div>
                    <input type="submit" class="btn btn-orange btn-block card-shadow-2" name="login" value="Login">
                </form>
            </div>
            <p class="text-center text-danger"><small><?php echo htmlspecialchars($message); ?></small></p>
        </div>
    </div>
    <!-- Bootstrap core JavaScript-->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <!-- Core plugin JavaScript-->
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>
</body>

</html>
