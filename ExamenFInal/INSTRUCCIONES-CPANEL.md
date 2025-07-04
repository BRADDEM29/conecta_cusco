# 🚀 INSTRUCCIONES DE DESPLIEGUE EN CPANEL
## Conecta Cusco - Plataforma de Servicios

### 📋 PREPARACIÓN ANTES DEL DESPLIEGUE

1. **Crear la base de datos en cPanel:**
   - Accede a tu cPanel
   - Ve a "MySQL Databases"
   - Crea una nueva base de datos (ej: `tuusuario_conecta_cusco`)
   - Crea un usuario de base de datos
   - Asigna el usuario a la base de datos con todos los privilegios

2. **Configurar el archivo de configuración:**
   - Renombra `config-cpanel.php` a `config.php`
   - Edita el archivo y reemplaza:
     - `tu_usuario_conecta_cusco_db` → Tu nombre de base de datos
     - `tu_usuario_conecta_cusco` → Tu usuario de base de datos
     - `tu_contraseña_bd` → Tu contraseña de base de datos

### 📁 ESTRUCTURA DE ARCHIVOS PARA SUBIR

```
public_html/
├── index.html
├── config.php (renombrado desde config-cpanel.php)
├── .htaccess
├── Estilos/
│   ├── index.css
│   ├── login.css
│   ├── registro.css
│   ├── buscar-servicios.css
│   ├── buscar-servicios-dinamico.css
│   ├── publicar-servicios.css
│   ├── perfil-profesional.css
│   ├── publicacion-detalle.css
│   ├── mensajes.css
│   ├── menu-usuario.css
│   └── dashboard-profesional.css
├── Imagenes/
│   ├── logo.png
│   ├── perfil.webp
│   ├── gasfiteria.jpg
│   ├── plomero.jpg
│   └── niñera.jpg
├── js/
│   └── script.js
├── Paginas/
│   ├── html/
│   │   ├── login.html
│   │   ├── registro.html
│   │   ├── buscar-servicios.html
│   │   ├── buscar-servicios.php
│   │   ├── ver-publicacion.php
│   │   ├── cliente.html
│   │   ├── cliente.php
│   │   ├── profesional.html
│   │   ├── profesional.php
│   │   ├── perfil.html
│   │   ├── publicar.html
│   │   ├── buscar.html
│   │   └── registro-estatico.html
│   ├── php/
│   │   ├── CreacionDB.php
│   │   ├── login.php
│   │   ├── registro.php
│   │   ├── logout.php
│   │   ├── procesar-publicacion.php
│   │   ├── buscar-dinamico.php
│   │   ├── obtener-resenas.php
│   │   ├── procesar-resena.php
│   │   ├── obtener-contactos.php
│   │   ├── procesar-contacto.php
│   │   ├── actualizar-bd.php
│   │   ├── completar-actualizacion.php
│   │   ├── cliente.php
│   │   └── publicar-servicios.php
│   ├── registro.php
│   ├── login.php
│   ├── logout.php
│   ├── procesar-publicacion.php
│   ├── publicar-servicios.html
│   ├── perfil-profecional.html
│   ├── Guardar-Registro.php
│   ├── Registro-Perfil.php
│   └── perfil-profesional-registro.php
└── uploads/ (carpeta vacía, permisos 755)
```

### 🔧 PASOS DE DESPLIEGUE

#### 1. SUBIR ARCHIVOS
- Sube todos los archivos a la carpeta `public_html` de tu hosting
- Mantén la estructura de carpetas exactamente como se muestra arriba

#### 2. CONFIGURAR PERMISOS
```bash
# En cPanel File Manager, establecer permisos:
uploads/ → 755
config.php → 644
.htaccess → 644
```

#### 3. CREAR LA BASE DE DATOS
- Accede a tu sitio: `https://tudominio.com/Paginas/php/CreacionDB.php`
- Esto creará automáticamente todas las tablas necesarias
- **IMPORTANTE:** Elimina este archivo después de usarlo por seguridad

#### 4. VERIFICAR CONFIGURACIÓN
- Accede a: `https://tudominio.com/verificar-servicios.php`
- Esto verificará que todo esté funcionando correctamente

### 🔒 CONFIGURACIÓN DE SEGURIDAD

1. **Archivos protegidos por .htaccess:**
   - `config.php` - No accesible desde web
   - Archivos `.env`, `.ini`, `.log`, `.sql` - Bloqueados
   - Directorio `uploads/` - Solo lectura de archivos

2. **Headers de seguridad configurados:**
   - X-Content-Type-Options: nosniff
   - X-Frame-Options: DENY
   - X-XSS-Protection: 1; mode=block
   - Referrer-Policy: strict-origin-when-cross-origin

### 🌐 CONFIGURACIÓN DE DOMINIO

1. **SSL/HTTPS:**
   - Activa SSL en cPanel si no está activado
   - El sitio funcionará automáticamente con HTTPS

2. **Subdominios (opcional):**
   - Si usas un subdominio, ajusta las rutas en `config.php`
   - Ejemplo: `https://conecta.tudominio.com`

### 📱 FUNCIONALIDADES VERIFICADAS

✅ **Páginas principales:**
- Página de inicio responsive
- Sistema de registro y login
- Búsqueda de servicios dinámica
- Publicación de servicios
- Perfiles de profesionales
- Sistema de reseñas
- Sistema de contactos

✅ **Características técnicas:**
- Diseño responsive (móvil, tablet, desktop)
- Validación de formularios
- Subida de imágenes segura
- Sesiones de usuario
- Base de datos optimizada
- Rutas absolutas para compatibilidad

### 🐛 SOLUCIÓN DE PROBLEMAS

#### Error de conexión a base de datos:
1. Verifica credenciales en `config.php`
2. Asegúrate de que la base de datos existe
3. Verifica que el usuario tiene permisos

#### Páginas no cargan:
1. Verifica que `.htaccess` está subido
2. Revisa permisos de archivos (644 para archivos, 755 para carpetas)
3. Verifica rutas en navegador

#### Imágenes no se muestran:
1. Verifica que la carpeta `Imagenes/` está subida
2. Revisa permisos de la carpeta (755)
3. Verifica rutas en el código

#### Subida de archivos no funciona:
1. Verifica permisos de carpeta `uploads/` (755)
2. Revisa límites de PHP en cPanel
3. Verifica configuración en `config.php`

### 📞 SOPORTE

Si encuentras problemas:
1. Revisa los logs de error en cPanel
2. Verifica la configuración de PHP en cPanel
3. Asegúrate de que todas las extensiones PHP están habilitadas:
   - mysqli
   - pdo_mysql
   - gd (para procesamiento de imágenes)
   - fileinfo

### 🎉 ¡LISTO PARA USAR!

Una vez completados todos los pasos, tu sitio estará completamente funcional en cPanel con:
- ✅ Diseño responsive
- ✅ Sistema de usuarios completo
- ✅ Base de datos optimizada
- ✅ Seguridad implementada
- ✅ Compatibilidad total con cPanel

**URL principal:** `https://tudominio.com` 