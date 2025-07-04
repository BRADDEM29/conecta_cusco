<?php
// Conexión a la base de datos
$conexion = new mysqli('localhost', 'root', '', 'conecta_cusco_db');

if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validar y sanitizar datos
    $nombre = $conexion->real_escape_string($_POST['nombre']);
    $email = $conexion->real_escape_string($_POST['email']);
    $telefono = $conexion->real_escape_string($_POST['telefono']);
    $password = password_hash($conexion->real_escape_string($_POST['password']), PASSWORD_BCRYPT);
    $tipo_servicio = $conexion->real_escape_string($_POST['tipo_servicio']);
    $descripcion = $conexion->real_escape_string($_POST['descripcion']);
    $area_cobertura = $conexion->real_escape_string($_POST['area_cobertura']);
    
    // Procesar foto de perfil
    $foto_perfil = '';
    if (isset($_FILES['foto_perfil']) && $_FILES['foto_perfil']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = '../Imagenes/perfiles/';
        $fileName = uniqid() . '_' . basename($_FILES['foto_perfil']['name']);
        $uploadFile = $uploadDir . $fileName;
        
        if (move_uploaded_file($_FILES['foto_perfil']['tmp_name'], $uploadFile)) {
            $foto_perfil = $fileName;
        }
    }
    
    // Insertar en la base de datos
    $sql = "INSERT INTO profesionales (nombre, email, telefono, password, tipo_servicio, descripcion, area_cobertura, foto_perfil) 
            VALUES ('$nombre', '$email', '$telefono', '$password', '$tipo_servicio', '$descripcion', '$area_cobertura', '$foto_perfil')";
    
    if ($conexion->query($sql) === TRUE) {
        $profesional_id = $conexion->insert_id;
        
        // Procesar servicios
        if (isset($_POST['servicios'])) {
            foreach ($_POST['servicios'] as $servicio) {
                $nombre_servicio = $conexion->real_escape_string($servicio['nombre']);
                $precio = $conexion->real_escape_string($servicio['precio']);
                
                $sql_servicio = "INSERT INTO servicios (profesional_id, nombre_servicio, precio)
                                VALUES ('$profesional_id', '$nombre_servicio', '$precio')";
                $conexion->query($sql_servicio);
            }
        }
        
        // Redirigir al perfil
        header("Location: perfil-profesional.php?id=$profesional_id");
        exit();
    } else {
        $error = "Error al registrar: " . $conexion->error;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Profesional - Conecta Cusco</title>
    <link rel="stylesheet" href="../Estilos/registro.css">
</head>
<body>
    <header>
        <!-- Mismo header que en tu página principal -->
    </header>

    <main>
        <section class="registration-form">
            <div class="container">
                <h2>Regístrate como Profesional</h2>
                <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
                
                <form action="Guardar-Registro.php" method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="nombre">Nombre Completo:</label>
                        <input type="text" id="nombre" name="nombre" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Correo Electrónico:</label>
                        <input type="email" id="email" name="email" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="telefono">Teléfono:</label>
                        <input type="tel" id="telefono" name="telefono" required>
                    </div>                    
                    <div class="form-group">
                        <label for="tipo_servicio">Tipo de Servicio Principal:</label>
                        <select id="tipo_servicio" name="tipo_servicio" required>
                            <option value="">Seleccione...</option>
                            <option value="Gasfitería">Gasfitería</option>
                            <option value="Electricista">Electricista</option>
                            <option value="Carpintería">Carpintería</option>
                            <!-- Agrega más opciones según necesites -->
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="descripcion">Descripción/Sobre Mí:</label>
                        <textarea id="descripcion" name="descripcion" rows="4" required></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="area_cobertura">Área de Cobertura (Distritos o zonas donde trabajas):</label>
                        <input type="text" id="area_cobertura" name="area_cobertura" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="foto_perfil">Foto de Perfil:</label>
                        <input type="file" id="foto_perfil" name="foto_perfil" accept="image/*">
                    </div>
                    
                    <div class="servicios-container">
                        <h3>Servicios Ofrecidos</h3>
                        <div class="servicio-item">
                            <input type="text" name="servicios[0][nombre]" placeholder="Nombre del servicio" required>
                            <input type="text" name="servicios[0][precio]" placeholder="Precio o rango">
                            <button type="button" class="add-servicio">+</button>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn-registro">Registrarme</button>
                </form>
            </div>
        </section>
    </main>

    <script>
        // Script para agregar más campos de servicios dinámicamente
        document.addEventListener('DOMContentLoaded', function() {
            const serviciosContainer = document.querySelector('.servicios-container');
            let servicioCount = 1;
            
            document.querySelector('.add-servicio').addEventListener('click', function() {
                const newServicio = document.createElement('div');
                newServicio.className = 'servicio-item';
                newServicio.innerHTML = `
                    <input type="text" name="servicios[${servicioCount}][nombre]" placeholder="Nombre del servicio" required>
                    <input type="text" name="servicios[${servicioCount}][precio]" placeholder="Precio o rango">
                    <button type="button" class="remove-servicio">-</button>
                `;
                serviciosContainer.appendChild(newServicio);
                servicioCount++;
                
                // Agregar evento al botón de eliminar
                newServicio.querySelector('.remove-servicio').addEventListener('click', function() {
                    serviciosContainer.removeChild(newServicio);
                });
            });
        });
    </script>
</body>
</html>