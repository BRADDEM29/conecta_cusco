<?php
session_start();
require 'conexion.php'; // Archivo con la conexión a la BD

// Verificar si el usuario está logueado y es profesional
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'profesional') {
    header("Location: login.html");
    exit();
}

// Variables para mensajes de estado
$mensaje = "";
$clase_mensaje = "";

// Procesar el formulario cuando se envía
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitizar y validar datos
    $titulo = trim($_POST['titulo']);
    $categoria = trim($_POST['categoria']);
    $descripcion = trim($_POST['descripcion']);
    $cobertura = trim($_POST['cobertura']);
    $telefono = trim($_POST['telefono']);
    $email = filter_var(trim($_POST['email']), FILTER_VALIDATE_EMAIL);
    $user_id = $_SESSION['user_id'];

    // Validar campos obligatorios
    if (empty($titulo) || empty($categoria) || empty($descripcion) || empty($cobertura)) {
        $mensaje = "Los campos titulo, categoría, descripción y cobertura son obligatorios.";
        $clase_mensaje = "error";
    } elseif (!$email) {
        $mensaje = "El formato del email no es válido.";
        $clase_mensaje = "error";
    } else {
        // Procesar la foto de perfil (si se subió)
        $foto_perfil = null;
        if (isset($_FILES['foto_perfil']) && $_FILES['foto_perfil']['error'] == UPLOAD_ERR_OK) {
            $directorio = "../uploads/";
            $nombre_archivo = uniqid() . '_' . basename($_FILES['foto_perfil']['name']);
            $ruta_completa = $directorio . $nombre_archivo;
            
            if (move_uploaded_file($_FILES['foto_perfil']['tmp_name'], $ruta_completa)) {
                $foto_perfil = $nombre_archivo;
            }
        }

        // Insertar en la base de datos
        $sql = "INSERT INTO servicios_publicados (
                user_id, titulo, categoria, descripcion, cobertura, 
                telefono, email, foto_perfil
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $con->prepare($sql);
        $stmt->bind_param("isssssss", $user_id, $titulo, $categoria, $descripcion, 
                         $cobertura, $telefono, $email, $foto_perfil);

        if ($stmt->execute()) {
            $mensaje = "Servicio publicado exitosamente!";
            $clase_mensaje = "success";
            // Limpiar campos después de éxito
            $_POST = array();
        } else {
            $mensaje = "Error al publicar el servicio: " . $con->error;
            $clase_mensaje = "error";
        }
        $stmt->close();
    }
}
$con->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Publicar Servicio</title>
    <style>
        .error { color: red; }
        .success { color: green; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; }
    </style>
</head>
<body>
    <h1>Publicar Nuevo Servicio</h1>
    
    <?php if (!empty($mensaje)): ?>
        <div class="<?php echo $clase_mensaje; ?>"><?php echo $mensaje; ?></div>
    <?php endif; ?>

    <form action="publicar_servicio.php" method="post" enctype="multipart/form-data">
        <div class="form-group">
            <label for="titulo">Título del Servicio:</label>
            <input type="text" id="titulo" name="titulo" required 
                   value="<?php echo htmlspecialchars($_POST['titulo'] ?? ''); ?>">
        </div>
        
        <div class="form-group">
            <label for="categoria">Categoría:</label>
            <select id="categoria" name="categoria" required>
                <option value="">Seleccione...</option>
                <option value="Tecnología" <?php echo ($_POST['categoria'] ?? '') == 'Tecnología' ? 'selected' : ''; ?>>Tecnología</option>
                <option value="Construcción" <?php echo ($_POST['categoria'] ?? '') == 'Construcción' ? 'selected' : ''; ?>>Construcción</option>
                <option value="Educación" <?php echo ($_POST['categoria'] ?? '') == 'Educación' ? 'selected' : ''; ?>>Educación</option>
            </select>
        </div>
        
        <div class="form-group">
            <label for="descripcion">Descripción:</label>
            <textarea id="descripcion" name="descripcion" required><?php echo htmlspecialchars($_POST['descripcion'] ?? ''); ?></textarea>
        </div>
        
        <div class="form-group">
            <label for="cobertura">Zona de Cobertura:</label>
            <input type="text" id="cobertura" name="cobertura" required 
                   value="<?php echo htmlspecialchars($_POST['cobertura'] ?? ''); ?>">
        </div>
        
        <div class="form-group">
            <label for="telefono">Teléfono de Contacto:</label>
            <input type="tel" id="telefono" name="telefono" 
                   value="<?php echo htmlspecialchars($_POST['telefono'] ?? ''); ?>">
        </div>
        
        <div class="form-group">
            <label for="email">Email de Contacto:</label>
            <input type="email" id="email" name="email" required 
                   value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
        </div>
        
        <div class="form-group">
            <label for="foto_perfil">Foto de Perfil (opcional):</label>
            <input type="file" id="foto_perfil" name="foto_perfil" accept="image/*">
        </div>
        
        <button type="submit">Publicar Servicio</button>
    </form>
</body>
</html>