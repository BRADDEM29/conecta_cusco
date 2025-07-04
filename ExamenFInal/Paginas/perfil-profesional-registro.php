<?php
// Conexión a la base de datos
$conexion = new mysqli('localhost', 'root', '', 'conecta_cusco_db');

if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

// Obtener ID del profesional desde la URL
$profesional_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Consultar datos del profesional
$sql_profesional = "SELECT * FROM profesionales WHERE id = $profesional_id";
$result_profesional = $conexion->query($sql_profesional);

if ($result_profesional->num_rows === 0) {
    die("Profesional no encontrado");
}

$profesional = $result_profesional->fetch_assoc();

// Consultar servicios del profesional
$sql_servicios = "SELECT * FROM servicios WHERE profesional_id = $profesional_id";
$result_servicios = $conexion->query($sql_servicios);

// Consultar trabajos del profesional
$sql_trabajos = "SELECT * FROM trabajos WHERE profesional_id = $profesional_id LIMIT 2";
$result_trabajos = $conexion->query($sql_trabajos);

// Consultar reseñas del profesional
$sql_resenas = "SELECT * FROM reseñas WHERE profesional_id = $profesional_id";
$result_resenas = $conexion->query($sql_resenas);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Perfil de <?php echo htmlspecialchars($profesional['nombre']); ?> - Conecta Cusco</title>
    <meta name="description" content="Perfil completo de <?php echo htmlspecialchars($profesional['nombre']); ?> ofreciendo servicios de <?php echo htmlspecialchars($profesional['tipo_servicio']); ?> en Cusco.">
    <link rel="stylesheet" href="../Estilos/perfil-profesional.css">
    <link rel="icon" href="../Imagenes/favicon.ico" type="image/x-icon">
</head>
<body>
    <!-- Header y Nav igual que en tu código original -->

    <main>
        <section class="professional-profile">
            <div class="container">
                <div class="profile-header">
                    <img src="../Imagenes/perfiles/<?php echo htmlspecialchars($profesional['foto_perfil'] ? htmlspecialchars($profesional['foto_perfil']) : 'perfil-default.webp'); ?>" 
                         alt="Foto de <?php echo htmlspecialchars($profesional['nombre']); ?>" class="profile-photo">
                    <h1><?php echo htmlspecialchars($profesional['nombre']); ?></h1>
                    <p class="service-type"><?php echo htmlspecialchars($profesional['tipo_servicio']); ?></p>
                </div>

                <div class="profile-details">
                    <section class="about-me">
                        <h2>Sobre Mí</h2>
                        <p><?php echo nl2br(htmlspecialchars($profesional['descripcion'])); ?></p>
                    </section>

                    <section class="services-offered">
                        <h2>Servicios Ofrecidos</h2>
                        <ul>
                            <?php while($servicio = $result_servicios->fetch_assoc()): ?>
                                <li><?php echo htmlspecialchars($servicio['nombre_servicio']); ?> - <?php echo htmlspecialchars($servicio['precio']); ?></li>
                            <?php endwhile; ?>
                        </ul>
                    </section>

                    <section class="service-area">
                        <h2>Área de Cobertura</h2>
                        <p><?php echo htmlspecialchars($profesional['area_cobertura']); ?></p>
                    </section>

                    <section class="contact-info">
                        <h2>Contacto</h2>
                        <p>Teléfono: <?php echo htmlspecialchars($profesional['telefono']); ?></p>
                        <p>Correo Electrónico: <?php echo htmlspecialchars($profesional['email']); ?></p>
                    </section>

                    <?php if ($result_trabajos->num_rows > 0): ?>
                    <section class="work-gallery">
                        <h2>Galería de Trabajos</h2>
                        <div class="gallery-grid">
                            <?php while($trabajo = $result_trabajos->fetch_assoc()): ?>
                                <img src="../Imagenes/trabajos/<?php echo htmlspecialchars($trabajo['imagen']); ?>" 
                                     alt="<?php echo htmlspecialchars($trabajo['descripcion']); ?>">
                            <?php endwhile; ?>
                        </div>
                    </section>
                    <?php endif; ?>

                    <?php if ($result_resenas->num_rows > 0): ?>
                    <section class="reviews">
                        <h2>Reseñas de Clientes</h2>
                        <?php while($resena = $result_resenas->fetch_assoc()): ?>
                            <article class="review">
                                <p>"<?php echo htmlspecialchars($resena['comentario']); ?>"</p>
                                <p class="reviewer">- <?php echo htmlspecialchars($resena['cliente_nombre']); ?></p>
                            </article>
                        <?php endwhile; ?>
                    </section>
                    <?php endif; ?>
                </div>
            </div>
        </section>
    </main>

    <!-- Footer igual que en tu código original -->
</body>
</html>
<?php $conexion->close(); ?>