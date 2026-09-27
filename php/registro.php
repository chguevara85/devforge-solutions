<?php
require 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Validaciones
    if (empty($nombre) || empty($email) || empty($password)) {
        die("Todos los campos son obligatorios.");
    }

    // Hash de la contraseña
    $hash = password_hash($password, PASSWORD_BCRYPT);

    // Insertar en la base de datos
    $sql = "INSERT INTO usuarios (nombre_completo, email, password_hash, id_rol)
            VALUES (:nombre, :email, :hash, 2)";
    $stmt = $conexion->prepare($sql);

    try {
        $stmt->execute([
            ':nombre' => $nombre,
            ':email' => $email,
            ':hash' => $hash
        ]);
        header("Location: ../index.html?registro=exitoso");
        exit();
    } catch (PDOException $e) {
        if ($e->getCode() == 23000) {
            die("El email ya está registrado.");
        }
        die("Error al registrar: " . $e->getMessage());
    }
}
?>