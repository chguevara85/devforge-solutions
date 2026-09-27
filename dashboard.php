<?php
require __DIR__ . '/php/validar_sesion.php';
require __DIR__ . '/php/conexion.php';

$estadisticas = [
    'proyectos' => (int) $conexion->query('SELECT COUNT(*) FROM proyectos')->fetchColumn(),
    'clientes' => (int) $conexion->query('SELECT COUNT(*) FROM clientes')->fetchColumn(),
    'cotizaciones' => (int) $conexion->query('SELECT COUNT(*) FROM cotizaciones')->fetchColumn(),
    'usuarios' => (int) $conexion->query('SELECT COUNT(*) FROM usuarios WHERE estado = "activo"')->fetchColumn()
];

$nombre = htmlspecialchars($_SESSION['nombre'], ENT_QUOTES, 'UTF-8');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DevForge Solutions | Dashboard</title>
    <link rel="stylesheet" href="css/estilos.css">
</head>
<body class="app-page">
    <header class="app-header">
        <a class="app-brand" href="dashboard.php">DevForge <span>Solutions</span></a>
        <div class="user-menu">
            <span><?= $nombre ?></span>
            <a href="php/logout.php">Cerrar sesión</a>
        </div>
    </header>

    <main class="app-main">
        <section class="page-heading">
            <p class="eyebrow">Panel principal</p>
            <h3>HOLA, <?= $nombre ?>.</h3>
            <p class="muted-text">Este es el resumen operativo de DevForge Solutions.</p>
        </section>

        <section class="metric-grid" aria-label="Indicadores principales">
            <article class="metric-card">
                <span>Proyectos</span>
                <strong><?= $estadisticas['proyectos'] ?></strong>
                <a href="proyectos.html">Ver proyectos</a>
            </article>
            <article class="metric-card">
                <span>Clientes</span>
                <strong><?= $estadisticas['clientes'] ?></strong>
                <a href="clientes.html">Ver clientes</a>
            </article>
            <article class="metric-card">
                <span>Cotizaciones</span>
                <strong><?= $estadisticas['cotizaciones'] ?></strong>
                <a href="cotizaciones.html">Ver cotizaciones</a>
            </article>
            <article class="metric-card">
                <span>Usuarios activos</span>
                <strong><?= $estadisticas['usuarios'] ?></strong>
                <a href="reportes.html">Ver reportes</a>
            </article>
        </section>

        <section class="quick-section">
            <div>
                <p class="eyebrow">Accesos rápidos</p>
                <h2>Módulos del proyecto</h2>
            </div>
            <nav class="module-grid" aria-label="Módulos de DevForge Solutions">
                <a href="proyectos.php"><strong>Proyectos</strong><span>Seguimiento de entregas y estados.</span></a>
                <a href="clientes.php"><strong>Clientes</strong><span>Contactos e historial comercial.</span></a>
                <a href="cotizaciones.php"><strong>Cotizaciones</strong><span>Presupuestos y aprobaciones.</span></a>
                <a href="reportes.php"><strong>Reportes</strong><span>Indicadores para la toma de decisiones.</span></a>
            </nav>
        </section>
    </main>
</body>
</html>
