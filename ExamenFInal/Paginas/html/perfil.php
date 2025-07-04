<?php
session_start();

// Verificar si el usuario está logueado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.html?mensaje=" . urlencode("Debes iniciar sesión para acceder al perfil"));
    exit();
}

// Verificar que NO sea una empresa (las empresas no pueden acceder al perfil individual)
if ($_SESSION['tipo_cuenta'] === 'empresa') {
    header("Location: profesional.php?mensaje=" . urlencode("Las empresas no pueden acceder al perfil individual"));
    exit();
}

// Si es cliente o profesional, redirigir al perfil HTML
header("Location: perfil.html");
exit();
?> 