<?php
// ========================================
// VERIFICACIÓN DE ARCHIVOS PARA RAILWAY
// ========================================
// Este archivo verifica que todos los archivos necesarios estén presentes

echo "<h1>🔍 Verificación de Archivos para Railway</h1>";
echo "<h2>Conecta Cusco - Archivos Críticos</h2>";

// Archivos críticos que deben estar en la raíz
$archivos_criticos = [
    'composer.json' => 'Define que es una aplicación PHP',
    'index.php' => 'Punto de entrada para Railway',
    'nixpacks.toml' => 'Configuración específica de Nixpacks',
    'railway.json' => 'Configuración de Railway',
    'Procfile' => 'Configuración alternativa',
    'index.html' => 'Página principal',
    'config-railway.php' => 'Configuración para Railway'
];

echo "<h3>📁 Archivos Críticos en Raíz:</h3>";
echo "<ul>";

$todos_presentes = true;
foreach ($archivos_criticos as $archivo => $descripcion) {
    if (file_exists($archivo)) {
        echo "<li>✅ <strong>$archivo</strong> - $descripcion</li>";
    } else {
        echo "<li>❌ <strong>$archivo</strong> - $descripcion (FALTANTE)</li>";
        $todos_presentes = false;
    }
}
echo "</ul>";

// Verificar carpetas importantes
$carpetas_importantes = [
    'Estilos' => 'Archivos CSS',
    'Imagenes' => 'Imágenes del sitio',
    'js' => 'JavaScript',
    'Paginas' => 'Páginas PHP y HTML',
    'uploads' => 'Archivos subidos'
];

echo "<h3>📂 Carpetas Importantes:</h3>";
echo "<ul>";
foreach ($carpetas_importantes as $carpeta => $descripcion) {
    if (is_dir($carpeta)) {
        echo "<li>✅ <strong>$carpeta/</strong> - $descripcion</li>";
    } else {
        echo "<li>❌ <strong>$carpeta/</strong> - $descripcion (FALTANTE)</li>";
        $todos_presentes = false;
    }
}
echo "</ul>";

// Verificar archivos de configuración
echo "<h3>⚙️ Archivos de Configuración:</h3>";
echo "<ul>";
$archivos_config = [
    'config-railway.php' => 'Configuración Railway',
    'CreacionDB-Railway.php' => 'Script de creación BD',
    'verificar-railway.php' => 'Verificación de despliegue'
];

foreach ($archivos_config as $archivo => $descripcion) {
    if (file_exists($archivo)) {
        echo "<li>✅ <strong>$archivo</strong> - $descripcion</li>";
    } else {
        echo "<li>❌ <strong>$archivo</strong> - $descripcion (FALTANTE)</li>";
        $todos_presentes = false;
    }
}
echo "</ul>";

// Resumen final
echo "<h3>🎯 Resumen:</h3>";
if ($todos_presentes) {
    echo "<div style='background-color: #e8f5e8; padding: 15px; border-radius: 5px;'>";
    echo "<p><strong>✅ ¡Todos los archivos críticos están presentes!</strong></p>";
    echo "<p>Tu proyecto está listo para desplegar en Railway.</p>";
    echo "</div>";
} else {
    echo "<div style='background-color: #ffe8e8; padding: 15px; border-radius: 5px;'>";
    echo "<p><strong>❌ Faltan algunos archivos críticos.</strong></p>";
    echo "<p>Por favor, asegúrate de que todos los archivos estén presentes antes de subir a Railway.</p>";
    echo "</div>";
}

echo "<h3>📋 Pasos para Railway:</h3>";
echo "<ol>";
echo "<li>Sube todos los archivos a GitHub/GitLab</li>";
echo "<li>Ve a <a href='https://railway.com/new' target='_blank'>Railway</a></li>";
echo "<li>Conecta tu repositorio</li>";
echo "<li>Agrega un servicio PostgreSQL</li>";
echo "<li>Renombra <code>config-railway.php</code> a <code>config.php</code></li>";
echo "<li>Accede a <code>CreacionDB-Railway.php</code> para crear la BD</li>";
echo "<li>Verifica con <code>verificar-railway.php</code></li>";
echo "</ol>";

echo "<p><strong>💡 Consejo:</strong> Elimina este archivo después de confirmar que todo está correcto.</p>";
?> 