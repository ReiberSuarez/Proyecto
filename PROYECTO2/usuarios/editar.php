<?php
require_once('../config_db.php');
if (session_status() === PHP_SESSION_NONE) session_start();
if(!isset($_SESSION['usuario'])) {
    header('Location: ../login.php');
    exit;
}

$id = isset($_GET['id']) && is_numeric($_GET['id']) ? (int)$_GET['id'] : null;
if (!$id) {
    header('Location: /PROYECTO2/usuarios/listar.php');
    exit;
}

// Obtener usuario actual
$stmt = $pdo->prepare("SELECT * FROM usuario WHERE id_usuario = ?");
$stmt->execute([$id]);
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$usuario) {
    header('Location: /PROYECTO2/usuarios/listar.php');
    exit;
}

// Obtener médicos para el select
$medicos = $pdo->query("SELECT id_medico, nombres, apellidos FROM medico ORDER BY nombres")->fetchAll(PDO::FETCH_ASSOC);

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre_usuario = $_POST['nombre_usuario'] ?? '';
    $rol = $_POST['rol'] ?? '';
    $id_medico = $_POST['id_medico'] ?? null;
    $estatus = $_POST['estatus'] ?? 'Activo';

    // Para cambio de contraseña
    $contrasena = $_POST['contrasena'] ?? '';
    $contrasena2 = $_POST['contrasena2'] ?? '';

    if (!$nombre_usuario || !$rol) {
        $error = "Usuario y rol son obligatorios.";
    } elseif ($rol === 'medico' && !$id_medico) {
        $error = "Debe seleccionar el médico asociado.";
    } elseif (($contrasena || $contrasena2) && $contrasena !== $contrasena2) {
        $error = "Las contraseñas nuevas no coinciden.";
    } else {
        try {
            // Verificar si se cambia la contraseña
            if ($contrasena) {
                $hash = password_hash($contrasena, PASSWORD_BCRYPT);
                $stmt = $pdo->prepare("UPDATE usuario SET nombre_usuario=?, contrasena=?, rol=?, id_medico=?, estatus=? WHERE id_usuario=?");
                $stmt->execute([
                    $nombre_usuario, $hash, $rol, $rol === 'medico' ? $id_medico : null, $estatus, $id
                ]);
            } else {
                $stmt = $pdo->prepare("UPDATE usuario SET nombre_usuario=?, rol=?, id_medico=?, estatus=? WHERE id_usuario=?");
                $stmt->execute([
                    $nombre_usuario, $rol, $rol === 'medico' ? $id_medico : null, $estatus, $id
                ]);
            }
            header('Location: listar.php');
            exit;
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                $error = "El nombre de usuario ya existe.";
            } else {
                $error = "Error al actualizar usuario: ".$e->getMessage();
            }
        }
    }
}
include('../templates/header.php');
?>
<h2>Editar Usuario</h2>
<?php if($error): ?>
    <div class="alert alert-danger"><?=$error?></div>
<?php endif; ?>
<form method="post" id="usuarioForm">
    <div class="mb-3">
        <label>Usuario *</label>
        <input type="text" name="nombre_usuario" class="form-control" required value="<?=htmlspecialchars($_POST['nombre_usuario'] ?? $usuario['nombre_usuario'])?>">
    </div>
    <div class="mb-3">
        <label>Rol *</label>
        <select name="rol" id="rolSelect" class="form-select" required>
            <option value="">Seleccione</option>
            <option value="admin" <?=(@($_POST['rol'] ?? $usuario['rol'])=='admin')?'selected':''?>>Admin</option>
            <option value="medico" <?=(@($_POST['rol'] ?? $usuario['rol'])=='medico')?'selected':''?>>Médico</option>
            <option value="secretaria" <?=(@($_POST['rol'] ?? $usuario['rol'])=='secretaria')?'selected':''?>>Secretaria</option>
            <option value="otro" <?=(@($_POST['rol'] ?? $usuario['rol'])=='otro')?'selected':''?>>Otro</option>
        </select>
    </div>
    <div class="mb-3" id="medicoSelectDiv" style="display:none;">
        <label>Médico asociado *</label>
        <select name="id_medico" class="form-select">
            <option value="">Seleccione un médico</option>
            <?php foreach($medicos as $m): ?>
                <option value="<?=$m['id_medico']?>"
                <?php
                $selectedMedico = @$_POST['id_medico'] ?? $usuario['id_medico'];
                if ($selectedMedico == $m['id_medico']) echo 'selected';
                ?>>
                    <?=htmlspecialchars($m['nombres'].' '.$m['apellidos'])?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="mb-3">
        <label>Estatus</label>
        <select name="estatus" class="form-select">
            <option value="Activo" <?=(@($_POST['estatus'] ?? $usuario['estatus'])=='Activo')?'selected':''?>>Activo</option>
            <option value="Inactivo" <?=(@($_POST['estatus'] ?? $usuario['estatus'])=='Inactivo')?'selected':''?>>Inactivo</option>
        </select>
    </div>
    <div class="mb-3">
        <label>Nueva contraseña (dejar en blanco para no cambiar)</label>
        <input type="password" name="contrasena" class="form-control">
    </div>
    <div class="mb-3">
        <label>Repetir nueva contraseña</label>
        <input type="password" name="contrasena2" class="form-control">
    </div>
    <button type="submit" class="btn btn-primary">Actualizar</button>
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