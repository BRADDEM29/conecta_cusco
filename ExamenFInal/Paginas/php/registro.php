<?php
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
$nombre_completo = '';
$correo_electronico = '';
$telefono = '';
$tipo_cuenta = 'cliente'; // Valor por defecto

if (isset($_POST['registro'])) {
    $nombre_completo = trim($_POST['nombre_completo'] ?? '');
    $correo_electronico = trim($_POST['correo_electronico'] ?? '');
    $telefono = trim($_POST['telefono'] ?? '');
    $password = $_POST['contrasena'] ?? ''; 
    $confirm_password = $_POST['confirmar_contrasena'] ?? ''; 
    $tipo_cuenta = $_POST['tipo_cuenta'] ?? 'cliente';

    // Validación de datos básicos
    if (empty($nombre_completo) || empty($correo_electronico) || empty($telefono) || empty($password) || empty($confirm_password)) {
        $mensaje_estado = "Todos los campos son obligatorios.";
        $clase_mensaje = "error";
    } elseif (strlen($nombre_completo) < 3) {
        $mensaje_estado = "El nombre debe tener al menos 3 caracteres.";
        $clase_mensaje = "error";
    } elseif (!filter_var($correo_electronico, FILTER_VALIDATE_EMAIL)) {
        $mensaje_estado = "El formato del correo electrónico no es válido.";
        $clase_mensaje = "error";
    } elseif (strlen($password) < 6) {
        $mensaje_estado = "La contraseña debe tener al menos 6 caracteres.";
        $clase_mensaje = "error";
    } elseif ($password !== $confirm_password) {
        $mensaje_estado = "Las contraseñas no coinciden.";
        $clase_mensaje = "error";
        $password = '';
        $confirm_password = '';
    } else {
        $password_hash = password_hash($password, PASSWORD_DEFAULT);

        // Iniciar transacción
        $con->begin_transaction();

        try {
            // Insertar usuario principal
            $sql = "INSERT INTO usuarios (nombre_completo, correo_electronico, telefono, contrasena_hash, tipo_cuenta) VALUES (?, ?, ?, ?, ?)";

            if ($stmt = mysqli_prepare($con, $sql)) {
                mysqli_stmt_bind_param($stmt, "sssss", $param_nombre_completo, $param_correo_electronico, $param_telefono, $param_password_hash, $param_tipo_cuenta);

                $param_nombre_completo = $nombre_completo;
                $param_correo_electronico = $correo_electronico;
                $param_telefono = $telefono;
                $param_password_hash = $password_hash;
                $param_tipo_cuenta = $tipo_cuenta;

                if (mysqli_stmt_execute($stmt)) {
                    $usuario_id = $con->insert_id;
                    
                    // Si es empresa, insertar información adicional
                    if ($tipo_cuenta === 'empresa') {
                        $nombre_empresa = trim($_POST['nombre_empresa'] ?? '');
                        $telefono_empresa = trim($_POST['telefono_empresa'] ?? '');
                        $direccion_empresa = trim($_POST['direccion_empresa'] ?? '');
                        $sitio_web = trim($_POST['sitio_web'] ?? '');
                        $horarios = trim($_POST['horarios'] ?? '');

                        // Validar campos obligatorios de empresa
                        if (empty($nombre_empresa) || empty($telefono_empresa) || empty($direccion_empresa)) {
                            throw new Exception("Los campos de empresa son obligatorios.");
                        }

                        $sql_empresa = "INSERT INTO empresas (usuario_id, nombre_empresa, telefono_empresa, direccion_empresa, sitio_web, horarios) VALUES (?, ?, ?, ?, ?, ?)";
                        
                        if ($stmt_empresa = mysqli_prepare($con, $sql_empresa)) {
                            mysqli_stmt_bind_param($stmt_empresa, "isssss", $usuario_id, $nombre_empresa, $telefono_empresa, $direccion_empresa, $sitio_web, $horarios);
                            
                            if (!mysqli_stmt_execute($stmt_empresa)) {
                                throw new Exception("Error al guardar información de empresa.");
                            }
                            mysqli_stmt_close($stmt_empresa);
                        } else {
                            throw new Exception("Error al preparar consulta de empresa.");
                        }
                    }

                    // Confirmar transacción
                    $con->commit();
                    
                    $mensaje_estado = "¡Registro exitoso! Ahora puedes iniciar sesión.";
                    $clase_mensaje = "success";
                    
                    // Redirigir a login con mensaje de éxito
                    header("Location: /Paginas/html/login.html?mensaje=" . urlencode($mensaje_estado) . "&tipo=success");
                    exit();
                    
                } else {
                    throw new Exception("Error al ejecutar la consulta de usuario.");
                }

                mysqli_stmt_close($stmt);
            } else {
                throw new Exception("Error al preparar la consulta de usuario.");
            }
            
        } catch (Exception $e) {
            // Revertir transacción en caso de error
            $con->rollback();
            
            if (mysqli_errno($con) == 1062) {
                $mensaje_estado = "El correo electrónico ya está registrado. Por favor, utiliza otro.";
            } else {
                $mensaje_estado = "Error: " . $e->getMessage();
            }
            $clase_mensaje = "error";
            
            // Redirigir con mensaje de error
            header("Location: /Paginas/html/registro.html?mensaje=" . urlencode($mensaje_estado) . "&tipo=error");
            exit();
        }
    }
} else {
    // Si no hay POST, redirigir al formulario
    header("Location: /Paginas/html/registro.html");
    exit();
}

mysqli_close($con);
?> 