<?php
require_once('../../config_db.php');
session_start();
if(!isset($_SESSION['usuario'])) {
    header('Location: ../../login.php');
    exit;
}

// Recibir IDs
$id_medico = isset($_GET['id_medico']) ? intval($_GET['id_medico']) : 0;
$id_especialidad = isset($_GET['id_especialidad']) ? intval($_GET['id_especialidad']) : 0;

// Traer nombre del médico y especialidad
$stmt = $pdo->prepare("SELECT CONCAT(m.nombres, ' ', m.apellidos) AS medico, e.nombre_especialidad, me.id_medico_especialidad
    FROM medico m
    JOIN medico_especialidad me ON m.id_medico = me.id_medico
    JOIN especialidad e ON e.id_especialidad = me.id_especialidad
    WHERE m.id_medico = ? AND e.id_especialidad = ?");
$stmt->execute([$id_medico, $id_especialidad]);
$data = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$data) {
    include ('../../templates/header.php');
    echo "<div class='alert alert-danger mt-4'>No existe la relación médico-especialidad.</div>";
    include ('../../templates/footer.php');
    exit;
}
$id_medico_especialidad = $data['id_medico_especialidad'];

// Procesar eliminación de horario
if (isset($_GET['del'])) {
    $id_horario = intval($_GET['del']);
    $del = $pdo->prepare("DELETE FROM medico_especialidad_horario WHERE id_medico_especialidad = ? AND id_horario = ?");
    $del->execute([$id_medico_especialidad, $id_horario]);
    header("Location: /PROYECTO2/medicos/especialidad/gestionar_horarios.php?id_medico=$id_medico&id_especialidad=$id_especialidad");
    exit;
}

// Procesar adición de horario
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_horario'])) {
    $id_horario_new = intval($_POST['id_horario']);
    // Validar que no exista ya
    $existe = $pdo->prepare("SELECT COUNT(*) FROM medico_especialidad_horario WHERE id_medico_especialidad=? AND id_horario=?");
    $existe->execute([$id_medico_especialidad, $id_horario_new]);
    if ($existe->fetchColumn() == 0) {
        $add = $pdo->prepare("INSERT INTO medico_especialidad_horario (id_medico_especialidad, id_horario) VALUES (?, ?)");
        $add->execute([$id_medico_especialidad, $id_horario_new]);
    }
    header("Location: /PROYECTO2/medicos/especialidad/gestionar_horarios.php?id_medico=$id_medico&id_especialidad=$id_especialidad");
    exit;
}

// Obtener horarios asignados
$stmt = $pdo->prepare("SELECT h.id_horario, h.descripcion
    FROM medico_especialidad_horario meh
    JOIN horario h ON meh.id_horario = h.id_horario
    WHERE meh.id_medico_especialidad = ?");
$stmt->execute([$id_medico_especialidad]);
$horarios_asignados = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Obtener horarios disponibles para asignar
$stmt = $pdo->prepare("SELECT id_horario, descripcion FROM horario WHERE id_horario NOT IN (
    SELECT id_horario FROM medico_especialidad_horario WHERE id_medico_especialidad = ?
) ORDER BY descripcion");
$stmt->execute([$id_medico_especialidad]);
$horarios_disponibles = $stmt->fetchAll(PDO::FETCH_ASSOC);

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
    min-width: 350px;
    max-width: 480px;
    margin: auto;
    box-shadow: 0 8px 32px rgba(0,0,0,0.13);
    border-radius: 18px;
    border: 0;
}
.card-header.bg-primary {
    background: #1677ff !important;
    font-size: 2rem;
    font-weight: 700;
    border-radius: 18px 18px 0 0;
    letter-spacing: .5px;
}
.badge-horario {
    background: #f0f4ff;
    color: #1677ff;
    font-size: 1.09em;
    padding: 0.55em 1.2em;
    border-radius: 2em;
    margin-right: .4em;
    margin-bottom: .6em;
    display: inline-flex;
    align-items: center;
    border: 1px solid #e3eaff;
    box-shadow: 0 0 2px #b6cefa40;
}
.badge-horario .btn {
    margin-left: .5em;
    padding: 0.15em 0.45em;
    border-radius: 50%;
    font-size: 1.1em;
}
.section-title {
    font-weight: 700;
    margin-bottom: .4em;
    font-size: 1.08em;
    color: #222;
}
.bg-card {
    background: #fff;
}
.btn-volver-grande {
    font-size: 1.15em;
    padding: 0.6em 2.5em;
    margin-top: 2.5em;
    border-radius: 2.2em;
    display: block;
    margin-left: auto;
    margin-right: auto;
    font-weight: 500;
    box-shadow: 0 2px 8px #b6cefa25;
    letter-spacing: .5px;
}
@media (max-width: 600px) {
    .card-form { max-width: 98vw; }
}
</style>

<div class="center-card">
    <div class="card card-form bg-card">
        <div class="card-header bg-primary text-white text-center">
            Horarios para <?=htmlspecialchars($data['medico'])?> <br> <span style="font-size:.95em;font-weight:400"><?=htmlspecialchars($data['nombre_especialidad'])?></span>
        </div>
        <div class="card-body">

            <div class="mb-4">
                <div class="section-title">Agregar horario:</div>
                <?php if(count($horarios_disponibles)): ?>
                    <form method="post" class="d-flex gap-2 flex-wrap" style="max-width:360px;">
                        <select name="id_horario" class="form-select" required style="flex:2">
                            <option value="">Seleccione...</option>
                            <?php foreach($horarios_disponibles as $h): ?>
                                <option value="<?=$h['id_horario']?>"><?=htmlspecialchars($h['descripcion'])?></option>
                            <?php endforeach; ?>
                        </select>
                        <button type="submit" class="btn btn-primary d-flex align-items-center gap-1" style="flex:1">
                            <i class="bi bi-plus-circle"></i> Agregar
                        </button>
                    </form>
                <?php else: ?>
                    <span class="text-muted">No hay horarios disponibles para agregar.</span>
                <?php endif; ?>
            </div>

            <hr class="my-3">

            <div class="mb-2">
                <div class="section-title">Horarios asignados:</div>
                <?php if(count($horarios_asignados)): ?>
                    <div>
                    <?php foreach($horarios_asignados as $h): ?>
                        <span class="badge-horario">
                            <?=htmlspecialchars($h['descripcion'])?>
                            <a href="?id_medico=<?=$id_medico?>&id_especialidad=<?=$id_especialidad?>&del=<?=$h['id_horario']?>"
                                class="btn btn-link text-danger"
                                onclick="return confirm('¿Quitar este horario?')"
                                title="Eliminar horario">
                                <i class="bi bi-trash-fill"></i>
                            </a>
                        </span>
                    <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <span class="text-muted">Sin horarios asignados.</span>
                <?php endif; ?>
            </div>

            <a href="listar.php" class="btn btn-outline-secondary btn-volver-grande mt-4">
                <i class="bi bi-arrow-left-circle"></i> Volver
            </a>
        </div>
    </div>
</div>
<?php include ('../../templates/footer.php'); ?>