<?php
require_once 'config.php';

echo "=== VERIFICACIÓN DE SERVICIOS PUBLICADOS ===\n\n";

try {
    $con = conectarDB();
    
    // Verificar si la tabla existe
    $result = $con->query("SHOW TABLES LIKE 'servicios_publicados'");
    if ($result->num_rows == 0) {
        echo "❌ ERROR: La tabla 'servicios_publicados' NO existe\n";
        exit;
    }
    echo "✅ La tabla 'servicios_publicados' existe\n";
    
    // Contar servicios
    $result = $con->query("SELECT COUNT(*) as total FROM servicios_publicados");
    $row = $result->fetch_assoc();
    echo "📊 Total de servicios publicados: " . $row['total'] . "\n\n";
    
    if ($row['total'] > 0) {
        // Mostrar servicios
        $result = $con->query("SELECT sp.*, u.nombre_completo, u.tipo_cuenta 
                               FROM servicios_publicados sp 
                               INNER JOIN usuarios u ON sp.user_id = u.id 
                               ORDER BY sp.fecha_publicacion DESC");
        
        echo "=== SERVICIOS EN LA BASE DE DATOS ===\n";
        while ($row = $result->fetch_assoc()) {
            echo "ID: " . $row['id'] . "\n";
            echo "Título: " . $row['titulo'] . "\n";
            echo "Categoría: " . $row['categoria'] . "\n";
            echo "Profesional: " . $row['nombre_completo'] . " (" . $row['tipo_cuenta'] . ")\n";
            echo "Cobertura: " . $row['cobertura'] . "\n";
            echo "Estado: " . $row['estado'] . "\n";
            echo "Fecha: " . $row['fecha_publicacion'] . "\n";
            echo "---\n";
        }
    }
    
    // Verificar estructura de la tabla
    echo "\n=== ESTRUCTURA DE LA TABLA ===\n";
    $result = $con->query("DESCRIBE servicios_publicados");
    while ($row = $result->fetch_assoc()) {
        echo $row['Field'] . " - " . $row['Type'] . " - " . $row['Null'] . " - " . $row['Default'] . "\n";
    }
    
    $con->close();
    
} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
}
?> 