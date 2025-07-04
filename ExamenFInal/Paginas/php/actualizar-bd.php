<?php
// Configuración para phpMyAdmin
$servidor = "localhost";
$usuario = "root";
$password = "";
$BD = "conecta_cusco_db";

// Conectar a la base de datos
try {
    $con = new mysqli($servidor, $usuario, $password, $BD);
    $con->set_charset("utf8mb4");
} catch (mysqli_sql_exception $e) {
    die("Error de conexión: " . $e->getMessage());
}

echo "<h2>🔄 Actualizando Base de Datos</h2>";

// 1. Agregar columna telefono a usuarios si no existe
$sql_check_telefono = "SHOW COLUMNS FROM usuarios LIKE 'telefono'";
$result = $con->query($sql_check_telefono);

if ($result->num_rows == 0) {
    $sql_add_telefono = "ALTER TABLE usuarios ADD COLUMN telefono VARCHAR(20) AFTER correo_electronico";
    if ($con->query($sql_add_telefono) === TRUE) {
        echo "✅ Columna 'telefono' agregada a la tabla usuarios.<br>";
    } else {
        echo "❌ Error agregando columna telefono: " . $con->error . "<br>";
    }
} else {
    echo "✅ Columna 'telefono' ya existe en usuarios.<br>";
}

// 2. Actualizar tipo_cuenta para incluir 'empresa'
$sql_update_enum = "ALTER TABLE usuarios MODIFY COLUMN tipo_cuenta ENUM('cliente', 'profesional', 'empresa') NOT NULL DEFAULT 'cliente'";
if ($con->query($sql_update_enum) === TRUE) {
    echo "✅ Tipo de cuenta actualizado para incluir 'empresa'.<br>";
} else {
    echo "❌ Error actualizando tipo de cuenta: " . $con->error . "<br>";
}

// 3. Crear tabla empresas si no existe
$sql_empresas = "CREATE TABLE IF NOT EXISTS empresas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    nombre_empresa VARCHAR(255) NOT NULL,
    telefono_empresa VARCHAR(20),
    direccion_empresa TEXT,
    sitio_web VARCHAR(255),
    horarios VARCHAR(255),
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    INDEX idx_usuario_id (usuario_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

if ($con->query($sql_empresas) === TRUE) {
    echo "✅ Tabla 'empresas' creada o ya existente.<br>";
} else {
    echo "❌ Error creando tabla empresas: " . $con->error . "<br>";
}

// 4. Agregar columnas faltantes a servicios_publicados
$columns_to_add = [
    'precio' => "ADD COLUMN precio DECIMAL(10,2) AFTER foto_perfil",
    'disponibilidad' => "ADD COLUMN disponibilidad ENUM('disponible', 'no_disponible') DEFAULT 'disponible' AFTER precio",
    'calificacion_promedio' => "ADD COLUMN calificacion_promedio DECIMAL(3,2) DEFAULT 0.00 AFTER disponibilidad",
    'total_calificaciones' => "ADD COLUMN total_calificaciones INT DEFAULT 0 AFTER calificacion_promedio"
];

foreach ($columns_to_add as $column => $sql_part) {
    $sql_check = "SHOW COLUMNS FROM servicios_publicados LIKE '$column'";
    $result = $con->query($sql_check);
    
    if ($result->num_rows == 0) {
        $sql_add = "ALTER TABLE servicios_publicados $sql_part";
        if ($con->query($sql_add) === TRUE) {
            echo "✅ Columna '$column' agregada a servicios_publicados.<br>";
        } else {
            echo "❌ Error agregando columna $column: " . $con->error . "<br>";
        }
    } else {
        echo "✅ Columna '$column' ya existe en servicios_publicados.<br>";
    }
}

// 5. Agregar índices faltantes
$indexes_to_add = [
    'idx_disponibilidad' => "ADD INDEX idx_disponibilidad (disponibilidad)",
    'idx_calificacion' => "ADD INDEX idx_calificacion (calificacion_promedio)",
    'idx_fecha_publicacion' => "ADD INDEX idx_fecha_publicacion (fecha_publicacion)"
];

foreach ($indexes_to_add as $index => $sql_part) {
    $sql_check = "SHOW INDEX FROM servicios_publicados WHERE Key_name = '$index'";
    $result = $con->query($sql_check);
    
    if ($result->num_rows == 0) {
        $sql_add = "ALTER TABLE servicios_publicados $sql_part";
        if ($con->query($sql_add) === TRUE) {
            echo "✅ Índice '$index' agregado a servicios_publicados.<br>";
        } else {
            echo "❌ Error agregando índice $index: " . $con->error . "<br>";
        }
    } else {
        echo "✅ Índice '$index' ya existe en servicios_publicados.<br>";
    }
}

// 6. Actualizar tabla resenas
$sql_update_resenas = "ALTER TABLE resenas MODIFY COLUMN estado ENUM('activa', 'eliminada') DEFAULT 'activa'";
if ($con->query($sql_update_resenas) === TRUE) {
    echo "✅ Tabla 'resenas' actualizada.<br>";
} else {
    echo "❌ Error actualizando resenas: " . $con->error . "<br>";
}

// 7. Agregar índice de fecha a resenas
$sql_check_fecha_resena = "SHOW INDEX FROM resenas WHERE Key_name = 'idx_fecha_resena'";
$result = $con->query($sql_check_fecha_resena);

if ($result->num_rows == 0) {
    $sql_add_fecha_resena = "ALTER TABLE resenas ADD INDEX idx_fecha_resena (fecha_resena)";
    if ($con->query($sql_add_fecha_resena) === TRUE) {
        echo "✅ Índice de fecha agregado a resenas.<br>";
    } else {
        echo "❌ Error agregando índice de fecha a resenas: " . $con->error . "<br>";
    }
} else {
    echo "✅ Índice de fecha ya existe en resenas.<br>";
}

// 8. Actualizar tabla contactos
$sql_update_contactos = "ALTER TABLE contactos MODIFY COLUMN mensaje TEXT NOT NULL";
if ($con->query($sql_update_contactos) === TRUE) {
    echo "✅ Tabla 'contactos' actualizada.<br>";
} else {
    echo "❌ Error actualizando contactos: " . $con->error . "<br>";
}

// 9. Agregar índice de fecha a contactos
$sql_check_fecha_contacto = "SHOW INDEX FROM contactos WHERE Key_name = 'idx_fecha_contacto'";
$result = $con->query($sql_check_fecha_contacto);

if ($result->num_rows == 0) {
    $sql_add_fecha_contacto = "ALTER TABLE contactos ADD INDEX idx_fecha_contacto (fecha_contacto)";
    if ($con->query($sql_add_fecha_contacto) === TRUE) {
        echo "✅ Índice de fecha agregado a contactos.<br>";
    } else {
        echo "❌ Error agregando índice de fecha a contactos: " . $con->error . "<br>";
    }
} else {
    echo "✅ Índice de fecha ya existe en contactos.<br>";
}

// 10. Insertar datos de ejemplo actualizados
$sql_insert_ejemplo = "INSERT IGNORE INTO usuarios (nombre_completo, correo_electronico, telefono, contrasena_hash, tipo_cuenta) VALUES 
('Juan Pérez', 'juan@ejemplo.com', '984123456', '" . password_hash('123456', PASSWORD_DEFAULT) . "', 'profesional'),
('María García', 'maria@ejemplo.com', '984654321', '" . password_hash('123456', PASSWORD_DEFAULT) . "', 'cliente'),
('Empresa Constructora ABC', 'abc@empresa.com', '984789123', '" . password_hash('123456', PASSWORD_DEFAULT) . "', 'empresa')";

if ($con->query($sql_insert_ejemplo) === TRUE) {
    echo "✅ Datos de ejemplo insertados (si no existían).<br>";
} else {
    echo "❌ Error insertando datos de ejemplo: " . $con->error . "<br>";
}

// 11. Insertar datos de empresa de ejemplo
$sql_empresa_ejemplo = "INSERT IGNORE INTO empresas (usuario_id, nombre_empresa, telefono_empresa, direccion_empresa, sitio_web, horarios) VALUES 
(3, 'Empresa Constructora ABC', '984789123', 'Av. Principal 123, Cusco', 'www.abc-constructora.com', 'Lunes a Viernes 8:00 - 18:00')";

if ($con->query($sql_empresa_ejemplo) === TRUE) {
    echo "✅ Datos de empresa de ejemplo insertados.<br>";
} else {
    echo "❌ Error insertando datos de empresa: " . $con->error . "<br>";
}

$con->close();

echo "<br><strong>🎉 ¡Base de datos actualizada correctamente!</strong>";
echo "<br><br><strong>Credenciales de prueba:</strong>";
echo "<br>• Profesional: juan@ejemplo.com / 123456";
echo "<br>• Cliente: maria@ejemplo.com / 123456";
echo "<br>• Empresa: abc@empresa.com / 123456";
echo "<br><br><a href='../index.html'>← Volver al inicio</a>";
?> 