<?php
require_once('../config_db.php');
if (session_status() === PHP_SESSION_NONE) session_start();
if(!isset($_SESSION['usuario'])) {
    header('Location: ../login.php');
    exit;
}

$id = isset($_GET['id']) && is_numeric($_GET['id']) ? (int)$_GET['id'] : null;
if (!$id) {
    header('Location: /PROYECTO2/especialidades/listar.php?error=ID inválido');
    exit;
}

try {
    $stmt = $pdo->prepare("DELETE FROM especialidad WHERE id_especialidad = ?");
    $stmt->execute([$id]);
    // Si no hubo error, redirige con éxito
    header('Location: /PROYECTO2/especialidades/listar.php?msg=Especialidad eliminada');
    exit;
} catch (PDOException $e) {
    // Redirige con mensaje de error
    header('Location: /PROYECTO2/especialidades/listar.php?error=No se pudo eliminar la especialidad. Puede estar vinculada a médicos u otros datos.');
    exit;
}