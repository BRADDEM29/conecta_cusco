# Conecta Cusco - Plataforma de Servicios Locales

## Descripción
Conecta Cusco es una plataforma web que conecta profesionales y empresas con clientes que necesitan servicios en la ciudad de Cusco. Permite a los usuarios buscar, contactar y contratar servicios de manera fácil y segura.

## Características Principales

### Para Clientes:
- Búsqueda de servicios por categoría, ubicación y calificación
- Filtros avanzados (disponibilidad, calificación mínima)
- Contacto directo con profesionales
- Sistema de reseñas y calificaciones
- Visualización de perfiles detallados

### Para Profesionales y Empresas:
- Registro como profesional individual o empresa
- Publicación de servicios con fotos y descripciones
- Gestión de perfil y disponibilidad
- Recepción de consultas de clientes
- Sistema de calificaciones

## Requisitos del Sistema

- PHP 7.4 o superior
- MySQL 5.7 o superior
- Servidor web (Apache/Nginx)
- Extensión PDO para PHP
- Extensión GD para PHP (para procesamiento de imágenes)

## Instalación

### 1. Clonar o descargar el proyecto
```bash
git clone [URL_DEL_REPOSITORIO]
cd EvaluacionParcial
```

### 2. Configurar la base de datos
1. Crear una base de datos MySQL llamada `conecta_cusco`
2. Importar el archivo `Paginas/php/CreacionDB.php` o ejecutar el script de creación de base de datos

### 3. Configurar la conexión
Editar el archivo `config.php` y actualizar las credenciales de la base de datos:
```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'conecta_cusco');
define('DB_USER', 'tu_usuario');
define('DB_PASS', 'tu_contraseña');
```

### 4. Configurar permisos
Asegurarse de que la carpeta `uploads/` tenga permisos de escritura:
```bash
chmod 755 uploads/
```

### 5. Configurar el servidor web
- Colocar el proyecto en el directorio web del servidor
- Configurar el DocumentRoot para apuntar a la carpeta del proyecto
- Asegurarse de que el archivo `.htaccess` esté habilitado

## Estructura del Proyecto

```
EvaluacionParcial/
├── config.php                 # Configuración principal
├── conexion.php              # Conexión a base de datos
├── .htaccess                 # Configuración de Apache
├── index.html                # Página principal
├── Estilos/                  # Archivos CSS
├── Imagenes/                 # Imágenes del sitio
├── js/                       # Archivos JavaScript
├── uploads/                  # Archivos subidos por usuarios
├── Paginas/
│   ├── html/                 # Páginas HTML
│   └── php/                  # Scripts PHP
└── README.md                 # Este archivo
```

## Configuración de Seguridad

### Archivos Protegidos
- `config.php` - Configuración de la aplicación
- `conexion.php` - Conexión a base de datos
- Archivos `.env`, `.ini`, `.log`, `.sql`

### Validaciones Implementadas
- Sanitización de datos de entrada
- Validación de archivos subidos
- Protección contra inyección SQL
- Headers de seguridad
- Timeout de sesiones

## Uso

### Acceso al Sitio
1. Abrir el navegador y navegar a la URL del proyecto
2. La página principal mostrará el formulario de búsqueda

### Registro de Usuarios
1. Hacer clic en "Registrarse"
2. Seleccionar el tipo de cuenta (Cliente, Profesional Individual, Empresa)
3. Completar el formulario con los datos requeridos
4. Para empresas, completar información adicional

### Publicación de Servicios
1. Iniciar sesión como profesional o empresa
2. Hacer clic en "Publicar mi Servicio"
3. Completar el formulario con detalles del servicio
4. Subir fotos del trabajo (opcional)

### Búsqueda de Servicios
1. Usar el formulario principal de búsqueda
2. Aplicar filtros adicionales (categoría, calificación, disponibilidad)
3. Revisar los resultados y hacer clic en "Ver Perfil"
4. Contactar al profesional directamente

## Mantenimiento

### Respaldos
- Realizar respaldos regulares de la base de datos
- Mantener copias de seguridad de archivos subidos
- Documentar cambios en la configuración

### Actualizaciones
- Mantener PHP y MySQL actualizados
- Revisar logs de errores regularmente
- Monitorear el rendimiento del sitio

## Soporte

Para soporte técnico o reportar problemas:
- Revisar la documentación de la base de datos
- Verificar los logs de errores del servidor
- Contactar al equipo de desarrollo

## Licencia

Este proyecto está desarrollado para uso educativo y comercial en la ciudad de Cusco.

---

**Desarrollado para Conecta Cusco - 2025** 