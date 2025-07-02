<?php
require_once('../../config_db.php');
session_start();
if(!isset($_SESSION['usuario'])) {
    header('Location: ../../login.php');
    exit;
}
include ('../../templates/header.php');

// Consulta para obtener médicos y todas sus especialidades con los IDs de la relación
$sql = "SELECT 
    m.id_medico,
    m.nombres,
    m.apellidos,
    e.nombre_especialidad,
    me.id_medico_especialidad,
    e.id_especialidad
FROM medico_especialidad me
JOIN medico m ON me.id_medico = m.id_medico
JOIN especialidad e ON me.id_especialidad = e.id_especialidad
ORDER BY m.nombres, m.apellidos, e.nombre_especialidad";

$stmt = $pdo->query($sql);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Agrupar por médico
$medicos = [];
foreach ($rows as $row) {
    $id = $row['id_medico'];
    if (!isset($medicos[$id])) {
        $medicos[$id] = [
            'nombre' => $row['nombres'] . ' ' . $row['apellidos'],
            'especialidades' => []
        ];
    }
    $medicos[$id]['especialidades'][] = [
        'nombre' => $row['nombre_especialidad'],
        'id_medico_especialidad' => $row['id_medico_especialidad'],
        'id_especialidad' => $row['id_especialidad']
    ];
}
?>

<div class="container mt-4">
    <h2>Asignaciones Médico-Especialidad</h2>
    <a href="crear.php" class="btn btn-success mb-3">Asignar Especialidad a Médico</a>
    <table class="table table-bordered align-middle">
        <thead>
            <tr>
                <th>Médico</th>
                <th>Especialidades</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach($medicos as $id_medico => $medico): ?>
            <tr>
                <td><?=htmlspecialchars($medico['nombre'])?></td>
                <td>
                    <?php foreach($medico['especialidades'] as $esp): ?>
                        <span class="badge bg-primary me-2 mb-1" style="font-size:1em;">
                            <?=htmlspecialchars($esp['nombre'])?>
                            <!-- Botón eliminar -->
                            <a href="eliminar.php?id=<?=intval($esp['id_medico_especialidad'])?>"
                               style="color:white; margin-left:5px; text-decoration:none; font-weight:bold;"
                               onclick="return confirm('¿Seguro que desea quitar esta especialidad?')"
                               title="Eliminar especialidad">
                               &times;
                            </a>
                            <!-- Botón gestionar horarios -->
                            <a href="gestionar_horarios.php?id_medico=<?=intval($id_medico)?>&id_especialidad=<?=intval($esp['id_especialidad'])?>"
                               style="color:white; margin-left:8px; text-decoration:none;"
                               title="Gestionar horarios">
                                <i class="bi bi-clock"></i>
                            </a>
                        </span>
                    <?php endforeach; ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<!-- Si usas Bootstrap Icons, asegúrate de incluir su CDN en tu header -->
<?php include ('../../templates/footer.php'); ?>