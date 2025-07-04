<?php
session_start();

// Verificar si el usuario está logueado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: /Paginas/html/login.html?mensaje=" . urlencode("Debes iniciar sesión para publicar servicios"));
    exit();
}

// Verificar que el usuario sea profesional o empresa
if ($_SESSION['tipo_cuenta'] != 'profesional' && $_SESSION['tipo_cuenta'] != 'empresa') {
    header("Location: cliente.php?mensaje=" . urlencode("Solo profesionales y empresas pueden publicar servicios"));
    exit();
}

// Si está autorizado, redirigir a la página HTML
header("Location: /Paginas/html/publicar.html");
exit();
?> 