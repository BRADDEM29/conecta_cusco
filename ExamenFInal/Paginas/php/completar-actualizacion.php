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

echo "<h2>🔄 Completando Actualización</h2>";

// Insertar datos de ejemplo actualizados
$sql_insert_ejemplo = "INSERT IGNORE INTO usuarios (nombre_completo, correo_electronico, telefono, contrasena_hash, tipo_cuenta) VALUES 
('Juan Pérez', 'juan@ejemplo.com', '984123456', '" . password_hash('123456', PASSWORD_DEFAULT) . "', 'profesional'),
('María García', 'maria@ejemplo.com', '984654321', '" . password_hash('123456', PASSWORD_DEFAULT) . "', 'cliente'),
('Empresa Constructora ABC', 'abc@empresa.com', '984789123', '" . password_hash('123456', PASSWORD_DEFAULT) . "', 'empresa')";

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

// Insertar algunos servicios de ejemplo
$sql_servicios_ejemplo = "INSERT IGNORE INTO servicios_publicados (user_id, titulo, categoria, descripcion, cobertura, telefono, email, precio, disponibilidad) VALUES 
(1, 'Plomería Profesional', 'Plomería', 'Servicios de plomería residencial y comercial. Reparaciones, instalaciones y mantenimiento.', 'Cusco Centro', '984123456', 'juan@ejemplo.com', 50.00, 'disponible'),
(3, 'Construcción y Remodelación', 'Construcción', 'Servicios de construcción, remodelación y mantenimiento de edificios. Trabajos de calidad garantizada.', 'Cusco y alrededores', '984789123', 'abc@empresa.com', 150.00, 'disponible')";

if ($con->query($sql_servicios_ejemplo) === TRUE) {
    echo "✅ Servicios de ejemplo insertados.<br>";
} else {
    echo "❌ Error insertando servicios de ejemplo: " . $con->error . "<br>";
}

$con->close();

echo "<br><strong>🎉 ¡Actualización completada!</strong>";
echo "<br><br><strong>Credenciales de prueba:</strong>";
echo "<br>• Profesional: juan@ejemplo.com / 123456";
echo "<br>• Cliente: maria@ejemplo.com / 123456";
echo "<br>• Empresa: abc@empresa.com / 123456";
echo "<br><br><strong>Funcionalidades disponibles:</strong>";
echo "<br>✅ Registro de clientes, profesionales y empresas";
echo "<br>✅ Login con redirección según tipo de usuario";
echo "<br>✅ Búsqueda dinámica de servicios";
echo "<br>✅ Sistema de reseñas y calificaciones";
echo "<br>✅ Sistema de contactos entre usuarios";
echo "<br>✅ Filtros avanzados de búsqueda";
echo "<br><br><a href='../index.html'>← Volver al inicio</a>";
?> 