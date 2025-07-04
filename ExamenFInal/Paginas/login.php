<?php
session_start();

$servidor = "localhost"; 
$usuario = "GrupoConecta"; 
$password = "cr143216"; 
$BD = "conecta_cusco_db"; 

$con = mysqli_connect($servidor, $usuario, $password, $BD);

if (!$con) {
    die("ERROR: No se pudo conectar a la base de datos. " . mysqli_connect_error());
}

mysqli_set_charset($con, "utf8mb4");

$mensaje_estado = "";
$clase_mensaje = "";

if (isset($_POST['email']) && isset($_POST['password'])) {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Buscar el usuario en la base de datos
    $sql = "SELECT id, nombre_completo, correo_electronico, contrasena_hash, tipo_cuenta FROM usuarios WHERE correo_electronico = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($row = mysqli_fetch_assoc($result)) {
        // Verificar la contraseña
        if (password_verify($password, $row['contrasena_hash'])) {
            // Iniciar sesión
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['user_email'] = $row['correo_electronico'];
            $_SESSION['user_name'] = $row['nombre_completo'];
            $_SESSION['user_type'] = $row['tipo_cuenta'];
            
            // Redirigir según el tipo de usuario
            if ($row['tipo_cuenta'] == 'profesional') {
                header("Location: dashboard-profesional.php");
            } else {
                header("Location: dashboard-cliente.php");
            }
            exit();
        } else {
            $mensaje_estado = "Contraseña incorrecta.";
            $clase_mensaje = "error";
        }
    } else {
        $mensaje_estado = "No existe una cuenta con este correo electrónico.";
        $clase_mensaje = "error";
    }
    mysqli_stmt_close($stmt);
}

mysqli_close($con);
?>
