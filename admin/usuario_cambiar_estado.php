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

    $id = $_POST['id'];
    $nuevo_estado = $_POST['nuevo_estado'];

    $query = "UPDATE usuarios SET status = :status WHERE id = :id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(":status", $nuevo_estado);
    $stmt->bindParam(":id", $id);
    
    header('Content-Type: application/json');
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false]);
    }
    exit;
} 