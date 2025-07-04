<?php
session_start();

// Configuración para phpMyAdmin
$servidor = "localhost";
$usuario = "root"; // Usuario por defecto de phpMyAdmin
$password = ""; // Contraseña vacía por defecto en XAMPP/WAMP
$BD = "conecta_cusco_db";

// Intenta conectar con MySQLi orientado a objetos
try {
    $con = new mysqli($servidor, $usuario, $password, $BD);
    $con->set_charset("utf8mb4");
} catch (mysqli_sql_exception $e) {
    die("Error de conexión: " . $e->getMessage());
}

mysqli_set_charset($con, "utf8mb4");

$mensaje_estado = "";
$clase_mensaje = "";

if (isset($_POST['login'])) {
    $correo_electronico = trim($_POST['correo_electronico'] ?? '');
    $password = $_POST['contrasena'] ?? '';

    // Validación de datos
    if (empty($correo_electronico) || empty($password)) {
        $mensaje_estado = "Por favor, completa todos los campos.";
        $clase_mensaje = "error";
    } elseif (!filter_var($correo_electronico, FILTER_VALIDATE_EMAIL)) {
        $mensaje_estado = "El formato del correo electrónico no es válido.";
        $clase_mensaje = "error";
    } else {
        // Consultar usuario
        $sql = "SELECT id, nombre_completo, correo_electronico, contrasena_hash, tipo_cuenta FROM usuarios WHERE correo_electronico = ?";
        
        if ($stmt = mysqli_prepare($con, $sql)) {
            mysqli_stmt_bind_param($stmt, "s", $param_correo);
            $param_correo = $correo_electronico;
            
            if (mysqli_stmt_execute($stmt)) {
                mysqli_stmt_store_result($stmt);
                
                if (mysqli_stmt_num_rows($stmt) == 1) {
                    mysqli_stmt_bind_result($stmt, $id, $nombre_completo, $correo_electronico, $contrasena_hash, $tipo_cuenta);
                    
                    if (mysqli_stmt_fetch($stmt)) {
                        if (password_verify($password, $contrasena_hash)) {
                            // Login exitoso - crear sesión
                            $_SESSION['usuario_id'] = $id;
                            $_SESSION['nombre_completo'] = $nombre_completo;
                            $_SESSION['correo_electronico'] = $correo_electronico;
                            $_SESSION['tipo_cuenta'] = $tipo_cuenta;
                            $_SESSION['login_time'] = time();
                            
                            // Redirigir según el tipo de cuenta
                            switch ($tipo_cuenta) {
                                case 'cliente':
                                    header("Location: cliente.php");
                                    break;
                                case 'profesional':
                                    header("Location: ../html/profesional.php");
                                    break;
                                case 'empresa':
                                    header("Location: ../html/profesional.php"); // Las empresas usan el mismo panel que profesionales
                                    break;
                                default:
                                    header("Location: cliente.php");
                                    break;
                            }
                            exit();
                        } else {
                            $mensaje_estado = "Contraseña incorrecta.";
                            $clase_mensaje = "error";
                        }
                    }
                } else {
                    $mensaje_estado = "No existe una cuenta con ese correo electrónico.";
                    $clase_mensaje = "error";
                }
            } else {
                $mensaje_estado = "Error al ejecutar la consulta.";
                $clase_mensaje = "error";
            }
            
            mysqli_stmt_close($stmt);
        } else {
            $mensaje_estado = "Error al preparar la consulta.";
            $clase_mensaje = "error";
        }
    }
    
    // Si hay error, redirigir con mensaje
    if (!empty($mensaje_estado)) {
        header("Location: ../html/login.html?mensaje=" . urlencode($mensaje_estado) . "&tipo=error");
        exit();
    }
} else {
    // Si no hay POST, redirigir al formulario
    header("Location: ../html/login.html");
    exit();
}

mysqli_close($con);
?>
