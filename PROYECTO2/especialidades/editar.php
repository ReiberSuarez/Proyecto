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

<style>
    .center-card {
        min-height: 80vh;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .card-edit {
        min-width: 340px;
        max-width: 420px;
        margin: auto;
        box-shadow: 0 6px 24px rgba(0,0,0,0.12);
        border-radius: 18px;
    }
</style>

<div class="center-card">
    <div class="card card-edit">
        <div class="card-header bg-primary text-white text-center">
            <h3 class="mb-0"><i class="bi bi-journal-medical"></i> Editar Especialidad</h3>
        </div>
        <form method="post" class="card-body">
            <?php if($error): ?>
                <div class="alert alert-danger"><?=$error?></div>
            <?php endif; ?>
            <div class="mb-3">
                <label class="form-label">Nombre de la Especialidad *</label>
                <input type="text" name="nombre_especialidad" class="form-control" required
                       value="<?=htmlspecialchars($_POST['nombre_especialidad'] ?? $especialidad['nombre_especialidad'])?>">
            </div>
            <div class="mb-3">
                <label class="form-label">Descripción</label>
                <input type="text" name="descripcion" class="form-control"
                       value="<?=htmlspecialchars($_POST['descripcion'] ?? $especialidad['descripcion'])?>">
            </div>
            <div class="d-flex justify-content-between mt-4">
                <a href="/PROYECTO2/especialidades/listar.php" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left-circle"></i> Cancelar
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-floppy"></i> Actualizar
                </button>
            </div>
        </form>
    </div>
</div>
<?php include('../templates/footer.php'); ?>