/**
 * Sistema de Spinners para Formularios
 * Proporciona funcionalidad para mostrar spinners de carga y prevenir múltiples envíos
 */

class FormSpinner {
    constructor(formId, options = {}) {
        this.form = document.getElementById(formId);
        this.submitBtn = this.form.querySelector('button[type="submit"]');
        this.submitText = this.submitBtn.querySelector('span');
        this.submitSpinner = this.submitBtn.querySelector('.spinner');
        
        this.options = {
            loadingText: 'Procesando...',
            ...options
        };
        
        this.init();
    }
    
    init() {
        this.form.addEventListener('submit', (e) => this.handleSubmit(e));
    }
    
    handleSubmit(e) {
        // Deshabilitar el botón y mostrar spinner
        this.submitBtn.disabled = true;
        this.submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
        this.submitText.textContent = this.options.loadingText;
        this.submitSpinner.classList.remove('hidden');
        
        // El formulario se enviará normalmente
    }
    
    reset() {
        this.submitBtn.disabled = false;
        this.submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
        this.submitText.textContent = this.options.originalText || 'Enviar';
        this.submitSpinner.classList.add('hidden');
    }
}

class ModalSpinner {
    constructor(modalId, options = {}) {
        this.modal = document.getElementById(modalId);
        this.confirmBtn = this.modal.querySelector('#confirmDelete');
        this.confirmText = this.modal.querySelector('#confirmText');
        this.confirmSpinner = this.modal.querySelector('#confirmSpinner');
        this.cancelBtn = this.modal.querySelector('#cancelDelete');
        
        this.options = {
            loadingText: 'Procesando...',
            ...options
        };
        
        this.init();
    }
    
    init() {
        this.confirmBtn.addEventListener('click', (e) => this.handleConfirm(e));
    }
    
    handleConfirm(e) {
        // Deshabilitar botones y mostrar spinner
        this.confirmBtn.disabled = true;
        this.confirmBtn.classList.add('opacity-50', 'cursor-not-allowed');
        this.cancelBtn.disabled = true;
        this.cancelBtn.classList.add('opacity-50', 'cursor-not-allowed');
        this.confirmText.textContent = this.options.loadingText;
        this.confirmSpinner.classList.remove('hidden');
        
        // Continuar con la acción original
    }
    
    reset() {
        this.confirmBtn.disabled = false;
        this.confirmBtn.classList.remove('opacity-50', 'cursor-not-allowed');
        this.cancelBtn.disabled = false;
        this.cancelBtn.classList.remove('opacity-50', 'cursor-not-allowed');
        this.confirmText.textContent = this.options.originalText || 'Confirmar';
        this.confirmSpinner.classList.add('hidden');
    }
}

// Función helper para crear spinners rápidamente
function createFormSpinner(formId, loadingText = 'Procesando...') {
    return new FormSpinner(formId, { loadingText });
}

function createModalSpinner(modalId, loadingText = 'Procesando...') {
    return new ModalSpinner(modalId, { loadingText });
}

// Exportar para uso global
window.FormSpinner = FormSpinner;
window.ModalSpinner = ModalSpinner;
window.createFormSpinner = createFormSpinner;
window.createModalSpinner = createModalSpinner; 