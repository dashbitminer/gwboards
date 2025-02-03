<?php
session_start();
if (!isset($_SESSION['usuario']) || $_SESSION['rol'] !== 'administrador') {
    header("Location: ../index.php");
    exit;
}

require_once '../config/database.php';
$database = new Database();
$db = $database->getConnection();

$titulo = '';
$contenido = '';
$id = '';
$modo = 'crear';

if (isset($_GET['id'])) {
    $modo = 'editar';
    $id = $_GET['id'];
    $query = "SELECT * FROM boards WHERE id = :id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(":id", $id);
    $stmt->execute();
    
    if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $titulo = $row['titulo'];
        $contenido = $row['contenido'];
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = $_POST['titulo'];
    $contenido = $_POST['contenido'];
    $usuario_id = $_SESSION['usuario_id']; // Asegúrate de tener el ID del usuario en la sesión

    if ($modo === 'crear') {
        $query = "INSERT INTO boards (titulo, contenido, creado_por) VALUES (:titulo, :contenido, :creado_por)";
        $stmt = $db->prepare($query);
    } else {
        $query = "UPDATE boards SET titulo = :titulo, contenido = :contenido WHERE id = :id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(":id", $id);
    }

    $stmt->bindParam(":titulo", $titulo);
    $stmt->bindParam(":contenido", $contenido);
    if ($modo === 'crear') {
        $stmt->bindParam(":creado_por", $usuario_id);
    }

    if ($stmt->execute()) {
        header("Location: boards.php?mensaje=guardado");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $modo === 'crear' ? 'Crear' : 'Editar'; ?> Board</title>
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    
    <!-- Bootstrap 4 (solo para Summernote) -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-4">
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script>
    
    <!-- Bootstrap 5 (para el resto de la aplicación) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-5">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <!-- Summernote -->
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/lang/summernote-es-ES.min.js"></script>

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
            background-color: #007bff;
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
        .note-editor {
            margin-bottom: 20px;
        }
        .note-editing-area {
            min-height: 400px;
        }
        .dropdown-menu {
            position: absolute !important;
        }
        .note-btn-group .dropdown-menu {
            position: absolute !important;
            transform: none !important;
        }

        /* Estilos específicos para el navbar y sidebar */
        .navbar {
            background-color: #212529 !important;
        }
        .navbar .nav-link {
            color: rgba(255,255,255,.75) !important;
        }
        .navbar .nav-link:hover {
            color: rgba(255,255,255,1) !important;
        }
        .navbar-nav {
            margin-left: auto !important;
        }
        
        /* Asegurar que los estilos de Bootstrap 5 se apliquen al navbar y sidebar */
        #bootstrap-5 {
            position: relative;
            z-index: 2;
        }
        #bootstrap-4 {
            position: relative;
            z-index: 1;
        }
        
        /* Mantener los estilos del editor */
        .note-editor {
            margin-bottom: 20px;
            z-index: 1;
        }
        .note-editing-area {
            min-height: 400px;
        }
        .dropdown-menu {
            position: absolute !important;
        }
        .note-btn-group .dropdown-menu {
            position: absolute !important;
            transform: none !important;
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
                    <h5><?php echo $modo === 'crear' ? 'Crear Nuevo' : 'Editar'; ?> Board</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="">
                        <div class="form-group">
                            <label for="titulo">Título</label>
                            <input type="text" class="form-control" id="titulo" name="titulo" 
                                   value="<?php echo htmlspecialchars($titulo); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="contenido">Contenido</label>
                            <textarea id="contenido" name="contenido"><?php echo htmlspecialchars($contenido); ?></textarea>
                        </div>
                        <div class="text-right">
                            <a href="boards.php" class="btn btn-secondary">Cancelar</a>
                            <button type="submit" class="btn btn-primary">Guardar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 bundle al final -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Asegurar que los estilos de Bootstrap 5 tengan prioridad para el navbar y sidebar
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.navbar, .sidebar').forEach(function(el) {
                el.style.zIndex = '1050';
            });
        });
        
        // Configuración de Summernote
        $(document).ready(function() {
            $('#contenido').summernote({
                lang: 'es-ES',
                height: 500,
                toolbar: [
                    ['style', ['style']],
                    ['font', ['bold', 'underline', 'italic', 'clear']],
                    ['fontname', ['fontname']],
                    ['fontsize', ['fontsize']],
                    ['color', ['color']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['table', ['table']],
                    ['insert', ['link', 'picture', 'video', 'hr']],
                    ['view', ['fullscreen', 'codeview', 'help']]
                ],
                fontNames: ['Arial', 'Arial Black', 'Comic Sans MS', 'Courier New', 'Helvetica', 'Impact', 'Tahoma', 'Times New Roman', 'Verdana'],
                fontSizes: ['8', '9', '10', '11', '12', '14', '16', '18', '24', '36'],
                popover: {
                    image: [
                        ['image', ['resizeFull', 'resizeHalf', 'resizeQuarter', 'resizeNone']],
                        ['float', ['floatLeft', 'floatRight', 'floatNone']],
                        ['remove', ['removeMedia']]
                    ]
                },
                callbacks: {
                    onPaste: function (e) {
                        var bufferText = ((e.originalEvent || e).clipboardData || window.clipboardData).getData('Text');
                        e.preventDefault();
                        document.execCommand('insertText', false, bufferText);
                    }
                },
                codeviewFilter: false,
                codeviewIframeFilter: false
            });
        });
    </script>
</body>
</html> 