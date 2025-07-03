<?php
require_once('../../config_db.php');
session_start();
if(!isset($_SESSION['usuario'])) {
    header('Location: ../../login.php');
    exit;
}
$id_orden = $_GET['id'] ?? '';
if ($id_orden) {
    $stmt = $pdo->prepare("DELETE FROM orden WHERE id_orden = ?");
    $stmt->execute([$id_orden]);
}
header("Location: /PROYECTO2/servicios/ordenes/orden_listar.php");
exit;