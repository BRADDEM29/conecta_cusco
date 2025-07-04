# 🚀 Conecta Cusco - Despliegue en Railway

## 📋 Descripción
Plataforma web para conectar profesionales de servicios con clientes en Cusco, Perú. Desarrollada en PHP con base de datos PostgreSQL.

## ⚡ Despliegue Rápido en Railway

### 1. Preparación
- Sube tu código a GitHub/GitLab
- Asegúrate de que todos los archivos estén incluidos

### 2. En Railway
1. Ve a [Railway](https://railway.com/new)
2. Selecciona "Deploy from GitHub repo"
3. Selecciona tu repositorio
4. Railway detectará automáticamente que es PHP

### 3. Base de Datos
1. En tu proyecto Railway, ve a "New Service" → "Database" → "PostgreSQL"
2. Railway configurará automáticamente las variables de entorno

### 4. Configuración
1. Renombra `config-railway.php` a `config.php`
2. Las variables de entorno se configuran automáticamente

### 5. Crear Base de Datos
1. Accede a: `https://tu-app.railway.app/CreacionDB-Railway.php`
2. Elimina el archivo después de usarlo

### 6. Verificar
1. Accede a: `https://tu-app.railway.app/verificar-railway.php`
2. Confirma que todo funciona correctamente

## 🏗️ Estructura del Proyecto

```
/
├── index.html                 # Página principal
├── config-railway.php         # Configuración para Railway
├── railway.json              # Configuración de Railway
├── Procfile                  # Configuración alternativa
├── CreacionDB-Railway.php    # Script de creación de BD
├── verificar-railway.php     # Verificación de despliegue
├── Estilos/                  # Archivos CSS
├── Imagenes/                 # Imágenes del sitio
├── js/                       # JavaScript
├── Paginas/                  # Páginas PHP y HTML
└── uploads/                  # Archivos subidos
```

## 🔧 Tecnologías

- **Backend:** PHP 8.x
- **Base de Datos:** PostgreSQL
- **Frontend:** HTML5, CSS3, JavaScript
- **Hosting:** Railway
- **Características:** Responsive, Seguro, Escalable

## 🌟 Características

✅ **Sistema de Usuarios**
- Registro y login
- Perfiles de profesionales y clientes
- Gestión de sesiones

✅ **Gestión de Servicios**
- Publicación de servicios
- Búsqueda dinámica
- Categorización

✅ **Sistema de Contacto**
- Mensajes entre usuarios
- Sistema de reseñas
- Calificaciones

✅ **Diseño Responsive**
- Compatible con móviles
- Tablet y desktop
- Interfaz moderna

## 🔒 Seguridad

- Variables de entorno para credenciales
- Validación de formularios
- Sanitización de datos
- Protección contra XSS
- Subida segura de archivos

## 📱 Funcionalidades Principales

1. **Página de Inicio** - Presentación de la plataforma
2. **Registro/Login** - Sistema de autenticación
3. **Buscar Servicios** - Búsqueda dinámica
4. **Publicar Servicios** - Para profesionales
5. **Perfiles** - Gestión de información personal
6. **Mensajes** - Comunicación entre usuarios
7. **Reseñas** - Sistema de calificaciones

## 🚀 Ventajas de Railway

- ✅ Despliegue automático desde Git
- ✅ Base de datos PostgreSQL incluida
- ✅ SSL automático
- ✅ Escalabilidad automática
- ✅ Monitoreo integrado
- ✅ Variables de entorno seguras
- ✅ Rollbacks automáticos

## 📞 Soporte

Si encuentras problemas:
1. Revisa los logs en Railway Dashboard
2. Verifica las variables de entorno
3. Confirma que la base de datos esté creada
4. Revisa el archivo de verificación

## 🎉 ¡Listo!

Tu aplicación estará disponible en: `https://tu-app-name.railway.app`

---

**Desarrollado para Railway** 🚂 