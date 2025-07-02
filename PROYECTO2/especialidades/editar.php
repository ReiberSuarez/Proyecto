<?php
require_once('../config_db.php');
if (session_status() === PHP_SESSION_NONE) session_start();
if(!isset($_SESSION['usuario'])) {
    header('Location: ../login.php');
    exit;
}

$id = isset($_GET['id']) && is_numeric($_GET['id']) ? (int)$_GET['id'] : null;
if (!$id) {
    header('Location: /PROYECTO2/especialidades/listar.php');
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM especialidad WHERE id_especialidad = ?");
$stmt->execute([$id]);
$especialidad = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$especialidad) {
    header('Location: /PROYECTO2/especialidades/listar.php');
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre_especialidad = trim($_POST['nombre_especialidad'] ?? '');
    $descripcion = trim($_POST['descripcion'] ?? '');

    if (!$nombre_especialidad) {
        $error = "El nombre de la especialidad es obligatorio.";
    } else {
        try {
            $stmt = $pdo->prepare("UPDATE especialidad SET nombre_especialidad=?, descripcion=? WHERE id_especialidad=?");
            $stmt->execute([$nombre_especialidad, $descripcion, $id]);
            header('Location: /PROYECTO2/especialidades/listar.php');
            exit;
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                $error = "Ya existe una especialidad con ese nombre.";
            } else {
                $error = "Error al actualizar especialidad: " . $e->getMessage();
            }
        }
    }
}
include('../templates/header.php');
?>
<h2>Editar Especialidad</h2>
<?php if($error): ?>
    <div class="alert alert-danger"><?=$error?></div>
<?php endif; ?>
<form method="post">
    <div class="mb-3">
        <label>Nombre de la Especialidad *</label>
        <input type="text" name="nombre_especialidad" class="form-control" required value="<?=htmlspecialchars($_POST['nombre_especialidad'] ?? $especialidad['nombre_especialidad'])?>">
    </div>
    <div class="mb-3">
        <label>Descripción</label>
        <input type="text" name="descripcion" class="form-control" value="<?=htmlspecialchars($_POST['descripcion'] ?? $especialidad['descripcion'])?>">
    </div>
    <button type="submit" class="btn btn-primary">Actualizar</button>
    <a href="/PROYECTO2/especialidades/listar.php" class="btn btn-secondary">Cancelar</a>
</form>
<?php include('../templates/footer.php'); ?>