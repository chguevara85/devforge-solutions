<?php
require __DIR__ . '/php/validar_sesion.php';
require __DIR__ . '/php/conexion.php';

$totales = [
    'proyectos' => (int) $conexion->query('SELECT COUNT(*) FROM proyectos')->fetchColumn(),
    'clientes' => (int) $conexion->query('SELECT COUNT(*) FROM clientes')->fetchColumn(),
    'cotizaciones' => (int) $conexion->query('SELECT COUNT(*) FROM cotizaciones')->fetchColumn(),
    'monto' => (float) $conexion->query("SELECT COALESCE(SUM(monto), 0) FROM cotizaciones WHERE estado <> 'rechazada'")->fetchColumn()
];

$estados = $conexion->query('SELECT estado, COUNT(*) AS total FROM proyectos GROUP BY estado ORDER BY estado')->fetchAll();
$estadoLabels = ['cotizacion' => 'En cotización', 'desarrollo' => 'En desarrollo', 'pruebas' => 'En pruebas', 'entregado' => 'Entregados'];
$maximoEstado = 1;
foreach ($estados as $estado) {
    $maximoEstado = max($maximoEstado, (int) $estado['total']);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DevForge Solutions | Reportes</title>
    <link rel="stylesheet" href="css/estilos.css">
</head>
<body class="app-page">
    <header class="app-header"><a class="app-brand" href="dashboard.php">DevForge <span>Solutions</span></a><div class="user-menu"><a href="dashboard.php">Dashboard</a><a href="php/logout.php">Cerrar sesión</a></div></header>
    <main class="app-main">
        <section class="page-heading"><p class="eyebrow">Información para decidir</p><h1>Reportes</h1><p class="muted-text">Consulta indicadores clave para conocer la situación del negocio.</p></section>
        <section class="report-grid">
            <article class="report-card"><span>Proyectos registrados</span><strong><?= $totales['proyectos'] ?></strong><small>Todos los estados</small></article>
            <article class="report-card"><span>Clientes registrados</span><strong><?= $totales['clientes'] ?></strong><small>Directorio comercial</small></article>
            <article class="report-card"><span>Ingresos proyectados</span><strong>S/ <?= number_format($totales['monto'], 2) ?></strong><small>Cotizaciones no rechazadas</small></article>
        </section>
        <section class="panel report-panel">
            <div class="panel-heading"><div><p class="eyebrow">Resumen operativo</p><h2>Proyectos por estado</h2></div><span class="status-chip"><?= $totales['cotizaciones'] ?> cotizaciones</span></div>
            <?php if ($estados): ?><div class="bar-chart" aria-label="Cantidad de proyectos por estado"><?php foreach ($estados as $estado): $porcentaje = ((int) $estado['total'] / $maximoEstado) * 100; ?><div class="bar-row"><span><?= htmlspecialchars($estadoLabels[$estado['estado']] ?? $estado['estado'], ENT_QUOTES, 'UTF-8') ?></span><div class="bar-track"><div class="bar-fill" style="width: <?= $porcentaje ?>%"></div></div><strong><?= (int) $estado['total'] ?></strong></div><?php endforeach; ?></div><?php else: ?><div class="empty-state"><strong>Aún no hay actividad para graficar</strong><span>Los indicadores se actualizarán cuando se registren proyectos y cotizaciones.</span></div><?php endif; ?>
        </section>
    </main>
</body>
</html>
