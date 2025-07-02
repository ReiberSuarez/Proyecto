<?php
require_once('../config_db.php');
if (session_status() === PHP_SESSION_NONE) session_start();
if(!isset($_SESSION['usuario'])) {
    header('Location: ../login.php');
    exit;
}

// Obtener médicos para el select (si rol es médico)
$medicos = $pdo->query("SELECT id_medico, nombres, apellidos FROM medico ORDER BY nombres")->fetchAll(PDO::FETCH_ASSOC);

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre_usuario = $_POST['nombre_usuario'] ?? '';
    $contrasena = $_POST['contrasena'] ?? '';
    $contrasena2 = $_POST['contrasena2'] ?? '';
    $rol = $_POST['rol'] ?? '';
    $id_medico = $_POST['id_medico'] ?? null;
    $estatus = $_POST['estatus'] ?? 'Activo';

    if (!$nombre_usuario || !$contrasena || !$rol) {
        $error = "Usuario, contraseña y rol son obligatorios.";
    } elseif ($contrasena !== $contrasena2) {
        $error = "Las contraseñas no coinciden.";
    } elseif ($rol === 'medico' && !$id_medico) {
        $error = "Debe seleccionar el médico asociado para este usuario.";
    } else {
        try {
            $hash = password_hash($contrasena, PASSWORD_BCRYPT);
            $stmt = $pdo->prepare("INSERT INTO usuario (nombre_usuario, contrasena, rol, id_medico, estatus) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$nombre_usuario, $hash, $rol, $rol === 'medico' ? $id_medico : null, $estatus]);
            header('Location: listar.php');
            exit;
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                $error = "El nombre de usuario ya existe.";
            } else {
                $error = "Error al crear usuario: ".$e->getMessage();
            }
        }
    }
}
include('../templates/header.php');
?>
<h2>Nuevo Usuario</h2>
<?php if($error): ?>
    <div class="alert alert-danger"><?=$error?></div>
<?php endif; ?>
<form method="post" id="usuarioForm">
    <div class="mb-3">
        <label>Usuario *</label>
        <input type="text" name="nombre_usuario" class="form-control" required value="<?=htmlspecialchars($_POST['nombre_usuario'] ?? '')?>">
    </div>
    <div class="mb-3">
        <label>Contraseña *</label>
        <input type="password" name="contrasena" class="form-control" required>
    </div>
    <div class="mb-3">
        <label>Repetir Contraseña *</label>
        <input type="password" name="contrasena2" class="form-control" required>
    </div>
    <div class="mb-3">
        <label>Rol *</label>
        <select name="rol" id="rolSelect" class="form-select" required>
            <option value="">Seleccione</option>
            <option value="admin" <?=(@$_POST['rol']=='admin')?'selected':''?>>Admin</option>
            <option value="medico" <?=(@$_POST['rol']=='medico')?'selected':''?>>Médico</option>
            <option value="secretaria" <?=(@$_POST['rol']=='secretaria')?'selected':''?>>Secretaria</option>
            <option value="otro" <?=(@$_POST['rol']=='otro')?'selected':''?>>Otro</option>
        </select>
    </div>
    <div class="mb-3" id="medicoSelectDiv" style="display:none;">
        <label>Médico asociado *</label>
        <select name="id_medico" class="form-select">
            <option value="">Seleccione un médico</option>
            <?php foreach($medicos as $m): ?>
                <option value="<?=$m['id_medico']?>" <?=(@$_POST['id_medico']==$m['id_medico'])?'selected':''?>>
                    <?=htmlspecialchars($m['nombres'].' '.$m['apellidos'])?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="mb-3">
        <label>Estatus</label>
        <select name="estatus" class="form-select">
            <option value="Activo" <?=(@$_POST['estatus']=='Activo')?'selected':''?>>Activo</option>
            <option value="Inactivo" <?=(@$_POST['estatus']=='Inactivo')?'selected':''?>>Inactivo</option>
        </select>
    </div>
    <button type="submit" class="btn btn-success">Crear</button>
    <a href="/PROYECTO2/usuarios/listar.php" class="btn btn-secondary">Cancelar</a>
</form>
<script>
function mostrarMedicoSelect() {
    var rol = document.getElementById('rolSelect').value;
    document.getElementById('medicoSelectDiv').style.display = (rol === 'medico') ? 'block' : 'none';
}
document.getElementById('rolSelect').addEventListener('change', mostrarMedicoSelect);
window.onload = mostrarMedicoSelect;
</script>
<?php include('../templates/footer.php'); ?>