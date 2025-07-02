<?php
include '../../config_db.php';

$sql = "SELECT se.id_servicio_especialidad, s.nombre_servicio, e.nombre_especialidad
        FROM servicio_especialidad se
        JOIN servicio s ON se.id_servicio = s.id_servicio
        JOIN especialidad e ON se.id_especialidad = e.id_especialidad
        ORDER BY s.nombre_servicio, e.nombre_especialidad";
$stmt = $pdo->query($sql);
$relaciones = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h2>Relaciones Servicios & Especialidades</h2>
<a href="especialidad_agregar.php">Agregar relación</a>
<table border="1" cellpadding="5">
    <thead>
        <tr>
            <th>Servicio</th>
            <th>Especialidad</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($relaciones as $rel): ?>
        <tr>
            <td><?= htmlspecialchars($rel['nombre_servicio']) ?></td>
            <td><?= htmlspecialchars($rel['nombre_especialidad']) ?></td>
            <td>
                <a href="/PROYECTO2/servicio/especialidad/especialidad_eliminar.php?id=<?= $rel['id_servicio_especialidad'] ?>" onclick="return confirm('¿Eliminar esta relación?')">Eliminar</a>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>