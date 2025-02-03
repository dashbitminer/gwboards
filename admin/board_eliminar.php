<?php
session_start();
if (!isset($_SESSION['usuario']) || $_SESSION['rol'] !== 'administrador') {
    header("Location: ../index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    require_once '../config/database.php';
    $database = new Database();
    $db = $database->getConnection();

    $id = $_POST['id'];

    // Primero eliminar los permisos asociados
    $query = "DELETE FROM board_permisos WHERE board_id = :id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(":id", $id);
    $stmt->execute();

    // Luego eliminar el board
    $query = "DELETE FROM boards WHERE id = :id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(":id", $id);
    
    if ($stmt->execute()) {
        header("Location: boards.php?mensaje=eliminado");
    } else {
        header("Location: boards.php?mensaje=error");
    }
    exit;
}

header("Location: boards.php");
exit; 