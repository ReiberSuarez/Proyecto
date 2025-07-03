<?php
require_once('../../config_db.php');
session_start();
if(!isset($_SESSION['usuario'])) {
    header('Location: ../../login.php');
    exit;
}
$id_orden = $_GET['id'] ?? '';
if (!$id_orden) {
    header("Location: /PROYECTO2/servicios/ordenes/orden_listar.php");
    exit;
}

// Obtener listas para selects
$pacientes = $pdo->query("SELECT id_paciente, CONCAT(nombres, ' ', apellidos) AS nombre FROM paciente ORDER BY nombres, apellidos")->fetchAll(PDO::FETCH_ASSOC);
$medicos = $pdo->query("SELECT id_medico, CONCAT(nombres, ' ', apellidos) AS nombre FROM medico ORDER BY nombres, apellidos")->fetchAll(PDO::FETCH_ASSOC);
$servicios = $pdo->query("SELECT id_servicio, nombre_servicio FROM servicio ORDER BY nombre_servicio")->fetchAll(PDO::FETCH_ASSOC);

// Obtener datos de la orden
$stmt = $pdo->prepare("SELECT * FROM orden WHERE id_orden = ?");
$stmt->execute([$id_orden]);
$orden = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$orden) {
    header("Location: /PROYECTO2/servicios/ordenes/orden_listar.php");
    exit;
}

$error = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_paciente = $_POST['id_paciente'] ?? '';
    $id_medico = $_POST['id_medico'] ?? '';
    $id_servicio = $_POST['id_servicio'] ?? '';
    $descripcion = trim($_POST['descripcion'] ?? '');
    $estatus = $_POST['estatus'] ?? 'Pendiente';

    if (!$id_paciente || !$id_medico || !$id_servicio) {
        $error = "Todos los campos son obligatorios.";
    } else {
        $stmt = $pdo->prepare("UPDATE orden SET id_paciente=?, id_medico=?, id_servicio=?, descripcion=?, estatus=? WHERE id_orden=?");
        $stmt->execute([$id_paciente, $id_medico, $id_servicio, $descripcion, $estatus, $id_orden]);
        header("Location: /PROYECTO2/servicios/ordenes/orden_listar.php?msg=Orden actualizada correctamente");
        exit;
    }
}

include ('../../templates/header.php');
?>

<style>
.center-card {
    min-height: 85vh;
    display: flex;
    align-items: center;
    justify-content: center;
}
.card-form {
    min-width: 340px;
    max-width: 540px;
    margin: auto;
    box-shadow: 0 8px 32px rgba(0,0,0,0.13);
    border-radius: 18px;
    border: 0;
}
.card-header.bg-primary {
    background: #1677ff !important;
    font-size: 1.7rem;
    font-weight: 700;
    border-radius: 18px 18px 0 0;
    letter-spacing: .5px;
}
.btn-form {
    font-size: 0.96em;
    padding: 0.42em 1.4em;
    border-radius: 8px;
    font-weight: 500;
    letter-spacing: .3px;
}
@media (max-width: 600px) {
    .card-form { max-width: 98vw; }
}
</style>

<div class="center-card">
    <div class="card card-form">
        <div class="card-header bg-primary text-white text-center">
            Editar Orden / Remisión
        </div>
        <form method="post" class="card-body">
            <?php if($error): ?>
                <div class="alert alert-danger"><?=$error?></div>
            <?php endif; ?>
            <div class="row mb-3">
                <div class="col-md-6 mb-3 mb-md-0">
                    <label for="id_paciente" class="form-label">Paciente</label>
                    <select name="id_paciente" id="id_paciente" class="form-select" required>
                        <option value="">Seleccione un paciente</option>
                        <?php foreach($pacientes as $p): ?>
                            <option value="<?=$p['id_paciente']?>" <?=$orden['id_paciente']==$p['id_paciente']?'selected':''?>><?=htmlspecialchars($p['nombre'])?></option>
                        <?php endforeach;?>
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="id_medico" class="form-label">Médico</label>
                    <select name="id_medico" id="id_medico" class="form-select" required>
                        <option value="">Seleccione un médico</option>
                        <?php foreach($medicos as $m): ?>
                            <option value="<?=$m['id_medico']?>" <?=$orden['id_medico']==$m['id_medico']?'selected':''?>><?=htmlspecialchars($m['nombre'])?></option>
                        <?php endforeach;?>
                    </select>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-6 mb-3 mb-md-0">
                    <label for="id_servicio" class="form-label">Servicio</label>
                    <select name="id_servicio" id="id_servicio" class="form-select" required>
                        <option value="">Seleccione un servicio</option>
                        <?php foreach($servicios as $s): ?>
                            <option value="<?=$s['id_servicio']?>" <?=$orden['id_servicio']==$s['id_servicio']?'selected':''?>><?=htmlspecialchars($s['nombre_servicio'])?></option>
                        <?php endforeach;?>
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="estatus" class="form-label">Estatus</label>
                    <select name="estatus" id="estatus" class="form-select">
                        <option value="Pendiente" <?=$orden['estatus']=='Pendiente'?'selected':''?>>Pendiente</option>
                        <option value="Realizada" <?=$orden['estatus']=='Realizada'?'selected':''?>>Realizada</option>
                        <option value="Cancelada" <?=$orden['estatus']=='Cancelada'?'selected':''?>>Cancelada</option>
                    </select>
                </div>
            </div>
            <div class="mb-3">
                <label for="descripcion" class="form-label">Descripción</label>
                <textarea name="descripcion" id="descripcion" class="form-control" rows="2"><?=htmlspecialchars($orden['descripcion'])?></textarea>
            </div>
            <div class="row mt-4">
                <div class="col-6 d-grid">
                    <a href="/PROYECTO2/servicios/ordenes/orden_listar.php" class="btn btn-secondary btn-form">
                        <i class="bi bi-arrow-left-circle"></i> Volver
                    </a>
                </div>
                <div class="col-6 d-grid">
                    <button type="submit" class="btn btn-success btn-form">
                        <i class="bi bi-floppy"></i> Actualizar Orden
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
<?php include ('../../templates/footer.php'); ?>