<?php
require_once('../config_db.php');
session_start();
if(!isset($_SESSION['usuario'])) {
    header('Location: ../login.php');
    exit;
}

$id = $_GET['id'] ?? null;
if (!$id) {
    header('Location: /PROYECTO2/pacientes/listar.php');
    exit;
}

try {
    $stmt = $pdo->prepare("DELETE FROM paciente WHERE id_paciente = ?");
    $stmt->execute([$id]);
    header('Location: /PROYECTO2/pacientes/listar.php?msg=Paciente eliminado correctamente');
    exit;
} catch (PDOException $e) {
    if ($e->getCode() == 23000) {
        // Error de restricción de clave foránea
        header('Location: /PROYECTO2/pacientes/listar.php?error=No puedes eliminar este paciente porque tiene historial médico.');
        exit;
    }
    throw $e;
}
?>