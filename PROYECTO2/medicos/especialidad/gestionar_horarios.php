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
    echo "<div class='alert alert-danger'>No existe la relación médico-especialidad.</div>";
    include ('../../templates/footer.php');
    exit;
}
$id_medico_especialidad = $data['id_medico_especialidad'];

// Procesar eliminación de horario
if (isset($_GET['del'])) {
    $id_horario = intval($_GET['del']);
    $del = $pdo->prepare("DELETE FROM medico_especialidad_horario WHERE id_medico_especialidad = ? AND id_horario = ?");
    $del->execute([$id_medico_especialidad, $id_horario]);
    header("Location: gestionar_horarios.php?id_medico=$id_medico&id_especialidad=$id_especialidad");
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
    header("Location: gestionar_horarios.php?id_medico=$id_medico&id_especialidad=$id_especialidad");
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

<div class="container mt-4">
    <h2>Horarios para <?=htmlspecialchars($data['medico'])?> - <?=htmlspecialchars($data['nombre_especialidad'])?></h2>
    <a href="listar.php" class="btn btn-secondary mb-3">&laquo; Volver</a>
    <div class="mb-3">
        <strong>Horarios asignados:</strong>
        <?php if(count($horarios_asignados)): ?>
            <ul>
            <?php foreach($horarios_asignados as $h): ?>
                <li>
                    <?=htmlspecialchars($h['descripcion'])?>
                    <a href="?id_medico=<?=$id_medico?>&id_especialidad=<?=$id_especialidad?>&del=<?=$h['id_horario']?>"
                        class="btn btn-danger btn-sm ms-2"
                        onclick="return confirm('¿Quitar este horario?')"
                        title="Eliminar horario">
                        Quitar
                    </a>
                </li>
            <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <span class="text-muted">Sin horarios asignados.</span>
        <?php endif; ?>
    </div>
    <div>
        <strong>Agregar horario:</strong>
        <?php if(count($horarios_disponibles)): ?>
            <form method="post" class="d-flex" style="max-width:350px;">
                <select name="id_horario" class="form-select me-2" required>
                    <option value="">Seleccione...</option>
                    <?php foreach($horarios_disponibles as $h): ?>
                        <option value="<?=$h['id_horario']?>"><?=htmlspecialchars($h['descripcion'])?></option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" class="btn btn-primary">Agregar</button>
            </form>
        <?php else: ?>
            <span class="text-muted">No hay horarios disponibles para agregar.</span>
        <?php endif; ?>
    </div>
</div>
<?php include ('../../templates/footer.php'); ?>