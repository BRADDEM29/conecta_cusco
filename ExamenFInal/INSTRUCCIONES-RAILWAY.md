# 🚀 INSTRUCCIONES DE DESPLIEGUE EN RAILWAY
## Conecta Cusco - Plataforma de Servicios

### 📋 PREPARACIÓN ANTES DEL DESPLIEGUE

1. **Crear cuenta en Railway:**
   - Ve a [Railway](https://railway.com/new)
   - Inicia sesión con GitHub, GitLab o Google
   - Crea un nuevo proyecto

2. **Preparar el repositorio:**
   - Sube tu código a GitHub/GitLab
   - Asegúrate de que todos los archivos estén incluidos

### 🔧 CONFIGURACIÓN EN RAILWAY

#### 1. CONECTAR REPOSITORIO
- En Railway, selecciona "Deploy from GitHub repo"
- Selecciona tu repositorio con el proyecto Conecta Cusco
- Railway detectará automáticamente que es una aplicación PHP gracias a los archivos:
  - `composer.json`
  - `index.php`
  - `nixpacks.toml`

#### 2. CONFIGURAR BASE DE DATOS
- En tu proyecto Railway, ve a "New Service" → "Database" → "PostgreSQL"
- Railway creará automáticamente una base de datos PostgreSQL
- Las variables de entorno se configurarán automáticamente

#### 3. CONFIGURAR VARIABLES DE ENTORNO
En la pestaña "Variables" de tu proyecto, Railway ya habrá configurado:
```
DB_HOST=tu_host_postgresql
DB_NAME=tu_nombre_bd
DB_USER=tu_usuario_bd
DB_PASS=tu_contraseña_bd
DB_PORT=5432
```

#### 4. CONFIGURAR EL ARCHIVO DE CONFIGURACIÓN
- Renombra `config-railway.php` a `config.php`
- El archivo ya está configurado para usar las variables de entorno de Railway

### 📁 ESTRUCTURA DE ARCHIVOS PARA RAILWAY

```
/
├── index.html
├── config.php (renombrado desde config-railway.php)
├── railway.json
├── CreacionDB-Railway.php
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
└── uploads/ (carpeta vacía)
```

### 🚀 PASOS DE DESPLIEGUE

#### 1. DESPLIEGUE AUTOMÁTICO
- Railway detectará automáticamente que es una aplicación PHP
- El archivo `railway.json` configurará el entorno
- El despliegue comenzará automáticamente

#### 2. CREAR LA BASE DE DATOS
- Una vez desplegado, accede a: `https://tu-app.railway.app/CreacionDB-Railway.php`
- Esto creará automáticamente todas las tablas en PostgreSQL
- **IMPORTANTE:** Elimina este archivo después de usarlo por seguridad

#### 3. VERIFICAR DESPLIEGUE
- Railway proporcionará una URL como: `https://tu-app-name.railway.app`
- La aplicación debería estar funcionando correctamente

### 🔒 CONFIGURACIÓN DE SEGURIDAD

1. **Variables de entorno:**
   - Railway maneja automáticamente las credenciales de la base de datos
   - No necesitas configurar archivos de configuración manualmente

2. **SSL/HTTPS:**
   - Railway proporciona HTTPS automáticamente
   - No necesitas configuración adicional

3. **Archivos protegidos:**
   - Railway ejecuta PHP en un entorno seguro
   - Los archivos de configuración están protegidos automáticamente

### 🌐 CONFIGURACIÓN DE DOMINIO

1. **Dominio personalizado (opcional):**
   - En Railway, ve a "Settings" → "Domains"
   - Agrega tu dominio personalizado
   - Railway configurará automáticamente SSL

2. **Subdominio de Railway:**
   - Railway proporciona un subdominio automáticamente
   - Formato: `https://tu-app-name.railway.app`

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
- Base de datos PostgreSQL optimizada
- Variables de entorno para configuración

### 🐛 SOLUCIÓN DE PROBLEMAS

#### Error "Nixpacks build failed":
1. **Verifica que estos archivos estén en la raíz del repositorio:**
   - `composer.json` - Define que es una aplicación PHP
   - `index.php` - Punto de entrada para Railway
   - `railway.json` - Configuración de Railway
   - `Dockerfile` - Configuración Docker alternativa

2. **Si el error persiste:**
   - Asegúrate de que todos los archivos estén subidos a Git
   - Verifica que no haya archivos `.gitignore` excluyendo archivos importantes
   - Revisa que el repositorio tenga la estructura correcta

3. **Archivos críticos que deben estar presentes:**
   ```
   /
   ├── composer.json
   ├── index.php
   ├── railway.json
   ├── Dockerfile
   ├── index.html
   ├── config-railway.php
   └── [resto de archivos]
   ```

4. **Solución alternativa con Docker:**
   - Si Nixpacks sigue fallando, Railway usará automáticamente el Dockerfile
   - El Dockerfile incluye PHP 8.2 con todas las extensiones necesarias
   - Esta es una solución más confiable y estable

#### Error de conexión a base de datos:
1. Verifica que la base de datos PostgreSQL esté creada en Railway
2. Revisa las variables de entorno en Railway
3. Asegúrate de que `config.php` esté usando las variables correctas

#### Páginas no cargan:
1. Verifica los logs en Railway Dashboard
2. Revisa que todos los archivos estén subidos
3. Verifica que `railway.json` esté configurado correctamente

#### Imágenes no se muestran:
1. Verifica que la carpeta `Imagenes/` esté subida
2. Revisa las rutas en el código
3. Verifica permisos de archivos

#### Subida de archivos no funciona:
1. Verifica que la carpeta `uploads/` exista
2. Revisa los límites de PHP en Railway
3. Verifica configuración en `config.php`

### 📞 SOPORTE

Si encuentras problemas:
1. Revisa los logs en Railway Dashboard
2. Verifica la configuración de PHP en Railway
3. Asegúrate de que todas las extensiones PHP están habilitadas:
   - pdo_pgsql (para PostgreSQL)
   - gd (para procesamiento de imágenes)
   - fileinfo

### 🎉 VENTAJAS DE RAILWAY

✅ **Automatización completa:**
- Despliegue automático desde Git
- Configuración automática de base de datos
- SSL automático

✅ **Escalabilidad:**
- Escalado automático según tráfico
- Monitoreo integrado
- Logs en tiempo real

✅ **Desarrollo moderno:**
- Integración con GitHub/GitLab
- Variables de entorno seguras
- Rollbacks automáticos

### 🎉 ¡LISTO PARA USAR!

Una vez completados todos los pasos, tu sitio estará completamente funcional en Railway con:
- ✅ Despliegue automático
- ✅ Base de datos PostgreSQL
- ✅ SSL automático
- ✅ Escalabilidad automática
- ✅ Monitoreo integrado

**URL principal:** `https://tu-app-name.railway.app` 