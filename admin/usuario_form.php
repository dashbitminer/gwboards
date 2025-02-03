<?php
session_start();
if (!isset($_SESSION['usuario']) || $_SESSION['rol'] !== 'administrador') {
    header("Location: ../index.php");
    exit;
}

require_once '../config/database.php';
$database = new Database();
$db = $database->getConnection();

$usuario = '';
$rol = '';
$id = '';
$modo = 'crear';

if (isset($_GET['id'])) {
    $modo = 'editar';
    $id = $_GET['id'];
    $query = "SELECT * FROM usuarios WHERE id = :id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(":id", $id);
    $stmt->execute();
    
    if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $usuario = $row['usuario'];
        $rol = $row['rol'];
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = $_POST['usuario'];
    $rol = $_POST['rol'];
    $password = $_POST['password'];

    if ($modo === 'crear') {
        $query = "INSERT INTO usuarios (usuario, password, rol) VALUES (:usuario, :password, :rol)";
        $stmt = $db->prepare($query);
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt->bindParam(":password", $password_hash);
    } else {
        if (!empty($password)) {
            $query = "UPDATE usuarios SET usuario = :usuario, password = :password, rol = :rol WHERE id = :id";
            $stmt = $db->prepare($query);
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt->bindParam(":password", $password_hash);
        } else {
            $query = "UPDATE usuarios SET usuario = :usuario, rol = :rol WHERE id = :id";
            $stmt = $db->prepare($query);
        }
        $stmt->bindParam(":id", $id);
    }

    $stmt->bindParam(":usuario", $usuario);
    $stmt->bindParam(":rol", $rol);

    if ($stmt->execute()) {
        header("Location: usuarios.php?mensaje=guardado");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $modo === 'crear' ? 'Crear' : 'Editar'; ?> Usuario</title>
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
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="text-white text-center mb-4">
            <h5>Panel Administrativo</h5>
        </div>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link" href="dashboard.php">
                    <i class="bi bi-speedometer2"></i> Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link active" href="usuarios.php">
                    <i class="bi bi-people"></i> Gestión de Usuarios
                </a>
            </li>
            <!-- Resto de los items del menú -->
        </ul>
    </div>

    <!-- Top Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top top-navbar">
        <div class="container-fluid">
            <span class="navbar-brand"><?php echo $modo === 'crear' ? 'Crear' : 'Editar'; ?> Usuario</span>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="../logout.php">
                    <i class="bi bi-box-arrow-right"></i> Cerrar Sesión
                </a>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="main-content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-body">
                    <form method="POST" action="">
                        <div class="mb-3">
                            <label for="usuario" class="form-label">Usuario</label>
                            <input type="text" class="form-control" id="usuario" name="usuario" 
                                   value="<?php echo htmlspecialchars($usuario); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">
                                <?php echo $modo === 'crear' ? 'Contraseña' : 'Nueva Contraseña (dejar en blanco para mantener la actual)'; ?>
                            </label>
                            <input type="password" class="form-control" id="password" name="password" 
                                   <?php echo $modo === 'crear' ? 'required' : ''; ?>>
                        </div>
                        <div class="mb-3">
                            <label for="rol" class="form-label">Rol</label>
                            <select class="form-select" id="rol" name="rol" required>
                                <option value="usuario" <?php echo $rol === 'usuario' ? 'selected' : ''; ?>>Usuario</option>
                                <option value="administrador" <?php echo $rol === 'administrador' ? 'selected' : ''; ?>>Administrador</option>
                            </select>
                        </div>
                        <div class="d-flex justify-content-end gap-2">
                            <a href="usuarios.php" class="btn btn-secondary">Cancelar</a>
                            <button type="submit" class="btn btn-primary">Guardar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 