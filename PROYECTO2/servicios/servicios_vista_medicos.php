<?php
require_once('../config_db.php');
session_start();
//if(!isset($_SESSION['usuario']) || $_SESSION['rol'] !== 'medico') {
   // header('Location: ../login.php');
    //exit;
//}

$stmt = $pdo->query("SELECT * FROM servicio ORDER BY nombre_servicio");
$servicios = $stmt->fetchAll(PDO::FETCH_ASSOC);

include('../templates/header.php');
?>
<h2 class="mb-4">Servicios activos</h2>
<div class="row g-4">
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
<?php include('../templates/footer.php'); ?>