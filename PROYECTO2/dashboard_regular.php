<?php
session_start();
if(!isset($_SESSION['usuario']) || $_SESSION['rol'] !== 'medico') {
    header('Location: login.php');
    exit;
}
include('templates/header.php');
?>
<div class="row">
    <div class="col-md-12">
        <h1>Bienvenido, Doctor(a) <?=htmlspecialchars($_SESSION['usuario'])?></h1>
        <p>Desde aquí puede acceder a las áreas permitidas del sistema CDI.</p>
        <div class="row g-3">
            <!-- Pacientes -->
            <div class="col-md-4">
                <div class="card text-bg-primary h-100">
                    <div class="card-body">
                        <h5 class="card-title">Pacientes</h5>
                        <p class="card-text">Consulte sus pacientes registrados.</p>
                        <a href="/PROYECTO2/pacientes/listar.php" class="btn btn-light">Ver pacientes</a>
                    </div>
                </div>
            </div>
            <!-- Especialidades -->
            <div class="col-md-4">
                <div class="card text-bg-success h-100">
                    <div class="card-body">
                        <h5 class="card-title">Especialidades</h5>
                        <p class="card-text">Consulte las especialidades médicas.</p>
                        <a href="/PROYECTO2/especialidades/listar.php" class="btn btn-light">Ver especialidades</a>
                    </div>
                </div>
            </div>
            <!-- Historial Médico -->
            <div class="col-md-4">
                <div class="card text-bg-info h-100">
                    <div class="card-body">
                        <h5 class="card-title">Historial Médico</h5>
                        <p class="card-text">Consulte y registre historial médico de pacientes.</p>
                        <a href="/PROYECTO2/historial/medico/listar.php" class="btn btn-light">Ver historial</a>
                    </div>
                </div>
            </div>
            <!-- Servicios -->
            <div class="col-md-4">
                <div class="card text-bg-warning h-100">
                    <div class="card-body">
                        <h5 class="card-title">Servicios</h5>
                        <p class="card-text">Consulte los servicios disponibles del CDI.</p>
                        <a href="/PROYECTO2/servicios/listar.php" class="btn btn-light">Ver servicios</a>
                    </div>
                </div>
            </div>
            <!-- Órdenes / Remisiones -->
            <div class="col-md-4">
                <div class="card text-bg-secondary h-100">
                    <div class="card-body">
                        <h5 class="card-title">Órdenes / Remisiones</h5>
                        <p class="card-text">Consulte y registre órdenes y remisiones.</p>
                        <a href="/PROYECTO2/servicios/ordenes/orden_listar.php" class="btn btn-light">Ver órdenes</a>
                    </div>
                </div>
            </div>
            <!-- Opcional: Cerrar sesión -->
            <div class="col-md-4">
                <div class="card text-bg-dark h-100">
                    <div class="card-body">
                        <h5 class="card-title">Salir</h5>
                        <p class="card-text">Cerrar sesión del sistema.</p>
                        <a href="/PROYECTO2/logout.php" class="btn btn-light">Cerrar sesión</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include ('templates/footer.php'); ?>