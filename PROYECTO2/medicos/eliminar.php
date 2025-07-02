<?php
require_once('../config_db.php');
session_start();
if(!isset($_SESSION['usuario'])) {
    header('Location: ../login.php');
    exit;
}
$id = $_GET['id'] ?? null;
if (!$id) {
    header('Location: /PROYECTO2/medicos/listar.php');
    exit;
}

try {
    $stmt = $pdo->prepare("DELETE FROM medico WHERE id_medico = ?");
    $stmt->execute([$id]);
    header('Location: /PROYECTO2/medicos/listar.php?msg=Médico eliminado correctamente');
    exit;
} catch (PDOException $e) {
    if ($e->getCode() == 23000) {
        header('Location: /PROYECTO2/medicos/listar.php?error=No puedes eliminar este médico porque tiene datos asociados (historiales, órdenes, etc).');
        exit;
    }
    throw $e;
}
?>