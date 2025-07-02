<?php
require_once('../../config_db.php');
session_start();
if(!isset($_SESSION['usuario'])) {
    header('Location: ../../login.php');
    exit;
}

$id = $_GET['id'] ?? null;
if ($id) {
    // Eliminar primero en medico_especialidad_horario
$stmt = $pdo->prepare("DELETE FROM medico_especialidad_horario WHERE id_medico_especialidad = ?");
$stmt->execute([$id]);

// Ahora sí elimina en medico_especialidad
$stmt2 = $pdo->prepare("DELETE FROM medico_especialidad WHERE id_medico_especialidad = ?");
$stmt2->execute([$id]);
}

header("Location: /PROYECTO2/medicos/especialidad/listar.php?msg=Relación eliminada correctamente");
exit;
?>