<?php
session_start();

// Verificar si el usuario está logueado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../html/login.html?mensaje=" . urlencode("Debes iniciar sesión para acceder al dashboard"));
    exit();
}

// Verificar que sea un cliente
if ($_SESSION['tipo_cuenta'] !== 'cliente') {
    header("Location: ../html/profesional.php?mensaje=" . urlencode("Esta sección es solo para clientes"));
    exit();
}

// Incluir configuración de base de datos
require_once '/config.php';

// Conectar a la base de datos
$con = conectarDB();

// Obtener información del usuario
$usuario_id = $_SESSION['usuario_id'];
$sql_usuario = "SELECT nombre_completo, correo_electronico FROM usuarios WHERE id = ?";
$stmt_usuario = $con->prepare($sql_usuario);
$stmt_usuario->bind_param("i", $usuario_id);
$stmt_usuario->execute();
$result_usuario = $stmt_usuario->get_result();
$user_info = $result_usuario->fetch_assoc();

// Obtener servicios disponibles - CORREGIDO
$sql_servicios = "SELECT sp.*, u.nombre_completo, u.correo_electronico 
                  FROM servicios_publicados sp 
                  JOIN usuarios u ON sp.user_id = u.id 
                  WHERE sp.disponibilidad = 'disponible' AND sp.estado = 'activo'
                  ORDER BY sp.fecha_publicacion DESC 
                  LIMIT 12";
$result_servicios = $con->query($sql_servicios);

// Obtener mensaje de éxito si existe
$mensaje_exito = $_GET['mensaje'] ?? '';

// Cerrar conexión
cerrarConexion($con);

// Redirigir al archivo cliente.php en la carpeta HTML
header("Location: ../html/cliente.php");
exit();
?> 