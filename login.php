<?php
session_start();
require_once 'config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = $_POST['usuario'];
    $password = $_POST['password'];

    $database = new Database();
    $db = $database->getConnection();

    $query = "SELECT * FROM usuarios WHERE usuario = :usuario";
    $stmt = $db->prepare($query);
    $stmt->bindParam(":usuario", $usuario);
    $stmt->execute();

    if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        if (password_verify($password, $row['password'])) {
            $_SESSION['usuario'] = $row['usuario'];
            $_SESSION['rol'] = $row['rol'];
            $_SESSION['usuario_id'] = $row['id'];
            
            if ($row['rol'] === 'administrador') {
                header("Location: admin/dashboard.php");
            } else {
                header("Location: usuario/dashboard.php");
            }
            exit;
        } else {
            header("Location: index.php?error=password");
            exit;
        }
    } else {
        header("Location: index.php?error=usuario");
        exit;
    }
}
?> 