<?php
$servidor = "localhost";
$usuario = "GrupoConecta";
$password = "cr143216"; 
$BD = "conecta_cusco_db";

// Intenta conectar con MySQLi orientado a objetos (más moderno)
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
$tipo_cuenta = $_POST['account-type']; 

if (isset($_POST['registro'])) {
    $nombre_completo = trim($_POST['completename'] ?? '');
    $correo_electronico = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? ''; 
    $confirm_password = $_POST['confirm-password'] ?? ''; 
    $tipo_cuenta = ($_POST['account-type'] == 'professional') ? 'profesional' : 'cliente'; // Conversión de valores

    // Validación de datos
    if (empty($nombre_completo) || empty($correo_electronico) || empty($password) || empty($confirm_password)) {
        $mensaje_estado = "Todos los campos son obligatorios.";
        $clase_mensaje = "error";
    } elseif (!filter_var($correo_electronico, FILTER_VALIDATE_EMAIL)) {
        $mensaje_estado = "El formato del correo electrónico no es válido.";
        $clase_mensaje = "error";
    } elseif ($password !== $confirm_password) {
        $mensaje_estado = "Las contraseñas no coinciden.";
        $clase_mensaje = "error";
        $password = '';
        $confirm_password = '';
    } else {
        $password_hash = password_hash($password, PASSWORD_DEFAULT);

        $sql = "INSERT INTO usuarios (nombre_completo, correo_electronico, contrasena_hash, tipo_cuenta) VALUES (?, ?, ?, ?)";

        if ($stmt = mysqli_prepare($con, $sql)) {
            mysqli_stmt_bind_param($stmt, "ssss", $param_nombre_completo, $param_correo_electronico, $param_password_hash, $param_tipo_cuenta);

            $param_nombre_completo = $nombre_completo;
            $param_correo_electronico = $correo_electronico;
            $param_password_hash = $password_hash;
            $param_tipo_cuenta = $tipo_cuenta;

            if (mysqli_stmt_execute($stmt)) {
                $mensaje_estado = "¡Registro exitoso! Ahora puedes <a href='login.html'>iniciar sesión</a>.";
                $clase_mensaje = "success";
                
                $nombre_completo = '';
                $correo_electronico = '';
                $tipo_cuenta = 'cliente'; // Resetear a valor por defecto
            } else {
                if (mysqli_errno($con) == 1062) {
                    $mensaje_estado = "El correo electrónico ya está registrado. Por favor, utiliza otro.";
                    $clase_mensaje = "error";
                } else {
                    $mensaje_estado = "Algo salió mal al registrar. Por favor, inténtalo de nuevo más tarde. Error: " . mysqli_error($con);
                    $clase_mensaje = "error";
                }
            }

            mysqli_stmt_close($stmt);
        } else {
            $mensaje_estado = "Error al preparar la consulta: " . mysqli_error($con);
            $clase_mensaje = "error";
        }
    }
}

mysqli_close($con);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Registrarse - Conecta Cusco</title>
    <meta name="description" content="Crea una nueva cuenta en Conecta Cusco para ofrecer o encontrar servicios.">
    <link rel="stylesheet" href="/Estilos/registro.css">
    <link rel="stylesheet" href="/Estilos/mensajes.css">
    <link rel="icon" href="/Imagenes/favicon.ico" type="image/x-icon">
</head>
<body>
    <header>
        <div class="container">
            <div class="logo">
                <img src="/Imagenes/logo.png" alt="Conecta Cusco Logo">
            </div>
            <h1>Conecta Cusco</h1>
        </div>
    </header>

    <nav>
        <div class="container">
            <ul>
                <li><a href="/index.html">Inicio</a></li>
                <li><a href="/Paginas/html/buscar-servicios.html">Buscar Servicios</a></li>
                <li><a href="/Paginas/publicar-servicios.html">Publicar mi Servicio</a></li>
                <li class="auth-links">
                    <a href="/Paginas/html/login.html">Iniciar Sesión</a>
                    <a href="/Paginas/registro.php">Registrarse</a> </li>
            </ul>
        </div>
    </nav>

    <main>
        <section class="auth-form">
            <div class="container">
                <h2>Crear una Nueva Cuenta</h2>

                <?php if (!empty($mensaje_estado)): ?>
                    <div class="message-container <?php echo $clase_mensaje; ?>">
                        <p><?php echo $mensaje_estado; ?></p>
                    </div>
                <?php endif; ?>

                <form action="registro.php" method="post"> <div class="form-group">
                        <label for="completename">Nombre Completo:</label>
                        <input type="text" id="completename" name="completename" required value="<?php echo htmlspecialchars($nombre_completo); ?>">
                    </div>
                    <div class="form-group">
                        <label for="email">Correo Electrónico:</label>
                        <input type="email" id="email" name="email" required value="<?php echo htmlspecialchars($correo_electronico); ?>">
                    </div>
                    <div class="form-group">
                        <label for="password">Contraseña:</label>
                        <input type="password" id="password" name="password" required>
                    </div>
                    <div class="form-group">
                        <label for="confirm-password">Confirmar Contraseña:</label>
                        <input type="password" id="confirm-password" name="confirm-password" required>
                    </div>
                    <div class="form-group">
    					<label>Tipo de Cuenta:</label><br>
    					<input type="radio" id="type-client" name="account-type" value="client">
    					<label for="type-client">Busco Servicios (Cliente)</label><br>
    					<input type="radio" id="type-professional" name="account-type" value="professional">
   			 			<label for="type-professional">Ofrezco Servicios (Profesional)</label>
					</div>

                    <button type="submit" class="btn-primary" name="registro">Registrarse</button>
                </form>
                <p>¿Ya tienes cuenta? <a href="/Paginas/html/login.html">Inicia Sesión aquí</a>.</p>
            </div>
        </section>
    </main>

    <footer>
        <div class="container">
            <p>&copy; 2025 Conecta Cusco. Todos los derechos reservados.</p>
        </div>
    </footer>

    <script src="/js/script.js"></script>
</body>
</html>