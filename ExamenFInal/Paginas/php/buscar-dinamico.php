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

// Obtener parámetros de búsqueda
$q = isset($_GET['q']) ? trim($_GET['q']) : '';
$location = isset($_GET['location']) ? trim($_GET['location']) : '';
$categoria = isset($_GET['categoria']) ? trim($_GET['categoria']) : '';
$calificacion = isset($_GET['calificacion']) ? (int)$_GET['calificacion'] : 0;
$disponibilidad = isset($_GET['disponibilidad']) ? trim($_GET['disponibilidad']) : '';

// Construir consulta SQL
$sql = "SELECT sp.*, u.nombre_completo, u.tipo_cuenta 
        FROM servicios_publicados sp 
        INNER JOIN usuarios u ON sp.user_id = u.id 
        WHERE sp.estado = 'activo'";

$params = [];
$types = "";

// Filtro por texto de búsqueda
if (!empty($q)) {
    $sql .= " AND (sp.titulo LIKE ? OR sp.descripcion LIKE ? OR sp.categoria LIKE ?)";
    $search_term = "%$q%";
    $params[] = $search_term;
    $params[] = $search_term;
    $params[] = $search_term;
    $types .= "sss";
}

// Filtro por ubicación
if (!empty($location)) {
    $sql .= " AND sp.cobertura LIKE ?";
    $params[] = "%$location%";
    $types .= "s";
}

// Filtro por categoría
if (!empty($categoria)) {
    $sql .= " AND sp.categoria = ?";
    $params[] = $categoria;
    $types .= "s";
}

// Filtro por calificación mínima
if ($calificacion > 0) {
    $sql .= " AND sp.calificacion_promedio >= ?";
    $params[] = $calificacion;
    $types .= "d";
}

// Filtro por disponibilidad
if (!empty($disponibilidad)) {
    $sql .= " AND sp.disponibilidad = ?";
    $params[] = $disponibilidad;
    $types .= "s";
}

$sql .= " ORDER BY sp.calificacion_promedio DESC, sp.fecha_publicacion DESC";

// Ejecutar consulta
$resultados = [];
if ($stmt = mysqli_prepare($con, $sql)) {
    if (!empty($params)) {
        mysqli_stmt_bind_param($stmt, $types, ...$params);
    }
    
    if (mysqli_stmt_execute($stmt)) {
        $result = mysqli_stmt_get_result($stmt);
        
        while ($row = mysqli_fetch_assoc($result)) {
            $resultados[] = $row;
        }
    }
    
    mysqli_stmt_close($stmt);
}

mysqli_close($con);

// Devolver resultados como JSON
header('Content-Type: application/json');
echo json_encode([
    'success' => true,
    'resultados' => $resultados,
    'total' => count($resultados),
    'filtros' => [
        'q' => $q,
        'location' => $location,
        'categoria' => $categoria,
        'calificacion' => $calificacion,
        'disponibilidad' => $disponibilidad
    ]
]);
?> 