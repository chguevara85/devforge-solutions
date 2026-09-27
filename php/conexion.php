<?php
// =====================================================
// conexion.php
// Conexión a la base de datos MySQL con PDO
// =====================================================

// Parámetros de configuración
$host = "localhost";        // Servidor de la base de datos
$dbname = "devforge_db";    // Nombre de la base de datos
$usuario = "root";          // Usuario de MySQL
$password = "";             // Contraseña (vacía en local)

try {
    // Cadena de conexión DSN
    $dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";

    // Crear conexión PDO
    $conexion = new PDO($dsn, $usuario, $password);

    // Configurar manejo de errores
    $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Configurar modo de fetch por defecto
    $conexion->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    // Mensaje de conexión exitosa (comentar en producción)
    // echo "Conexión exitosa a la base de datos";

} catch (PDOException $e) {
    error_log('Error de conexión a la base de datos: ' . $e->getMessage());
    die('No fue posible conectar con la base de datos.');
}
?>