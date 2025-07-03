<?php
require_once '../config_db.php';  // Tu archivo de conexión
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['usuario']) || $_SESSION['usuario'] == '') {
    header("Location: index.php");
    exit();
}

// Determina el área (por GET o POST)
$area = isset($_GET['area']) ? $_GET['area'] : (isset($_POST['area']) ? $_POST['area'] : null);
if (!$area) {
    die("Área no especificada");
}

// Obtiene los campos del formulario para el área
$stmt = $pdo->prepare("SELECT * FROM formulario_area WHERE area = ? ORDER BY orden");
$stmt->execute([$area]);
$campos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Procesar el formulario
$mensaje = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $datos = [];
    $errores = [];
    foreach ($campos as $campo) {
        $nombre = $campo['nombre_campo'];
        $etiqueta = $campo['etiqueta'];
        $requerido = $campo['requerido'];
        $valor = $_POST[$nombre] ?? '';
        if($requerido && $valor === '') {
            $errores[] = "El campo '$etiqueta' es obligatorio.";
        }
        $datos[$nombre] = $valor;
    }
    if (count($errores) === 0) {
        // Guarda los datos en historial_medico, campo detalles (JSON)
        $detalles_json = json_encode($datos, JSON_UNESCAPED_UNICODE);
        // Aquí puedes agregar otros datos básicos (id_paciente, id_medico, fecha, etc.)
        $id_paciente = 1; // Busca el id_paciente según tus reglas o crea el paciente si es nuevo
        $id_medico = $_SESSION['id_medico'] ?? null;
        $fecha = date("Y-m-d H:i:s");
        $sql = "INSERT INTO historial_medico (id_paciente, id_medico, area, fecha, detalles) VALUES (?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        if ($stmt->execute([$id_paciente, $id_medico, $area, $fecha, $detalles_json])) {
            $mensaje = "<div class='Success_message'>Registro guardado exitosamente</div>";
        } else {
            $mensaje = "<div class='error_message'>Error al guardar el registro</div>";
        }
    } else {
        $mensaje = "<div class='error_message'>" . implode('<br>', $errores) . "</div>";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro <?=ucfirst($area)?></title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <main class="main-content">
        <form method="POST" class="register-form">
            <input type="hidden" name="area" value="<?=htmlspecialchars($area)?>">
            <h2>Registro <?=ucfirst(str_replace('_',' ',$area))?></h2>
            <?= $mensaje ?>
            <?php foreach ($campos as $campo): ?>
                <div class="form-group">
                    <label for="<?=$campo['nombre_campo']?>"><?=htmlspecialchars($campo['etiqueta'])?><?= $campo['requerido'] ? "*" : "" ?></label>
                    <?php if ($campo['tipo'] === 'select'): ?>
                        <select id="<?=$campo['nombre_campo']?>" name="<?=$campo['nombre_campo']?>" <?=$campo['requerido'] ? 'required' : ''?>>
                            <option value="">Seleccione</option>
                            <?php foreach (explode(',', $campo['opciones']) as $opcion): ?>
                                <option value="<?=trim($opcion)?>"><?=ucfirst(trim($opcion))?></option>
                            <?php endforeach; ?>
                        </select>
                    <?php elseif ($campo['tipo'] === 'textarea'): ?>
                        <textarea id="<?=$campo['nombre_campo']?>" name="<?=$campo['nombre_campo']?>" <?=$campo['requerido'] ? 'required' : ''?>></textarea>
                    <?php else: ?>
                        <input type="<?=$campo['tipo']?>" id="<?=$campo['nombre_campo']?>" name="<?=$campo['nombre_campo']?>" <?=$campo['requerido'] ? 'required' : ''?>>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
            <button type="submit">Registrar</button>
        </form>
    </main>
</body>
</html>