<?php
session_start();
if (!isset($_SESSION['usuario']) || $_SESSION['rol'] !== 'usuario') {
    header("Location: ../index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: 250px;
            background-color: #077e92;
            padding-top: 60px;
        }
        .sidebar .nav-link {
            color: #fff;
            padding: 10px 20px;
            margin: 5px 15px;
            border-radius: 5px;
        }
        .sidebar .nav-link:hover {
            background-color: rgba(255,255,255,0.1);
        }
        .sidebar .nav-link.active {
            background-color: #056674;
        }
        .sidebar .nav-link i {
            margin-right: 10px;
        }
        .main-content {
            margin-left: 250px;
            padding: 20px;
            margin-top: 60px;
        }
        .top-navbar {
            margin-left: 250px;
            z-index: 1030;
        }
    </style>
</head>
<body>
    <?php include 'templates/sidebar.php'; ?>
    <?php include 'templates/navbar.php'; ?>

    <div class="main-content">
        <div class="container-fluid">
            <h1 class="mb-4">Dashboard</h1>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 