<?php
// ========================================
// CREACIÓN DE BASE DE DATOS PARA RAILWAY
// ========================================
// Script para crear las tablas en PostgreSQL

require_once 'config-railway.php';

try {
    $pdo = conectarDB();
    
    echo "<h2>🚀 Creando base de datos para Railway...</h2>";
    
    // Crear tabla de usuarios
    $sql_usuarios = "
    CREATE TABLE IF NOT EXISTS usuarios (
        id SERIAL PRIMARY KEY,
        nombre VARCHAR(100) NOT NULL,
        email VARCHAR(100) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL,
        tipo_cuenta VARCHAR(20) NOT NULL DEFAULT 'cliente',
        telefono VARCHAR(20),
        direccion TEXT,
        fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        activo BOOLEAN DEFAULT TRUE
    )";
    
    $pdo->exec($sql_usuarios);
    echo "✅ Tabla 'usuarios' creada correctamente<br>";
    
    // Crear tabla de servicios
    $sql_servicios = "
    CREATE TABLE IF NOT EXISTS servicios (
        id SERIAL PRIMARY KEY,
        usuario_id INTEGER REFERENCES usuarios(id) ON DELETE CASCADE,
        titulo VARCHAR(200) NOT NULL,
        descripcion TEXT NOT NULL,
        categoria VARCHAR(100) NOT NULL,
        precio DECIMAL(10,2),
        ubicacion VARCHAR(200),
        imagen VARCHAR(255),
        fecha_publicacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        activo BOOLEAN DEFAULT TRUE
    )";
    
    $pdo->exec($sql_servicios);
    echo "✅ Tabla 'servicios' creada correctamente<br>";
    
    // Crear tabla de reseñas
    $sql_resenas = "
    CREATE TABLE IF NOT EXISTS resenas (
        id SERIAL PRIMARY KEY,
        servicio_id INTEGER REFERENCES servicios(id) ON DELETE CASCADE,
        usuario_id INTEGER REFERENCES usuarios(id) ON DELETE CASCADE,
        calificacion INTEGER CHECK (calificacion >= 1 AND calificacion <= 5),
        comentario TEXT,
        fecha_resena TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    
    $pdo->exec($sql_resenas);
    echo "✅ Tabla 'resenas' creada correctamente<br>";
    
    // Crear tabla de contactos
    $sql_contactos = "
    CREATE TABLE IF NOT EXISTS contactos (
        id SERIAL PRIMARY KEY,
        servicio_id INTEGER REFERENCES servicios(id) ON DELETE CASCADE,
        usuario_id INTEGER REFERENCES usuarios(id) ON DELETE CASCADE,
        mensaje TEXT NOT NULL,
        fecha_contacto TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        leido BOOLEAN DEFAULT FALSE
    )";
    
    $pdo->exec($sql_contactos);
    echo "✅ Tabla 'contactos' creada correctamente<br>";
    
    // Crear tabla de perfiles profesionales
    $sql_perfiles = "
    CREATE TABLE IF NOT EXISTS perfiles_profesionales (
        id SERIAL PRIMARY KEY,
        usuario_id INTEGER REFERENCES usuarios(id) ON DELETE CASCADE,
        experiencia TEXT,
        especialidades TEXT,
        certificaciones TEXT,
        horario_trabajo VARCHAR(200),
        imagen_perfil VARCHAR(255),
        fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    
    $pdo->exec($sql_perfiles);
    echo "✅ Tabla 'perfiles_profesionales' creada correctamente<br>";
    
    // Insertar datos de ejemplo
    $sql_insert_usuarios = "
    INSERT INTO usuarios (nombre, email, password, tipo_cuenta, telefono, direccion) VALUES
    ('Admin', 'admin@conectacusco.com', '" . password_hash('admin123', PASSWORD_DEFAULT) . "', 'admin', '123456789', 'Cusco, Perú'),
    ('Juan Pérez', 'juan@example.com', '" . password_hash('123456', PASSWORD_DEFAULT) . "', 'profesional', '987654321', 'Cusco, Perú'),
    ('María García', 'maria@example.com', '" . password_hash('123456', PASSWORD_DEFAULT) . "', 'cliente', '555555555', 'Cusco, Perú')
    ON CONFLICT (email) DO NOTHING";
    
    $pdo->exec($sql_insert_usuarios);
    echo "✅ Datos de ejemplo insertados correctamente<br>";
    
    // Insertar servicios de ejemplo
    $sql_insert_servicios = "
    INSERT INTO servicios (usuario_id, titulo, descripcion, categoria, precio, ubicacion) VALUES
    (2, 'Plomería Profesional', 'Servicios de plomería residencial y comercial. Reparaciones, instalaciones y mantenimiento.', 'Plomería', 50.00, 'Cusco Centro'),
    (2, 'Electricidad Domiciliaria', 'Instalaciones eléctricas, reparaciones y mantenimiento de sistemas eléctricos.', 'Electricidad', 60.00, 'Cusco y alrededores')
    ON CONFLICT DO NOTHING";
    
    $pdo->exec($sql_insert_servicios);
    echo "✅ Servicios de ejemplo insertados correctamente<br>";
    
    echo "<h3>🎉 ¡Base de datos creada exitosamente!</h3>";
    echo "<p>La aplicación está lista para usar en Railway.</p>";
    echo "<p><strong>IMPORTANTE:</strong> Elimina este archivo por seguridad después del primer uso.</p>";
    
} catch (Exception $e) {
    echo "<h3>❌ Error al crear la base de datos:</h3>";
    echo "<p>" . $e->getMessage() . "</p>";
}
?> 