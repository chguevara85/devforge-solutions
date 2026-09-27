<?php
require __DIR__ . '/php/validar_sesion.php';
require __DIR__ . '/php/conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idProyecto = filter_input(INPUT_POST, 'id_proyecto', FILTER_VALIDATE_INT);
    $monto = trim($_POST['monto'] ?? '');
    $detalle = trim($_POST['detalle'] ?? '');
    $fechaEmision = $_POST['fecha_emision'] ?? date('Y-m-d');
    $fechaValidez = $_POST['fecha_validez'] ?? null;
    $estadosPermitidos = ['pendiente', 'aprobada', 'rechazada'];
    $estadoSolicitado = $_POST['estado'] ?? 'pendiente';
    $estado = in_array($estadoSolicitado, $estadosPermitidos, true) ? $estadoSolicitado : 'pendiente';

    if (!$idProyecto || $monto === '' || !is_numeric($monto) || (float) $monto < 0 || $fechaEmision === '') {
        header('Location: cotizaciones.php?error=datos');
        exit();
    }

    try {
        $sql = 'INSERT INTO cotizaciones (id_proyecto, monto, detalle, estado, fecha_emision, fecha_validez)
                VALUES (:proyecto, :monto, :detalle, :estado, :emision, :validez)';
        $stmt = $conexion->prepare($sql);
        $stmt->execute([
            ':proyecto' => $idProyecto,
            ':monto' => $monto,
            ':detalle' => $detalle !== '' ? $detalle : null,
            ':estado' => $estado,
            ':emision' => $fechaEmision,
            ':validez' => $fechaValidez ?: null
        ]);
        header('Location: cotizaciones.php?guardado=1');
        exit();
    } catch (PDOException $e) {
        header('Location: cotizaciones.php?error=bd');
        exit();
    }
}

$proyectos = $conexion->query('SELECT id_proyecto, nombre_proyecto FROM proyectos ORDER BY nombre_proyecto')->fetchAll();
$cotizaciones = $conexion->query('SELECT c.monto, c.detalle, c.estado, c.fecha_emision, c.fecha_validez, p.nombre_proyecto
    FROM cotizaciones c INNER JOIN proyectos p ON p.id_proyecto = c.id_proyecto ORDER BY c.id_cotizacion DESC')->fetchAll();
$mensaje = $_GET['guardado'] ?? '';
$error = $_GET['error'] ?? '';
$estadoLabels = ['pendiente' => 'Pendiente', 'aprobada' => 'Aprobada', 'rechazada' => 'Rechazada'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DevForge Solutions | Cotizaciones</title>
    <link rel="stylesheet" href="css/estilos.css">
</head>
<body class="app-page">
    <header class="app-header"><a class="app-brand" href="dashboard.php">DevForge <span>Solutions</span></a><div class="user-menu"><a href="dashboard.php">Dashboard</a><a href="php/logout.php">Cerrar sesión</a></div></header>
    <main class="app-main">
        <section class="page-heading"><p class="eyebrow">Módulo financiero</p><h1>Cotizaciones</h1><p class="muted-text">Elabora presupuestos claros y controla su estado de aprobación.</p></section>
        <?php if ($mensaje === '1'): ?><div class="notice success-message">Cotización registrada correctamente.</div><?php endif; ?>
        <?php if ($error === 'datos'): ?><div class="notice error-message">Selecciona un proyecto e indica un monto válido.</div><?php endif; ?>
        <?php if ($error === 'bd'): ?><div class="notice error-message">No fue posible registrar la cotización.</div><?php endif; ?>
        <section class="workspace-grid">
            <form class="panel" action="cotizaciones.php" method="post">
                <p class="eyebrow">Nueva propuesta</p><h2>Crear cotización</h2>
                <label for="proyecto-cotizacion">Proyecto</label><select id="proyecto-cotizacion" name="id_proyecto" required><option value="">Seleccionar proyecto</option><?php foreach ($proyectos as $proyecto): ?><option value="<?= (int) $proyecto['id_proyecto'] ?>"><?= htmlspecialchars($proyecto['nombre_proyecto'], ENT_QUOTES, 'UTF-8') ?></option><?php endforeach; ?></select>
                <label for="monto">Monto</label><input id="monto" name="monto" type="number" min="0" step="0.01" required>
                <label for="fecha-emision">Fecha de emisión</label><input id="fecha-emision" name="fecha_emision" type="date" value="<?= date('Y-m-d') ?>" required>
                <label for="fecha-validez">Válida hasta</label><input id="fecha-validez" name="fecha_validez" type="date">
                <label for="detalle">Detalle del servicio</label><textarea id="detalle" name="detalle" rows="4"></textarea>
                <button type="submit">Guardar cotización</button>
            </form>
            <section class="panel table-panel"><div class="panel-heading"><div><p class="eyebrow">Seguimiento</p><h2>Cotizaciones recientes</h2></div><span class="status-chip"><?= count($cotizaciones) ?> cotizaciones</span></div>
                <?php if ($cotizaciones): ?><div class="data-table-wrap"><table class="data-table"><thead><tr><th>Proyecto</th><th>Monto</th><th>Estado</th><th>Emisión</th><th>Validez</th></tr></thead><tbody><?php foreach ($cotizaciones as $cotizacion): ?><tr><td><?= htmlspecialchars($cotizacion['nombre_proyecto'], ENT_QUOTES, 'UTF-8') ?></td><td>S/ <?= number_format((float) $cotizacion['monto'], 2) ?></td><td><?= htmlspecialchars($estadoLabels[$cotizacion['estado']] ?? $cotizacion['estado'], ENT_QUOTES, 'UTF-8') ?></td><td><?= htmlspecialchars($cotizacion['fecha_emision'], ENT_QUOTES, 'UTF-8') ?></td><td><?= htmlspecialchars($cotizacion['fecha_validez'] ?: 'Sin fecha', ENT_QUOTES, 'UTF-8') ?></td></tr><?php endforeach; ?></tbody></table></div><?php else: ?><div class="empty-state"><strong>No hay cotizaciones registradas</strong><span>Las propuestas creadas aparecerán en este listado.</span></div><?php endif; ?>
            </section>
        </section>
    </main>
</body>
</html>
