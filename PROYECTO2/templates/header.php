<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>CDI Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="/assets/css/styles.css">
    <style>
        body { min-height: 100vh; }
        .sidebar {
            min-width: 220px;
            max-width: 220px;
            min-height: 100vh;
            background: #0d6efd;
            color: #fff;
        }
        .sidebar .nav-link,
        .sidebar .dropdown-item {
            color: #fff;
        }
        .sidebar .nav-link.active,
        .sidebar .dropdown-item.active {
            background-color: #0b5ed7;
            font-weight: bold;
        }
        .sidebar .dropdown-menu {
            background-color: #0d6efd;
            border: none;
        }
        .sidebar .dropdown-toggle::after {
            filter: invert(1);
        }
        .sidebar .nav-link:hover,
        .sidebar .dropdown-item:hover {
            background: #0b5ed7;
            color: #fff;
        }
        .content {
            padding: 40px 30px 30px 30px;
            min-height: 100vh;
            background: #f8f9fa;
            width: 100%;
            box-sizing: border-box;
        }
        .sidebar .navbar-brand {
            color: #fff !important;
            font-weight: bold;
            font-size: 1.3rem;
            margin-bottom: 2rem;
        }
        @media (max-width: 991.98px) {
            .sidebar {
                min-width: 100vw;
                max-width: 100vw;
                min-height: auto;
                position: static;
            }
            .content {
                margin-left: 0;
                padding: 20px 5px;
                width: 100vw;
            }
        }
    </style>
</head>
<body>
<?php if(isset($_SESSION['usuario'])): ?>
<div class="d-flex">
    <nav class="sidebar d-flex flex-column p-3">
        <a class="navbar-brand mb-4" href="/PROYECTO2/dashboard.php">CDI Dashboard</a>
        <ul class="nav nav-pills flex-column mb-auto">
            <li class="nav-item mb-1">
                <a class="nav-link" href="/PROYECTO2/dashboard.php">Inicio</a>
            </li>
            <!-- Dropdown Pacientes -->
            <li class="nav-item mb-1">
                <div class="dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="dropdownPacientes" data-bs-toggle="dropdown" aria-expanded="false">
                        Pacientes
                    </a>
                    <ul class="dropdown-menu shadow" aria-labelledby="dropdownPacientes">
                        <li>
                            <a class="dropdown-item" href="/PROYECTO2/pacientes/listar.php">
                                <i class="bi bi-people"></i> Ver pacientes
                            </a>
                        </li>
                    </ul>
                </div>
            </li>
            <!-- Dropdown Médicos y Gestión Clínica -->
            <li class="nav-item mb-1">
                <div class="dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="dropdownMedicos" data-bs-toggle="dropdown" aria-expanded="false">
                        Médicos y Clínica
                    </a>
                    <ul class="dropdown-menu shadow" aria-labelledby="dropdownMedicos">
                        <li><a class="dropdown-item" href="/PROYECTO2/medicos/listar.php">Médicos</a></li>
                        <li><a class="dropdown-item" href="/PROYECTO2/especialidades/listar.php">Especialidades</a></li>
                        <li><a class="dropdown-item" href="/PROYECTO2/medicos/especialidad/listar.php">Médico-Especialidad</a></li>
                        <li><a class="dropdown-item" href="/PROYECTO2/historial/medico/listar.php">Historial Médico</a></li>
                    </ul>
                </div>
            </li>
            <!-- Dropdown Servicios -->
            <li class="nav-item mb-1">
                <div class="dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="dropdownServicios" data-bs-toggle="dropdown" aria-expanded="false">
                        Servicios
                    </a>
                    <ul class="dropdown-menu shadow" aria-labelledby="dropdownServicios">
                        <li><a class="dropdown-item" href="/PROYECTO2/servicios/listar.php">Servicios</a></li>
                        <li><a class="dropdown-item" href="/PROYECTO2/servicios/especialidad/especialidad_listar.php">Servicio-Especialidad</a></li>
                        <li><a class="dropdown-item" href="/PROYECTO2/orden/listar.php">Órdenes / Remisiones</a></li>
                    </ul>
                </div>
            </li>
            <li class="nav-item mb-1">
                <a class="nav-link" href="/PROYECTO2/usuarios/listar.php">Usuarios</a>
            </li>
            <li class="nav-item mt-4">
                <a class="nav-link" href="/PROYECTO2/logout.php">Salir</a>
            </li>
        </ul>
    </nav>
    <div class="content flex-grow-1">
<?php else: ?>
    <!-- Si no está logueado, solo navbar simple -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4">
        <div class="container">
            <a class="navbar-brand" href="/PROYECTO2/dashboard.php">CDI Dashboard</a>
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link" href="/PROYECTO2/login.php">Iniciar sesión</a></li>
            </ul>
        </div>
    </nav>
    <div class="container">
<?php endif; ?>