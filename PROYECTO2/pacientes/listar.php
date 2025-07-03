<?php
require_once('../config_db.php');
session_start();
if(!isset($_SESSION['usuario'])) {
    header('Location: ../login.php');
    exit;
}
include ('../templates/header.php');

$stmt = $pdo->query("SELECT * FROM paciente ORDER BY nombres");
$pacientes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h2>Listado de Pacientes</h2>

<?php if(isset($_GET['error'])): ?>
    <div class="alert alert-danger"><?=htmlspecialchars($_GET['error'])?></div>
<?php endif; ?>
<?php if(isset($_GET['msg'])): ?>
    <div class="alert alert-success"><?=htmlspecialchars($_GET['msg'])?></div>
<?php endif; ?>

<a href="crear.php" class="btn btn-success mb-2"><i class="bi bi-person-plus"></i> Agregar Paciente</a>
<table class="table table-striped table-bordered">
    <thead>
        <tr>
            <th>Cédula</th>
            <th>Nombre</th>
            <th>Apellido</th>
            <th>Sexo</th>
            <th>Teléfono</th>
            <th>Fecha de Nacimiento</th>
            <th>Dirección</th>
            <th>Email</th>
            <th>Fecha de Registro</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
    <?php foreach($pacientes as $p): ?>
        <tr>
            <td><?=htmlspecialchars($p['cedula'])?></td>
            <td><?=htmlspecialchars($p['nombres'])?></td>
            <td><?=htmlspecialchars($p['apellidos'])?></td>
            <td><?=htmlspecialchars($p['sexo'])?></td>
            <td><?=htmlspecialchars($p['telefono'])?></td>
            <td><?=htmlspecialchars($p['fecha_nacimiento'])?></td>
            <td><?=htmlspecialchars($p['direccion'])?></td>
            <td><?=htmlspecialchars($p['email'])?></td>
            <td><?=htmlspecialchars($p['fecha_registro'])?></td>
            <td>
                <a href="/PROYECTO2/pacientes/editar.php?id=<?=$p['id_paciente']?>" 
                   class="btn btn-outline-primary btn-sm" title="Editar">
                    <i class="bi bi-pencil-square"></i>
                </a>
                <?php if(isset($_SESSION['rol']) && $_SESSION['rol'] === 'admin'): ?>
                    <a href="/PROYECTO2/pacientes/eliminar.php?id=<?=$p['id_paciente']?>" 
                       class="btn btn-outline-danger btn-sm" 
                       onclick="return confirm('¿Seguro que deseas eliminar este paciente?')" 
                       title="Eliminar">
                        <i class="bi bi-trash-fill"></i>
                    </a>
                <?php endif; ?>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
<?php include ('../templates/footer.php'); ?>