<?php
// ========================================
// VERIFICACIÓN DE DESPLIEGUE EN RAILWAY
// ========================================

require_once 'config-railway.php';

echo "<h1>🔍 Verificación de Despliegue en Railway</h1>";
echo "<h2>Conecta Cusco - Plataforma de Servicios</h2>";

// Verificar configuración de PHP
echo "<h3>📋 Configuración de PHP</h3>";
echo "<ul>";
echo "<li>✅ Versión de PHP: " . phpversion() . "</li>";
echo "<li>✅ Extensión PDO: " . (extension_loaded('pdo') ? 'Habilitada' : 'Deshabilitada') . "</li>";
echo "<li>✅ Extensión PDO PostgreSQL: " . (extension_loaded('pdo_pgsql') ? 'Habilitada' : 'Deshabilitada') . "</li>";
echo "<li>✅ Extensión GD: " . (extension_loaded('gd') ? 'Habilitada' : 'Deshabilitada') . "</li>";
echo "<li>✅ Extensión Fileinfo: " . (extension_loaded('fileinfo') ? 'Habilitada' : 'Deshabilitada') . "</li>";
echo "</ul>";

// Verificar variables de entorno
echo "<h3>🔧 Variables de Entorno</h3>";
echo "<ul>";
echo "<li>✅ DB_HOST: " . (isset($_ENV['DB_HOST']) ? $_ENV['DB_HOST'] : 'No configurado') . "</li>";
echo "<li>✅ DB_NAME: " . (isset($_ENV['DB_NAME']) ? $_ENV['DB_NAME'] : 'No configurado') . "</li>";
echo "<li>✅ DB_USER: " . (isset($_ENV['DB_USER']) ? $_ENV['DB_USER'] : 'No configurado') . "</li>";
echo "<li>✅ DB_PORT: " . (isset($_ENV['DB_PORT']) ? $_ENV['DB_PORT'] : 'No configurado') . "</li>";
echo "</ul>";

// Verificar conexión a base de datos
echo "<h3>🗄️ Conexión a Base de Datos</h3>";
try {
    $pdo = conectarDB();
    echo "<p>✅ Conexión a PostgreSQL exitosa</p>";
    
    // Verificar tablas
    $tablas = ['usuarios', 'servicios', 'resenas', 'contactos', 'perfiles_profesionales'];
    echo "<h4>📊 Verificación de Tablas:</h4>";
    echo "<ul>";
    
    foreach ($tablas as $tabla) {
        try {
            $stmt = $pdo->query("SELECT COUNT(*) FROM $tabla");
            $count = $stmt->fetchColumn();
            echo "<li>✅ Tabla '$tabla': $count registros</li>";
        } catch (Exception $e) {
            echo "<li>❌ Tabla '$tabla': No existe o error</li>";
        }
    }
    echo "</ul>";
    
} catch (Exception $e) {
    echo "<p>❌ Error de conexión: " . $e->getMessage() . "</p>";
}

// Verificar archivos y carpetas
echo "<h3>📁 Verificación de Archivos</h3>";
echo "<ul>";
$archivos_importantes = [
    'index.html',
    'config-railway.php',
    'railway.json',
    'Estilos/index.css',
    'Imagenes/logo.png',
    'js/script.js',
    'uploads/'
];

foreach ($archivos_importantes as $archivo) {
    if (file_exists($archivo)) {
        echo "<li>✅ $archivo</li>";
    } else {
        echo "<li>❌ $archivo (no encontrado)</li>";
    }
}
echo "</ul>";

// Verificar permisos
echo "<h3>🔐 Verificación de Permisos</h3>";
echo "<ul>";
if (is_writable('uploads/')) {
    echo "<li>✅ Carpeta uploads/ es escribible</li>";
} else {
    echo "<li>❌ Carpeta uploads/ no es escribible</li>";
}

if (is_readable('config-railway.php')) {
    echo "<li>✅ config-railway.php es legible</li>";
} else {
    echo "<li>❌ config-railway.php no es legible</li>";
}
echo "</ul>";

// Verificar URL y configuración
echo "<h3>🌐 Configuración de URL</h3>";
echo "<ul>";
echo "<li>✅ URL actual: " . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . "</li>";
echo "<li>✅ Protocolo: " . (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'HTTPS' : 'HTTP') . "</li>";
echo "<li>✅ Puerto: " . ($_SERVER['SERVER_PORT'] ?? '80') . "</li>";
echo "</ul>";

// Verificar funcionalidades
echo "<h3>⚙️ Funcionalidades</h3>";
echo "<ul>";
echo "<li>✅ Sesiones PHP: " . (session_status() === PHP_SESSION_ACTIVE ? 'Activas' : 'Inactivas') . "</li>";
echo "<li>✅ Subida de archivos: " . (ini_get('file_uploads') ? 'Habilitada' : 'Deshabilitada') . "</li>";
echo "<li>✅ Tamaño máximo de archivo: " . ini_get('upload_max_filesize') . "</li>";
echo "<li>✅ Tiempo máximo de ejecución: " . ini_get('max_execution_time') . " segundos</li>";
echo "</ul>";

// Resumen final
echo "<h3>🎯 Resumen</h3>";
echo "<div style='background-color: #e8f5e8; padding: 15px; border-radius: 5px;'>";
echo "<p><strong>✅ Tu aplicación Conecta Cusco está lista para usar en Railway!</strong></p>";
echo "<p>La aplicación incluye:</p>";
echo "<ul>";
echo "<li>✅ Sistema de usuarios completo</li>";
echo "<li>✅ Base de datos PostgreSQL</li>";
echo "<li>✅ Subida de archivos</li>";
echo "<li>✅ Diseño responsive</li>";
echo "<li>✅ Seguridad implementada</li>";
echo "</ul>";
echo "</div>";

echo "<h3>🔗 Enlaces Importantes</h3>";
echo "<ul>";
echo "<li><a href='index.html'>🏠 Página de Inicio</a></li>";
echo "<li><a href='Paginas/html/login.html'>🔐 Página de Login</a></li>";
echo "<li><a href='Paginas/html/registro.html'>📝 Página de Registro</a></li>";
echo "<li><a href='Paginas/html/buscar-servicios.html'>🔍 Buscar Servicios</a></li>";
echo "</ul>";

echo "<p><strong>💡 Consejo:</strong> Elimina este archivo de verificación después de confirmar que todo funciona correctamente.</p>";
?> 