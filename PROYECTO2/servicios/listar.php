<?php
require_once('../config_db.php');
if (session_status() === PHP_SESSION_NONE) session_start();
if(!isset($_SESSION['usuario'])) {
    header('Location: ../login.php');
    exit;
}

$stmt = $pdo->query("SELECT * FROM servicio ORDER BY nombre_servicio");
$servicios = $stmt->fetchAll(PDO::FETCH_ASSOC);

include('../templates/header.php');
?>
<h2>Servicios</h2>
<a href="crear.php" class="btn btn-success mb-3">Nuevo Servicio</a>
<table class="table table-bordered">
    <thead>
        <tr>
            <th>ID</th>
            <th>Nombre del Servicio</th>
            <th>Descripción</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($servicios as $s): ?>
            <tr>
                <td><?=htmlspecialchars($s['id_servicio'])?></td>
                <td><?=htmlspecialchars($s['nombre_servicio'])?></td>
                <td><?=htmlspecialchars($s['descripcion'])?></td>
                <td>
                    <a href="/PROYECTO2/servicios/editar.php?id=<?=$s['id_servicio']?>" class="btn btn-sm btn-primary">Editar</a>
                    <a href="/PROYECTO2/servicios/eliminar.php?id=<?=$s['id_servicio']?>" class="btn btn-sm btn-danger" onclick="return confirm('¿Seguro que desea eliminar este servicio?')">Eliminar</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php include('../templates/footer.php'); ?>