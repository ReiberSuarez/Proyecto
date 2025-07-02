<?php
require_once('../config_db.php');
session_start();
if(!isset($_SESSION['usuario'])) {
    header('Location: ../login.php');
    exit;
}
include ('../templates/header.php');

// 1. Obtener todos los médicos
$stmt = $pdo->query("SELECT * FROM medico ORDER BY nombres");
$medicos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 2. Obtener especialidades y horarios para todos los médicos (para evitar N+1 queries)
$ids_medicos = array_column($medicos, 'id_medico');
$especialidades_medico = [];
$horarios_medico = [];

if (count($ids_medicos)) {
    // Especialidades por médico
    $in = str_repeat('?,', count($ids_medicos)-1) . '?';
    $stmt2 = $pdo->prepare("
        SELECT me.id_medico, GROUP_CONCAT(e.nombre_especialidad ORDER BY e.nombre_especialidad SEPARATOR ', ') AS especialidades
        FROM medico_especialidad me
        JOIN especialidad e ON me.id_especialidad = e.id_especialidad
        WHERE me.id_medico IN ($in)
        GROUP BY me.id_medico
    ");
    $stmt2->execute($ids_medicos);
    foreach($stmt2 as $row) {
        $especialidades_medico[$row['id_medico']] = $row['especialidades'];
    }

    // Horarios por médico (considerando todas sus especialidades, sin duplicados)
    $stmt3 = $pdo->prepare("
        SELECT m.id_medico, GROUP_CONCAT(DISTINCT h.descripcion ORDER BY h.descripcion SEPARATOR ', ') AS horarios
        FROM medico m
        LEFT JOIN medico_especialidad me ON me.id_medico = m.id_medico
        LEFT JOIN medico_especialidad_horario meh ON me.id_medico_especialidad = meh.id_medico_especialidad
        LEFT JOIN horario h ON meh.id_horario = h.id_horario
        WHERE m.id_medico IN ($in)
        GROUP BY m.id_medico
    ");
    $stmt3->execute($ids_medicos);
    foreach($stmt3 as $row) {
        $horarios_medico[$row['id_medico']] = $row['horarios'];
    }
}
?>

<h2>Listado de Médicos</h2>

<?php if(isset($_GET['error'])): ?>
    <div class="alert alert-danger"><?=htmlspecialchars($_GET['error'])?></div>
<?php endif; ?>
<?php if(isset($_GET['msg'])): ?>
    <div class="alert alert-success"><?=htmlspecialchars($_GET['msg'])?></div>
<?php endif; ?>

<a href="crear.php" class="btn btn-success mb-2">Agregar Médico</a>
<table class="table table-striped table-bordered">
    <thead>
        <tr>
            <th>Cédula</th>
            <th>Nombres</th>
            <th>Apellidos</th>
            <th>Especialidades Asignadas</th>
            <th>Horarios</th>
            <th>Teléfono</th>
            <th>Email</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
    <?php foreach($medicos as $m): ?>
        <tr>
            <td><?=htmlspecialchars($m['cedula'])?></td>
            <td><?=htmlspecialchars($m['nombres'])?></td>
            <td><?=htmlspecialchars($m['apellidos'])?></td>
            <td><?=htmlspecialchars($especialidades_medico[$m['id_medico']] ?? '-')?></td>
            <td>
                <?= $horarios_medico[$m['id_medico']] 
                    ? htmlspecialchars($horarios_medico[$m['id_medico']])
                    : '<span class="text-muted">Sin horario</span>' ?>
            </td>
            <td><?=htmlspecialchars($m['telefono'])?></td>
            <td><?=htmlspecialchars($m['email'])?></td>
            <td>
                <a href="/PROYECTO2/medicos/editar.php?id=<?=$m['id_medico']?>" class="btn btn-sm btn-primary">Editar</a>
                <a href="/PROYECTO2/medicos/eliminar.php?id=<?=$m['id_medico']?>" class="btn btn-sm btn-danger" onclick="return confirm('¿Seguro que deseas eliminar este médico?')">Eliminar</a>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
<?php include ('../templates/footer.php'); ?>