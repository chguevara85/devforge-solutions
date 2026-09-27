<?php
require __DIR__ . '/php/validar_sesion.php';
require __DIR__ . '/php/conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $razonSocial = trim($_POST['razon_social'] ?? '');
    $ruc = trim($_POST['ruc'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $telefono = trim($_POST['telefono'] ?? '');

    if ($razonSocial === '') {
        header('Location: clientes.php?error=razon_social');
        exit();
    }

    try {
        $sql = 'INSERT INTO clientes (razon_social, ruc, telefono, email) VALUES (:razon_social, :ruc, :telefono, :email)';
        $stmt = $conexion->prepare($sql);
        $stmt->execute([
            ':razon_social' => $razonSocial,
            ':ruc' => $ruc !== '' ? $ruc : null,
            ':telefono' => $telefono !== '' ? $telefono : null,
            ':email' => $email !== '' ? $email : null
        ]);
        header('Location: clientes.php?guardado=1');
        exit();
    } catch (PDOException $e) {
        $codigoError = $e->getCode() === '23000' ? 'ruc_duplicado' : 'bd';
        header('Location: clientes.php?error=' . $codigoError);
        exit();
    }
}

$clientes = $conexion->query('SELECT id_cliente, razon_social, ruc, telefono, email, fecha_registro FROM clientes ORDER BY id_cliente DESC')->fetchAll();
$mensaje = $_GET['guardado'] ?? '';
$error = $_GET['error'] ?? '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DevForge Solutions | Clientes</title>
    <link rel="stylesheet" href="css/estilos.css">
</head>
<body class="app-page">
    <header class="app-header">
        <a class="app-brand" href="dashboard.php">DevForge <span>Solutions</span></a>
        <div class="user-menu"><a href="dashboard.php">Dashboard</a><a href="php/logout.php">Cerrar sesión</a></div>
    </header>
    <main class="app-main">
        <section class="page-heading">
            <p class="eyebrow">Módulo comercial</p>
            <h1>Clientes</h1>
            <p class="muted-text">Administra la información de contacto y el historial de cada cliente.</p>
        </section>
        <?php if ($mensaje === '1'): ?><div class="notice success-message">Cliente registrado correctamente.</div><?php endif; ?>
        <?php if ($error === 'razon_social'): ?><div class="notice error-message">La razón social es obligatoria.</div><?php endif; ?>
        <?php if ($error === 'ruc_duplicado'): ?><div class="notice error-message">El RUC ya está registrado.</div><?php endif; ?>
        <?php if ($error === 'bd'): ?><div class="notice error-message">No fue posible registrar el cliente.</div><?php endif; ?>
        <section class="workspace-grid">
            <form class="panel" action="clientes.php" method="post">
                <p class="eyebrow">Nuevo registro</p>
                <h2>Agregar cliente</h2>
                <label for="razon-social">Razón social</label>
                <input id="razon-social" name="razon_social" type="text" maxlength="150" required>
                <label for="ruc">RUC</label>
                <input id="ruc" name="ruc" type="text" maxlength="11" pattern="[0-9]{11}">
                <label for="email-cliente">Correo electrónico</label>
                <input id="email-cliente" name="email" type="email" maxlength="100">
                <label for="telefono">Teléfono</label>
                <input id="telefono" name="telefono" type="tel" maxlength="15">
                <button type="submit">Guardar cliente</button>
            </form>
            <section class="panel table-panel">
                <div class="panel-heading"><div><p class="eyebrow">Directorio</p><h2>Clientes registrados</h2></div><span class="status-chip"><?= count($clientes) ?> clientes</span></div>
                <?php if ($clientes): ?>
                    <div class="data-table-wrap"><table class="data-table"><thead><tr><th>Razón social</th><th>RUC</th><th>Contacto</th><th>Registro</th></tr></thead><tbody>
                    <?php foreach ($clientes as $cliente): ?><tr><td><?= htmlspecialchars($cliente['razon_social'], ENT_QUOTES, 'UTF-8') ?></td><td><?= htmlspecialchars($cliente['ruc'] ?: 'No registrado', ENT_QUOTES, 'UTF-8') ?></td><td><?= htmlspecialchars($cliente['email'] ?: $cliente['telefono'] ?: 'Sin contacto', ENT_QUOTES, 'UTF-8') ?></td><td><?= htmlspecialchars($cliente['fecha_registro'], ENT_QUOTES, 'UTF-8') ?></td></tr><?php endforeach; ?>
                    </tbody></table></div>
                <?php else: ?><div class="empty-state"><strong>Aún no hay clientes</strong><span>Registra el primer cliente para comenzar el seguimiento.</span></div><?php endif; ?>
            </section>
        </section>
    </main>
</body>
</html>
