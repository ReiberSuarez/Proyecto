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
$stmt = $pdo->prepare("SELECT * FROM paciente WHERE id_paciente = ?");
$stmt->execute([$id]);
$paciente = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$paciente) {
    header('Location: /PROYECTO2/pacientes/listar.php');
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cedula = $_POST['cedula'] ?? '';
    $nombres = $_POST['nombres'] ?? '';
    $apellidos = $_POST['apellidos'] ?? '';
    $fecha_nacimiento = $_POST['fecha_nacimiento'] ?? '';
    $sexo = $_POST['sexo'] ?? '';
    $direccion = $_POST['direccion'] ?? '';
    $telefono = $_POST['telefono'] ?? '';
    $email = $_POST['email'] ?? '';

    if(!$cedula || !$nombres || !$apellidos) {
        $error = "Cédula, nombres y apellidos son obligatorios.";
    } else {
        try {
            $stmt = $pdo->prepare("UPDATE paciente SET cedula=?, nombres=?, apellidos=?, fecha_nacimiento=?, sexo=?, direccion=?, telefono=?, email=? WHERE id_paciente=?");
            $stmt->execute([$cedula, $nombres, $apellidos, $fecha_nacimiento, $sexo, $direccion, $telefono, $email, $id]);
            header('Location: /PROYECTO2/pacientes/listar.php');
            exit;
        } catch (PDOException $e) {
            $error = "Error al actualizar: " . $e->getMessage();
        }
    }
}
include ('../templates/header.php');
?>
<h2>Editar Paciente</h2>
<?php if($error): ?>
    <div class="alert alert-danger"><?=$error?></div>
<?php endif; ?>
<form method="post">
    <div class="mb-3">
        <label class="form-label">Cédula *</label>
        <input type="text" name="cedula" class="form-control" value="<?=htmlspecialchars($paciente['cedula'])?>" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Nombres *</label>
        <input type="text" name="nombres" class="form-control" value="<?=htmlspecialchars($paciente['nombres'])?>" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Apellidos *</label>
        <input type="text" name="apellidos" class="form-control" value="<?=htmlspecialchars($paciente['apellidos'])?>" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Fecha de nacimiento</label>
        <input type="date" name="fecha_nacimiento" class="form-control" value="<?=htmlspecialchars($paciente['fecha_nacimiento'])?>">
    </div>
    <div class="mb-3">
        <label class="form-label">Sexo</label>
        <select name="sexo" class="form-select">
            <option value="">Seleccione</option>
            <option value="M" <?=$paciente['sexo']=='M'?'selected':''?>>Masculino</option>
            <option value="F" <?=$paciente['sexo']=='F'?'selected':''?>>Femenino</option>
            <option value="Otro" <?=$paciente['sexo']=='Otro'?'selected':''?>>Otro</option>
        </select>
    </div>
    <div class="mb-3">
        <label class="form-label">Dirección</label>
        <input type="text" name="direccion" class="form-control" value="<?=htmlspecialchars($paciente['direccion'])?>">
    </div>
    <div class="mb-3">
        <label class="form-label">Teléfono</label>
        <input type="text" name="telefono" class="form-control" value="<?=htmlspecialchars($paciente['telefono'])?>">
    </div>
    <div class="mb-3">
        <label class="form-label">Email</label>
        <input type="email" name="email" class="form-control" value="<?=htmlspecialchars($paciente['email'])?>">
    </div>
    <button type="submit" class="btn btn-primary">Actualizar</button>
    <a href="/PROYECTO2/pacientes/listar.php" class="btn btn-secondary">Cancelar</a>
</form>
<?php include ('../templates/footer.php'); ?>