<?php
require_once('../config_db.php');
if (session_status() === PHP_SESSION_NONE) session_start();
if(!isset($_SESSION['usuario'])) {
    header('Location: ../login.php');
    exit;
}

$stmt = $pdo->query("SELECT * FROM especialidad ORDER BY nombre_especialidad");
$especialidades = $stmt->fetchAll(PDO::FETCH_ASSOC);

include('../templates/header.php');
?>
<h2>Especialidades</h2>
<a href="crear.php" class="btn btn-success mb-3">Nueva Especialidad</a>
<table class="table table-bordered">
    <thead>
        <tr>
            <th>ID</th>
            <th>Nombre de la Especialidad</th>
            <th>Descripción</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($especialidades as $e): ?>
            <tr>
                <td><?=htmlspecialchars($e['id_especialidad'])?></td>
                <td><?=htmlspecialchars($e['nombre_especialidad'])?></td>
                <td><?=htmlspecialchars($e['descripcion'])?></td>
                <td>
                    <a href="/PROYECTO2/especialidades/editar.php?id=<?=$e['id_especialidad']?>" class="btn btn-sm btn-primary">Editar</a>
                    <a href="/PROYECTO2/especialidades/eliminar.php?id=<?=$e['id_especialidad']?>" class="btn btn-sm btn-danger" onclick="return confirm('¿Seguro que desea eliminar esta especialidad?')">Eliminar</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php include('../templates/footer.php'); ?>