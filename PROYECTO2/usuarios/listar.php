<?php
require_once('../config_db.php');
if (session_status() === PHP_SESSION_NONE) session_start();
if(!isset($_SESSION['usuario'])) {
    header('Location: ../login.php');
    exit;
}

// Obtener usuarios y su médico asociado (si aplica)
$stmt = $pdo->query("SELECT u.*, m.nombres AS nombre_medico, m.apellidos AS apellido_medico FROM usuario u
    LEFT JOIN medico m ON u.id_medico = m.id_medico
    ORDER BY u.id_usuario DESC");
$usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

include('../templates/header.php');
?>
<h2>Usuarios</h2>
<a href="crear.php" class="btn btn-success mb-3">
    <i class="bi bi-person-plus"></i> Nuevo Usuario
</a>
<table class="table table-bordered align-middle">
    <thead>
        <tr>
            <th>ID</th>
            <th>Usuario</th>
            <th>Rol</th>
            <th>Médico</th>
            <th>Estatus</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($usuarios as $u): ?>
            <tr>
                <td><?=htmlspecialchars($u['id_usuario'])?></td>
                <td><?=htmlspecialchars($u['nombre_usuario'])?></td>
                <td><?=htmlspecialchars($u['rol'])?></td>
                <td>
                    <?php
                        if ($u['rol'] === 'medico' && $u['id_medico']) {
                            echo htmlspecialchars($u['nombre_medico'] . ' ' . $u['apellido_medico']);
                        } else {
                            echo '<span class="text-muted">-</span>';
                        }
                    ?>
                </td>
                <td>
                    <?php if($u['estatus'] === 'Activo'): ?>
                        <span class="badge bg-success"><i class="bi bi-check-circle"></i> Activo</span>
                    <?php elseif($u['estatus'] === 'Inactivo'): ?>
                        <span class="badge bg-secondary"><i class="bi bi-x-circle"></i> Inactivo</span>
                    <?php elseif($u['estatus'] === 'Cancelado'): ?>
                        <span class="badge bg-danger"><i class="bi bi-x-circle"></i> Cancelado</span>
                    <?php else: ?>
                        <span class="badge bg-secondary"><?=htmlspecialchars($u['estatus'])?></span>
                    <?php endif; ?>
                </td>
                <td>
                    <a href="/PROYECTO2/usuarios/editar.php?id=<?=$u['id_usuario']?>" class="btn btn-sm btn-outline-primary" title="Editar">
                        <i class="bi bi-pencil-square"></i>
                    </a>
                    <a href="/PROYECTO2/usuarios/eliminar.php?id=<?=$u['id_usuario']?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('¿Seguro que desea eliminar este usuario?')" title="Eliminar">
                        <i class="bi bi-trash-fill"></i>
                    </a>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php include('../templates/footer.php'); ?>