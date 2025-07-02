<?php
require_once('../config_db.php');
session_start();
if(!isset($_SESSION['usuario'])) {
    header('Location: ../login.php');
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

    // Validación básica
    if(!$cedula || !$nombres || !$apellidos) {
        $error = "Cédula, nombres y apellidos son obligatorios.";
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO paciente (cedula, nombres, apellidos, fecha_nacimiento, sexo, direccion, telefono, email) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$cedula, $nombres, $apellidos, $fecha_nacimiento, $sexo, $direccion, $telefono, $email]);
            header('Location: /PROYECTO2/pacientes/listar.php');
            exit;
        } catch (PDOException $e) {
            $error = "Error al guardar: " . $e->getMessage();
        }
    }
}
include ('../templates/header.php');
?>
<h2>Agregar Paciente</h2>
<?php if($error): ?>
    <div class="alert alert-danger"><?=$error?></div>
<?php endif; ?>
<form method="post">
    <div class="mb-3">
        <label class="form-label">Cédula *</label>
        <input type="text" name="cedula" class="form-control" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Nombres *</label>
        <input type="text" name="nombres" class="form-control" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Apellidos *</label>
        <input type="text" name="apellidos" class="form-control" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Fecha de nacimiento</label>
        <input type="date" name="fecha_nacimiento" class="form-control">
    </div>
    <div class="mb-3">
        <label class="form-label">Sexo</label>
        <select name="sexo" class="form-select">
            <option value="">Seleccione</option>
            <option value="M">Masculino</option>
            <option value="F">Femenino</option>
            <option value="Otro">Otro</option>
        </select>
    </div>
    <div class="mb-3">
        <label class="form-label">Dirección</label>
        <input type="text" name="direccion" class="form-control">
    </div>
    <div class="mb-3">
        <label class="form-label">Teléfono</label>
        <input type="text" name="telefono" class="form-control">
    </div>
    <div class="mb-3">
        <label class="form-label">Email</label>
        <input type="email" name="email" class="form-control">
    </div>
    <button type="submit" class="btn btn-success">Guardar</button>
    <a href="/PROYECTO2/pacientes/listar.php" class="btn btn-secondary">Cancelar</a>
</form>
<?php include ('../templates/footer.php'); ?>