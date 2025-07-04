<?php
session_start();

// Verificar si el usuario está logueado usando las variables correctas
if (!isset($_SESSION['usuario_id'])) {
    header("Location: /Paginas/html/login.html");
    exit();
}

// Verificar que el usuario sea profesional o empresa
if ($_SESSION['tipo_cuenta'] != 'profesional' && $_SESSION['tipo_cuenta'] != 'empresa') {
    header("Location: cliente.php");
    exit();
}

// Usar la configuración consolidada
require_once '/config.php';

$user_id = $_SESSION['usuario_id'];
$titulo = sanitizarDatos($_POST['service-title'] ?? '');
$categoria = sanitizarDatos($_POST['category'] ?? '');
$descripcion = sanitizarDatos($_POST['description'] ?? '');
$cobertura = sanitizarDatos($_POST['service-area'] ?? '');
$telefono = sanitizarDatos($_POST['contact-phone'] ?? '');
$email = sanitizarDatos($_POST['contact-email'] ?? '');

// Validar campos obligatorios
if (empty($titulo) || empty($categoria) || empty($descripcion) || empty($cobertura)) {
    die("Error: Todos los campos son obligatorios.");
}

// Validar email solo si se proporciona
if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    die("Error: El formato del email no es válido.");
}

// Conectar a la base de datos
$con = conectarDB();

// Manejar imagen con validación
$foto_nombre = '';
if (!empty($_FILES['profile-photo']['name'])) {
    $archivo = $_FILES['profile-photo'];
    $nombre_archivo = $archivo['name'];
    $tipo_archivo = $archivo['type'];
    $tamano_archivo = $archivo['size'];
    $archivo_temporal = $archivo['tmp_name'];
    
    // Validar tipo de archivo
    $tipos_permitidos = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
    if (!in_array($tipo_archivo, $tipos_permitidos)) {
        die("Error: Solo se permiten archivos de imagen (JPG, PNG, GIF, WEBP).");
    }
    
    // Validar tamaño (máximo 5MB)
    if ($tamano_archivo > MAX_FILE_SIZE) {
        die("Error: El archivo es demasiado grande. Máximo 5MB.");
    }
    
    // Generar nombre único
    $extension = pathinfo($nombre_archivo, PATHINFO_EXTENSION);
    $foto_nombre = uniqid() . '_' . time() . '.' . $extension;
    $ruta_destino = '/uploads/' . $foto_nombre;
    
    // Crear directorio si no existe
    if (!is_dir('/uploads/')) {
    mkdir('/uploads/', 0755, true);
}
    
    // Mover archivo
    if (!move_uploaded_file($archivo_temporal, $ruta_destino)) {
        die("Error: No se pudo subir el archivo.");
    }
}

// Insertar en la base de datos
$sql = "INSERT INTO servicios_publicados 
    (user_id, titulo, categoria, descripcion, cobertura, telefono, email, foto_perfil, fecha_publicacion) 
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())";

$stmt = $con->prepare($sql);
if (!$stmt) {
    die("Error al preparar la consulta: " . $con->error);
}

$stmt->bind_param("isssssss", $user_id, $titulo, $categoria, $descripcion, $cobertura, $telefono, $email, $foto_nombre);

if ($stmt->execute()) {
    // Redirigir al dashboard del profesional con mensaje de éxito
    header("Location: /Paginas/html/profesional.php?mensaje=" . urlencode("¡Servicio publicado exitosamente!"));
    exit();
} else {
    $error = "Error al publicar el servicio: " . $stmt->error;
    die($error);
}

$stmt->close();
cerrarConexion($con);
?>
