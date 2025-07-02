<?php
require_once('../config_db.php');
if (session_status() === PHP_SESSION_NONE) session_start();
if(!isset($_SESSION['usuario'])) {
    header('Location: ../login.php');
    exit;
}

$id = isset($_GET['id']) && is_numeric($_GET['id']) ? (int)$_GET['id'] : null;
if (!$id) {
    header('Location: /PROYECTO2/usuarios/listar.php');
    exit;
}

// Opcional: evitar que un usuario se elimine a sí mismo
if ($_SESSION['usuario'] == $id) {
    header('Location: /PROYECTO2/usuarios/listar.php');
    exit;
}

$stmt = $pdo->prepare("DELETE FROM usuario WHERE id_usuario = ?");
$stmt->execute([$id]);

header('Location: /PROYECTO2/usuarios/listar.php');
exit;