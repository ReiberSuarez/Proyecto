<?php
require_once('../config_db.php');
if (session_status() === PHP_SESSION_NONE) session_start();
if(!isset($_SESSION['usuario'])) {
    header('Location: ../login.php');
    exit;
}

$id = isset($_GET['id']) && is_numeric($_GET['id']) ? (int)$_GET['id'] : null;
if (!$id) {
    header('Location: /PROYECTO2/servicios/listar.php');
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM servicio WHERE id_servicio = ?");
$stmt->execute([$id]);
$servicio = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$servicio) {
    header('Location: /PROYECTO2/servicios/listar.php');
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
            $stmt = $pdo->prepare("UPDATE servicio SET nombre_servicio=?, descripcion=? WHERE id_servicio=?");
            $stmt->execute([$nombre_servicio, $descripcion, $id]);
            header('Location: /PROYECTO2/servicios/listar.php');
            exit;
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                $error = "Ya existe un servicio con ese nombre.";
            } else {
                $error = "Error al actualizar servicio: " . $e->getMessage();
            }
        }
    }
}
include('../templates/header.php');
?>
<h2>Editar Servicio</h2>
<?php if($error): ?>
    <div class="alert alert-danger"><?=$error?></div>
<?php endif; ?>
<form method="post">
    <div class="mb-3">
        <label>Nombre del Servicio *</label>
        <input type="text" name="nombre_servicio" class="form-control" required value="<?=htmlspecialchars($_POST['nombre_servicio'] ?? $servicio['nombre_servicio'])?>">
    </div>
    <div class="mb-3">
        <label>Descripción</label>
        <input type="text" name="descripcion" class="form-control" value="<?=htmlspecialchars($_POST['descripcion'] ?? $servicio['descripcion'])?>">
    </div>
    <button type="submit" class="btn btn-primary">Actualizar</button>
    <a href="/PROYECTO2/servicios/listar.php" class="btn btn-secondary">Cancelar</a>
</form>
<?php include('../templates/footer.php'); ?>