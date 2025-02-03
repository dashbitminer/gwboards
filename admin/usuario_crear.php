<?php
session_start();
if (!isset($_SESSION['usuario']) || $_SESSION['rol'] !== 'administrador') {
    header("Location: ../index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once '../config/database.php';
    $database = new Database();
    $db = $database->getConnection();

    $usuario = $_POST['usuario'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $rol = $_POST['rol'];

    // Verificar si el usuario ya existe
    $query = "SELECT COUNT(*) FROM usuarios WHERE usuario = :usuario";
    $stmt = $db->prepare($query);
    $stmt->bindParam(":usuario", $usuario);
    $stmt->execute();
    
    if ($stmt->fetchColumn() > 0) {
        header("Location: usuarios.php?mensaje=error&error=usuario_existe");
        exit;
    }

    // Crear el nuevo usuario
    $query = "INSERT INTO usuarios (usuario, password, rol) VALUES (:usuario, :password, :rol)";
    $stmt = $db->prepare($query);
    $stmt->bindParam(":usuario", $usuario);
    $stmt->bindParam(":password", $password);
    $stmt->bindParam(":rol", $rol);
    
    if ($stmt->execute()) {
        header("Location: usuarios.php?mensaje=creado");
    } else {
        header("Location: usuarios.php?mensaje=error");
    }
    exit;
}

header("Location: usuarios.php");
exit; 