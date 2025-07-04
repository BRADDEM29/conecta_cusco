<?php
// Script para verificar y corregir la estructura de la base de datos
require_once 'config.php';

try {
    $con = conectarDB();
    
    echo "<h2>🔍 Verificando estructura de la base de datos...</h2>";
    
    // Verificar si existe la tabla resenas
    $result = $con->query("SHOW TABLES LIKE 'resenas'");
    if ($result->num_rows === 0) {
        echo "❌ La tabla 'resenas' no existe. Creándola...<br>";
        
        $sql_resenas = "CREATE TABLE resenas (
            id INT AUTO_INCREMENT PRIMARY KEY,
            servicio_id INT NOT NULL,
            cliente_id INT NOT NULL,
            profesional_id INT NOT NULL,
            calificacion INT NOT NULL CHECK (calificacion >= 1 AND calificacion <= 5),
            comentario TEXT,
            fecha_resena TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            estado ENUM('activa', 'eliminada') DEFAULT 'activa',
            FOREIGN KEY (servicio_id) REFERENCES servicios_publicados(id) ON DELETE CASCADE,
            FOREIGN KEY (cliente_id) REFERENCES usuarios(id) ON DELETE CASCADE,
            FOREIGN KEY (profesional_id) REFERENCES usuarios(id) ON DELETE CASCADE,
            INDEX idx_servicio_id (servicio_id),
            INDEX idx_profesional_id (profesional_id),
            INDEX idx_calificacion (calificacion),
            INDEX idx_fecha_resena (fecha_resena)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
        
        if ($con->query($sql_resenas) === TRUE) {
            echo "✅ Tabla 'resenas' creada exitosamente.<br>";
        } else {
            echo "❌ Error creando tabla resenas: " . $con->error . "<br>";
        }
    } else {
        echo "✅ La tabla 'resenas' existe.<br>";
        
        // Verificar si tiene la columna estado
        $result = $con->query("SHOW COLUMNS FROM resenas LIKE 'estado'");
        if ($result->num_rows === 0) {
            echo "❌ La columna 'estado' no existe en resenas. Agregándola...<br>";
            
            $sql_add_estado = "ALTER TABLE resenas ADD COLUMN estado ENUM('activa', 'eliminada') DEFAULT 'activa' AFTER fecha_resena";
            if ($con->query($sql_add_estado) === TRUE) {
                echo "✅ Columna 'estado' agregada exitosamente.<br>";
            } else {
                echo "❌ Error agregando columna estado: " . $con->error . "<br>";
            }
        } else {
            echo "✅ La columna 'estado' existe en resenas.<br>";
        }
    }
    
    // Verificar si existe la tabla servicios_publicados
    $result = $con->query("SHOW TABLES LIKE 'servicios_publicados'");
    if ($result->num_rows === 0) {
        echo "❌ La tabla 'servicios_publicados' no existe. Creándola...<br>";
        
        $sql_servicios = "CREATE TABLE servicios_publicados (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            titulo VARCHAR(255) NOT NULL,
            categoria VARCHAR(100) NOT NULL,
            descripcion TEXT NOT NULL,
            cobertura VARCHAR(255) NOT NULL,
            telefono VARCHAR(20),
            email VARCHAR(255) NOT NULL,
            foto_perfil VARCHAR(255),
            precio DECIMAL(10,2),
            disponibilidad ENUM('disponible', 'no_disponible') DEFAULT 'disponible',
            calificacion_promedio DECIMAL(3,2) DEFAULT 0.00,
            total_calificaciones INT DEFAULT 0,
            fecha_publicacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            estado ENUM('activo', 'inactivo', 'eliminado') DEFAULT 'activo',
            FOREIGN KEY (user_id) REFERENCES usuarios(id) ON DELETE CASCADE,
            INDEX idx_user_id (user_id),
            INDEX idx_categoria (categoria),
            INDEX idx_estado (estado),
            INDEX idx_disponibilidad (disponibilidad),
            INDEX idx_calificacion (calificacion_promedio),
            INDEX idx_fecha_publicacion (fecha_publicacion)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
        
        if ($con->query($sql_servicios) === TRUE) {
            echo "✅ Tabla 'servicios_publicados' creada exitosamente.<br>";
        } else {
            echo "❌ Error creando tabla servicios_publicados: " . $con->error . "<br>";
        }
    } else {
        echo "✅ La tabla 'servicios_publicados' existe.<br>";
        
        // Verificar si tiene la columna estado
        $result = $con->query("SHOW COLUMNS FROM servicios_publicados LIKE 'estado'");
        if ($result->num_rows === 0) {
            echo "❌ La columna 'estado' no existe en servicios_publicados. Agregándola...<br>";
            
            $sql_add_estado = "ALTER TABLE servicios_publicados ADD COLUMN estado ENUM('activo', 'inactivo', 'eliminado') DEFAULT 'activo' AFTER fecha_actualizacion";
            if ($con->query($sql_add_estado) === TRUE) {
                echo "✅ Columna 'estado' agregada exitosamente.<br>";
            } else {
                echo "❌ Error agregando columna estado: " . $con->error . "<br>";
            }
        } else {
            echo "✅ La columna 'estado' existe en servicios_publicados.<br>";
        }
    }
    
    // Insertar algunos datos de prueba si no existen
    $result = $con->query("SELECT COUNT(*) as count FROM servicios_publicados");
    $row = $result->fetch_assoc();
    
    if ($row['count'] == 0) {
        echo "📝 Insertando datos de prueba...<br>";
        
        // Insertar un servicio de prueba
        $sql_insert = "INSERT INTO servicios_publicados (user_id, titulo, categoria, descripcion, cobertura, email, precio, disponibilidad) VALUES 
        (1, 'Servicio de Plomería Profesional', 'plomeria', 'Ofrezco servicios de plomería de alta calidad. Reparación de tuberías, instalación de grifos, desatascos y más. Trabajo con garantía y precios justos.', 'Cusco Centro', 'juan@ejemplo.com', 50.00, 'disponible')";
        
        if ($con->query($sql_insert) === TRUE) {
            echo "✅ Servicio de prueba insertado.<br>";
        } else {
            echo "❌ Error insertando servicio de prueba: " . $con->error . "<br>";
        }
    } else {
        echo "✅ Ya existen servicios en la base de datos.<br>";
    }
    
    cerrarConexion($con);
    
    echo "<br><strong>🎉 Verificación completada!</strong><br>";
    echo "<a href='Paginas/html/buscar-servicios.php'>← Ir a Buscar Servicios</a>";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage();
}
?> 