<?php
// ========================================
// CONFIGURACIÓN PARA RAILWAY
// ========================================
// Railway usa variables de entorno para la configuración

// DATOS DE LA BASE DE DATOS (Railway proporciona PostgreSQL por defecto)
// Estas variables se configuran en el dashboard de Railway
define('DB_HOST', $_ENV['DB_HOST'] ?? 'localhost');
define('DB_NAME', $_ENV['DB_NAME'] ?? 'railway');
define('DB_USER', $_ENV['DB_USER'] ?? 'postgres');
define('DB_PASS', $_ENV['DB_PASS'] ?? '');
define('DB_PORT', $_ENV['DB_PORT'] ?? '5432');

// Configuración de la aplicación
function getBaseUrl() {
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    
    // Para Railway - usar la URL proporcionada por Railway
    return $protocol . '://' . $host;
}

define('SITE_URL', getBaseUrl());
define('UPLOAD_PATH', 'uploads/');
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'gif', 'webp']);

// Configuración de seguridad
define('SESSION_TIMEOUT', 3600); // 1 hora
define('PASSWORD_MIN_LENGTH', 6);

// Función para conectar a la base de datos usando PostgreSQL
function conectarDB() {
    try {
        // Railway usa PostgreSQL por defecto
        $dsn = "pgsql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME;
        $pdo = new PDO($dsn, DB_USER, DB_PASS);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch (Exception $e) {
        die("Error de conexión: " . $e->getMessage());
    }
}

// Función para conectar usando MySQLi (si prefieres MySQL)
function conectarDB_MySQL() {
    try {
        $con = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT);
        $con->set_charset("utf8mb4");
        
        if ($con->connect_error) {
            throw new Exception("Error de conexión: " . $con->connect_error);
        }
        return $con;
    } catch (Exception $e) {
        die("Error de conexión: " . $e->getMessage());
    }
}

// Función para validar archivos subidos
function validarArchivo($archivo) {
    $extension = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));
    
    if (!in_array($extension, ALLOWED_EXTENSIONS)) {
        return false;
    }
    
    if ($archivo['size'] > MAX_FILE_SIZE) {
        return false;
    }
    
    return true;
}

// Función para sanitizar datos
function sanitizarDatos($datos) {
    return htmlspecialchars(strip_tags(trim($datos)));
}

// Función para generar token de seguridad
function generarToken() {
    return bin2hex(random_bytes(32));
}

// Función para verificar si el usuario está logueado
function estaLogueado() {
    return isset($_SESSION['usuario_id']);
}

// Función para verificar el tipo de usuario
function obtenerTipoUsuario() {
    return $_SESSION['tipo_cuenta'] ?? null;
}

// Función para cerrar la conexión
function cerrarConexion($conexion) {
    if ($conexion) {
        $conexion = null; // Para PDO
    }
}
?> 