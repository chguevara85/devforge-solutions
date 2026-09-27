<?php
require __DIR__ . '/php/validar_sesion.php';
require __DIR__ . '/php/conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre_proyecto'] ?? '');
    $descripcion = trim($_POST['descripcion'] ?? '');
    $idCliente = filter_input(INPUT_POST, 'id_cliente', FILTER_VALIDATE_INT);
    $idResponsable = filter_input(INPUT_POST, 'id_responsable', FILTER_VALIDATE_INT);
    $estado = $_POST['estado'] ?? 'cotizacion';
    $fechaInicio = $_POST['fecha_inicio'] ?? null;
    $fechaFin = $_POST['fecha_fin'] ?? null;
    $presupuesto = trim($_POST['presupuesto'] ?? '');
    $estadosPermitidos = ['cotizacion', 'desarrollo', 'pruebas', 'entregado'];

    if ($nombre === '' || !$idCliente || !$idResponsable || !in_array($estado, $estadosPermitidos, true)) {
        header('Location: proyectos.php?error=datos');
        exit();
    }

    try {
        $sql = 'INSERT INTO proyectos (nombre_proyecto, descripcion, id_cliente, id_responsable, estado, fecha_inicio, fecha_fin, presupuesto)
                VALUES (:nombre, :descripcion, :cliente, :responsable, :estado, :fecha_inicio, :fecha_fin, :presupuesto)';
        $stmt = $conexion->prepare($sql);
        $stmt->execute([
            ':nombre' => $nombre,
            ':descripcion' => $descripcion !== '' ? $descripcion : null,
            ':cliente' => $idCliente,
            ':responsable' => $idResponsable,
            ':estado' => $estado,
            ':fecha_inicio' => $fechaInicio ?: null,
            ':fecha_fin' => $fechaFin ?: null,
            ':presupuesto' => $presupuesto !== '' ? $presupuesto : null
        ]);
        header('Location: proyectos.php?guardado=1');
        exit();
    } catch (PDOException $e) {
        header('Location: proyectos.php?error=bd');
        exit();
    }
}

$clientes = $conexion->query('SELECT id_cliente, razon_social FROM clientes ORDER BY razon_social')->fetchAll();
$usuarios = $conexion->query("SELECT id_usuario, nombre_completo FROM usuarios WHERE estado = 'activo' ORDER BY nombre_completo")->fetchAll();
$proyectos = $conexion->query('SELECT p.nombre_proyecto, p.estado, p.presupuesto, c.razon_social, u.nombre_completo
    FROM proyectos p INNER JOIN clientes c ON c.id_cliente = p.id_cliente
    INNER JOIN usuarios u ON u.id_usuario = p.id_responsable ORDER BY p.id_proyecto DESC')->fetchAll();
$mensaje = $_GET['guardado'] ?? '';
$error = $_GET['error'] ?? '';
$estadoLabels = ['cotizacion' => 'En cotización', 'desarrollo' => 'En desarrollo', 'pruebas' => 'En pruebas', 'entregado' => 'Entregado'];
$hayClientes = count($clientes) > 0;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DevForge Solutions | Proyectos</title>
    <link rel="stylesheet" href="css/estilos.css">
</head>
<body class="app-page">
    <header class="app-header"><a class="app-brand" href="dashboard.php">DevForge <span>Solutions</span></a><div class="user-menu"><a href="dashboard.php">Dashboard</a><a href="php/logout.php">Cerrar sesión</a></div></header>
    <main class="app-main">
        <section class="page-heading"><p class="eyebrow">Módulo operativo</p><h1>Proyectos</h1><p class="muted-text">Organiza el avance, responsables y entregas de cada proyecto.</p></section>
        <?php if ($mensaje === '1'): ?><div class="notice success-message">Proyecto registrado correctamente.</div><?php endif; ?>
        <?php if ($error === 'datos'): ?><div class="notice error-message">Completa el nombre, cliente, responsable y estado del proyecto.</div><?php endif; ?>
        <?php if ($error === 'bd'): ?><div class="notice error-message">No fue posible registrar el proyecto.</div><?php endif; ?>
        <section class="workspace-grid">
            <form class="panel" action="proyectos.php" method="post">
                <p class="eyebrow">Nuevo registro</p><h2>Crear proyecto</h2>
                <label for="nombre-proyecto">Nombre del proyecto</label><input id="nombre-proyecto" name="nombre_proyecto" type="text" maxlength="150" required>
                <label for="cliente-proyecto">Cliente</label>
                <?php if (!$hayClientes): ?><div class="field-help">Primero registra un cliente en <a href="clientes.php">Clientes</a>.</div><?php endif; ?>
                <select id="cliente-proyecto" name="id_cliente" required <?= !$hayClientes ? 'disabled' : '' ?>><option value=""><?= $hayClientes ? 'Seleccionar cliente' : 'No hay clientes registrados' ?></option><?php foreach ($clientes as $cliente): ?><option value="<?= (int) $cliente['id_cliente'] ?>"><?= htmlspecialchars($cliente['razon_social'], ENT_QUOTES, 'UTF-8') ?></option><?php endforeach; ?></select>
                <label for="responsable-proyecto">Responsable</label><select id="responsable-proyecto" name="id_responsable" required><option value="">Seleccionar responsable</option><?php foreach ($usuarios as $usuario): ?><option value="<?= (int) $usuario['id_usuario'] ?>"><?= htmlspecialchars($usuario['nombre_completo'], ENT_QUOTES, 'UTF-8') ?></option><?php endforeach; ?></select>
                <label for="estado-proyecto">Estado</label><select id="estado-proyecto" name="estado"><option value="cotizacion">En cotización</option><option value="desarrollo">En desarrollo</option><option value="pruebas">En pruebas</option><option value="entregado">Entregado</option></select>
                <label for="presupuesto">Presupuesto</label><input id="presupuesto" name="presupuesto" type="number" min="0" step="0.01">
                <label for="descripcion-proyecto">Descripción</label><textarea id="descripcion-proyecto" name="descripcion" rows="3"></textarea>
                <button type="submit">Guardar proyecto</button>
            </form>
            <section class="panel table-panel"><div class="panel-heading"><div><p class="eyebrow">Seguimiento</p><h2>Proyectos recientes</h2></div><span class="status-chip"><?= count($proyectos) ?> proyectos</span></div>
                <?php if ($proyectos): ?><div class="data-table-wrap"><table class="data-table"><thead><tr><th>Proyecto</th><th>Cliente</th><th>Responsable</th><th>Estado</th><th>Presupuesto</th></tr></thead><tbody><?php foreach ($proyectos as $proyecto): ?><tr><td><span class="project-name"><?= htmlspecialchars($proyecto['nombre_proyecto'], ENT_QUOTES, 'UTF-8') ?></span></td><td><?= htmlspecialchars($proyecto['razon_social'], ENT_QUOTES, 'UTF-8') ?></td><td><?= htmlspecialchars($proyecto['nombre_completo'], ENT_QUOTES, 'UTF-8') ?></td><td><?= htmlspecialchars($estadoLabels[$proyecto['estado']] ?? $proyecto['estado'], ENT_QUOTES, 'UTF-8') ?></td><td><?= $proyecto['presupuesto'] !== null ? 'S/ ' . number_format((float) $proyecto['presupuesto'], 2) : 'No definido' ?></td></tr><?php endforeach; ?></tbody></table></div><?php else: ?><div class="empty-state"><strong>No hay proyectos registrados</strong><span>Los nuevos proyectos aparecerán aquí.</span></div><?php endif; ?>
            </section>
        </section>
    </main>
</body>
</html>
