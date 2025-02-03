<?php
session_start();
if (!isset($_SESSION['usuario']) || $_SESSION['rol'] !== 'administrador') {
    header("Location: ../index.php");
    exit;
}

require_once '../config/database.php';
$database = new Database();
$db = $database->getConnection();

if (!isset($_GET['id'])) {
    header("Location: boards.php");
    exit;
}

$board_id = $_GET['id'];

// Obtener información del board
$query = "SELECT titulo FROM boards WHERE id = :id";
$stmt = $db->prepare($query);
$stmt->bindParam(":id", $board_id);
$stmt->execute();
$board = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$board) {
    header("Location: boards.php");
    exit;
}

// Procesar cambios en permisos
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['usuarios']) && is_array($_POST['usuarios'])) {
        // Primero eliminar todos los permisos existentes para este board
        $query = "DELETE FROM board_permisos WHERE board_id = :board_id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(":board_id", $board_id);
        $stmt->execute();

        // Insertar los nuevos permisos
        $query = "INSERT INTO board_permisos (usuario_id, board_id) VALUES (:usuario_id, :board_id)";
        $stmt = $db->prepare($query);
        $stmt->bindParam(":board_id", $board_id);

        foreach ($_POST['usuarios'] as $usuario_id) {
            $stmt->bindParam(":usuario_id", $usuario_id);
            $stmt->execute();
        }

        header("Location: boards.php?mensaje=permisos_actualizados");
        exit;
    }
}

// Obtener todos los usuarios que no son administradores
$query = "SELECT id, usuario FROM usuarios WHERE rol = 'usuario'";
$stmt = $db->prepare($query);
$stmt->execute();
$usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Obtener usuarios con permiso actual
$query = "SELECT usuario_id FROM board_permisos WHERE board_id = :board_id";
$stmt = $db->prepare($query);
$stmt->bindParam(":board_id", $board_id);
$stmt->execute();
$permisos_actuales = $stmt->fetchAll(PDO::FETCH_COLUMN);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestionar Permisos - <?php echo htmlspecialchars($board['titulo']); ?></title>
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
                    <h5>Gestionar Permisos - <?php echo htmlspecialchars($board['titulo']); ?></h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="">
                        <div class="mb-3">
                            <label class="form-label">Seleccionar usuarios con acceso</label>
                            <div class="row">
                                <?php foreach ($usuarios as $usuario): ?>
                                <div class="col-md-4 mb-2">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" 
                                               name="usuarios[]" 
                                               value="<?php echo $usuario['id']; ?>"
                                               id="usuario<?php echo $usuario['id']; ?>"
                                               <?php echo in_array($usuario['id'], $permisos_actuales) ? 'checked' : ''; ?>>
                                        <label class="form-check-label" for="usuario<?php echo $usuario['id']; ?>">
                                            <?php echo htmlspecialchars($usuario['usuario']); ?>
                                        </label>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <div class="d-flex justify-content-end gap-2">
                            <a href="boards.php" class="btn btn-secondary">Cancelar</a>
                            <button type="submit" class="btn btn-primary">Guardar Permisos</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 