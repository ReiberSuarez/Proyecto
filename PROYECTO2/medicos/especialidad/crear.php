<?php
require_once('../../config_db.php');
session_start();
if(!isset($_SESSION['usuario'])) {
    header('Location: ../../login.php');
    exit;
}

// Obtener médicos y especialidades
$medicos = $pdo->query("SELECT id_medico, CONCAT(nombres, ' ', apellidos) AS nombre FROM medico ORDER BY nombres, apellidos")->fetchAll(PDO::FETCH_ASSOC);
$especialidades = $pdo->query("SELECT id_especialidad, nombre_especialidad FROM especialidad ORDER BY nombre_especialidad")->fetchAll(PDO::FETCH_ASSOC);

$error = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_medico = $_POST['id_medico'] ?? '';
    $id_especialidad = $_POST['id_especialidad'] ?? '';

    // Validar que no exista ya la relación
    $check = $pdo->prepare("SELECT COUNT(*) FROM medico_especialidad WHERE id_medico = ? AND id_especialidad = ?");
    $check->execute([$id_medico, $id_especialidad]);
    if ($check->fetchColumn() > 0) {
        $error = "¡Este médico ya tiene asignada esta especialidad!";
    } else {
        $stmt = $pdo->prepare("INSERT INTO medico_especialidad (id_medico, id_especialidad) VALUES (?, ?)");
        $stmt->execute([$id_medico, $id_especialidad]);
        header("Location: /PROYECTO2/medicos/especialidad/listar.php?msg=Especialidad asignada correctamente");
        exit;
    }
}

include ('../../templates/header.php');
?>

<div class="container mt-4">
    <h2>Asignar Especialidad a Médico</h2>
    <?php if($error): ?>
        <div class="alert alert-danger"><?=$error?></div>
    <?php endif; ?>
    <form method="post">
        <div class="mb-3">
            <label for="id_medico" class="form-label">Médico</label>
            <select name="id_medico" id="id_medico" class="form-select" required>
                <option value="">Seleccione un médico</option>
                <?php foreach($medicos as $m): ?>
                    <option value="<?=$m['id_medico']?>" <?=(@$_POST['id_medico']==$m['id_medico'])?'selected':''?>><?=htmlspecialchars($m['nombre'])?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="mb-3">
            <label for="id_especialidad" class="form-label">Especialidad</label>
            <select name="id_especialidad" id="id_especialidad" class="form-select" required>
                <option value="">Seleccione una especialidad</option>
                <?php foreach($especialidades as $e): ?>
                    <option value="<?=$e['id_especialidad']?>" <?=(@$_POST['id_especialidad']==$e['id_especialidad'])?'selected':''?>><?=htmlspecialchars($e['nombre_especialidad'])?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Asignar</button>
        <a href="/PROYECTO2/medicos/especialidad/listar.php" class="btn btn-secondary">Volver</a>
    </form>
</div>
<?php include ('../../templates/footer.php'); ?>