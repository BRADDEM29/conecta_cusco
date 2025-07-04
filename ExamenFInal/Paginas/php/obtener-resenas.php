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

// Obtener ID del servicio
$servicio_id = isset($_GET['servicio_id']) ? (int)$_GET['servicio_id'] : 0;

if ($servicio_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'ID de servicio inválido']);
    exit();
}

// Consultar reseñas del servicio
$sql = "SELECT r.*, u.nombre_completo as cliente_nombre 
        FROM resenas r 
        INNER JOIN usuarios u ON r.cliente_id = u.id 
        WHERE r.servicio_id = ? AND r.estado = 'activa' 
        ORDER BY r.fecha_resena DESC";

$resenas = [];

if ($stmt = mysqli_prepare($con, $sql)) {
    mysqli_stmt_bind_param($stmt, "i", $servicio_id);
    
    if (mysqli_stmt_execute($stmt)) {
        $result = mysqli_stmt_get_result($stmt);
        
        while ($row = mysqli_fetch_assoc($result)) {
            // Formatear fecha
            $row['fecha_formateada'] = date('d/m/Y H:i', strtotime($row['fecha_resena']));
            
            // Generar estrellas HTML
            $estrellas = '';
            for ($i = 1; $i <= 5; $i++) {
                if ($i <= $row['calificacion']) {
                    $estrellas .= '<span class="star filled">★</span>';
                } else {
                    $estrellas .= '<span class="star">☆</span>';
                }
            }
            $row['estrellas_html'] = $estrellas;
            
            $resenas[] = $row;
        }
    }
    
    mysqli_stmt_close($stmt);
}

// Obtener estadísticas del servicio
$sql_stats = "SELECT calificacion_promedio, total_calificaciones 
              FROM servicios_publicados 
              WHERE id = ?";

$stats = null;

if ($stmt_stats = mysqli_prepare($con, $sql_stats)) {
    mysqli_stmt_bind_param($stmt_stats, "i", $servicio_id);
    
    if (mysqli_stmt_execute($stmt_stats)) {
        $result_stats = mysqli_stmt_get_result($stmt_stats);
        $stats = mysqli_fetch_assoc($result_stats);
    }
    
    mysqli_stmt_close($stmt_stats);
}

mysqli_close($con);

// Devolver resultados como JSON
header('Content-Type: application/json');
echo json_encode([
    'success' => true,
    'resenas' => $resenas,
    'total_resenas' => count($resenas),
    'stats' => $stats
]);
?> 