<?php
session_start();
if (!isset($_SESSION['usuario']) || $_SESSION['rol'] !== 'usuario') {
    header("Location: ../index.php");
    exit;
}

require_once '../config/database.php';
$database = new Database();
$db = $database->getConnection();

if (!isset($_GET['id'])) {
    header("Location: mis_boards.php");
    exit;
}

$board_id = $_GET['id'];

// Verificar si el usuario tiene permiso para ver este board
$query = "SELECT COUNT(*) FROM board_permisos 
          WHERE board_id = :board_id AND usuario_id = :usuario_id";
$stmt = $db->prepare($query);
$stmt->bindParam(":board_id", $board_id);
$stmt->bindParam(":usuario_id", $_SESSION['usuario_id']);
$stmt->execute();

if ($stmt->fetchColumn() == 0) {
    header("Location: mis_boards.php");
    exit;
}

// Obtener la información del board
$query = "SELECT * FROM boards WHERE id = :id";
$stmt = $db->prepare($query);
$stmt->bindParam(":id", $board_id);
$stmt->execute();
$board = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$board) {
    header("Location: mis_boards.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($board['titulo']); ?></title>
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

    <!-- Main Content -->
    <div class="main-content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><?php echo htmlspecialchars($board['titulo']); ?></h5>
                        <a href="mis_boards.php" class="btn btn-secondary btn-sm">
                            <i class="bi bi-arrow-left"></i> Volver
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="board-content">
                        <?php echo $board['contenido']; ?>
                    </div>
                </div>
                <div class="card-footer">
                    <span class="text-muted">
                        <?php 
                        // Limpiar cualquier HTML que pudiera estar almacenado en fecha_modificacion
                        $fecha = strip_tags(date('d/m/Y H:i', strtotime($board['fecha_modificacion'])));
                        ?>
                        <i class="bi bi-clock-history"></i> Última actualización: <?php echo $fecha; ?>
                    </span>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 