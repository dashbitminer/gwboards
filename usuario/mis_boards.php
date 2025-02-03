<?php
session_start();
if (!isset($_SESSION['usuario']) || $_SESSION['rol'] !== 'usuario') {
    header("Location: ../index.php");
    exit;
}

require_once '../config/database.php';
$database = new Database();
$db = $database->getConnection();

// Obtener los boards a los que el usuario tiene acceso
$query = "SELECT b.* FROM boards b 
          INNER JOIN board_permisos bp ON b.id = bp.board_id 
          WHERE bp.usuario_id = :usuario_id 
          ORDER BY b.fecha_modificacion DESC";
$stmt = $db->prepare($query);
$stmt->bindParam(":usuario_id", $_SESSION['usuario_id']);
$stmt->execute();
$boards = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Boards</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
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
            <h2 class="mb-4">Mis Boards</h2>
            
            <?php if (empty($boards)): ?>
            <div class="alert alert-info">
                No tienes acceso a ningún board en este momento.
            </div>
            <?php else: ?>
            <div class="card">
                <div class="card-body">
                    <table id="tablaBoards" class="table table-striped" style="width:100%">
                        <thead>
                            <tr>
                                <th>Título</th>
                                <th>Fecha de Modificación</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($boards as $board): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($board['titulo']); ?></td>
                                <td><?php echo date('d/m/Y H:i', strtotime($board['fecha_modificacion'])); ?></td>
                                <td>
                                    <a href="ver_board.php?id=<?php echo $board['id']; ?>" class="btn btn-primary btn-sm">
                                        <i class="bi bi-eye"></i> Ver Completo
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#tablaBoards').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json'
                },
                order: [[1, 'desc']], // Ordenar por fecha de modificación descendente
                columns: [
                    { width: "50%" }, // Título
                    { width: "30%" }, // Fecha
                    { width: "20%", orderable: false } // Acciones
                ]
            });
        });
    </script>
</body>
</html> 