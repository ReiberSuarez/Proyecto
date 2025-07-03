<?php
require_once('../config_db.php');
if (session_status() === PHP_SESSION_NONE) session_start();
if(!isset($_SESSION['usuario'])) {
    header('Location: ../login.php');
    exit;
}

$stmt = $pdo->query("SELECT * FROM especialidad ORDER BY id_especialidad ASC");
$especialidades = $stmt->fetchAll(PDO::FETCH_ASSOC);

include('../templates/header.php');
?>
<h2>Especialidades</h2>

<?php if(isset($_GET['error'])): ?>
    <div class="alert alert-danger"><?=htmlspecialchars($_GET['error'])?></div>
<?php endif; ?>
<?php if(isset($_GET['msg'])): ?>
    <div class="alert alert-success"><?=htmlspecialchars($_GET['msg'])?></div>
<?php endif; ?>

<?php if(isset($_SESSION['rol']) && $_SESSION['rol'] === 'admin'): ?>
    <a href="/PROYECTO2/especialidades/crear.php" class="btn btn-success mb-3">
        <i class="bi bi-journal-plus"></i> Nueva Especialidad
    </a>
<?php endif; ?>
<table class="table table-bordered align-middle">
    <thead>
        <tr>
            <th>ID</th>
            <th>Nombre de la Especialidad</th>
            <th>Descripción</th>
            <?php if(isset($_SESSION['rol']) && $_SESSION['rol'] === 'admin'): ?>
                <th class="text-center">Acciones</th>
            <?php endif; ?>
        </tr>
    </thead>
    <tbody>
        <?php foreach($especialidades as $e): ?>
            <tr>
                <td><?=htmlspecialchars($e['id_especialidad'])?></td>
                <td><?=htmlspecialchars($e['nombre_especialidad'])?></td>
                <td><?=htmlspecialchars($e['descripcion'])?></td>
                <?php if(isset($_SESSION['rol']) && $_SESSION['rol'] === 'admin'): ?>
                    <td class="text-center">
                        <a href="/PROYECTO2/especialidades/editar.php?id=<?=$e['id_especialidad']?>" 
                           class="btn btn-outline-primary btn-sm" title="Editar">
                            <i class="bi bi-pencil-square"></i>
                        </a>
                        <a href="/PROYECTO2/especialidades/eliminar.php?id=<?=$e['id_especialidad']?>" 
                           class="btn btn-outline-danger btn-sm" title="Eliminar"
                           onclick="return confirm('¿Seguro que desea eliminar esta especialidad?')">
                            <i class="bi bi-trash-fill"></i>
                        </a>
                    </td>
                <?php endif; ?>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php include('../templates/footer.php'); ?>