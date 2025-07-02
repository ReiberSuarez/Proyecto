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
<a href="crear.php" class="btn btn-success mb-3">Nuevo Usuario</a>
<table class="table table-bordered">
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
                            echo '-';
                        }
                    ?>
                </td>
                <td><?=htmlspecialchars($u['estatus'])?></td>
                <td>
                    <a href="/PROYECTO2/usuarios/editar.php?id=<?=$u['id_usuario']?>" class="btn btn-sm btn-primary">Editar</a>
                    <a href="/PROYECTO2/usuarios/eliminar.php?id=<?=$u['id_usuario']?>" class="btn btn-sm btn-danger" onclick="return confirm('¿Seguro que desea eliminar este usuario?')">Eliminar</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php include('../templates/footer.php'); ?>