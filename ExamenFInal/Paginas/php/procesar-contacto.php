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
    echo json_encode(['success' => false, 'message' => 'Debes iniciar sesión para enviar un mensaje']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $servicio_id = isset($_POST['servicio_id']) ? (int)$_POST['servicio_id'] : 0;
    $mensaje = isset($_POST['mensaje']) ? trim($_POST['mensaje']) : '';
    
    // Validaciones
    if ($servicio_id <= 0) {
        echo json_encode(['success' => false, 'message' => 'ID de servicio inválido']);
        exit();
    }
    
    if (empty($mensaje)) {
        echo json_encode(['success' => false, 'message' => 'El mensaje es obligatorio']);
        exit();
    }
    
    if (strlen($mensaje) < 10) {
        echo json_encode(['success' => false, 'message' => 'El mensaje debe tener al menos 10 caracteres']);
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
            
            // Verificar que el cliente no esté contactando su propio servicio
            if ($cliente_id == $profesional_id) {
                echo json_encode(['success' => false, 'message' => 'No puedes contactar tu propio servicio']);
                exit();
            }
            
            // Insertar el contacto
            $sql_insert = "INSERT INTO contactos (servicio_id, cliente_id, profesional_id, mensaje) VALUES (?, ?, ?, ?)";
            if ($stmt_insert = mysqli_prepare($con, $sql_insert)) {
                mysqli_stmt_bind_param($stmt_insert, "iiis", $servicio_id, $cliente_id, $profesional_id, $mensaje);
                
                if (mysqli_stmt_execute($stmt_insert)) {
                    echo json_encode(['success' => true, 'message' => '¡Mensaje enviado exitosamente! El profesional te contactará pronto.']);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Error al enviar el mensaje']);
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