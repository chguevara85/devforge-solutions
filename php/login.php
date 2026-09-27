<?php
session_start();
require 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $sql = "SELECT * FROM usuarios WHERE email = :email AND estado = 'activo'";
    $stmt = $conexion->prepare($sql);
    $stmt->execute([':email' => $email]);
    $usuario = $stmt->fetch();

    if ($usuario && password_verify($password, $usuario['password_hash'])) {
        // Actualizar último acceso
        $update = $conexion->prepare("UPDATE usuarios SET ultimo_acceso = NOW() WHERE id_usuario = ?");
        $update->execute([$usuario['id_usuario']]);

        // Crear sesión
        session_regenerate_id(true);
        $_SESSION['id_usuario'] = $usuario['id_usuario'];
        $_SESSION['nombre'] = $usuario['nombre_completo'];
        $_SESSION['id_rol'] = $usuario['id_rol'];

        header("Location: ../dashboard.php");
        exit();
    } else {
        header("Location: ../index.html?error=1");
        exit();
    }
}
?>