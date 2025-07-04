<?php
// Configuración para phpMyAdmin
$servidor = "localhost";
$usuario = "root"; // Usuario por defecto de phpMyAdmin
$password = ""; // Contraseña vacía por defecto en XAMPP/WAMP

// Conectar sin seleccionar base de datos
try {
    $con = new mysqli($servidor, $usuario, $password);
    $con->set_charset("utf8mb4");
    
    if ($con->connect_error) {
        throw new Exception("Error de conexión: " . $con->connect_error);
    }
} catch (Exception $e) {
    die("Error de conexión: " . $e->getMessage());
}

// Crear base de datos si no existe
$sql_crear_bd = "CREATE DATABASE IF NOT EXISTS conecta_cusco_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
if ($con->query($sql_crear_bd) === TRUE) {
    echo "✅ Base de datos 'conecta_cusco_db' creada o ya existente.<br>";
} else {
    echo "❌ Error creando la base de datos: " . $con->error . "<br>";
}

// Seleccionar la base de datos
$con->select_db("conecta_cusco_db");

// Crear tabla usuarios (actualizada con tipo empresa)
$sql_usuarios = "CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre_completo VARCHAR(255) NOT NULL,
    correo_electronico VARCHAR(255) NOT NULL UNIQUE,
    contrasena_hash VARCHAR(255) NOT NULL,
    tipo_cuenta ENUM('cliente', 'profesional', 'empresa') NOT NULL DEFAULT 'cliente',
    telefono VARCHAR(20),
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_correo (correo_electronico),
    INDEX idx_tipo_cuenta (tipo_cuenta)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

if ($con->query($sql_usuarios) === TRUE) {
    echo "✅ Tabla 'usuarios' creada o actualizada.<br>";
} else {
    echo "❌ Error creando tabla usuarios: " . $con->error . "<br>";
}

// Crear tabla empresas para información específica
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

// Crear tabla servicios_publicados (actualizada)
$sql_servicios = "CREATE TABLE IF NOT EXISTS servicios_publicados (
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
    echo "✅ Tabla 'servicios_publicados' creada o actualizada.<br>";
} else {
    echo "❌ Error creando tabla servicios_publicados: " . $con->error . "<br>";
}

// Crear tabla reseñas (mejorada)
$sql_resenas = "CREATE TABLE IF NOT EXISTS resenas (
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
    echo "✅ Tabla 'resenas' creada o actualizada.<br>";
} else {
    echo "❌ Error creando tabla resenas: " . $con->error . "<br>";
}

// Crear tabla contactos (mejorada)
$sql_contactos = "CREATE TABLE IF NOT EXISTS contactos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    servicio_id INT NOT NULL,
    cliente_id INT NOT NULL,
    profesional_id INT NOT NULL,
    mensaje TEXT NOT NULL,
    fecha_contacto TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    estado ENUM('pendiente', 'respondido', 'cerrado') DEFAULT 'pendiente',
    FOREIGN KEY (servicio_id) REFERENCES servicios_publicados(id) ON DELETE CASCADE,
    FOREIGN KEY (cliente_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (profesional_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    INDEX idx_servicio_id (servicio_id),
    INDEX idx_cliente_id (cliente_id),
    INDEX idx_profesional_id (profesional_id),
    INDEX idx_estado (estado),
    INDEX idx_fecha_contacto (fecha_contacto)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

if ($con->query($sql_contactos) === TRUE) {
    echo "✅ Tabla 'contactos' creada o actualizada.<br>";
} else {
    echo "❌ Error creando tabla contactos: " . $con->error . "<br>";
}

// Insertar datos de ejemplo (actualizados)
$sql_insert_ejemplo = "INSERT IGNORE INTO usuarios (nombre_completo, correo_electronico, contrasena_hash, tipo_cuenta, telefono) VALUES 
('Juan Pérez', 'juan@ejemplo.com', '" . password_hash('123456', PASSWORD_DEFAULT) . "', 'profesional', '984123456'),
('María García', 'maria@ejemplo.com', '" . password_hash('123456', PASSWORD_DEFAULT) . "', 'cliente', '984654321'),
('Empresa Constructora ABC', 'abc@empresa.com', '" . password_hash('123456', PASSWORD_DEFAULT) . "', 'empresa', '984789123')";

if ($con->query($sql_insert_ejemplo) === TRUE) {
    echo "✅ Datos de ejemplo insertados (si no existían).<br>";
} else {
    echo "❌ Error insertando datos de ejemplo: " . $con->error . "<br>";
}

// Insertar datos de empresa de ejemplo
$sql_empresa_ejemplo = "INSERT IGNORE INTO empresas (usuario_id, nombre_empresa, telefono_empresa, direccion_empresa, sitio_web, horarios) VALUES 
(3, 'Empresa Constructora ABC', '984789123', 'Av. Principal 123, Cusco', 'www.abc-constructora.com', 'Lunes a Viernes 8:00 - 18:00')";

if ($con->query($sql_empresa_ejemplo) === TRUE) {
    echo "✅ Datos de empresa de ejemplo insertados.<br>";
} else {
    echo "❌ Error insertando datos de empresa: " . $con->error . "<br>";
}

$con->close();
echo "<br><strong>🎉 ¡Base de datos configurada correctamente para phpMyAdmin!</strong>";
echo "<br><br><strong>Credenciales de prueba:</strong>";
echo "<br>• Profesional: juan@ejemplo.com / 123456";
echo "<br>• Cliente: maria@ejemplo.com / 123456";
echo "<br>• Empresa: abc@empresa.com / 123456";
echo "<br><br><a href='../index.html'>← Volver al inicio</a>";
?>
