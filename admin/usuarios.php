<?php
session_start();
if (!isset($_SESSION['usuario']) || $_SESSION['rol'] !== 'administrador') {
    header("Location: ../index.php");
    exit;
}

require_once '../config/database.php';
$database = new Database();
$db = $database->getConnection();

// Eliminar usuario si se recibe la petición
if (isset($_POST['eliminar_usuario'])) {
    $id = $_POST['eliminar_usuario'];
    $query = "DELETE FROM usuarios WHERE id = :id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(":id", $id);
    
    if ($stmt->execute()) {
        header("Location: usuarios.php?mensaje=eliminado");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Usuarios</title>
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
            <?php if (isset($_GET['mensaje'])): ?>
                <?php if ($_GET['mensaje'] == 'creado'): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        Usuario creado exitosamente
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php elseif ($_GET['mensaje'] == 'eliminado'): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        Usuario eliminado exitosamente
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php elseif ($_GET['mensaje'] == 'error'): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        Error al procesar la solicitud
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>
            <?php endif; ?>

            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Gestión de Usuarios</h5>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalCrearUsuario">
                        <i class="bi bi-person-plus"></i> Crear Usuario
                    </button>
                </div>
                <div class="card-body">
                    <table id="tablaUsuarios" class="table table-striped" style="width:100%">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Usuario</th>
                                <th>Rol</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $query = "SELECT * FROM usuarios ORDER BY id DESC";
                            $stmt = $db->prepare($query);
                            $stmt->execute();
                            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)):
                            ?>
                            <tr>
                                <td><?php echo $row['id']; ?></td>
                                <td><?php echo htmlspecialchars($row['usuario']); ?></td>
                                <td><?php echo ucfirst($row['rol']); ?></td>
                                <td>
                                    <button type="button" 
                                            class="btn btn-sm <?php echo $row['status'] === 'activo' ? 'btn-success' : 'btn-danger'; ?>"
                                            onclick="cambiarEstado(<?php echo $row['id']; ?>, '<?php echo $row['status']; ?>', '<?php echo htmlspecialchars($row['usuario']); ?>')">
                                        <i class="bi bi-power"></i> <?php echo ucfirst($row['status']); ?>
                                    </button>
                                </td>
                                <td>
                                    <a href="usuario_form.php?id=<?php echo $row['id']; ?>" 
                                       class="btn btn-sm btn-warning">
                                        <i class="bi bi-pencil"></i> Editar
                                    </a>
                                    <button type="button" 
                                            class="btn btn-sm btn-danger"
                                            onclick="confirmarEliminacion(<?php echo $row['id']; ?>, '<?php echo htmlspecialchars($row['usuario']); ?>')">
                                        <i class="bi bi-trash"></i> Eliminar
                                    </button>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Confirmación -->
    <div class="modal fade" id="modalConfirmacion" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirmar Eliminación</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    ¿Está seguro que desea eliminar al usuario <span id="nombreUsuario"></span>?
                </div>
                <div class="modal-footer">
                    <form action="" method="POST">
                        <input type="hidden" name="eliminar_usuario" id="idUsuarioEliminar">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-danger">Eliminar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Crear Usuario -->
    <div class="modal fade" id="modalCrearUsuario" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Crear Nuevo Usuario</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="usuario_crear.php" method="POST">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="usuario" class="form-label">Usuario</label>
                            <input type="text" class="form-control" id="usuario" name="usuario" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Contraseña</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        <div class="mb-3">
                            <label for="rol" class="form-label">Rol</label>
                            <select class="form-select" id="rol" name="rol" required>
                                <option value="usuario">Usuario</option>
                                <option value="administrador">Administrador</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Crear Usuario</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#tablaUsuarios').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json'
                }
            });
        });

        function confirmarEliminacion(id, usuario) {
            document.getElementById('nombreUsuario').textContent = usuario;
            document.getElementById('idUsuarioEliminar').value = id;
            var modal = new bootstrap.Modal(document.getElementById('modalConfirmacion'));
            modal.show();
        }

        function cambiarEstado(id, estadoActual, usuario) {
            const nuevoEstado = estadoActual === 'activo' ? 'inactivo' : 'activo';
            if (confirm(`¿Estás seguro de que deseas cambiar el estado de ${usuario} a ${nuevoEstado}?`)) {
                $.ajax({
                    type: 'POST',
                    url: 'usuario_cambiar_estado.php',
                    data: {
                        id: id,
                        nuevo_estado: nuevoEstado
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            // Obtener el botón que se clickeó
                            const boton = $(`button[onclick="cambiarEstado(${id}, '${estadoActual}', '${usuario}')"]`);
                            
                            // Cambiar la clase del botón
                            boton.removeClass(estadoActual === 'activo' ? 'btn-success' : 'btn-danger')
                                 .addClass(nuevoEstado === 'activo' ? 'btn-success' : 'btn-danger');
                            
                            // Actualizar el texto del botón
                            boton.html(`<i class="bi bi-power"></i> ${nuevoEstado.charAt(0).toUpperCase() + nuevoEstado.slice(1)}`);
                            
                            // Actualizar el onclick del botón
                            boton.attr('onclick', `cambiarEstado(${id}, '${nuevoEstado}', '${usuario}')`);
                            
                            // Mostrar mensaje de éxito
                            const alert = $('<div class="alert alert-success alert-dismissible fade show" role="alert">')
                                .html(`
                                    Estado de usuario actualizado exitosamente a ${nuevoEstado}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                `);
                            
                            // Insertar alerta al principio de la card-body
                            $('.card-body').first().prepend(alert);
                            
                            // Remover la alerta después de 3 segundos
                            setTimeout(() => {
                                alert.alert('close');
                            }, 3000);
                        } else {
                            alert('Error al actualizar el estado del usuario');
                        }
                    },
                    error: function() {
                        alert('Error en la solicitud');
                    }
                });
            }
        }
    </script>
</body>
</html> 