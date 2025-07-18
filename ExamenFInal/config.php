<?php
// Configuración de la base de datos
define('DB_HOST', 'localhost');
define('DB_NAME', 'conecta_cusco_db');
define('DB_USER', 'root');
define('DB_PASS', '');

// Configuración de la aplicación
define('SITE_NAME', 'Conecta Cusco');
define('SITE_URL', 'http://localhost/EvaluacionParcial');
define('UPLOAD_PATH', 'uploads/');
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'gif', 'webp']);

// Configuración de seguridad
define('SESSION_TIMEOUT', 3600); // 1 hora
define('PASSWORD_MIN_LENGTH', 6);

// Configuración de correo (para futuras funcionalidades)
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USER', 'tu-email@gmail.com');
define('SMTP_PASS', 'tu-contraseña');

// Función para conectar a la base de datos usando MySQLi
function conectarDB() {
    try {
        $con = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        $con->set_charset("utf8mb4");
        
        if ($con->connect_error) {
            throw new Exception("Error de conexión: " . $con->connect_error);
        }
        return $con;
    } catch (Exception $e) {
        die("Error de conexión: " . $e->getMessage());
    }
}

// Función para conectar usando PDO (alternativa)
function conectarDB_PDO() {
    try {
        $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8", DB_USER, DB_PASS);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch(PDOException $e) {
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
        $conexion->close();
    }
}
?> 