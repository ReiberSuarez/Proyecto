<?php
require_once('../config_db.php');
if (session_status() === PHP_SESSION_NONE) session_start();
if(!isset($_SESSION['usuario'])) {
    header('Location: ../login.php');
    exit;
}

$servicios = [];

if ($_SESSION['rol'] === 'admin') {
    // Admin ve todos los servicios
    $stmt = $pdo->query("SELECT * FROM servicio ORDER BY id_servicio");
    $servicios = $stmt->fetchAll(PDO::FETCH_ASSOC);
} elseif ($_SESSION['rol'] === 'medico') {
    // Médico: solo servicios asignados directamente (usando medico_servicio)
    $id_medico = $_SESSION['id_medico'] ?? null;
    if ($id_medico) {
        $stmt = $pdo->prepare("
            SELECT s.*
            FROM servicio s
            INNER JOIN medico_servicio ms ON ms.id_servicio = s.id_servicio
            WHERE ms.id_medico = ?
            ORDER BY s.id_servicio
        ");
        $stmt->execute([$id_medico]);
        $servicios = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

include('../templates/header.php');
?>

<h2>Servicios</h2>

<?php if(isset($_GET['error'])): ?>
    <div class="alert alert-danger"><?=htmlspecialchars($_GET['error'])?></div>
<?php endif; ?>
<?php if(isset($_GET['msg'])): ?>
    <div class="alert alert-success"><?=htmlspecialchars($_GET['msg'])?></div>
<?php endif; ?>

<?php if ($_SESSION['rol'] === 'admin'): ?>
    <a href="/PROYECTO2/servicios/crear.php" class="btn btn-success mb-3">
        <i class="bi bi-journal-plus"></i> Nuevo Servicio
    </a>
    <table class="table table-bordered align-middle">
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
                        <a href="/PROYECTO2/servicios/editar.php?id=<?=$s['id_servicio']?>" class="btn btn-outline-primary btn-sm" title="Editar">
                            <i class="bi bi-pencil-square"></i>
                        </a>
                        <a href="/PROYECTO2/servicios/eliminar.php?id=<?=$s['id_servicio']?>" class="btn btn-outline-danger btn-sm" title="Eliminar"
                           onclick="return confirm('¿Seguro que desea eliminar este servicio?')">
                            <i class="bi bi-trash-fill"></i>
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php elseif ($_SESSION['rol'] === 'medico'): ?>
    <div class="row g-4">
    <?php if (empty($servicios)): ?>
        <div class="alert alert-warning">No tienes servicios asignados.</div>
    <?php endif; ?>
    <?php foreach($servicios as $servicio): ?>
        <div class="col-md-4">
            <div class="card h-100 shadow">
                <div class="card-body">
                    <h5 class="card-title"><?=htmlspecialchars($servicio['nombre_servicio'])?></h5>
                    <p class="card-text"><?=htmlspecialchars($servicio['descripcion'])?></p>
                    <a href="/PROYECTO2/pacientes/registrar.php?id_servicio=<?=$servicio['id_servicio']?>" class="btn btn-success mb-2">
                        <i class="bi bi-person-plus"></i> Registrar paciente
                    </a>
                    <a href="/PROYECTO2/pacientes/listar.php?id_servicio=<?=$servicio['id_servicio']?>" class="btn btn-primary">
                        <i class="bi bi-search"></i> Consultar pacientes
                    </a>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
    </div>
<?php else: ?>
    <div class="alert alert-warning">No tienes permisos para ver esta sección.</div>
<?php endif; ?>

<?php include('../templates/footer.php'); ?>