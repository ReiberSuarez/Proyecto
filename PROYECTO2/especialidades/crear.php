<?php
require_once('../config_db.php');
if (session_status() === PHP_SESSION_NONE) session_start();
if(!isset($_SESSION['usuario'])) {
    header('Location: ../login.php');
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
            $stmt = $pdo->prepare("INSERT INTO especialidad (nombre_especialidad, descripcion) VALUES (?, ?)");
            $stmt->execute([$nombre_especialidad, $descripcion]);
            header('Location: /PROYECTO2/especialidades/listar.php');
            exit;
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                $error = "Ya existe una especialidad con ese nombre.";
            } else {
                $error = "Error al crear especialidad: " . $e->getMessage();
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
    .card-create {
        min-width: 340px;
        max-width: 420px;
        margin: auto;
        box-shadow: 0 6px 24px rgba(0,0,0,0.12);
        border-radius: 18px;
    }
</style>

<div class="center-card">
    <div class="card card-create">
        <div class="card-header bg-success text-white text-center">
            <h3 class="mb-0"><i class="bi bi-journal-plus"></i> Nueva Especialidad</h3>
        </div>
        <form method="post" class="card-body">
            <?php if($error): ?>
                <div class="alert alert-danger"><?=$error?></div>
            <?php endif; ?>
            <div class="mb-3">
                <label class="form-label">Nombre de la Especialidad *</label>
                <input type="text" name="nombre_especialidad" class="form-control" required
                       value="<?=htmlspecialchars($_POST['nombre_especialidad'] ?? '')?>">
            </div>
            <div class="mb-3">
                <label class="form-label">Descripción</label>
                <input type="text" name="descripcion" class="form-control"
                       value="<?=htmlspecialchars($_POST['descripcion'] ?? '')?>">
            </div>
            <div class="d-flex justify-content-between mt-4">
                <a href="/PROYECTO2/especialidades/listar.php" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left-circle"></i> Cancelar
                </a>
                <button type="submit" class="btn btn-success">
                    <i class="bi bi-plus-square"></i> Crear
                </button>
            </div>
        </form>
    </div>
</div>
<?php include('../templates/footer.php'); ?>