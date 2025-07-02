<?php
require_once('../config_db.php');
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if(!isset($_SESSION['usuario'])) {
    header('Location: ../login.php');
    exit;
}
$id = isset($_GET['id']) && is_numeric($_GET['id']) ? (int)$_GET['id'] : null;
if (!$id) {
    header('Location: /PROYECTO2/medicos/listar.php');
    exit;
}

// Obtener datos del médico
$stmt = $pdo->prepare("SELECT * FROM medico WHERE id_medico = ?");
$stmt->execute([$id]);
$medico = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$medico) {
    header('Location: /PROYECTO2/medicos/listar.php');
    exit;
}

// Obtener especialidades disponibles
try {
    $especialidades = $pdo->query("SELECT * FROM especialidad ORDER BY nombre_especialidad")->fetchAll(PDO::FETCH_ASSOC);
} catch(Exception $e) {
    $especialidades = [];
    $error = "No se pudo cargar la lista de especialidades: " . $e->getMessage();
}

// Especialidades actuales del médico (IDs)
$stmt2 = $pdo->prepare("SELECT id_especialidad FROM medico_especialidad WHERE id_medico = ?");
$stmt2->execute([$id]);
$especialidades_medico = $stmt2->fetchAll(PDO::FETCH_COLUMN);
// Aseguramos enteros para comparación
$especialidades_medico_int = array_map('intval', $especialidades_medico);

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
    $fecha_contratacion = $_POST['fecha_contratacion'] ?? '';
    $estatus = $_POST['estatus'] ?? '';
    $especialidades_seleccionadas = $_POST['especialidades'] ?? [];
    $especialidades_seleccionadas = array_map('intval', $especialidades_seleccionadas);

    if(!$cedula || !$nombres || !$apellidos) {
        $error = "Cédula, nombres y apellidos son obligatorios.";
    } else {
        try {
            $stmt = $pdo->prepare("UPDATE medico SET cedula=?, nombres=?, apellidos=?, fecha_nacimiento=?, sexo=?, direccion=?, telefono=?, email=?, fecha_contratacion=?, estatus=? WHERE id_medico=?");
            $stmt->execute([$cedula, $nombres, $apellidos, $fecha_nacimiento, $sexo, $direccion, $telefono, $email, $fecha_contratacion, $estatus, $id]);

            // Obtener especialidades previas del médico
            $stmt2 = $pdo->prepare("SELECT id_especialidad, id_medico_especialidad FROM medico_especialidad WHERE id_medico = ?");
            $stmt2->execute([$id]);
            $especialidades_previas = $stmt2->fetchAll(PDO::FETCH_ASSOC);
            $prev_ids_especialidad = array_map('intval', array_column($especialidades_previas, 'id_especialidad'));

            // Determinar especialidades eliminadas y agregadas
            $eliminadas = array_diff($prev_ids_especialidad, $especialidades_seleccionadas);
            $agregadas = array_diff($especialidades_seleccionadas, $prev_ids_especialidad);

            // Guardar id_medico_especialidad para eliminar horarios
            $id_eliminadas = [];
            foreach ($especialidades_previas as $esp) {
                if (in_array((int)$esp['id_especialidad'], $eliminadas)) {
                    $id_eliminadas[] = $esp['id_medico_especialidad'];
                }
            }

            // Eliminar horarios solo de especialidades eliminadas
            if (!empty($id_eliminadas)) {
                $in = implode(',', array_fill(0, count($id_eliminadas), '?'));
                $pdo->prepare("DELETE FROM medico_especialidad_horario WHERE id_medico_especialidad IN ($in)")->execute($id_eliminadas);
            }

            // Eliminar solo las especialidades eliminadas
            if (!empty($eliminadas)) {
                $in = implode(',', array_fill(0, count($eliminadas), '?'));
                $params = array_values($eliminadas);
                $params[] = $id; // id_medico al final para el WHERE
                $pdo->prepare("DELETE FROM medico_especialidad WHERE id_especialidad IN ($in) AND id_medico = ?")->execute($params);
            }

            // Insertar solo especialidades nuevas
            foreach($agregadas as $id_especialidad) {
                $pdo->prepare("INSERT INTO medico_especialidad (id_medico, id_especialidad) VALUES (?, ?)")->execute([$id, $id_especialidad]);
            }

            header('Location: /PROYECTO2/medicos/listar.php');
            exit;
        } catch (PDOException $e) {
            $error = "Error al actualizar: " . $e->getMessage();
        }
    }
}
include ('../templates/header.php');
?>
<h2>Editar Médico</h2>
<?php if($error): ?>
    <div class="alert alert-danger"><?=$error?></div>
<?php endif; ?>
<?php if (empty($especialidades)): ?>
    <div class="alert alert-warning">No hay especialidades registradas. Por favor, agregue al menos una especialidad antes de editar médicos.</div>
<?php endif; ?>
<form method="post">
    <div class="mb-3">
        <label class="form-label">Cédula *</label>
        <input type="text" name="cedula" class="form-control" value="<?=htmlspecialchars($medico['cedula'])?>" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Nombres *</label>
        <input type="text" name="nombres" class="form-control" value="<?=htmlspecialchars($medico['nombres'])?>" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Apellidos *</label>
        <input type="text" name="apellidos" class="form-control" value="<?=htmlspecialchars($medico['apellidos'])?>" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Fecha de nacimiento</label>
        <input type="date" name="fecha_nacimiento" class="form-control" value="<?=htmlspecialchars($medico['fecha_nacimiento'])?>">
    </div>
    <div class="mb-3">
        <label class="form-label">Sexo</label>
        <select name="sexo" class="form-select">
            <option value="">Seleccione</option>
            <option value="M" <?=$medico['sexo']=='M'?'selected':''?>>Masculino</option>
            <option value="F" <?=$medico['sexo']=='F'?'selected':''?>>Femenino</option>
            <option value="Otro" <?=$medico['sexo']=='Otro'?'selected':''?>>Otro</option>
        </select>
    </div>
    <div class="mb-3">
        <label class="form-label">Dirección</label>
        <input type="text" name="direccion" class="form-control" value="<?=htmlspecialchars($medico['direccion'])?>">
    </div>
    <div class="mb-3">
        <label class="form-label">Teléfono</label>
        <input type="text" name="telefono" class="form-control" value="<?=htmlspecialchars($medico['telefono'])?>">
    </div>
    <div class="mb-3">
        <label class="form-label">Email</label>
        <input type="email" name="email" class="form-control" value="<?=htmlspecialchars($medico['email'])?>">
    </div>
    <div class="mb-3">
        <label class="form-label">Fecha de contratación</label>
        <input type="date" name="fecha_contratacion" class="form-control" value="<?=htmlspecialchars($medico['fecha_contratacion'])?>">
    </div>
    <div class="mb-3">
        <label class="form-label">Estatus</label>
        <select name="estatus" class="form-select">
            <option value="">Seleccione</option>
            <option value="Activo" <?=$medico['estatus']=='Activo'?'selected':''?>>Activo</option>
            <option value="Inactivo" <?=$medico['estatus']=='Inactivo'?'selected':''?>>Inactivo</option>
        </select>
    </div>
    <div class="mb-3">
        <label class="form-label">Especialidades</label>
        <select name="especialidades[]" class="form-select" multiple>
            <?php foreach($especialidades as $esp): ?>
                <option value="<?=$esp['id_especialidad']?>" <?=in_array((int)$esp['id_especialidad'],$especialidades_medico_int)?'selected':''?>><?=htmlspecialchars($esp['nombre_especialidad'])?></option>
            <?php endforeach; ?>
        </select>
        <small class="form-text text-muted">Mantén presionada la tecla Ctrl (Windows) o Cmd (Mac) para seleccionar varias.</small>
    </div>
    <button type="submit" class="btn btn-primary">Actualizar</button>
    <a href="/PROYECTO2/medicos/listar.php" class="btn btn-secondary">Cancelar</a>
</form>
<script>
document.querySelector('form').addEventListener('submit', function(e) {
    const prev = <?=json_encode($especialidades_medico_int)?>;
    const selected = Array.from(document.querySelector('[name="especialidades[]"]').selectedOptions).map(opt => parseInt(opt.value));
    const eliminadas = prev.filter(x => !selected.includes(x));
    if(eliminadas.length > 0) {
        if(!confirm("¡Atención!\nSi eliminas una especialidad, se borrarán también los horarios de esa especialidad para este médico. ¿Deseas continuar?")) {
            e.preventDefault();
        }
    }
});
</script>
<?php include ('../templates/footer.php'); ?>