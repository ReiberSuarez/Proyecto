<?php
require_once('../../config_db.php');
session_start();
if(!isset($_SESSION['usuario'])) {
    header('Location: ../../login.php');
    exit;
}
include ('../../templates/header.php');

// Consulta para obtener servicios y todas sus especialidades con los IDs de la relación
$sql = "SELECT 
    s.id_servicio,
    s.nombre_servicio,
    e.nombre_especialidad,
    se.id_servicio_especialidad,
    e.id_especialidad
FROM servicio_especialidad se
JOIN servicio s ON se.id_servicio = s.id_servicio
JOIN especialidad e ON se.id_especialidad = e.id_especialidad
ORDER BY s.nombre_servicio, e.nombre_especialidad";

$stmt = $pdo->query($sql);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Agrupar por servicio
$servicios = [];
foreach ($rows as $row) {
    $id = $row['id_servicio'];
    if (!isset($servicios[$id])) {
        $servicios[$id] = [
            'nombre' => $row['nombre_servicio'],
            'especialidades' => []
        ];
    }
    $servicios[$id]['especialidades'][] = [
        'nombre' => $row['nombre_especialidad'],
        'id_servicio_especialidad' => $row['id_servicio_especialidad'],
        'id_especialidad' => $row['id_especialidad']
    ];
}

// Definir si el usuario es admin
$isAdmin = (isset($_SESSION['rol']) && $_SESSION['rol'] === 'admin');
?>

<div class="container mt-4">
    <h2>Asignaciones Servicio-Especialidad</h2>
    <?php if($isAdmin): ?>
        <a href="especialidad_agregar.php" class="btn btn-success mb-2">
            <i class="bi bi-journal-plus"></i> Asignar Especialidad a Servicio
        </a>
    <?php endif; ?>
    <table class="table table-bordered align-middle">
        <thead>
            <tr>
                <th>Servicio</th>
                <th>Especialidades</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach($servicios as $id_servicio => $servicio): ?>
            <tr>
                <td><?=htmlspecialchars($servicio['nombre'])?></td>
                <td>
                    <?php foreach($servicio['especialidades'] as $esp): ?>
                        <span class="badge bg-primary me-2 mb-1" style="font-size:1.1em;">
                            <?=htmlspecialchars($esp['nombre'])?>
                            <?php if($isAdmin): ?>
                                <!-- Botón eliminar con icono solo para admin -->
                                <a href="especialidad_eliminar.php?id=<?=intval($esp['id_servicio_especialidad'])?>"
                                   style="color:white; margin-left:10px; font-weight:bold;"
                                   onclick="return confirm('¿Seguro que desea quitar esta especialidad?')" 
                                   title="Quitar especialidad">
                                    <i class="bi bi-trash-fill"></i>
                                </a>
                            <?php endif; ?>
                        </span>
                    <?php endforeach; ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php include ('../../templates/footer.php'); ?>