<?php
require_once('../config_db.php');
if (session_status() === PHP_SESSION_NONE) session_start();
if(!isset($_SESSION['usuario'])) {
    header('Location: ../login.php');
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre_servicio = trim($_POST['nombre_servicio'] ?? '');
    $descripcion = trim($_POST['descripcion'] ?? '');

    if (!$nombre_servicio) {
        $error = "El nombre del servicio es obligatorio.";
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO servicio (nombre_servicio, descripcion) VALUES (?, ?)");
            $stmt->execute([$nombre_servicio, $descripcion]);
            header('Location: /PROYECTO2/servicios/listar.php');
            exit;
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                $error = "Ya existe un servicio con ese nombre.";
            } else {
                $error = "Error al crear servicio: " . $e->getMessage();
            }
        }
    }
}
include('../templates/header.php');
?>
<h2>Nuevo Servicio</h2>
<?php if($error): ?>
    <div class="alert alert-danger"><?=$error?></div>
<?php endif; ?>
<form method="post">
    <div class="mb-3">
        <label>Nombre del Servicio *</label>
        <input type="text" name="nombre_servicio" class="form-control" required value="<?=htmlspecialchars($_POST['nombre_servicio'] ?? '')?>">
    </div>
    <div class="mb-3">
        <label>Descripción</label>
        <input type="text" name="descripcion" class="form-control" value="<?=htmlspecialchars($_POST['descripcion'] ?? '')?>">
    </div>
    <button type="submit" class="btn btn-success">Crear</button>
    <a href="/PROYECTO2/servicios/listar.php" class="btn btn-secondary">Cancelar</a>
</form>
<?php include('../templates/footer.php'); ?>