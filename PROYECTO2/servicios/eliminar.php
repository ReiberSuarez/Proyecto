<?php
require_once('../config_db.php');
if (session_status() === PHP_SESSION_NONE) session_start();
if(!isset($_SESSION['usuario'])) {
    header('Location: ../login.php');
    exit;
}

$id = isset($_GET['id']) && is_numeric($_GET['id']) ? (int)$_GET['id'] : null;
if (!$id) {
    header('Location: /PROYECTO2/servicios/listar.php');
    exit;
}

try {
    $stmt = $pdo->prepare("DELETE FROM servicio WHERE id_servicio = ?");
    $stmt->execute([$id]);
} catch (PDOException $e) {
    // Puedes mostrar un mensaje o registrar el error si hay FKs relacionadas
}
header('Location: /PROYECTO2/servicios/listar.php');
exit;