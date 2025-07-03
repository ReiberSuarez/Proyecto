<?php
require_once('../config_db.php');
session_start();
if(!isset($_SESSION['usuario']) || $_SESSION['rol'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

$id_medico = $_GET['id'] ?? null;
if (!$id_medico || !is_numeric($id_medico)) {
    header('Location: listar.php?error=ID de médico no válido');
    exit;
}

// Obtener datos del médico
$stmt = $pdo->prepare("SELECT * FROM medico WHERE id_medico = ?");
$stmt->execute([$id_medico]);
$medico = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$medico) {
    header('Location: listar.php?error=Médico no encontrado');
    exit;
}

// Obtener todos los servicios
$servicios = $pdo->query("SELECT * FROM servicio ORDER BY nombre_servicio")->fetchAll(PDO::FETCH_ASSOC);

// Obtener servicios asignados
$stmt = $pdo->prepare("SELECT id_servicio FROM medico_servicio WHERE id_medico = ?");
$stmt->execute([$id_medico]);
$servicios_asignados = $stmt->fetchAll(PDO::FETCH_COLUMN);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $servicios_seleccionados = $_POST['servicios'] ?? [];
    // Borra asignaciones previas
    $pdo->prepare("DELETE FROM medico_servicio WHERE id_medico = ?")->execute([$id_medico]);
    // Inserta las nuevas
    $stmtIns = $pdo->prepare("INSERT INTO medico_servicio (id_medico, id_servicio) VALUES (?, ?)");
    foreach ($servicios_seleccionados as $id_servicio) {
        $stmtIns->execute([$id_medico, $id_servicio]);
    }
    header("Location: listar.php?msg=Servicios asignados correctamente");
    exit;
}

include ('../templates/header.php');
?>

<style>
    .center-card {
        min-height: 80vh;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .card-services {
        min-width: 340px;
        max-width: 420px;
        margin: auto;
        box-shadow: 0 6px 24px rgba(0,0,0,0.12);
        border-radius: 18px;
    }
    .serv-list label {
        font-size: 1.1rem;
        margin-bottom: .45rem;
    }
    .form-check-input {
        transform: scale(1.25);
        margin-right: 8px;
    }
</style>

<div class="center-card">
    <div class="card card-services">
        <div class="card-header bg-primary text-white text-center">
            <h3 class="mb-0">Asignar Servicios</h3>
            <div style="font-size: 1.1rem;">
                <i class="bi bi-person-badge"></i>
                <?=htmlspecialchars($medico['nombres'].' '.$medico['apellidos'])?>
            </div>
        </div>
        <form method="post" class="card-body">
            <div class="mb-3 serv-list">
                <?php foreach($servicios as $srv): ?>
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" name="servicios[]" value="<?=$srv['id_servicio']?>"
                            id="servicio<?=$srv['id_servicio']?>"
                            <?=in_array($srv['id_servicio'], $servicios_asignados) ? 'checked' : ''?>>
                        <label class="form-check-label" for="servicio<?=$srv['id_servicio']?>">
                            <span class="badge bg-info text-dark me-2"><i class="bi bi-gear-fill"></i></span>
                            <?=htmlspecialchars($srv['nombre_servicio'])?>
                        </label>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="d-flex justify-content-between mt-4">
                <a href="listar.php" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left-circle"></i> Volver
                </a>
                <button type="submit" class="btn btn-success">
                    <i class="bi bi-floppy"></i> Guardar
                </button>
            </div>
        </form>
    </div>
</div>
<?php include ('../templates/footer.php'); ?>