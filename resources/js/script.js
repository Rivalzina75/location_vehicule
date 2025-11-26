/**
 * MACHINA - Scripts JavaScript
 * Version 2.0 - Nettoyé et Optimisé
 */

import './bootstrap';

// ===================================
// MAIN INITIALIZATION
// ===================================

document.addEventListener('DOMContentLoaded', function() {
    // Core modules
    initLoginLockout();
    initRegisterValidation();
    initFormValidation();
    
    // UI Enhancements
    initPasswordToggle();
    initAutoFormatting();
    initLangSwitcher();
    initAlerts();
    initRippleEffect();
    
    // Dashboard specific
    initDashboard();
    initSidebarToggle();
    
    console.log('%c🚗 MACHINA - Scripts Loaded', 'color: #e94560; font-weight: bold; font-size: 14px;');
});

// ===================================
// SECURITY & AUTHENTICATION
// ===================================

/**
 * Login lockout - clears local storage after 5 minutes of inactivity
 */
function initLoginLockout() {
    const loginForm = document.getElementById('loginForm');
    if (!loginForm) return;
    
    const lastErrorTime = parseInt(localStorage.getItem('lastErrorTime') || '0');
    const now = Date.now();
    const FIVE_MINUTES = 300000;
    
    if (lastErrorTime > 0 && (now - lastErrorTime > FIVE_MINUTES)) {
        localStorage.removeItem('loginAttempts');
        localStorage.removeItem('lockoutEnd');
        localStorage.removeItem('lastErrorTime');
    }
}

/**
 * Registration form validation with password strength checking
 */
function initRegisterValidation() {
    const registerForm = document.getElementById('registerForm');
    if (!registerForm) return;

    const passwordInput = registerForm.querySelector('input[name="password"]');
    const passwordConfirm = registerForm.querySelector('input[name="password_confirmation"]');
    
    if (!passwordInput) return;

    // Create password strength indicator
    createPasswordStrengthUI(passwordInput);

    // Live validation on input
    passwordInput.addEventListener('input', function() {
        updatePasswordStrength(this);
        // Clear confirmation error when typing in password field
        if (passwordConfirm) {
            clearFieldError(passwordConfirm);
            passwordConfirm.classList.remove('is-invalid');
        }
    });
    
    // Also trigger on focus to show requirements
    passwordInput.addEventListener('focus', function() {
        if (this.value.length > 0) {
            updatePasswordStrength(this);
        }
    });
    
    // For confirmation field - only clear error while typing, don't show new errors
    if (passwordConfirm) {
        passwordConfirm.addEventListener('input', function() {
            // Just clear the error while user is typing
            clearFieldError(this);
            this.classList.remove('is-invalid');
        });
    }

    // Validate everything on form submit
    registerForm.addEventListener('submit', function(e) {
        const result = checkPasswordRequirements(passwordInput.value);
        const passwordsMatch = !passwordConfirm || passwordInput.value === passwordConfirm.value;
        
        let hasError = false;
        
        if (!result.valid) {
            e.preventDefault();
            hasError = true;
            updatePasswordStrength(passwordInput);
            passwordInput.classList.add('is-invalid');
        }
        
        if (!passwordsMatch) {
            e.preventDefault();
            hasError = true;
            passwordConfirm.classList.add('is-invalid');
            showFieldError(passwordConfirm, 'Les mots de passe ne correspondent pas.');
        }
        
        if (hasError) {
            // Scroll to first error
            const firstError = registerForm.querySelector('.is-invalid');
            if (firstError) {
                firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                firstError.focus();
            }
        }
    });
}

/**
 * Creates the password strength UI elements
 */
function createPasswordStrengthUI(input) {
    const wrapper = input.closest('.password-wrapper') || input.parentElement;
    
    // Check if already exists
    if (wrapper.parentElement.querySelector('.password-strength')) return;
    
    // Create strength bar container
    const strengthContainer = document.createElement('div');
    strengthContainer.className = 'password-strength';
    strengthContainer.innerHTML = `
        <div class="strength-bar">
            <div class="strength-fill"></div>
        </div>
        <span class="strength-text"></span>
    `;
    
    // Create requirements list
    const requirementsDiv = document.createElement('div');
    requirementsDiv.className = 'password-requirements';
    requirementsDiv.style.display = 'none';
    requirementsDiv.innerHTML = `
        <p>⚠️ Il vous manque :</p>
        <ul>
            <li data-req="length">14 caractères minimum</li>
            <li data-req="uppercase">Une lettre majuscule</li>
            <li data-req="lowercase">Une lettre minuscule</li>
            <li data-req="number">Un chiffre</li>
            <li data-req="special">Un caractère spécial (@$!%*#?&)</li>
        </ul>
    `;
    
    // Insert after the password wrapper
    wrapper.parentElement.appendChild(strengthContainer);
    wrapper.parentElement.appendChild(requirementsDiv);
}

/**
 * Check password requirements and return result
 */
function checkPasswordRequirements(value) {
    const requirements = {
        length: value.length >= 14,
        uppercase: /[A-Z]/.test(value),
        lowercase: /[a-z]/.test(value),
        number: /[0-9]/.test(value),
        special: /[@$!%*#?&\W_]/.test(value)
    };
    
    const passed = Object.values(requirements).filter(Boolean).length;
    const total = Object.keys(requirements).length;
    
    return {
        requirements,
        passed,
        total,
        percentage: (passed / total) * 100,
        valid: passed === total
    };
}

/**
 * Updates the password strength UI
 */
function updatePasswordStrength(input) {
    const value = input.value;
    const wrapper = input.closest('.password-wrapper') || input.parentElement;
    const container = wrapper.parentElement;
    
    const strengthContainer = container.querySelector('.password-strength');
    const requirementsDiv = container.querySelector('.password-requirements');
    
    if (!strengthContainer || !requirementsDiv) return;
    
    const fill = strengthContainer.querySelector('.strength-fill');
    const text = strengthContainer.querySelector('.strength-text');
    
    if (value.length === 0) {
        fill.style.width = '0%';
        text.textContent = '';
        requirementsDiv.style.display = 'none';
        input.classList.remove('is-invalid');
        return;
    }
    
    const result = checkPasswordRequirements(value);
    
    // Update strength bar
    let color, label;
    if (result.percentage <= 40) {
        color = '#ff4757';
        label = 'Faible';
    } else if (result.percentage <= 60) {
        color = '#ffa502';
        label = 'Moyen';
    } else if (result.percentage <= 80) {
        color = '#2ed573';
        label = 'Fort';
    } else {
        color = '#00d9a5';
        label = 'Excellent';
    }
    
    fill.style.width = result.percentage + '%';
    fill.style.background = color;
    text.textContent = label;
    text.style.color = color;
    
    // Update requirements list
    const items = requirementsDiv.querySelectorAll('li');
    items.forEach(item => {
        const req = item.dataset.req;
        if (result.requirements[req]) {
            item.classList.add('valid');
        } else {
            item.classList.remove('valid');
        }
    });
    
    // Show/hide requirements based on validity
    if (result.valid) {
        requirementsDiv.style.display = 'none';
        input.classList.remove('is-invalid');
    } else {
        requirementsDiv.style.display = 'block';
    }
}

// ===================================
// FORM UTILITIES
// ===================================

/**
 * Initialize form validation on blur
 */
function initFormValidation() {
    const forms = document.querySelectorAll('form');
    
    forms.forEach(form => {
        const inputs = form.querySelectorAll('input[required], select[required]');
        
        inputs.forEach(input => {
            input.addEventListener('blur', function() {
                if (this.value.trim() === '') {
                    this.classList.add('is-invalid');
                } else {
                    this.classList.remove('is-invalid');
                }
            });
            
            input.addEventListener('input', function() {
                if (this.classList.contains('is-invalid') && this.value.trim() !== '') {
                    this.classList.remove('is-invalid');
                }
            });
        });
    });
}

/**
 * Shows an error message for a field
 */
function showFieldError(field, message) {
    clearFieldError(field);
    
    const errorDiv = document.createElement('div');
    errorDiv.className = 'error-message dynamic-error';
    errorDiv.innerHTML = `<strong>${message}</strong>`;
    field.classList.add('is-invalid');
    field.parentElement.appendChild(errorDiv);
}

/**
 * Clears error message for a field
 */
function clearFieldError(field) {
    const existingError = field.parentElement.querySelector('.dynamic-error');
    if (existingError) existingError.remove();
}

// ===================================
// UI ENHANCEMENTS
// ===================================

/**
 * Initialize password visibility toggle
 */
function initPasswordToggle() {
    const passwordFields = document.querySelectorAll('input[type="password"]');
    
    passwordFields.forEach(field => {
        // Skip if already has a toggle
        if (field.dataset.toggleInitialized) return;
        field.dataset.toggleInitialized = 'true';
        
        // Get or create wrapper
        let wrapper = field.parentElement;
        
        // Only wrap if not already in a password-wrapper
        if (!wrapper.classList.contains('password-wrapper')) {
            wrapper = document.createElement('div');
            wrapper.className = 'password-wrapper';
            field.parentNode.insertBefore(wrapper, field);
            wrapper.appendChild(field);
        }
        
        // Create toggle button with SVG icons
        const toggleBtn = document.createElement('button');
        toggleBtn.type = 'button';
        toggleBtn.className = 'password-toggle-btn';
        toggleBtn.setAttribute('aria-label', 'Afficher/masquer le mot de passe');
        toggleBtn.setAttribute('tabindex', '-1');
        
        // SVG for eye open (show password)
        const eyeOpenSVG = `<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>`;
        
        // SVG for eye closed (hide password)
        const eyeClosedSVG = `<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line></svg>`;
        
        toggleBtn.innerHTML = eyeOpenSVG;
        
        toggleBtn.addEventListener('click', function(e) {
            e.preventDefault();
            if (field.type === 'password') {
                field.type = 'text';
                this.innerHTML = eyeClosedSVG;
            } else {
                field.type = 'password';
                this.innerHTML = eyeOpenSVG;
            }
            field.focus();
        });
        
        wrapper.appendChild(toggleBtn);
    });
}

/**
 * Initialize auto-formatting for phone and postal code
 */
function initAutoFormatting() {
    // Phone number formatting (French format)
    const phoneInput = document.querySelector('#phone_number, #telephone, #profile_phone');
    if (phoneInput) {
        phoneInput.addEventListener('input', function(e) {
            let value = e.target.value.replace(/[^\d]/g, '');
            if (value.length > 0) {
                let formatted = value.match(/.{1,2}/g)?.join(' ') || value;
                e.target.value = formatted.substring(0, 14); // Max: XX XX XX XX XX
            }
        });
    }
    
    // Postal code formatting (French format - 5 digits)
    const postalInput = document.querySelector('#postal_code, #profile_postal');
    if (postalInput) {
        postalInput.addEventListener('input', function(e) {
            e.target.value = e.target.value.replace(/[^\d]/g, '').substring(0, 5);
        });
    }
    
    // Date of birth formatting (DD/MM/YYYY)
    const dateInput = document.querySelector('#date_of_birth, #profile_dob');
    if (dateInput) {
        dateInput.addEventListener('input', function(e) {
            let value = e.target.value.replace(/[^\d]/g, '');
            if (value.length > 2 && value.length <= 4) {
                value = value.substring(0, 2) + '/' + value.substring(2);
            } else if (value.length > 4) {
                value = value.substring(0, 2) + '/' + value.substring(2, 4) + '/' + value.substring(4, 8);
            }
            e.target.value = value;
        });
    }
}

/**
 * Initialize language switcher dropdown
 */
function initLangSwitcher() {
    const toggleBtn = document.getElementById('lang-toggle-btn');
    const dropdownMenu = document.getElementById('lang-dropdown-menu');
    
    if (!toggleBtn || !dropdownMenu) return;

    toggleBtn.addEventListener('click', function(e) {
        e.stopPropagation();
        const isHidden = dropdownMenu.style.display === 'none' || dropdownMenu.style.display === '';
        dropdownMenu.style.display = isHidden ? 'block' : 'none';
    });
    
    document.addEventListener('click', () => {
        dropdownMenu.style.display = 'none';
    });
    
    dropdownMenu.addEventListener('click', (e) => e.stopPropagation());
}

/**
 * Initialize auto-dismissing alerts
 */
function initAlerts() {
    document.querySelectorAll('.alert').forEach(alert => {
        // Add close button
        const closeBtn = document.createElement('button');
        closeBtn.innerHTML = '×';
        closeBtn.className = 'alert-close';
        closeBtn.style.cssText = `
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            background: transparent;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            opacity: 0.7;
            line-height: 1;
            padding: 0;
        `;
        closeBtn.onclick = () => dismissAlert(alert);
        alert.style.position = 'relative';
        alert.appendChild(closeBtn);
        
        // Auto dismiss after 5 seconds
        setTimeout(() => dismissAlert(alert), 5000);
    });
}

/**
 * Dismiss an alert with animation
 */
function dismissAlert(alert) {
    if (!alert || alert.classList.contains('dismissing')) return;
    
    alert.classList.add('dismissing');
    alert.style.transition = 'opacity 0.3s, transform 0.3s';
    alert.style.opacity = '0';
    alert.style.transform = 'translateY(-10px)';
    
    setTimeout(() => alert.remove(), 300);
}

/**
 * Initialize ripple effect on buttons
 */
function initRippleEffect() {
    // Add ripple animation styles
    if (!document.getElementById('ripple-styles')) {
        const style = document.createElement('style');
        style.id = 'ripple-styles';
        style.textContent = `
            @keyframes ripple {
                0% { transform: scale(0); opacity: 0.6; }
                100% { transform: scale(4); opacity: 0; }
            }
            .ripple {
                position: absolute;
                border-radius: 50%;
                background: rgba(255, 255, 255, 0.5);
                animation: ripple 0.6s linear;
                pointer-events: none;
            }
        `;
        document.head.appendChild(style);
    }
    
    document.addEventListener('click', function(e) {
        const button = e.target.closest('.btn-primary, .btn-welcome, .btn-reserve, .btn-action');
        if (!button) return;
        
        const ripple = document.createElement('span');
        ripple.className = 'ripple';
        
        const rect = button.getBoundingClientRect();
        const size = Math.max(rect.width, rect.height);
        
        ripple.style.width = ripple.style.height = size + 'px';
        ripple.style.left = (e.clientX - rect.left - size / 2) + 'px';
        ripple.style.top = (e.clientY - rect.top - size / 2) + 'px';
        
        button.style.position = 'relative';
        button.style.overflow = 'hidden';
        button.appendChild(ripple);
        
        setTimeout(() => ripple.remove(), 600);
    });
}

// ===================================
// DASHBOARD FUNCTIONALITY
// ===================================

/**
 * Initialize dashboard navigation and forms
 */
function initDashboard() {
    // Check if we're on dashboard
    const dashboardWrapper = document.querySelector('.dashboard-wrapper');
    if (!dashboardWrapper) return;
    
    // Navigation
    initDashboardNavigation();
    
    // Vehicle filter
    initVehicleFilter();
    
    // Reservation form steps
    initReservationForm();
    
    // Photo upload preview
    initPhotoUpload();
    
    // Inspection type toggle
    initInspectionToggle();
}

/**
 * Initialize dashboard section navigation
 */
function initDashboardNavigation() {
    const sidebarLinks = document.querySelectorAll('.sidebar-link[data-section]');
    
    sidebarLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const section = this.getAttribute('data-section');
            navigateTo(section);
            
            // Close sidebar on mobile
            const sidebar = document.querySelector('.dashboard-sidebar');
            if (window.innerWidth <= 768 && sidebar.classList.contains('open')) {
                sidebar.classList.remove('open');
            }
        });
    });
}

/**
 * Navigate to a dashboard section
 */
window.navigateTo = function(section) {
    // Hide all sections
    document.querySelectorAll('.dashboard-section').forEach(s => {
        s.classList.remove('active');
    });
    
    // Remove active from all links
    document.querySelectorAll('.sidebar-link').forEach(link => {
        link.classList.remove('active');
    });
    
    // Show target section
    const targetSection = document.getElementById(section);
    if (targetSection) {
        targetSection.classList.add('active');
    }
    
    // Activate corresponding link
    const targetLink = document.querySelector(`[data-section="${section}"]`);
    if (targetLink) {
        targetLink.classList.add('active');
    }
    
    // Scroll to top
    const main = document.querySelector('.dashboard-main');
    if (main) main.scrollTop = 0;
};

/**
 * Select vehicle from catalog and go to reservation
 */
window.selectVehicle = function(model, type) {
    const vehicleType = document.getElementById('vehicle_type');
    const vehicleModel = document.getElementById('vehicle_model');
    
    if (vehicleType) vehicleType.value = type;
    if (vehicleModel) vehicleModel.value = model;
    
    navigateTo('reservation');
};

/**
 * Initialize vehicle catalog filter
 */
function initVehicleFilter() {
    const typeFilter = document.getElementById('type-filter');
    
    if (typeFilter) {
        typeFilter.addEventListener('change', function() {
            const selectedType = this.value;
            
            document.querySelectorAll('.vehicle-card').forEach(card => {
                if (!selectedType || card.getAttribute('data-type') === selectedType) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    }
}

/**
 * Initialize reservation form multi-step navigation
 */
function initReservationForm() {
    const form = document.getElementById('reservationForm');
    if (!form) return;
    
    let currentStep = 1;
    
    // Next step function
    window.nextStep = function(step) {
        const currentStepEl = document.querySelector(`.form-step[data-step="${currentStep}"]`);
        const inputs = currentStepEl.querySelectorAll('input[required], select[required]');
        let valid = true;
        
        inputs.forEach(input => {
            if (!input.value && input.type !== 'checkbox') {
                input.classList.add('is-invalid');
                valid = false;
            } else if (input.type === 'checkbox' && input.required && !input.checked) {
                valid = false;
            } else {
                input.classList.remove('is-invalid');
            }
        });
        
        if (!valid) {
            showNotification('Veuillez remplir tous les champs requis', 'warning');
            return;
        }
        
        currentStepEl.classList.remove('active');
        document.querySelector(`.form-step[data-step="${step}"]`).classList.add('active');
        currentStep = step;
        
        document.querySelector('.dashboard-main').scrollTop = 0;
        
        if (step === 5) updateReservationSummary();
    };
    
    // Previous step function
    window.prevStep = function(step) {
        document.querySelector(`.form-step[data-step="${currentStep}"]`).classList.remove('active');
        document.querySelector(`.form-step[data-step="${step}"]`).classList.add('active');
        currentStep = step;
        document.querySelector('.dashboard-main').scrollTop = 0;
    };
    
    // Form submission
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        showNotification('Réservation envoyée avec succès !', 'success');
        // Here you would typically send data to server via AJAX
    });
}

/**
 * Update reservation summary before final submission
 */
function updateReservationSummary() {
    const vehicle = document.getElementById('vehicle_model')?.value || '-';
    const contractSelect = document.getElementById('contract_type');
    const contractType = contractSelect?.options[contractSelect.selectedIndex]?.text || '-';
    const startDate = document.getElementById('start_date')?.value || '-';
    const endDate = document.getElementById('end_date')?.value || '-';
    const tarifSelect = document.getElementById('tarif_type');
    const tarifType = tarifSelect?.options[tarifSelect.selectedIndex]?.text || '-';
    const childSeat = document.getElementById('child_seat')?.checked;
    const insurance = document.getElementById('insurance')?.checked;
    
    const setElementText = (id, text) => {
        const el = document.getElementById(id);
        if (el) el.textContent = text;
    };
    
    setElementText('summary-vehicle', vehicle);
    setElementText('summary-contract', contractType);
    setElementText('summary-period', `${startDate} → ${endDate}`);
    setElementText('summary-tarif', tarifType);
    
    const options = [];
    if (childSeat) options.push('Siège enfant');
    if (insurance) options.push('Assurance tous risques');
    setElementText('summary-options', options.length > 0 ? options.join(', ') : 'Aucune');
    setElementText('summary-total', '350€ (estimation)');
}

/**
 * Initialize photo upload with preview
 */
function initPhotoUpload() {
    document.querySelectorAll('.photo-input').forEach(input => {
        input.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (!file) return;
            
            const reader = new FileReader();
            reader.onload = function(e) {
                const label = input.closest('.photo-upload-item').querySelector('.photo-placeholder');
                if (label) {
                    label.innerHTML = `<img src="${e.target.result}" style="width: 100%; height: 100%; object-fit: cover; border-radius: 8px;">`;
                }
            };
            reader.readAsDataURL(file);
        });
    });
}

/**
 * Initialize inspection type toggle
 */
function initInspectionToggle() {
    window.selectInspectionType = function(type) {
        document.querySelectorAll('.inspection-type-btn').forEach(btn => {
            btn.classList.remove('active');
        });
        event.target.classList.add('active');
    };
}

/**
 * Initialize mobile sidebar toggle
 */
function initSidebarToggle() {
    const sidebar = document.querySelector('.dashboard-sidebar');
    if (!sidebar) return;
    
    // Create toggle button if doesn't exist
    if (!document.querySelector('.sidebar-toggle')) {
        const toggleBtn = document.createElement('button');
        toggleBtn.className = 'sidebar-toggle';
        toggleBtn.innerHTML = '☰';
        toggleBtn.setAttribute('aria-label', 'Toggle sidebar');
        
        toggleBtn.addEventListener('click', function() {
            sidebar.classList.toggle('open');
            this.innerHTML = sidebar.classList.contains('open') ? '✕' : '☰';
        });
        
        document.body.appendChild(toggleBtn);
    }
    
    // Close sidebar when clicking outside on mobile
    document.addEventListener('click', function(e) {
        if (window.innerWidth <= 768 && 
            sidebar.classList.contains('open') && 
            !sidebar.contains(e.target) && 
            !e.target.classList.contains('sidebar-toggle')) {
            sidebar.classList.remove('open');
            document.querySelector('.sidebar-toggle').innerHTML = '☰';
        }
    });
}

// ===================================
// UTILITY FUNCTIONS
// ===================================

/**
 * Show a notification toast
 */
function showNotification(message, type = 'info') {
    // Remove existing notifications
    document.querySelectorAll('.notification-toast').forEach(n => n.remove());
    
    const toast = document.createElement('div');
    toast.className = `notification-toast notification-${type}`;
    toast.textContent = message;
    
    const colors = {
        success: '#00d9a5',
        warning: '#ffc107',
        danger: '#ff4757',
        info: '#3498db'
    };
    
    toast.style.cssText = `
        position: fixed;
        bottom: 100px;
        right: 20px;
        background: ${colors[type] || colors.info};
        color: white;
        padding: 12px 24px;
        border-radius: 8px;
        font-weight: 500;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        z-index: 10000;
        animation: slideInRight 0.3s ease;
    `;
    
    // Add animation
    if (!document.getElementById('notification-styles')) {
        const style = document.createElement('style');
        style.id = 'notification-styles';
        style.textContent = `
            @keyframes slideInRight {
                from { transform: translateX(100%); opacity: 0; }
                to { transform: translateX(0); opacity: 1; }
            }
        `;
        document.head.appendChild(style);
    }
    
    document.body.appendChild(toast);
    
    setTimeout(() => {
        toast.style.opacity = '0';
        toast.style.transform = 'translateX(100%)';
        toast.style.transition = 'all 0.3s ease';
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}

// Make showNotification globally available
window.showNotification = showNotification;

// ===================================
// ACCESSIBILITY ENHANCEMENTS
// ===================================

// Detect keyboard navigation
document.addEventListener('keydown', (e) => {
    if (e.key === 'Tab') {
        document.body.classList.add('keyboard-nav');
    }
});

document.addEventListener('mousedown', () => {
    document.body.classList.remove('keyboard-nav');
});

// Scroll to top on auth pages
if (document.querySelector('.auth-container') || document.getElementById('loginForm')) {
    if ('scrollRestoration' in history) {
        history.scrollRestoration = 'manual';
    }
    window.scrollTo(0, 0);
}