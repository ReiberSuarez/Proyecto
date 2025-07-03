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

<style>
.center-card {
    min-height: 85vh;
    display: flex;
    align-items: center;
    justify-content: center;
}
.card-form {
    min-width: 340px;
    max-width: 520px;
    margin: auto;
    box-shadow: 0 8px 32px rgba(0,0,0,0.13);
    border-radius: 18px;
    border: 0;
}
.card-header.bg-primary {
    background: #1677ff !important;
    font-size: 1.6rem;
    font-weight: 700;
    border-radius: 18px 18px 0 0;
    letter-spacing: .5px;
}
.btn-form {
    font-size: 0.96em;
    padding: 0.42em 1.4em;
    border-radius: 8px;
    font-weight: 500;
    letter-spacing: .3px;
}
@media (max-width: 600px) {
    .card-form { max-width: 98vw; }
}
</style>

<div class="center-card">
    <div class="card card-form">
        <div class="card-header bg-primary text-white text-center">
            Nuevo Usuario
        </div>
        <form method="post" id="usuarioForm" class="card-body">
            <?php if($error): ?>
                <div class="alert alert-danger"><?=$error?></div>
            <?php endif; ?>
            <div class="row mb-3">
                <div class="col-md-6 mb-3 mb-md-0">
                    <label>Usuario *</label>
                    <input type="text" name="nombre_usuario" class="form-control" required value="<?=htmlspecialchars($_POST['nombre_usuario'] ?? '')?>">
                </div>
                <div class="col-md-6">
                    <label>Rol *</label>
                    <select name="rol" id="rolSelect" class="form-select" required>
                        <option value="">Seleccione</option>
                        <option value="admin" <?=(@$_POST['rol']=='admin')?'selected':''?>>Admin</option>
                        <option value="medico" <?=(@$_POST['rol']=='medico')?'selected':''?>>Médico</option>
                        <option value="secretaria" <?=(@$_POST['rol']=='secretaria')?'selected':''?>>Secretaria</option>
                        <option value="otro" <?=(@$_POST['rol']=='otro')?'selected':''?>>Otro</option>
                    </select>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-6 mb-3 mb-md-0">
                    <label>Contraseña *</label>
                    <input type="password" name="contrasena" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label>Repetir Contraseña *</label>
                    <input type="password" name="contrasena2" class="form-control" required>
                </div>
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
            <div class="row mt-4">
                <div class="col-6 d-grid">
                    <a href="/PROYECTO2/usuarios/listar.php" class="btn btn-secondary btn-form">
                        <i class="bi bi-x-circle"></i> Cancelar
                    </a>
                </div>
                <div class="col-6 d-grid">
                    <button type="submit" class="btn btn-success btn-form">
                        <i class="bi bi-person-plus"></i> Crear
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
<script>
function mostrarMedicoSelect() {
    var rol = document.getElementById('rolSelect').value;
    document.getElementById('medicoSelectDiv').style.display = (rol === 'medico') ? 'block' : 'none';
}
document.getElementById('rolSelect').addEventListener('change', mostrarMedicoSelect);
window.onload = mostrarMedicoSelect;
</script>
<?php include('../templates/footer.php'); ?>