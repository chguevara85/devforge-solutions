<?php
session_set_cookie_params([
    'httponly' => true,
    'samesite' => 'Lax',
]);
session_start();

if (empty($_SESSION['id_usuario'])) {
	header('Location: index.html?error=sesion');
	exit();
}
