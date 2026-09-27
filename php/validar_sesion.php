<?php
session_start();

if (empty($_SESSION['id_usuario'])) {
	header('Location: index.html?error=sesion');
	exit();
}
