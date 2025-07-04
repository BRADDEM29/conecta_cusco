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
    echo json_encode(['success' => false, 'message' => 'Debes iniciar sesión para dejar una reseña']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $servicio_id = isset($_POST['servicio_id']) ? (int)$_POST['servicio_id'] : 0;
    $calificacion = isset($_POST['calificacion']) ? (int)$_POST['calificacion'] : 0;
    $comentario = isset($_POST['comentario']) ? trim($_POST['comentario']) : '';
    
    // Validaciones
    if ($servicio_id <= 0) {
        echo json_encode(['success' => false, 'message' => 'ID de servicio inválido']);
        exit();
    }
    
    if ($calificacion < 1 || $calificacion > 5) {
        echo json_encode(['success' => false, 'message' => 'Calificación debe estar entre 1 y 5']);
        exit();
    }
    
    if (empty($comentario)) {
        echo json_encode(['success' => false, 'message' => 'El comentario es obligatorio']);
        exit();
    }
    
    // Verificar que el servicio existe y obtener información
    $sql_servicio = "SELECT user_id FROM servicios_publicados WHERE id = ? AND estado = 'activo'";
    if ($stmt = mysqli_prepare($con, $sql_servicio)) {
        mysqli_stmt_bind_param($stmt, "i", $servicio_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if ($servicio = mysqli_fetch_assoc($result)) {
            $profesional_id = $servicio['user_id'];
            $cliente_id = $_SESSION['usuario_id'];
            
            // Verificar que el cliente no esté reseñando su propio servicio
            if ($cliente_id == $profesional_id) {
                echo json_encode(['success' => false, 'message' => 'No puedes reseñar tu propio servicio']);
                exit();
            }
            
            // Verificar si ya existe una reseña del mismo cliente
            $sql_check = "SELECT id FROM resenas WHERE servicio_id = ? AND cliente_id = ? AND estado = 'activa'";
            if ($stmt_check = mysqli_prepare($con, $sql_check)) {
                mysqli_stmt_bind_param($stmt_check, "ii", $servicio_id, $cliente_id);
                mysqli_stmt_execute($stmt_check);
                mysqli_stmt_store_result($stmt_check);
                
                if (mysqli_stmt_num_rows($stmt_check) > 0) {
                    echo json_encode(['success' => false, 'message' => 'Ya has dejado una reseña para este servicio']);
                    exit();
                }
                mysqli_stmt_close($stmt_check);
            }
            
            // Insertar la reseña
            $sql_insert = "INSERT INTO resenas (servicio_id, cliente_id, profesional_id, calificacion, comentario) VALUES (?, ?, ?, ?, ?)";
            if ($stmt_insert = mysqli_prepare($con, $sql_insert)) {
                mysqli_stmt_bind_param($stmt_insert, "iiiis", $servicio_id, $cliente_id, $profesional_id, $calificacion, $comentario);
                
                if (mysqli_stmt_execute($stmt_insert)) {
                    // Actualizar calificación promedio del servicio
                    $sql_update = "UPDATE servicios_publicados SET 
                                  calificacion_promedio = (
                                      SELECT AVG(calificacion) 
                                      FROM resenas 
                                      WHERE servicio_id = ? AND estado = 'activa'
                                  ),
                                  total_calificaciones = (
                                      SELECT COUNT(*) 
                                      FROM resenas 
                                      WHERE servicio_id = ? AND estado = 'activa'
                                  )
                                  WHERE id = ?";
                    
                    if ($stmt_update = mysqli_prepare($con, $sql_update)) {
                        mysqli_stmt_bind_param($stmt_update, "iii", $servicio_id, $servicio_id, $servicio_id);
                        mysqli_stmt_execute($stmt_update);
                        mysqli_stmt_close($stmt_update);
                    }
                    
                    echo json_encode(['success' => true, 'message' => '¡Reseña enviada exitosamente!']);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Error al guardar la reseña']);
                }
                mysqli_stmt_close($stmt_insert);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error al preparar la consulta']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Servicio no encontrado']);
        }
        mysqli_stmt_close($stmt);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al verificar el servicio']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
}

mysqli_close($con);
?> 