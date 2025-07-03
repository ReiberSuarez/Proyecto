<?php
require_once('../../config_db.php');
session_start();
if(!isset($_SESSION['usuario'])) {
    header('Location: ../../login.php');
    exit;
}

// Obtener servicios y especialidades
$servicios = $pdo->query("SELECT id_servicio, nombre_servicio FROM servicio ORDER BY nombre_servicio")->fetchAll(PDO::FETCH_ASSOC);
$especialidades = $pdo->query("SELECT id_especialidad, nombre_especialidad FROM especialidad ORDER BY nombre_especialidad")->fetchAll(PDO::FETCH_ASSOC);

$error = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_servicio = $_POST['id_servicio'] ?? '';
    $especialidades_seleccionadas = $_POST['especialidades'] ?? [];

    if (!$id_servicio || empty($especialidades_seleccionadas)) {
        $error = "Debe seleccionar un servicio y al menos una especialidad.";
    } else {
        // Eliminar relaciones previas
        $del = $pdo->prepare("DELETE FROM servicio_especialidad WHERE id_servicio = ?");
        $del->execute([$id_servicio]);
        // Insertar nuevas relaciones
        $ins = $pdo->prepare("INSERT INTO servicio_especialidad (id_servicio, id_especialidad) VALUES (?, ?)");
        foreach ($especialidades_seleccionadas as $id_especialidad) {
            $ins->execute([$id_servicio, $id_especialidad]);
        }
        header("Location: /PROYECTO2/servicios/especialidad/especialidad_listar.php?msg=Especialidades asignadas correctamente");
        exit;
    }
}

include ('../../templates/header.php');
?>

<style>
.center-card {
    min-height: 80vh;
    display: flex;
    align-items: center;
    justify-content: center;
}
.card-form {
    min-width: 340px;
    max-width: 420px;
    margin: auto;
    box-shadow: 0 6px 24px rgba(0,0,0,0.12);
    border-radius: 18px;
}
.serv-list label {
    font-size: 1.07rem;
    margin-bottom: .35rem;
}
.form-check-input {
    transform: scale(1.18);
    margin-right: 8px;
}
</style>

<div class="center-card">
    <div class="card card-form">
        <div class="card-header bg-primary text-white text-center">
            <h3 class="mb-0">Asignar Especialidades a Servicio</h3>
        </div>
        <form method="post" class="card-body">
            <?php if($error): ?>
                <div class="alert alert-danger"><?=$error?></div>
            <?php endif; ?>
            <div class="mb-3">
                <label for="id_servicio" class="form-label">Servicio</label>
                <select name="id_servicio" id="id_servicio" class="form-select" required>
                    <option value="">Seleccione un servicio</option>
                    <?php foreach($servicios as $s): ?>
                        <option value="<?=$s['id_servicio']?>" <?=(@$_POST['id_servicio']==$s['id_servicio'])?'selected':''?>><?=htmlspecialchars($s['nombre_servicio'])?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3 serv-list">
                <label class="form-label mb-2">Especialidades</label>
                <?php foreach($especialidades as $e): ?>
                    <div class="form-check mb-1">
                        <input class="form-check-input" type="checkbox" name="especialidades[]" value="<?=$e['id_especialidad']?>"
                            id="esp<?=$e['id_especialidad']?>"
                            <?=isset($_POST['especialidades']) && in_array($e['id_especialidad'], $_POST['especialidades']) ? 'checked' : ''?>>
                        <label class="form-check-label" for="esp<?=$e['id_especialidad']?>">
                            <span class="badge bg-info text-dark me-2"><i class="bi bi-person-badge"></i></span>
                            <?=htmlspecialchars($e['nombre_especialidad'])?>
                        </label>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="d-flex justify-content-between mt-4">
                <a href="especialidad_listar.php" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left-circle"></i> Volver
                </a>
                <button type="submit" class="btn btn-success">
                    <i class="bi bi-floppy"></i> Guardar
                </button>
            </div>
        </form>
    </div>
</div>
<?php include ('../../templates/footer.php'); ?>