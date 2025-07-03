<?php
require_once('config_db.php');
session_start();

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = $_POST['usuario'] ?? '';
    $contrasena = $_POST['contrasena'] ?? '';

    // Buscar usuario activo
    $stmt = $pdo->prepare("SELECT * FROM usuario WHERE nombre_usuario = ? AND estatus = 'Activo' LIMIT 1");
    $stmt->execute([$usuario]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && $contrasena === $user['contrasena']) { // Usa password_verify si usas password_hash
        $_SESSION['usuario'] = $user['nombre_usuario'];
        $_SESSION['rol'] = $user['rol'];

        if ($user['rol'] === 'medico') {
            // Guardar id_medico en la sesión
            $_SESSION['id_medico'] = $user['id_medico'];

            // Guardar todas las especialidades de este médico en la sesión
            $stmtEsp = $pdo->prepare("
                SELECT e.id_especialidad, e.nombre_especialidad 
                FROM medico_especialidad me
                JOIN especialidad e ON me.id_especialidad = e.id_especialidad
                WHERE me.id_medico = ?
            ");
            $stmtEsp->execute([$user['id_medico']]);
            $especialidades = $stmtEsp->fetchAll(PDO::FETCH_ASSOC);
            $_SESSION['especialidades'] = $especialidades;

            // También puedes guardar solo los id_especialidad en un array por si necesitas solo los ids:
            $_SESSION['id_especialidades'] = array_column($especialidades, 'id_especialidad');

            header('Location: dashboard_regular.php');
            exit;
        } elseif ($user['rol'] === 'admin') {
            header('Location: dashboard.php');
            exit;
        } else {
            // Otros roles, si los tienes, pueden ir a su propio panel o ser rechazados
            header('Location: login.php');
            exit;
        }
    } else {
        $error = "Usuario o contraseña incorrectos.";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Iniciar sesión | CDI</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap 5 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons CDN -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #0d6efd 0%, #198754 100%);
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            background: #fff;
            border-radius: 18px;
            box-shadow: 0 4px 24px rgba(0,0,0,0.10);
            padding: 2.5rem 2rem 2rem 2rem;
            max-width: 390px;
            width: 100%;
            margin: 40px 0;
        }
        .login-header {
            text-align: center;
            margin-bottom: 1.5rem;
        }
        .login-logo {
            font-size: 2.7rem;
            color: #0d6efd;
            margin-bottom: .5rem;
            font-weight: bold;
            letter-spacing: 2px;
        }
        .btn-primary {
            background: #0d6efd;
            border: none;
        }
        .btn-primary:hover {
            background: #0056d6;
        }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="login-header">
            <div class="login-logo">
                <i class="bi bi-hospital"></i> CDI
            </div>
            <h4 class="mb-0">Iniciar sesión</h4>
        </div>
        <?php if($error): ?>
            <div class="alert alert-danger"><?=$error?></div>
        <?php endif; ?>
        <form method="post" autocomplete="off">
            <div class="mb-3">
                <label for="usuario" class="form-label">Usuario</label>
                <input type="text" name="usuario" id="usuario" class="form-control" required autofocus>
            </div>
            <div class="mb-3">
                <label for="contrasena" class="form-label">Contraseña</label>
                <input type="password" name="contrasena" id="contrasena" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary w-100 mb-2">Entrar</button>
        </form>
    </div>
</body>
</html>