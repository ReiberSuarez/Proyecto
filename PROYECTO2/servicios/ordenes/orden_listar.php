<?php
require_once('../../config_db.php');
session_start();
if(!isset($_SESSION['usuario'])) {
    header('Location: ../../login.php');
    exit;
}
include ('../../templates/header.php');

// Consulta para obtener órdenes/remisiones con datos de paciente, médico y servicio
$sql = "SELECT 
    o.id_orden,
    p.nombres AS paciente_nombre,
    p.apellidos AS paciente_apellido,
    m.nombres AS medico_nombre,
    m.apellidos AS medico_apellido,
    s.nombre_servicio,
    o.fecha,
    o.descripcion,
    o.estatus
FROM orden o
JOIN paciente p ON o.id_paciente = p.id_paciente
JOIN medico m ON o.id_medico = m.id_medico
JOIN servicio s ON o.id_servicio = s.id_servicio
ORDER BY o.fecha DESC";

$stmt = $pdo->query($sql);
$ordenes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container mt-4">
    <h2>Órdenes / Remisiones</h2>
    <a href="/PROYECTO2/servicios/ordenes/orden_crear.php" class="btn btn-success mb-3">
        <i class="bi bi-journal-plus"></i> Nueva Orden / Remisión
    </a>
    <table class="table table-bordered align-middle">
        <thead>
            <tr>
                <th>Paciente</th>
                <th>Médico</th>
                <th>Servicio</th>
                <th>Fecha</th>
                <th>Descripción</th>
                <th>Estatus</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach($ordenes as $orden): ?>
            <tr>
                <td><?=htmlspecialchars($orden['paciente_nombre'].' '.$orden['paciente_apellido'])?></td>
                <td><?=htmlspecialchars($orden['medico_nombre'].' '.$orden['medico_apellido'])?></td>
                <td><?=htmlspecialchars($orden['nombre_servicio'])?></td>
                <td><?=htmlspecialchars($orden['fecha'])?></td>
                <td><?=htmlspecialchars($orden['descripcion'])?></td>
                <td>
                    <?php
                        $badge = [
                            'Pendiente' => 'warning',
                            'Realizada' => 'success',
                            'Cancelada' => 'danger'
                        ][$orden['estatus']] ?? 'secondary';
                    ?>
                    <span class="badge bg-<?=$badge?>"><?=htmlspecialchars($orden['estatus'])?></span>
                </td>
                <td>
                    <a href="/PROYECTO2/servicios/ordenes/orden_editar.php?id=<?=$orden['id_orden']?>" class="btn btn-outline-primary btn-sm" title="Editar">
                        <i class="bi bi-pencil-square"></i>
                    </a>
                    <?php if(isset($_SESSION['rol']) && $_SESSION['rol'] === 'admin'): ?>
                        <a href="/PROYECTO2/servicios/ordenes/orden_eliminar.php?id=<?=$orden['id_orden']?>" 
                           class="btn btn-outline-danger btn-sm" 
                           onclick="return confirm('¿Seguro que desea eliminar esta orden?')" title="Eliminar">
                            <i class="bi bi-trash-fill"></i>
                        </a>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach;?>
        </tbody>
    </table>
</div>
<?php include ('../../templates/footer.php'); ?>