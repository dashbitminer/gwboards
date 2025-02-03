<?php
session_start();
if(isset($_SESSION['usuario'])) {
    header("Location: " . ($_SESSION['rol'] === 'administrador' ? 'admin/dashboard.php' : 'usuario/dashboard.php'));
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once 'config/database.php';
    $database = new Database();
    $db = $database->getConnection();

    $usuario = $_POST['usuario'];
    $password = $_POST['password'];

    $query = "SELECT * FROM usuarios WHERE usuario = :usuario";
    $stmt = $db->prepare($query);
    $stmt->bindParam(":usuario", $usuario);
    $stmt->execute();

    if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        if (password_verify($password, $row['password'])) {
            if ($row['status'] === 'inactivo') {
                echo json_encode(['error' => 'cuenta_inactiva']);
                exit;
            }
            
            $_SESSION['usuario'] = $row['usuario'];
            $_SESSION['usuario_id'] = $row['id'];
            $_SESSION['rol'] = $row['rol'];
            
            echo json_encode(['redirect' => $row['rol'] === 'administrador' ? 'admin/dashboard.php' : 'usuario/dashboard.php']);
            exit;
        }
    }
    
    echo json_encode(['error' => 'credenciales_invalidas']);
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
</head>
<body class="bg-light">
    <div class="container">
        <div class="row justify-content-center mt-5">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header text-center">
                        <h4>Iniciar Sesión</h4>
                    </div>
                    <?php if(isset($_GET['error'])): ?>
                    <div class="alert alert-danger m-3">
                        <?php 
                        switch($_GET['error']) {
                            case 'password':
                                echo "Contraseña incorrecta";
                                break;
                            case 'usuario':
                                echo "Usuario no encontrado";
                                break;
                            default:
                                echo "Error al iniciar sesión";
                        }
                        ?>
                    </div>
                    <?php endif; ?>
                    <div class="card-body">
                        <form id="loginForm" method="POST">
                            <div class="mb-3">
                                <label for="usuario" class="form-label">Usuario</label>
                                <input type="text" class="form-control" id="usuario" name="usuario" required>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Contraseña</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Ingresar</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Cuenta Inactiva -->
    <div class="modal fade" id="cuentaInactivaModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                        Cuenta Inhabilitada
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center py-4">
                    <i class="bi bi-lock-fill text-danger display-1 mb-4"></i>
                    <p class="mb-0">Tu cuenta está inhabilitada. Por favor, ponte en contacto con soporte técnico para que den de alta tu cuenta.</p>
                </div>
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
    $(document).ready(function() {
        $('#loginForm').on('submit', function(e) {
            e.preventDefault();
            $.ajax({
                type: 'POST',
                url: 'index.php',
                data: $(this).serialize(),
                dataType: 'json',
                success: function(response) {
                    if (response.error) {
                        if (response.error === 'cuenta_inactiva') {
                            $('#cuentaInactivaModal').modal('show');
                        } else {
                            alert('Usuario o contraseña incorrectos');
                        }
                    } else if (response.redirect) {
                        window.location.href = response.redirect;
                    }
                },
                error: function() {
                    alert('Error en la solicitud');
                }
            });
        });
    });
    </script>
</body>
</html> 