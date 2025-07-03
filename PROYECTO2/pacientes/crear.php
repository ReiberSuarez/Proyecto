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

<style>
    .center-card {
        min-height: 85vh;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .card-formulario {
        min-width: 340px;
        max-width: 540px;
        margin: auto;
        box-shadow: 0 6px 24px rgba(0,0,0,0.12);
        border-radius: 18px;
    }
</style>

<div class="center-card">
    <div class="card card-formulario">
        <div class="card-header bg-primary text-white text-center">
            <h3 class="mb-0">Agregar Paciente</h3>
        </div>
        <form method="post" class="card-body">
            <?php if($error): ?>
                <div class="alert alert-danger"><?=$error?></div>
            <?php endif; ?>
            <div class="row">
                <div class="mb-3 col-md-6">
                    <label class="form-label">Cédula *</label>
                    <input type="text" name="cedula" class="form-control" required>
                </div>
                <div class="mb-3 col-md-6">
                    <label class="form-label">Fecha de nacimiento</label>
                    <input type="date" name="fecha_nacimiento" class="form-control">
                </div>
            </div>
            <div class="row">
                <div class="mb-3 col-md-6">
                    <label class="form-label">Nombres *</label>
                    <input type="text" name="nombres" class="form-control" required>
                </div>
                <div class="mb-3 col-md-6">
                    <label class="form-label">Apellidos *</label>
                    <input type="text" name="apellidos" class="form-control" required>
                </div>
            </div>
            <div class="row">
                <div class="mb-3 col-md-6">
                    <label class="form-label">Sexo</label>
                    <select name="sexo" class="form-select">
                        <option value="">Seleccione</option>
                        <option value="M">Masculino</option>
                        <option value="F">Femenino</option>
                        <option value="Otro">Otro</option>
                    </select>
                </div>
                <div class="mb-3 col-md-6">
                    <label class="form-label">Teléfono</label>
                    <input type="text" name="telefono" class="form-control">
                </div>
            </div>
            <div class="row">
                <div class="mb-3 col-md-6">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control">
                </div>
                <div class="mb-3 col-md-6">
                    <label class="form-label">Dirección</label>
                    <input type="text" name="direccion" class="form-control">
                </div>
            </div>
            <div class="d-flex justify-content-between mt-4">
                <a href="/PROYECTO2/pacientes/listar.php" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left-circle"></i> Cancelar
                </a>
                <button type="submit" class="btn btn-success">
                    <i class="bi bi-floppy"></i> Guardar
                </button>
            </div>
        </form>
    </div>
</div>
<?php include ('../templates/footer.php'); ?>