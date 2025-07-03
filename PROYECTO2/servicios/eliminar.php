<?php
require_once('../config_db.php');
if (session_status() === PHP_SESSION_NONE) session_start();
if(!isset($_SESSION['usuario'])) {
    header('Location: ../login.php');
    exit;
}

$id = isset($_GET['id']) && is_numeric($_GET['id']) ? (int)$_GET['id'] : null;
if (!$id) {
    header('Location: /PROYECTO2/servicios/listar.php?error=ID inválido');
    exit;
}

try {
    $stmt = $pdo->prepare("DELETE FROM servicio WHERE id_servicio = ?");
    $stmt->execute([$id]);
    header('Location: /PROYECTO2/servicios/listar.php?msg=Servicio eliminado correctamente');
    exit;
} catch (PDOException $e) {
    header('Location: /PROYECTO2/servicios/listar.php?error=No se pudo eliminar el servicio. Puede estar vinculado a otros registros.');
    exit;
}