<?php
session_start();
if (!isset($_SESSION['usuario']) || $_SESSION['rol'] !== 'administrador') {
    header("Location: ../index.php");
    exit;
}

require_once '../config/database.php';
$database = new Database();
$db = $database->getConnection();

// Obtener total de usuarios (excluyendo administradores)
$query = "SELECT COUNT(*) as total FROM usuarios WHERE rol = 'usuario'";
$stmt = $db->prepare($query);
$stmt->execute();
$total_usuarios = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

// Obtener total de boards
$query = "SELECT COUNT(*) as total FROM boards";
$stmt = $db->prepare($query);
$stmt->execute();
$total_boards = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

// Obtener total de permisos asignados
$query = "SELECT COUNT(*) as total FROM board_permisos";
$stmt = $db->prepare($query);
$stmt->execute();
$total_permisos = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Administrador</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: 250px;
            background-color: #212529;
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
            background-color: #0d6efd;
        }
        .sidebar .nav-link i {
            margin-right: 10px;
        }
        .main-content {
            margin-left: 250px;
            padding: 20px;
        }
        .top-navbar {
            margin-left: 250px;
        }
    </style>
</head>
<body>
    <?php include 'templates/sidebar.php'; ?>
    <?php include 'templates/navbar.php'; ?>

    <!-- Main Content -->
    <div class="main-content">
        <div class="container-fluid">
            <h2 class="mb-4">Dashboard</h2>
            <div class="row">
                <div class="col-md-4">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <h5 class="card-title">Usuarios Totales</h5>
                            <p class="card-text display-6"><?php echo $total_usuarios; ?></p>
                            <i class="bi bi-people position-absolute top-50 end-0 translate-middle-y opacity-25 display-1 me-3"></i>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <h5 class="card-title">Boards Creados</h5>
                            <p class="card-text display-6"><?php echo $total_boards; ?></p>
                            <i class="bi bi-layout-text-window position-absolute top-50 end-0 translate-middle-y opacity-25 display-1 me-3"></i>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-info text-white">
                        <div class="card-body">
                            <h5 class="card-title">Permisos Asignados</h5>
                            <p class="card-text display-6"><?php echo $total_permisos; ?></p>
                            <i class="bi bi-key position-absolute top-50 end-0 translate-middle-y opacity-25 display-1 me-3"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 