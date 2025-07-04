<?php
session_start();

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

// Verificar si el usuario está logueado
if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(['success' => false, 'message' => 'Debes iniciar sesión']);
    exit();
}

$profesional_id = $_SESSION['usuario_id'];

// Consultar contactos del profesional
$sql = "SELECT c.*, u.nombre_completo as cliente_nombre, sp.titulo as servicio_titulo 
        FROM contactos c 
        INNER JOIN usuarios u ON c.cliente_id = u.id 
        INNER JOIN servicios_publicados sp ON c.servicio_id = sp.id 
        WHERE c.profesional_id = ? 
        ORDER BY c.fecha_contacto DESC";

$contactos = [];

if ($stmt = mysqli_prepare($con, $sql)) {
    mysqli_stmt_bind_param($stmt, "i", $profesional_id);
    
    if (mysqli_stmt_execute($stmt)) {
        $result = mysqli_stmt_get_result($stmt);
        
        while ($row = mysqli_fetch_assoc($result)) {
            // Formatear fecha
            $row['fecha_formateada'] = date('d/m/Y H:i', strtotime($row['fecha_contacto']));
            
            // Formatear estado
            $estados = [
                'pendiente' => 'Pendiente',
                'respondido' => 'Respondido',
                'cerrado' => 'Cerrado'
            ];
            $row['estado_formateado'] = $estados[$row['estado']] ?? $row['estado'];
            
            $contactos[] = $row;
        }
    }
    
    mysqli_stmt_close($stmt);
}

mysqli_close($con);

// Devolver resultados como JSON
header('Content-Type: application/json');
echo json_encode([
    'success' => true,
    'contactos' => $contactos,
    'total_contactos' => count($contactos)
]);
?> 