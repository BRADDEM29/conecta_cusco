<?php
session_start();

// Verificar si el usuario está logueado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.html?mensaje=" . urlencode("Debes iniciar sesión para acceder al dashboard"));
    exit();
}

// Verificar que sea un profesional o empresa
if ($_SESSION['tipo_cuenta'] !== 'profesional' && $_SESSION['tipo_cuenta'] !== 'empresa') {
    header("Location: cliente.php?mensaje=" . urlencode("Esta sección es solo para profesionales y empresas"));
    exit();
}

// Incluir configuración de base de datos
require_once '/config.php';

// Conectar a la base de datos
$con = conectarDB();

// Obtener información del usuario
$usuario_id = $_SESSION['usuario_id'];
$sql_usuario = "SELECT nombre_completo, correo_electronico FROM usuarios WHERE id = ?";
$stmt_usuario = $con->prepare($sql_usuario);
$stmt_usuario->bind_param("i", $usuario_id);
$stmt_usuario->execute();
$result_usuario = $stmt_usuario->get_result();
$user_info = $result_usuario->fetch_assoc();

// Obtener servicios del profesional
$sql_servicios = "SELECT * FROM servicios_publicados WHERE user_id = ? AND estado = 'activo' ORDER BY fecha_publicacion DESC";
$stmt_servicios = $con->prepare($sql_servicios);
$stmt_servicios->bind_param("i", $usuario_id);
$stmt_servicios->execute();
$result_servicios = $stmt_servicios->get_result();

// Contar servicios
$total_servicios = $result_servicios->num_rows;

// Obtener contactos recibidos
$sql_contactos = "SELECT COUNT(*) as total FROM contactos WHERE profesional_id = ?";
$stmt_contactos = $con->prepare($sql_contactos);
$stmt_contactos->bind_param("i", $usuario_id);
$stmt_contactos->execute();
$result_contactos = $stmt_contactos->get_result();
$total_contactos = $result_contactos->fetch_assoc()['total'];

// Obtener reseñas
$sql_resenas = "SELECT COUNT(*) as total FROM resenas WHERE profesional_id = ?";
$stmt_resenas = $con->prepare($sql_resenas);
$stmt_resenas->bind_param("i", $usuario_id);
$stmt_resenas->execute();
$result_resenas = $stmt_resenas->get_result();
$total_resenas = $result_resenas->fetch_assoc()['total'];

// Obtener mensaje de éxito si existe
$mensaje_exito = $_GET['mensaje'] ?? '';

// Cerrar conexión
cerrarConexion($con);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard Profesional - Conecta Cusco</title>
    <link rel="stylesheet" href="/Estilos/index.css">
    <style>
        .dashboard-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        .welcome-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            border-radius: 10px;
            margin-bottom: 30px;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            text-align: center;
        }
        .stat-number {
            font-size: 2em;
            font-weight: bold;
            color: #667eea;
        }
        .stat-label {
            color: #666;
            margin-top: 5px;
        }
        .services-section {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .service-item {
            border: 1px solid #eee;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .service-info h3 {
            margin: 0 0 5px 0;
            color: #333;
        }
        .service-category {
            color: #666;
            font-size: 0.9em;
        }
        .service-actions {
            display: flex;
            gap: 10px;
        }
        .btn {
            padding: 8px 16px;
            border-radius: 5px;
            text-decoration: none;
            font-size: 0.9em;
            border: none;
            cursor: pointer;
        }
        .btn-primary {
            background: #667eea;
            color: white;
        }
        .btn-secondary {
            background: #6c757d;
            color: white;
        }
        .btn-danger {
            background: #e74c3c;
            color: white;
        }
        .nav-actions {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }
        .nav-btn {
            background: rgba(255,255,255,0.2);
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            border: 1px solid rgba(255,255,255,0.3);
        }
        .nav-btn:hover {
            background: rgba(255,255,255,0.3);
        }
        .logout-btn {
            background: #e74c3c;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            border: none;
            cursor: pointer;
        }
        .logout-btn:hover {
            background: #c0392b;
        }
        .empty-state {
            text-align: center;
            padding: 40px;
            color: #666;
        }
        
        /* Estilos para el menú de usuario */
        .user-menu {
            position: relative;
            display: inline-block;
        }
        
        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            cursor: pointer;
            border: 2px solid white;
            transition: all 0.3s ease;
        }
        
        .user-avatar:hover {
            transform: scale(1.1);
        }
        
        .dropdown-content {
            display: none;
            position: absolute;
            right: 0;
            background-color: white;
            min-width: 160px;
            box-shadow: 0 8px 16px rgba(0,0,0,0.2);
            z-index: 1000;
            border-radius: 8px;
            overflow: hidden;
        }
        
        .dropdown-content a {
            color: #333;
            padding: 12px 16px;
            text-decoration: none;
            display: block;
            transition: background-color 0.3s;
        }
        
        .dropdown-content a:hover {
            background-color: #f1f1f1;
        }
        
        .dropdown-content.show {
            display: block;
        }
        
        /* Menú de navegación dinámico */
        .nav-menu {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        
        .nav-left {
            display: flex;
            align-items: center;
            gap: 20px;
        }
        
        .nav-right {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        /* Mensaje de recomendación */
        .recommendation-banner {
            background: linear-gradient(135deg, #ffd700, #ffed4e);
            color: #333;
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            border-left: 4px solid #ff6b00;
        }
        
        .recommendation-banner h3 {
            margin: 0 0 5px 0;
            color: #333;
        }
        
        .recommendation-banner p {
            margin: 0;
            font-size: 0.9em;
        }

        .success-banner {
            background: linear-gradient(135deg, #28A745, #20C997);
            color: white;
            padding: 15px 20px;
            text-align: center;
            margin-bottom: 20px;
            border-radius: 8px;
        }
    </style>
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
            <div class="nav-menu">
                <div class="nav-left">
                    <ul style="display: flex; list-style: none; gap: 20px; margin: 0; padding: 0;">
                        <li><a href="/index.html">Inicio</a></li>
                        <li><a href="publicar.html">Publicar Servicio</a></li>
                        <?php if ($_SESSION['tipo_cuenta'] === 'profesional'): ?>
                        <li><a href="perfil.php">Mi Perfil</a></li>
                        <?php endif; ?>
                    </ul>
                </div>
                <div class="nav-right">
                    <div class="user-menu">
                        <img src="/Imagenes/perfil.webp" 
                             alt="Avatar" class="user-avatar" onclick="toggleDropdown()">
                        <div id="userDropdown" class="dropdown-content">
                            <?php if ($_SESSION['tipo_cuenta'] === 'profesional'): ?>
                            <a href="perfil.php">Mi Perfil</a>
                            <?php endif; ?>
                            <a href="/Paginas/php/logout.php">Cerrar Sesión</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <?php if (!empty($mensaje_exito)): ?>
    <div class="success-banner">
        <h3>✅ Éxito</h3>
        <p><?php echo htmlspecialchars($mensaje_exito); ?></p>
    </div>
    <?php endif; ?>

    <main class="dashboard-container">
        <section class="welcome-section">
            <h2>¡Bienvenido, <?php echo htmlspecialchars($user_info['nombre_completo']); ?>!</h2>
            <?php if ($_SESSION['tipo_cuenta'] === 'empresa'): ?>
            <p>Gestiona los servicios de tu empresa y conecta con nuevos clientes</p>
            <?php else: ?>
            <p>Gestiona tus servicios y conecta con nuevos clientes</p>
            <?php endif; ?>
            <div class="nav-actions">
                <a href="publicar.html" class="nav-btn">Publicar Nuevo Servicio</a>
                <?php if ($_SESSION['tipo_cuenta'] === 'profesional'): ?>
                <a href="perfil.php" class="nav-btn">Editar Perfil</a>
                <?php endif; ?>
            </div>
        </section>

        <!-- Banner de recomendación -->
        <div class="recommendation-banner">
            <h3>💡 Recomendación</h3>
            <?php if ($_SESSION['tipo_cuenta'] === 'empresa'): ?>
            <p>Publica servicios detallados de tu empresa para aumentar tus oportunidades de trabajo.</p>
            <?php else: ?>
            <p>Completa tu perfil y publica servicios detallados para aumentar tus oportunidades de trabajo.</p>
            <?php endif; ?>
        </div>

        <!-- Estadísticas -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number"><?php echo $total_servicios; ?></div>
                <div class="stat-label">Servicios Publicados</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $total_contactos; ?></div>
                <div class="stat-label">Contactos Recibidos</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $total_resenas; ?></div>
                <div class="stat-label">Reseñas</div>
            </div>
        </div>

        <!-- Servicios del profesional -->
        <section class="services-section">
            <h2>Mis Servicios</h2>
            <?php if ($result_servicios && $result_servicios->num_rows > 0): ?>
                <?php while ($row = $result_servicios->fetch_assoc()): ?>
                    <div class="service-item">
                        <div class="service-info">
                            <h3><?php echo htmlspecialchars($row['titulo']); ?></h3>
                            <p class="service-category"><?php echo htmlspecialchars($row['categoria']); ?></p>
                        </div>
                        <div class="service-actions">
                            <a href="editar-servicio.html?id=<?php echo $row['id']; ?>" class="btn btn-secondary">Editar</a>
                            <a href="eliminar-servicio.html?id=<?php echo $row['id']; ?>" class="btn btn-danger" onclick="return confirm('¿Estás seguro de que quieres eliminar este servicio?')">Eliminar</a>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="empty-state">
                    <h3>No tienes servicios publicados</h3>
                    <p>Comienza publicando tu primer servicio para conectar con clientes</p>
                    <div style="margin-top: 20px;">
                        <a href="publicar.html" class="btn btn-primary">Publicar Servicio</a>
                        <?php if ($_SESSION['tipo_cuenta'] === 'profesional'): ?>
                        <a href="perfil.php" class="btn btn-secondary">Editar Perfil</a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
        </section>
    </main>

    <footer>
        <div class="container">
            <p>&copy; 2025 Conecta Cusco. Todos los derechos reservados.</p>
        </div>
    </footer>

    <script>
        function toggleDropdown() {
            document.getElementById("userDropdown").classList.toggle("show");
        }

        // Cerrar dropdown si se hace clic fuera
        window.onclick = function(event) {
            if (!event.target.matches('.user-avatar')) {
                var dropdowns = document.getElementsByClassName("dropdown-content");
                for (var i = 0; i < dropdowns.length; i++) {
                    var openDropdown = dropdowns[i];
                    if (openDropdown.classList.contains('show')) {
                        openDropdown.classList.remove('show');
                    }
                }
            }
        }
    </script>
</body>
</html> 