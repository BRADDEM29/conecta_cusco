<?php
session_start();

// Verificar que se proporcione un ID
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: buscar-servicios.php?mensaje=" . urlencode("Publicación no encontrada"));
    exit();
}

$publicacion_id = (int)$_GET['id'];

// Incluir configuración de base de datos
require_once '/config.php';

// Conectar a la base de datos
$con = conectarDB();

// Obtener detalles de la publicación
$sql = "SELECT sp.*, u.nombre_completo, u.correo_electronico, u.telefono, u.tipo_cuenta
        FROM servicios_publicados sp 
        JOIN usuarios u ON sp.user_id = u.id 
        WHERE sp.id = ? AND sp.estado = 'activo'";

$stmt = $con->prepare($sql);
$stmt->bind_param("i", $publicacion_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: buscar-servicios.php?mensaje=" . urlencode("Publicación no encontrada"));
    exit();
}

$publicacion = $result->fetch_assoc();

// Obtener reseñas de la publicación
try {
    $sql_resenas = "SELECT r.*, u.nombre_completo as cliente_nombre
                    FROM resenas r
                    JOIN usuarios u ON r.cliente_id = u.id
                    WHERE r.servicio_id = ? AND r.estado = 'activa'
                    ORDER BY r.fecha_resena DESC
                    LIMIT 5";

    $stmt_resenas = $con->prepare($sql_resenas);
    if ($stmt_resenas) {
        $stmt_resenas->bind_param("i", $publicacion_id);
        $stmt_resenas->execute();
        $result_resenas = $stmt_resenas->get_result();
    } else {
        $result_resenas = null;
    }
} catch (Exception $e) {
    // Si hay error con la columna estado, intentar sin ella
    $sql_resenas = "SELECT r.*, u.nombre_completo as cliente_nombre
                    FROM resenas r
                    JOIN usuarios u ON r.cliente_id = u.id
                    WHERE r.servicio_id = ?
                    ORDER BY r.fecha_resena DESC
                    LIMIT 5";

    $stmt_resenas = $con->prepare($sql_resenas);
    if ($stmt_resenas) {
        $stmt_resenas->bind_param("i", $publicacion_id);
        $stmt_resenas->execute();
        $result_resenas = $stmt_resenas->get_result();
    } else {
        $result_resenas = null;
    }
}

// Cerrar conexión
cerrarConexion($con);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo htmlspecialchars($publicacion['titulo']); ?> - Conecta Cusco</title>
    <meta name="description" content="<?php echo htmlspecialchars(substr($publicacion['descripcion'], 0, 160)); ?>">
    <link rel="stylesheet" href="/Estilos/index.css">
    <link rel="stylesheet" href="/Estilos/publicacion-detalle.css">
    <link rel="icon" href="/Imagenes/favicon.ico" type="image/x-icon">
            line-height: 1.9;
            color: #555;
            font-size: 1.15em;
            background: #f8f9fa;
            padding: 25px;
            border-radius: 15px;
            border-left: 5px solid #667eea;
        }
        
        .contact-section {
            background: white;
            border-radius: 25px;
            padding: 40px;
            box-shadow: 0 8px 30px rgba(0,0,0,0.08);
            margin-bottom: 30px;
            border: 1px solid rgba(102, 126, 234, 0.1);
        }
        
        .contact-title {
            font-size: 1.8em;
            color: #2c3e50;
            margin-bottom: 25px;
            border-bottom: 3px solid #667eea;
            padding-bottom: 15px;
            font-weight: 700;
            position: relative;
        }
        
        .contact-title::after {
            content: '';
            position: absolute;
            bottom: -3px;
            left: 0;
            width: 60px;
            height: 3px;
            background: linear-gradient(90deg, #667eea, #764ba2);
        }
        
        .contact-info {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 25px;
            margin-bottom: 30px;
        }
        
        .contact-item {
            display: flex;
            align-items: center;
            gap: 20px;
            padding: 20px;
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            transition: all 0.3s ease;
        }
        
        .contact-item:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.15);
        }
        
        .contact-icon {
            font-size: 2em;
            color: #667eea;
            background: white;
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.2);
        }
        
        .contact-details h4 {
            margin: 0 0 8px 0;
            color: #2c3e50;
            font-weight: 700;
            font-size: 0.9em;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .contact-details p {
            margin: 0;
            color: #555;
            font-weight: 600;
            font-size: 1.1em;
        }
        
        .action-buttons {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
            justify-content: center;
        }
        
        .btn {
            padding: 15px 30px;
            border-radius: 15px;
            text-decoration: none;
            font-weight: 700;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            font-size: 1.1em;
            display: flex;
            align-items: center;
            gap: 10px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
        }
        
        .btn-secondary {
            background: linear-gradient(135deg, #6c757d, #495057);
            color: white;
        }
        
        .btn-success {
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
        }
        
        .btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.2);
        }
        
        .reviews-section {
            background: white;
            border-radius: 25px;
            padding: 40px;
            box-shadow: 0 8px 30px rgba(0,0,0,0.08);
            border: 1px solid rgba(102, 126, 234, 0.1);
        }
        
        .reviews-title {
            font-size: 1.8em;
            color: #2c3e50;
            margin-bottom: 25px;
            border-bottom: 3px solid #667eea;
            padding-bottom: 15px;
            font-weight: 700;
            position: relative;
        }
        
        .reviews-title::after {
            content: '';
            position: absolute;
            bottom: -3px;
            left: 0;
            width: 60px;
            height: 3px;
            background: linear-gradient(90deg, #667eea, #764ba2);
        }
        
        .review-item {
            border: 1px solid #e9ecef;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 20px;
            background: #f8f9fa;
            transition: all 0.3s ease;
        }
        
        .review-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0,0,0,0.1);
        }
        
        .review-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }
        
        .reviewer-name {
            font-weight: 700;
            color: #2c3e50;
            font-size: 1.1em;
        }
        
        .review-date {
            color: #666;
            font-size: 0.9em;
            background: white;
            padding: 5px 12px;
            border-radius: 20px;
        }
        
        .review-rating {
            color: #ffc107;
            font-size: 1.4em;
            margin-bottom: 15px;
        }
        
        .review-comment {
            color: #555;
            line-height: 1.7;
            font-size: 1.05em;
            background: white;
            padding: 15px;
            border-radius: 10px;
            border-left: 4px solid #667eea;
        }
        
        .no-reviews {
            text-align: center;
            color: #666;
            padding: 60px 20px;
            background: #f8f9fa;
            border-radius: 15px;
        }
        
        .no-reviews h3 {
            color: #2c3e50;
            margin-bottom: 15px;
            font-size: 1.5em;
        }
        
        .back-button {
            background: linear-gradient(135deg, #6c757d, #495057);
            color: white;
            padding: 12px 25px;
            border-radius: 10px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 25px;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(108, 117, 125, 0.3);
        }
        
        .back-button:hover {
            background: linear-gradient(135deg, #5a6268, #343a40);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(108, 117, 125, 0.4);
        }
        
        .availability-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 16px;
            border-radius: 25px;
            font-weight: 700;
            font-size: 0.9em;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .available {
            background: linear-gradient(135deg, #d4edda, #c3e6cb);
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .not-available {
            background: linear-gradient(135deg, #f8d7da, #f5c6cb);
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        /* Responsive design */
        @media (max-width: 768px) {
            .publication-container {
                padding: 15px;
            }
            
            .publication-title {
                font-size: 2.2em;
            }
            
            .publication-meta {
                grid-template-columns: 1fr;
                gap: 20px;
            }
            
            .contact-info {
                grid-template-columns: 1fr;
                gap: 20px;
            }
            
            .action-buttons {
                flex-direction: column;
                gap: 15px;
            }
            
            .btn {
                width: 100%;
                justify-content: center;
            }
            
            .meta-item {
                padding: 15px;
            }
            
            .contact-item {
                padding: 15px;
            }
        }
        
        @media (max-width: 480px) {
            .publication-container {
                padding: 10px;
            }
            
            .publication-header,
            .publication-description,
            .contact-section,
            .reviews-section {
                padding: 25px;
                border-radius: 20px;
            }
            
            .publication-title {
                font-size: 1.8em;
            }
            
            .meta-item,
            .contact-item {
                padding: 12px;
            }
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
                <li><a href="/Paginas/html/buscar-servicios.php">Buscar Servicios</a></li>
                <li><a href="/Paginas/php/publicar-servicios.php">Publicar mi Servicio</a></li>
                <li><a href="/Paginas/html/login.html">Iniciar Sesión</a></li>
                <li><a href="/Paginas/html/registro.html" class="btn-registro">Registrarse</a></li>
            </ul>
        </div>
    </nav>

    <main class="publication-container">
        <a href="/Paginas/html/buscar-servicios.php" class="back-button">← Volver a Búsqueda</a>

        <!-- Encabezado de la publicación -->
        <section class="publication-header">
            <h1 class="publication-title"><?php echo htmlspecialchars($publicacion['titulo']); ?></h1>
            <span class="publication-category"><?php echo htmlspecialchars($publicacion['categoria']); ?></span>
            
            <div class="publication-meta">
                <div class="meta-item">
                    <h4>Profesional</h4>
                    <p><?php echo htmlspecialchars($publicacion['nombre_completo']); ?></p>
                </div>
                <div class="meta-item">
                    <h4>Tipo de Cuenta</h4>
                    <p><?php echo ucfirst($publicacion['tipo_cuenta']); ?></p>
                </div>
                <div class="meta-item">
                    <h4>Área de Cobertura</h4>
                    <p><?php echo htmlspecialchars($publicacion['cobertura']); ?></p>
                </div>
                <div class="meta-item">
                    <h4>Disponibilidad</h4>
                    <span class="availability-badge <?php echo $publicacion['disponibilidad'] === 'disponible' ? 'available' : 'not-available'; ?>">
                        <?php echo $publicacion['disponibilidad'] === 'disponible' ? '✅ Disponible' : '❌ No disponible'; ?>
                    </span>
                </div>
                <?php if (!empty($publicacion['precio'])): ?>
                <div class="meta-item">
                    <h4>Precio</h4>
                    <p>S/ <?php echo htmlspecialchars($publicacion['precio']); ?></p>
                </div>
                <?php endif; ?>
                <div class="meta-item">
                    <h4>Calificación</h4>
                    <p><?php echo number_format($publicacion['calificacion_promedio'], 1); ?> ⭐ (<?php echo $publicacion['total_calificaciones']; ?> reseñas)</p>
                </div>
            </div>
        </section>

        <!-- Descripción del servicio -->
        <section class="publication-description">
            <h2 class="description-title">Descripción del Servicio</h2>
            <div class="description-content">
                <?php echo nl2br(htmlspecialchars($publicacion['descripcion'])); ?>
            </div>
        </section>

        <!-- Información de contacto -->
        <section class="contact-section">
            <h2 class="contact-title">Información de Contacto</h2>
            <div class="contact-info">
                <div class="contact-item">
                    <div class="contact-icon">👤</div>
                    <div class="contact-details">
                        <h4>Nombre</h4>
                        <p><?php echo htmlspecialchars($publicacion['nombre_completo']); ?></p>
                    </div>
                </div>
                <div class="contact-item">
                    <div class="contact-icon">📧</div>
                    <div class="contact-details">
                        <h4>Email</h4>
                        <p><?php echo htmlspecialchars($publicacion['correo_electronico']); ?></p>
                    </div>
                </div>
                <?php if (!empty($publicacion['telefono'])): ?>
                <div class="contact-item">
                    <div class="contact-icon">📞</div>
                    <div class="contact-details">
                        <h4>Teléfono</h4>
                        <p><?php echo htmlspecialchars($publicacion['telefono']); ?></p>
                    </div>
                </div>
                <?php endif; ?>
                <?php if (!empty($publicacion['email'])): ?>
                <div class="contact-item">
                    <div class="contact-icon">📧</div>
                    <div class="contact-details">
                        <h4>Email de Contacto</h4>
                        <p><?php echo htmlspecialchars($publicacion['email']); ?></p>
                    </div>
                </div>
                <?php endif; ?>
            </div>
            
            <div class="action-buttons">
                <a href="mailto:<?php echo htmlspecialchars($publicacion['correo_electronico']); ?>" class="btn btn-primary">📧 Enviar Mensaje</a>
                <?php if (!empty($publicacion['telefono'])): ?>
                <a href="tel:<?php echo htmlspecialchars($publicacion['telefono']); ?>" class="btn btn-success">📞 Llamar Ahora</a>
                <?php endif; ?>
                <a href="/Paginas/html/buscar-servicios.php" class="btn btn-secondary">🔍 Ver Más Servicios</a>
            </div>
        </section>

        <!-- Reseñas -->
        <section class="reviews-section">
            <h2 class="reviews-title">Reseñas de Clientes</h2>
            <?php if ($result_resenas && $result_resenas->num_rows > 0): ?>
                <?php while ($resena = $result_resenas->fetch_assoc()): ?>
                    <div class="review-item">
                        <div class="review-header">
                            <span class="reviewer-name"><?php echo htmlspecialchars($resena['cliente_nombre']); ?></span>
                            <span class="review-date"><?php echo date('d/m/Y', strtotime($resena['fecha_resena'])); ?></span>
                        </div>
                        <div class="review-rating">
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <?php echo $i <= $resena['calificacion'] ? '★' : '☆'; ?>
                            <?php endfor; ?>
                        </div>
                        <?php if (!empty($resena['comentario'])): ?>
                        <div class="review-comment">
                            <?php echo htmlspecialchars($resena['comentario']); ?>
                        </div>
                        <?php endif; ?>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="no-reviews">
                    <p>Esta publicación aún no tiene reseñas.</p>
                    <p>¡Sé el primero en dejar una reseña después de contratar este servicio!</p>
                </div>
            <?php endif; ?>
        </section>
    </main>

    <footer>
        <div class="container">
            <p>&copy; 2025 Conecta Cusco. Todos los derechos reservados.</p>
        </div>
    </footer>
</body>
</html> 