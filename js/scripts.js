// Validación de formularios
function validarFormulario(formId) {
    const form = document.getElementById(formId);
    if (!form) return;
    
    form.addEventListener('submit', function(e) {
        let valido = true;
        const inputs = form.querySelectorAll('input[required], select[required], textarea[required]');
        
        inputs.forEach(input => {
            if (!input.value.trim()) {
                valido = false;
                input.style.borderColor = 'red';
                // Agregar mensaje de error
                let errorMsg = input.nextElementSibling;
                if (!errorMsg || !errorMsg.classList.contains('error-msg')) {
                    errorMsg = document.createElement('div');
                    errorMsg.className = 'error-msg';
                    errorMsg.style.color = 'red';
                    errorMsg.style.fontSize = '0.8rem';
                    errorMsg.style.marginTop = '0.25rem';
                    errorMsg.textContent = 'Este campo es requerido';
                    input.parentNode.appendChild(errorMsg);
                }
            } else {
                input.style.borderColor = 'green';
                // Remover mensaje de error si existe
                const errorMsg = input.nextElementSibling;
                if (errorMsg && errorMsg.classList.contains('error-msg')) {
                    errorMsg.remove();
                }
            }
        });
        
        if (!valido) {
            e.preventDefault();
            alert('Por favor, completa todos los campos requeridos.');
        }
    });
}

// Formatear fecha para inputs
function formatearFechaParaInput(fecha) {
    const date = new Date(fecha);
    return date.toISOString().split('T')[0];
}

// Calcular total del pedido
function calcularTotalPedido() {
    const select = document.getElementById('menu_id');
    const cantidadInput = document.getElementById('cantidad');
    const totalSpan = document.getElementById('total-pedido');
    
    if (select && cantidadInput && totalSpan) {
        const precio = parseFloat(select.options[select.selectedIndex].dataset.precio || 0);
        const cantidad = parseInt(cantidadInput.value) || 1;
        const total = precio * cantidad;
        
        totalSpan.textContent = total.toFixed(2);
    }
}

// Confirmación para acciones críticas
function confirmarAccion(mensaje) {
    return confirm(mensaje);
}

// Inicializar todas las funcionalidades al cargar la página
document.addEventListener('DOMContentLoaded', function() {
    console.log('Página cargada - Inicializando scripts');
    
    // Inicializar validaciones
    validarFormulario('form-login');
    validarFormulario('form-registro');
    validarFormulario('form-reserva');
    validarFormulario('form-pedido');
    validarFormulario('form-menu');
    
    // Event listeners para cálculo de total
    const menuSelect = document.getElementById('menu_id');
    const cantidadInput = document.getElementById('cantidad');
    
    if (menuSelect) {
        menuSelect.addEventListener('change', calcularTotalPedido);
    }
    
    if (cantidadInput) {
        cantidadInput.addEventListener('input', calcularTotalPedido);
    }

    // Inicializar cálculo si existe
    if (menuSelect && cantidadInput) {
        calcularTotalPedido();
    }
    
    // Mejorar experiencia de usuario en formularios
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('focusin', function(e) {
            if (e.target.tagName === 'INPUT' || e.target.tagName === 'SELECT' || e.target.tagName === 'TEXTAREA') {
                e.target.parentElement.classList.add('focused');
            }
        });
        
        form.addEventListener('focusout', function(e) {
            if (e.target.tagName === 'INPUT' || e.target.tagName === 'SELECT' || e.target.tagName === 'TEXTAREA') {
                e.target.parentElement.classList.remove('focused');
            }
        });
    });

    // Animaciones suaves para navegación
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
});

// Función para mostrar/ocultar secciones (útil para admin)
function toggleSection(sectionId) {
    const section = document.getElementById(sectionId);
    if (section) {
        section.style.display = section.style.display === 'none' ? 'block' : 'none';
    }
}

// Manejo de mensajes flash (éxito/error)
function mostrarMensajeFlash(tipo, mensaje) {
    const container = document.createElement('div');
    container.className = `flash-message ${tipo}`;
    container.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 1rem;
        border-radius: 5px;
        color: white;
        z-index: 1000;
        animation: slideIn 0.3s ease;
    `;

    if (tipo === 'success') {
        container.style.backgroundColor = '#28a745';
    } else {
        container.style.backgroundColor = '#dc3545';
    }
    
    container.textContent = mensaje;
    document.body.appendChild(container);
    
    setTimeout(() => {
        container.style.animation = 'slideOut 0.3s ease';
        setTimeout(() => container.remove(), 300);
    }, 3000);
}

// CSS para animaciones (inyectado dinámicamente)
const style = document.createElement('style');
style.textContent = `
    @keyframes slideIn {
        from { transform: translateX(100%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }
    @keyframes slideOut {
        from { transform: translateX(0); opacity: 1; }
        to { transform: translateX(100%); opacity: 0; }
    }
    .form-group.focused {
        border-left: 4px solid #d2691e;
        padding-left: 0.5rem;
    }
`;
document.head.appendChild(style);
