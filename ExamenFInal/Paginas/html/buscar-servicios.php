<?php
session_start();

// Incluir configuración de base de datos
require_once '/config.php';

// Conectar a la base de datos
$con = conectarDB();

// Obtener parámetros de búsqueda
$busqueda = $_GET['q'] ?? '';
$ubicacion = $_GET['location'] ?? '';
$categoria = $_GET['categoria'] ?? '';
$calificacion = $_GET['calificacion'] ?? '';
$disponibilidad = $_GET['disponibilidad'] ?? '';

// Construir consulta SQL
$sql = "SELECT sp.*, u.nombre_completo, u.correo_electronico 
        FROM servicios_publicados sp 
        JOIN usuarios u ON sp.user_id = u.id 
        WHERE sp.estado = 'activo'";

$params = [];
$types = '';

// Agregar filtros si están presentes
if (!empty($busqueda)) {
    $sql .= " AND (sp.titulo LIKE ? OR sp.descripcion LIKE ? OR sp.categoria LIKE ?)";
    $busqueda_param = "%$busqueda%";
    $params[] = $busqueda_param;
    $params[] = $busqueda_param;
    $params[] = $busqueda_param;
    $types .= 'sss';
}

if (!empty($categoria)) {
    $sql .= " AND sp.categoria = ?";
    $params[] = $categoria;
    $types .= 's';
}

if (!empty($disponibilidad)) {
    $sql .= " AND sp.disponibilidad = ?";
    $params[] = $disponibilidad;
    $types .= 's';
}

if (!empty($calificacion)) {
    $sql .= " AND sp.calificacion_promedio >= ?";
    $params[] = $calificacion;
    $types .= 'd';
}

$sql .= " ORDER BY sp.fecha_publicacion DESC";

// Preparar y ejecutar consulta
$stmt = $con->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

// Cerrar conexión
cerrarConexion($con);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Buscar Servicios - Conecta Cusco</title>
    <meta name="description" content="Explora la lista de profesionales y servicios disponibles en Cusco.">
    <link rel="stylesheet" href="/Estilos/buscar-servicios.css">
    <link rel="stylesheet" href="/Estilos/buscar-servicios-dinamico.css">
    <link rel="icon" href="/Imagenes/favicon.ico" type="image/x-icon">
            height: 60px;
            border-radius: 50%;
            object-fit: cover;
            margin-right: 15px;
            border: 3px solid #667eea;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.2);
            transition: all 0.3s ease;
        }
        
        .service-card:hover .service-photo {
            transform: scale(1.1);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.35);
        }
        
        .service-info h3 {
            margin: 0 0 8px 0;
            color: #2c3e50;
            font-size: 1.2em;
            font-weight: 700;
            line-height: 1.2;
            text-shadow: 0 1px 2px rgba(0,0,0,0.1);
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            max-width: 200px;
        }
        
        .service-category {
            font-size: 0.85em;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 4px 12px;
            border-radius: 15px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            max-width: 150px;
            border: 2px solid;
        }
        
        /* Estilos específicos por categoría */
        .category-plomeria {
            color: #2563eb;
            background: linear-gradient(135deg, rgba(37, 99, 235, 0.1), rgba(59, 130, 246, 0.1));
            border-color: rgba(37, 99, 235, 0.3);
        }
        
        .category-gasfiteria {
            color: #0891b2;
            background: linear-gradient(135deg, rgba(8, 145, 178, 0.1), rgba(14, 165, 233, 0.1));
            border-color: rgba(8, 145, 178, 0.3);
        }
        
        .category-ninyera {
            color: #ec4899;
            background: linear-gradient(135deg, rgba(236, 72, 153, 0.1), rgba(244, 63, 94, 0.1));
            border-color: rgba(236, 72, 153, 0.3);
        }
        
        .category-limpieza {
            color: #059669;
            background: linear-gradient(135deg, rgba(5, 150, 105, 0.1), rgba(16, 185, 129, 0.1));
            border-color: rgba(5, 150, 105, 0.3);
        }
        
        .category-jardineria {
            color: #16a34a;
            background: linear-gradient(135deg, rgba(22, 163, 74, 0.1), rgba(34, 197, 94, 0.1));
            border-color: rgba(22, 163, 74, 0.3);
        }
        
        .category-electricidad {
            color: #f59e0b;
            background: linear-gradient(135deg, rgba(245, 158, 11, 0.1), rgba(251, 191, 36, 0.1));
            border-color: rgba(245, 158, 11, 0.3);
        }
        
        .category-albanileria {
            color: #7c3aed;
            background: linear-gradient(135deg, rgba(124, 58, 237, 0.1), rgba(139, 92, 246, 0.1));
            border-color: rgba(124, 58, 237, 0.3);
        }
        
        .category-carpinteria {
            color: #92400e;
            background: linear-gradient(135deg, rgba(146, 64, 14, 0.1), rgba(180, 83, 9, 0.1));
            border-color: rgba(146, 64, 14, 0.3);
        }
        
        .category-pintura {
            color: #dc2626;
            background: linear-gradient(135deg, rgba(220, 38, 38, 0.1), rgba(239, 68, 68, 0.1));
            border-color: rgba(220, 38, 38, 0.3);
        }
        
        .category-otros {
            color: #6b7280;
            background: linear-gradient(135deg, rgba(107, 114, 128, 0.1), rgba(156, 163, 175, 0.1));
            border-color: rgba(107, 114, 128, 0.3);
        }

        /* Descripción del servicio */
        .service-description {
            color: #555;
            margin-bottom: 20px;
            line-height: 1.5;
            font-size: 0.9em;
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            padding: 12px;
            border-radius: 12px;
            border-left: 4px solid #667eea;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            overflow: hidden;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            max-height: 4.5em;
        }

        /* Detalles del servicio */
        .service-details {
            display: flex;
            flex-direction: column;
            gap: 20px;
            flex-grow: 1;
            justify-content: space-between;
        }
        
        .service-meta {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 12px;
        }
        
        .meta-item {
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            padding: 10px 8px;
            border-radius: 12px;
            border-left: 3px solid #667eea;
            text-align: center;
            box-shadow: 0 2px 6px rgba(0,0,0,0.05);
            transition: all 0.3s ease;
            min-height: 50px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        
        .meta-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.15);
        }
        
        .meta-item strong {
            display: block;
            color: #2c3e50;
            font-size: 0.75em;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 2px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        
        .meta-item span {
            color: #555;
            font-weight: 600;
            font-size: 0.85em;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            display: block;
        }
        
        .availability-badge {
            display: inline-flex;
            align-items: center;
            gap: 3px;
            padding: 4px 8px;
            border-radius: 15px;
            font-weight: 600;
            font-size: 0.8em;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            max-width: 120px;
        }
        
        .available {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .not-available {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        /* Botones de acción */
        .service-actions {
            display: flex;
            flex-direction: row;
            gap: 10px;
            justify-content: center;
            margin-top: auto;
        }
        
        .btn {
            padding: 10px 15px;
            border-radius: 8px;
            text-decoration: none;
            font-size: 0.85em;
            font-weight: 600;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
            text-align: center;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            min-width: 100px;
            max-width: 140px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
        }
        
        .btn-secondary {
            background: linear-gradient(135deg, #6c757d, #495057);
            color: white;
            box-shadow: 0 4px 15px rgba(108, 117, 125, 0.3);
        }
        
        .btn-primary:hover {
            background: linear-gradient(135deg, #5a6fd8, #6a4c93);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
        }
        
        .btn-secondary:hover {
            background: linear-gradient(135deg, #5a6268, #343a40);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(108, 117, 125, 0.4);
        }

        /* Estado sin resultados */
        .no-results {
            text-align: center;
            padding: 80px 40px;
            color: #666;
            background: white;
            border-radius: 25px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            border: 1px solid rgba(102, 126, 234, 0.1);
            max-width: 600px;
            margin: 0 auto;
        }
        
        .no-results h3 {
            color: #2c3e50;
            margin-bottom: 15px;
            font-size: 1.5em;
        }
        
        .no-results p {
            font-size: 1.1em;
            line-height: 1.6;
        }

        /* Formulario de búsqueda mejorado */
        .search-form {
            background: white;
            padding: 30px;
            border-radius: 20px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.08);
            margin-bottom: 30px;
            border: 1px solid rgba(102, 126, 234, 0.1);
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #2c3e50;
            font-size: 0.95em;
        }
        
        .form-group input,
        .form-group select {
            width: 100%;
            padding: 15px;
            border: 2px solid #e9ecef;
            border-radius: 10px;
            font-size: 16px;
            transition: all 0.3s ease;
            background: #f8f9fa;
        }
        
        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: #667eea;
            background: white;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
        
        .search-btn {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            padding: 15px 30px;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 600;
            width: 100%;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
        }
        
        .search-btn:hover {
            background: linear-gradient(135deg, #5a6fd8, #6a4c93);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
        }

        /* Sección de filtros mejorada */
        .filters-section {
            background: white;
            padding: 30px;
            border-radius: 20px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.08);
            margin-bottom: 30px;
            border: 1px solid rgba(102, 126, 234, 0.1);
        }
        
        .filters-section h3 {
            color: #2c3e50;
            margin-bottom: 25px;
            font-size: 1.4em;
            text-align: center;
            position: relative;
        }
        
        .filters-section h3::after {
            content: '';
            position: absolute;
            bottom: -8px;
            left: 50%;
            transform: translateX(-50%);
            width: 60px;
            height: 2px;
            background: linear-gradient(90deg, #667eea, #764ba2);
            border-radius: 1px;
        }
        
        .filters-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
        }

        /* Responsive design */
        @media (max-width: 768px) {
            .listings-grid {
                grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
                gap: 20px;
                padding: 0 15px;
            }
            
            .service-card {
                min-height: auto;
                padding: 20px;
                max-width: none;
            }
            
            .service-details {
                gap: 20px;
            }
            
            .service-actions {
                flex-direction: column;
                gap: 10px;
            }
            
            .service-meta {
                grid-template-columns: 1fr;
                gap: 12px;
            }
            
            .meta-item {
                padding: 12px;
                text-align: left;
            }
            
            .filters-grid {
                grid-template-columns: 1fr;
            }
        }
        
        @media (max-width: 480px) {
            .listings-grid {
                grid-template-columns: 1fr;
                gap: 15px;
                padding: 0 10px;
            }
            
            .service-card {
                padding: 18px;
                max-width: none;
            }
            
            .service-header {
                flex-direction: column;
                text-align: center;
                gap: 15px;
            }
            
            .service-photo {
                margin-right: 0;
                margin-bottom: 10px;
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

    <main>
        <section class="hero">
            <div class="container">
                <h2>Encuentra el Profesional Ideal Cerca de Ti</h2>
                <form class="search-form" method="get">
                    <div class="form-group">
                        <input type="text" name="q" placeholder="Busca por servicio o habilidad (ej: gasfitero, niñera)" value="<?php echo htmlspecialchars($busqueda); ?>">
                    </div>
                    <div class="form-group">
                        <input type="text" name="location" placeholder="Tu distrito o zona" value="<?php echo htmlspecialchars($ubicacion); ?>">
                    </div>
                    <button type="submit" class="search-btn">Buscar</button>
                </form>
            </div>
        </section>
        
        <!-- Filtros de búsqueda -->
        <section class="filters-section">
            <div class="container">
                <h3>Filtros Avanzados</h3>
                <form method="get">
                    <input type="hidden" name="q" value="<?php echo htmlspecialchars($busqueda); ?>">
                    <input type="hidden" name="location" value="<?php echo htmlspecialchars($ubicacion); ?>">
                    <div class="filters-grid">
                        <div class="form-group">
                            <label for="categoria">Categoría:</label>
                            <select id="categoria" name="categoria">
                                <option value="">Todas las categorías</option>
                                <option value="plomeria" <?php echo $categoria === 'plomeria' ? 'selected' : ''; ?>>Plomería</option>
                                <option value="gasfiteria" <?php echo $categoria === 'gasfiteria' ? 'selected' : ''; ?>>Gasfitería</option>
                                <option value="ninyera" <?php echo $categoria === 'ninyera' ? 'selected' : ''; ?>>Niñera/Cuidado de niños</option>
                                <option value="limpieza" <?php echo $categoria === 'limpieza' ? 'selected' : ''; ?>>Limpieza del hogar</option>
                                <option value="jardineria" <?php echo $categoria === 'jardineria' ? 'selected' : ''; ?>>Jardinería</option>
                                <option value="electricidad" <?php echo $categoria === 'electricidad' ? 'selected' : ''; ?>>Electricidad</option>
                                <option value="albanileria" <?php echo $categoria === 'albanileria' ? 'selected' : ''; ?>>Albañilería</option>
                                <option value="carpinteria" <?php echo $categoria === 'carpinteria' ? 'selected' : ''; ?>>Carpintería</option>
                                <option value="pintura" <?php echo $categoria === 'pintura' ? 'selected' : ''; ?>>Pintura</option>
                                <option value="otros" <?php echo $categoria === 'otros' ? 'selected' : ''; ?>>Otros servicios</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="calificacion">Calificación mínima:</label>
                            <select id="calificacion" name="calificacion">
                                <option value="">Cualquier calificación</option>
                                <option value="4" <?php echo $calificacion === '4' ? 'selected' : ''; ?>>4+ estrellas</option>
                                <option value="3" <?php echo $calificacion === '3' ? 'selected' : ''; ?>>3+ estrellas</option>
                                <option value="2" <?php echo $calificacion === '2' ? 'selected' : ''; ?>>2+ estrellas</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="disponibilidad">Disponibilidad:</label>
                            <select id="disponibilidad" name="disponibilidad">
                                <option value="">Cualquier disponibilidad</option>
                                <option value="disponible" <?php echo $disponibilidad === 'disponible' ? 'selected' : ''; ?>>Disponible</option>
                                <option value="no_disponible" <?php echo $disponibilidad === 'no_disponible' ? 'selected' : ''; ?>>No disponible</option>
                            </select>
                        </div>
                    </div>
                    <button type="submit" class="search-btn">Aplicar Filtros</button>
                </form>
            </div>
        </section>
        
        <section class="service-listings">
            <div class="container">
                <h2>Servicios Disponibles</h2>
                <?php if ($result && $result->num_rows > 0): ?>
                    <div class="listings-grid">
                        <?php while ($row = $result->fetch_assoc()): ?>
                                                    <?php 
                        $categoria = strtolower($row['categoria']);
                        $categoria_class = 'category-' . $categoria;
                        $card_class = 'card-' . $categoria;
                        ?>
                        <div class="service-card <?php echo $card_class; ?>">
                            <div class="service-header">
                                <img src="/Imagenes/perfil.webp" alt="Foto de perfil" class="service-photo">
                                <div class="service-info">
                                    <h3><?php echo htmlspecialchars($row['titulo']); ?></h3>
                                    <p class="service-category <?php echo $categoria_class; ?>">
                                        <?php 
                                        // Iconos por categoría
                                        $iconos = [
                                            'plomeria' => '🔧',
                                            'gasfiteria' => '🚰',
                                            'ninyera' => '👶',
                                            'limpieza' => '🧹',
                                            'jardineria' => '🌱',
                                            'electricidad' => '⚡',
                                            'albanileria' => '🏗️',
                                            'carpinteria' => '🪚',
                                            'pintura' => '🎨',
                                            'otros' => '🔧'
                                        ];
                                        $icono = isset($iconos[$categoria]) ? $iconos[$categoria] : '🔧';
                                        echo $icono . ' ' . htmlspecialchars($row['categoria']); 
                                        ?>
                                    </p>
                                </div>
                            </div>
                                <p class="service-description"><?php 
                                $descripcion = htmlspecialchars($row['descripcion']);
                                if (strlen($descripcion) > 150) {
                                    echo substr($descripcion, 0, 150) . '...';
                                } else {
                                    echo $descripcion;
                                }
                            ?></p>
                                <div class="service-details">
                                    <div class="service-meta">
                                        <div class="meta-item">
                                            <strong>Profesional</strong>
                                            <span><?php echo htmlspecialchars($row['nombre_completo']); ?></span>
                                        </div>
                                        <div class="meta-item">
                                            <strong>Cobertura</strong>
                                            <span><?php echo htmlspecialchars($row['cobertura']); ?></span>
                                        </div>
                                        <?php if (!empty($row['precio'])): ?>
                                        <div class="meta-item">
                                            <strong>Precio</strong>
                                            <span>S/ <?php echo htmlspecialchars($row['precio']); ?></span>
                                        </div>
                                        <?php endif; ?>
                                        <div class="meta-item">
                                            <strong>Disponibilidad</strong>
                                            <span class="availability-badge <?php echo $row['disponibilidad'] === 'disponible' ? 'available' : 'not-available'; ?>">
                                                <?php echo $row['disponibilidad'] === 'disponible' ? '✅ Disponible' : '❌ No disponible'; ?>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="service-actions">
                                        <a href="ver-publicacion.php?id=<?php echo $row['id']; ?>" class="btn btn-primary">🔍 Ver Publicación</a>
                                        <a href="mailto:<?php echo htmlspecialchars($row['correo_electronico']); ?>" class="btn btn-secondary">📧 Contactar</a>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </div>
                <?php else: ?>
                    <div class="no-results">
                        <h3>No se encontraron servicios</h3>
                        <p>Intenta ajustar tus filtros de búsqueda o publica un nuevo servicio.</p>
                    </div>
                <?php endif; ?>
            </div>
        </section>
    </main>

    <footer>
        <div class="container">
            <p>&copy; 2025 Conecta Cusco. Todos los derechos reservados.</p>
        </div>
    </footer>
</body>
</html> 