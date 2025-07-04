// Script principal para Conecta Cusco

document.addEventListener('DOMContentLoaded', function() {
    // Validación de formularios
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const requiredFields = form.querySelectorAll('[required]');
            let isValid = true;
            
            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    isValid = false;
                    field.classList.add('error');
                } else {
                    field.classList.remove('error');
                }
            });
            
            if (!isValid) {
                e.preventDefault();
                alert('Por favor, completa todos los campos obligatorios.');
            }
        });
    });

    // Validación de contraseñas
    const passwordFields = document.querySelectorAll('input[type="password"]');
    passwordFields.forEach(field => {
        field.addEventListener('input', function() {
            const confirmField = document.querySelector('input[name="confirm-password"]');
            if (confirmField && field.value !== confirmField.value) {
                confirmField.setCustomValidity('Las contraseñas no coinciden');
            } else {
                confirmField.setCustomValidity('');
            }
        });
    });

    // Navegación responsive
    const navToggle = document.querySelector('.nav-toggle');
    const navMenu = document.querySelector('nav ul');
    
    if (navToggle) {
        navToggle.addEventListener('click', function() {
            navMenu.classList.toggle('active');
        });
    }

    // Galería de imágenes
    const galleryImages = document.querySelectorAll('.gallery-grid img');
    galleryImages.forEach(img => {
        img.addEventListener('click', function() {
            // Crear modal para ver imagen en tamaño completo
            const modal = document.createElement('div');
            modal.className = 'image-modal';
            modal.innerHTML = `
                <div class="modal-content">
                    <span class="close">&times;</span>
                    <img src="${this.src}" alt="${this.alt}">
                </div>
            `;
            document.body.appendChild(modal);
            
            // Cerrar modal
            modal.querySelector('.close').addEventListener('click', function() {
                document.body.removeChild(modal);
            });
            
            modal.addEventListener('click', function(e) {
                if (e.target === modal) {
                    document.body.removeChild(modal);
                }
            });
        });
    });

    // Filtros de búsqueda
    const searchForm = document.querySelector('form[action*="buscar-servicios"]');
    if (searchForm) {
        searchForm.addEventListener('submit', function(e) {
            const searchInput = this.querySelector('input[name="q"]');
            const locationInput = this.querySelector('input[name="location"]');
            
            if (!searchInput.value.trim() && !locationInput.value.trim()) {
                e.preventDefault();
                alert('Por favor, ingresa al menos un criterio de búsqueda.');
            }
        });
    }

    // Búsqueda dinámica
    const btnFiltrar = document.querySelector('.btn-filtrar');
    if (btnFiltrar) {
        btnFiltrar.addEventListener('click', function() {
            realizarBusqueda();
        });
    }

    // Función para realizar búsqueda dinámica
    function realizarBusqueda() {
        const categoria = document.getElementById('categoria')?.value || '';
        const calificacion = document.getElementById('calificacion')?.value || '';
        const disponibilidad = document.getElementById('disponibilidad')?.value || '';
        
        // Obtener parámetros de la URL actual
        const urlParams = new URLSearchParams(window.location.search);
        const q = urlParams.get('q') || '';
        const location = urlParams.get('location') || '';

        // Construir URL de búsqueda
        const searchParams = new URLSearchParams();
        if (q) searchParams.append('q', q);
        if (location) searchParams.append('location', location);
        if (categoria) searchParams.append('categoria', categoria);
        if (calificacion) searchParams.append('calificacion', calificacion);
        if (disponibilidad) searchParams.append('disponibilidad', disponibilidad);

        // Realizar búsqueda AJAX
        fetch(`../php/buscar-dinamico.php?${searchParams.toString()}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    mostrarResultados(data.resultados);
                } else {
                    mostrarMensaje('Error al realizar la búsqueda', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                mostrarMensaje('Error de conexión', 'error');
            });
    }

    // Función para mostrar resultados de búsqueda
    function mostrarResultados(resultados) {
        const listingsGrid = document.querySelector('.listings-grid');
        if (!listingsGrid) return;

        if (resultados.length === 0) {
            listingsGrid.innerHTML = `
                <div class="no-results">
                    <h3>No se encontraron resultados</h3>
                    <p>Intenta con otros criterios de búsqueda.</p>
                </div>
            `;
            return;
        }

        listingsGrid.innerHTML = resultados.map(servicio => `
            <article class="service-card">
                <img src="${servicio.foto_perfil || '../../../Imagenes/perfil.webp'}" alt="Foto de ${servicio.nombre_completo}">
                <h3>${servicio.nombre_completo}</h3>
                <p class="service-type">${servicio.titulo}</p>
                <p class="location">Área: ${servicio.cobertura}</p>
                <p class="description">${servicio.descripcion.substring(0, 100)}...</p>
                ${servicio.precio ? `<p class="precio">S/ ${servicio.precio}</p>` : ''}
                ${servicio.calificacion_promedio > 0 ? `
                    <div class="rating">
                        <span class="stars">${'★'.repeat(Math.round(servicio.calificacion_promedio))}${'☆'.repeat(5-Math.round(servicio.calificacion_promedio))}</span>
                        <span class="rating-text">${servicio.calificacion_promedio.toFixed(1)} (${servicio.total_calificaciones} reseñas)</span>
                    </div>
                ` : ''}
                <div class="disponibilidad ${servicio.disponibilidad}">
                    ${servicio.disponibilidad === 'disponible' ? '✅ Disponible' : '❌ No disponible'}
                </div>
                <a href="perfil.html?id=${servicio.id}" class="btn-secondary">Ver Perfil</a>
            </article>
        `).join('');
    }

    // Función para mostrar mensajes
    function mostrarMensaje(mensaje, tipo) {
        const messageDiv = document.createElement('div');
        messageDiv.className = `message ${tipo}`;
        messageDiv.textContent = mensaje;
        document.body.appendChild(messageDiv);
        
        setTimeout(() => {
            if (document.body.contains(messageDiv)) {
                document.body.removeChild(messageDiv);
            }
        }, 3000);
    }

    // Sistema de reseñas
    const ratingForms = document.querySelectorAll('.rating-form');
    ratingForms.forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const servicioId = formData.get('servicio_id');
            const calificacion = formData.get('calificacion');
            const comentario = formData.get('comentario');

            // Validaciones
            if (!calificacion) {
                mostrarMensaje('Por favor, selecciona una calificación', 'error');
                return;
            }

            if (!comentario.trim()) {
                mostrarMensaje('Por favor, escribe un comentario', 'error');
                return;
            }

            // Enviar reseña al servidor
            fetch('../php/procesar-resena.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    mostrarMensaje(data.message, 'success');
                    this.reset();
                    // Recargar la página para mostrar la nueva reseña
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                } else {
                    mostrarMensaje(data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                mostrarMensaje('Error de conexión', 'error');
            });
        });
    });

    // Formulario de contacto
    const contactForms = document.querySelectorAll('.contact-form');
    contactForms.forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const mensaje = formData.get('mensaje');
            const servicioId = formData.get('servicio_id');

            if (!mensaje.trim()) {
                mostrarMensaje('Por favor, escribe un mensaje', 'error');
                return;
            }

            if (mensaje.trim().length < 10) {
                mostrarMensaje('El mensaje debe tener al menos 10 caracteres', 'error');
                return;
            }

            // Enviar mensaje al servidor
            fetch('../php/procesar-contacto.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    mostrarMensaje(data.message, 'success');
                    this.reset();
                } else {
                    mostrarMensaje(data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                mostrarMensaje('Error de conexión', 'error');
            });
        });
    });

    // Mostrar servicios reales al cargar la página de búsqueda
    const listingsGrid = document.querySelector('.listings-grid');
    if (listingsGrid) {
        realizarBusqueda();
    }
});

// Funciones de utilidad
function showMessage(message, type = 'info') {
    const messageDiv = document.createElement('div');
    messageDiv.className = `message ${type}`;
    messageDiv.textContent = message;
    document.body.appendChild(messageDiv);
    
    setTimeout(() => {
        if (document.body.contains(messageDiv)) {
            document.body.removeChild(messageDiv);
        }
    }, 3000);
}

function validateEmail(email) {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
}

function validatePhone(phone) {
    const re = /^[\+]?[0-9\s\-\(\)]{7,15}$/;
    return re.test(phone);
}

// Función para formatear precios
function formatPrice(price) {
    return new Intl.NumberFormat('es-PE', {
        style: 'currency',
        currency: 'PEN'
    }).format(price);
}

// Función para formatear fechas
function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('es-PE', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
} 