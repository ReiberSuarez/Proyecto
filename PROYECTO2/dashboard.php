<?php
session_start();
if(!isset($_SESSION['usuario'])) {
    header('Location: login.php');
    exit;
}
include('templates/header.php');
?>
<div class="row">
    <div class="col-md-12">
        <h1>Bienvenido al Sistema CDI</h1>
        <p>Desde aquí puedes gestionar pacientes, médicos, servicios, usuarios y más.</p>
        <div class="row g-3">
            <div class="col-md-3">
                <div class="card text-bg-primary">
                    <div class="card-body">
                        <h5 class="card-title">Pacientes</h5>
                        <p class="card-text">Gestiona los registros de pacientes</p>
                        <a href="/PROYECTO2/pacientes/listar.php" class="btn btn-light">Ver pacientes</a>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-bg-success">
                    <div class="card-body">
                        <h5 class="card-title">Médicos</h5>
                        <p class="card-text">Administra los médicos y sus especialidades</p>
                        <a href="/PROYECTO2/medicos/listar.php" class="btn btn-light">Ver médicos</a>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-bg-warning">
                    <div class="card-body">
                        <h5 class="card-title">Servicios</h5>
                        <p class="card-text">Revisa los servicios del CDI</p>
                        <a href="/PROYECTO2/servicios/listar.php" class="btn btn-light">Ver servicios</a>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-bg-dark">
                    <div class="card-body">
                        <h5 class="card-title">Usuarios</h5>
                        <p class="card-text">Control de usuarios y permisos</p>
                        <a href="/PROYECTO2/usuarios/listar.php" class="btn btn-light">Ver usuarios</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include ('templates/footer.php'); ?>