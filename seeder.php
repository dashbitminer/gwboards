<?php
require_once 'config/database.php';

try {
    // Crear el usuario y la base de datos si no existen
    $rootConn = new PDO("mysql:host=localhost", "boardsgw_usr", "Gl\$ssWING01");
    $rootConn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Crear base de datos
    $rootConn->exec("CREATE DATABASE IF NOT EXISTS boardsgw");
    
    // Crear usuario
    $rootConn->exec("CREATE USER IF NOT EXISTS 'boardsgw_usr'@'localhost' IDENTIFIED BY 'Gl\$ssWING01'");
    $rootConn->exec("GRANT ALL PRIVILEGES ON boardsgw.* TO 'boardsgw_usr'@'localhost'");
    $rootConn->exec("FLUSH PRIVILEGES");

    // Conectar a la nueva base de datos
    $database = new Database();
    $db = $database->getConnection();

    // Crear las tablas
    $db->exec("CREATE TABLE IF NOT EXISTS usuarios (
        id INT AUTO_INCREMENT PRIMARY KEY,
        usuario VARCHAR(50) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        rol ENUM('administrador', 'usuario') NOT NULL,
        status ENUM('activo', 'inactivo') NOT NULL DEFAULT 'activo'
    )");

    $db->exec("CREATE TABLE IF NOT EXISTS boards (
        id INT AUTO_INCREMENT PRIMARY KEY,
        titulo VARCHAR(255) NOT NULL,
        contenido TEXT,
        creado_por INT,
        fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        fecha_modificacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (creado_por) REFERENCES usuarios(id)
    )");

    $db->exec("CREATE TABLE IF NOT EXISTS board_permisos (
        id INT AUTO_INCREMENT PRIMARY KEY,
        board_id INT,
        usuario_id INT,
        FOREIGN KEY (board_id) REFERENCES boards(id),
        FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
    )");

    // Limpiar tablas existentes
    $db->exec("SET FOREIGN_KEY_CHECKS = 0");
    $db->exec("TRUNCATE TABLE board_permisos");
    $db->exec("TRUNCATE TABLE boards");
    $db->exec("TRUNCATE TABLE usuarios");
    $db->exec("SET FOREIGN_KEY_CHECKS = 1");

    // Insertar usuarios de prueba
    $usuarios = [
        ['admin', '123', 'administrador', 'activo'],
        ['usuario1', '123', 'usuario', 'activo'],
        ['usuario2', '123', 'usuario', 'activo'],
        ['usuario3', '123', 'usuario', 'inactivo']
    ];

    $query = "INSERT INTO usuarios (usuario, password, rol, status) VALUES (:usuario, :password, :rol, :status)";
    $stmt = $db->prepare($query);

    foreach ($usuarios as $usuario) {
        $password_hash = password_hash($usuario[1], PASSWORD_DEFAULT);
        $stmt->bindValue(":usuario", $usuario[0]);
        $stmt->bindValue(":password", $password_hash);
        $stmt->bindValue(":rol", $usuario[2]);
        $stmt->bindValue(":status", $usuario[3]);
        $stmt->execute();
    }

    // Insertar boards de prueba
    $boards = [
        ['Board de Prueba 1', 'Contenido del board 1', 1],
        ['Board de Prueba 2', 'Contenido del board 2', 1],
        ['Board de Prueba 3', 'Contenido del board 3', 1]
    ];

    $query = "INSERT INTO boards (titulo, contenido, creado_por) VALUES (:titulo, :contenido, :creado_por)";
    $stmt = $db->prepare($query);

    foreach ($boards as $board) {
        $stmt->bindValue(":titulo", $board[0]);
        $stmt->bindValue(":contenido", $board[1]);
        $stmt->bindValue(":creado_por", $board[2]);
        $stmt->execute();
    }

    // Asignar permisos de prueba
    $permisos = [
        [1, 2], // Board 1 - usuario1
        [2, 2], // Board 2 - usuario1
        [2, 3], // Board 2 - usuario2
        [3, 3]  // Board 3 - usuario2
    ];

    $query = "INSERT INTO board_permisos (board_id, usuario_id) VALUES (:board_id, :usuario_id)";
    $stmt = $db->prepare($query);

    foreach ($permisos as $permiso) {
        $stmt->bindValue(":board_id", $permiso[0]);
        $stmt->bindValue(":usuario_id", $permiso[1]);
        $stmt->execute();
    }

    echo "Base de datos, tablas, usuario y datos de prueba creados correctamente\n";

} catch(PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Seeder</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 40px auto;
            padding: 20px;
            line-height: 1.6;
        }
        .success {
            color: #28a745;
        }
        .info {
            color: #17a2b8;
        }
        .button-container {
            margin-top: 20px;
            display: flex;
            gap: 10px;
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            cursor: pointer;
            border: none;
            font-size: 16px;
        }
        .btn-primary {
            background-color: #0d6efd;
            color: white;
        }
        .btn-danger {
            background-color: #dc3545;
            color: white;
        }
        .warning-text {
            color: #dc3545;
            font-weight: bold;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="button-container">
        <a href="index.php" class="btn btn-primary">Ir al inicio</a>
        
        <!-- Botón para resetear la base de datos -->
        <form method="POST" action="" style="display: inline;" onsubmit="return confirm('¿Estás seguro de que deseas borrar y reinstalar la base de datos? ¡Todos los datos serán eliminados!');">
            <button type="submit" name="reset_db" class="btn btn-danger">
                Borrar y Reinstalar Base de Datos
            </button>
        </form>
    </div>

    <?php if (isset($_POST['reset_db'])): ?>
        <div class="warning-text">
            ¡La base de datos ha sido reiniciada completamente!
        </div>
    <?php endif; ?>
</body>
</html> 