<?php
include '../../config_db.php';
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $stmt = $pdo->prepare("DELETE FROM servicio_especialidad WHERE id_servicio_especialidad = ?");
    $stmt->execute([$id]);
}
header("Location: /PROYECTO2/servicios/especialidad/especialidad_listar.php");
exit;