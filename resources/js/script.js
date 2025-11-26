import './bootstrap';

/**
 * MACHINA - Scripts JavaScript
 */

document.addEventListener('DOMContentLoaded', function() {
    initLoginLockout();       // Reset local après 5 min
    initRegisterValidation(); // Validation stricte
    
    // Fonctionnalités UI
    initFormValidation();
    initPasswordToggle();
    initSmoothAnimations();
    initAutoFormatting();
    initLangSwitcher();
    initGlobalUI();

    console.log('%c🚗 MACHINA - Scripts Chargés (5min Buffer)', 'color: #3498db; font-weight: bold;');
});

// =================================================================
// 1. LOGIQUE SÉCURITÉ
// =================================================================

function initLoginLockout() {
    const loginForm = document.getElementById('loginForm');
    if (loginForm) {
        // Nettoyage automatique du localStorage après 5 min (300 000 ms) d'inactivité
        const lastErrorTime = parseInt(localStorage.getItem('lastErrorTime') || '0');
        const now = Date.now();
        
        // 5 minutes = 300 000 ms
        if (lastErrorTime > 0 && (now - lastErrorTime > 300000)) {
            localStorage.removeItem('loginAttempts');
            localStorage.removeItem('lockoutEnd');
            localStorage.removeItem('lastErrorTime');
            console.log('🔒 Machina Security: Compteur local réinitialisé après 5 min.');
        }
    }
}

function initRegisterValidation() {
    const registerForm = document.getElementById('registerForm');
    if (!registerForm) return;

    const passwordInput = registerForm.querySelector('input[name="password"]');
    const passwordConfirm = registerForm.querySelector('input[name="password_confirmation"]');
    const matchError = registerForm.querySelector('.match-error');
    const passwordErrors = document.getElementById('passwordErrors');
    const errorList = document.getElementById('errorList');

    if (!passwordInput) return;

    function validateRobustness() {
        const val = passwordInput.value;
        const errors = [];
        
        if (val.length < 14) errors.push('14 caractères minimum');
        if (!/[A-Z]/.test(val)) errors.push('Une majuscule');
        if (!/[a-z]/.test(val)) errors.push('Une minuscule');
        if (!/[0-9]/.test(val)) errors.push('Un chiffre');
        if (!/[@$!%*#?&]/.test(val)) errors.push('Un caractère spécial');

        if (errors.length > 0 && val.length > 0) {
            if(passwordErrors) {
                passwordErrors.style.display = 'block';
                errorList.innerHTML = errors.map(e => `<li>${e}</li>`).join('');
            }
            return false;
        } else {
            if(passwordErrors) passwordErrors.style.display = 'none';
            return true;
        }
    }

    passwordInput.addEventListener('input', validateRobustness);
    
    if(passwordConfirm) {
        passwordConfirm.addEventListener('input', () => {
            if(matchError) matchError.style.display = 'none';
        });
    }

    registerForm.addEventListener('submit', function(e) {
        let hasError = false;
        if (passwordConfirm && passwordInput.value !== passwordConfirm.value) {
            e.preventDefault();
            hasError = true;
            if (matchError) {
                matchError.textContent = 'Les mots de passe ne correspondent pas.';
                matchError.style.display = 'block';
                matchError.style.color = '#e74c3c';
            }
        }
        if (!validateRobustness()) {
            e.preventDefault();
            hasError = true;
        }
        if(hasError) return false;
    });
}

// =================================================================
// 2. FONCTIONNALITÉS UI
// =================================================================

function initFormValidation() {
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        const inputs = form.querySelectorAll('input[required]');
        inputs.forEach(input => {
            input.addEventListener('blur', function() { validateField(this); });
            input.addEventListener('input', function() { 
                if (this.classList.contains('is-invalid')) validateField(this); 
            });
        });
    });
}

function validateField(field) {
    if (field.value.trim() === '' && field.hasAttribute('required')) {
        field.classList.add('is-invalid');
    } else {
        field.classList.remove('is-invalid');
    }
}

function initLangSwitcher() {
    const toggleBtn = document.getElementById('lang-toggle-btn');
    const dropdownMenu = document.getElementById('lang-dropdown-menu');
    if (!toggleBtn || !dropdownMenu) return;

    toggleBtn.addEventListener('click', function(e) {
        e.stopPropagation();
        const isHidden = dropdownMenu.style.display === 'none' || dropdownMenu.style.display === '';
        dropdownMenu.style.display = isHidden ? 'block' : 'none';
    });
    document.addEventListener('click', () => dropdownMenu.style.display = 'none');
    dropdownMenu.addEventListener('click', (e) => e.stopPropagation());
}

function initPasswordToggle() {
    // Cas A : Boutons HTML
    document.querySelectorAll('.password-toggle').forEach(btn => {
        if (btn.dataset.initialized) return;
        btn.addEventListener('click', function() {
            const container = this.closest('.password-container') || this.parentNode;
            const input = container.querySelector('input');
            if (input) {
                if (input.type === 'password') {
                    input.type = 'text';
                    this.textContent = '👁️‍🗨️';
                } else {
                    input.type = 'password';
                    this.textContent = '👁️';
                }
            }
        });
        btn.dataset.initialized = 'true';
    });

    // Cas B : Inputs orphelins
    const passwordFields = document.querySelectorAll('input[type="password"]');
    passwordFields.forEach(field => {
        if (field.parentNode.querySelector('.password-toggle')) return;
        if (field.nextElementSibling && field.nextElementSibling.classList.contains('password-toggle')) return;

        const wrapper = document.createElement('div');
        wrapper.style.position = 'relative';
        if(field.parentNode) {
            field.parentNode.insertBefore(wrapper, field);
            wrapper.appendChild(field);

            const toggleBtn = document.createElement('button');
            toggleBtn.type = 'button';
            toggleBtn.innerHTML = '👁️';
            toggleBtn.className = 'password-toggle-dynamic'; 
            toggleBtn.style.cssText = `
                position: absolute; right: 10px; top: 50%; transform: translateY(-50%);
                background: transparent; border: none; cursor: pointer; font-size: 1.2rem; opacity: 0.6;
            `;
            toggleBtn.addEventListener('click', function() {
                if (field.type === 'password') {
                    field.type = 'text';
                    this.innerHTML = '🔒';
                } else {
                    field.type = 'password';
                    this.innerHTML = '👁️';
                }
            });
            wrapper.appendChild(toggleBtn);
        }
    });
}

function initSmoothAnimations() {
    const observerOptions = { threshold: 0.1, rootMargin: '0px 0px -50px 0px' };
    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, observerOptions);

    document.querySelectorAll('.auth-container, .content-card, .welcome-container').forEach(card => {
        observer.observe(card);
    });
}

function initAutoFormatting() {
    const phoneInput = document.getElementById('phone_number') || document.getElementById('telephone');
    if (phoneInput) {
        phoneInput.addEventListener('input', function(e) {
            let value = e.target.value.replace(/[^\d]/g, '');
            if (value.length > 0) {
                let formatted = value.match(/.{1,2}/g)?.join(' ') || value;
                e.target.value = formatted.substring(0, 14);
            }
        });
    }
    const postalCodeInput = document.getElementById('postal_code');
    if (postalCodeInput) {
        postalCodeInput.addEventListener('input', function(e) {
            e.target.value = e.target.value.replace(/[^\d]/g, '').substring(0, 5);
        });
    }
}

function initGlobalUI() {
    if (document.querySelector('.auth-container') || document.getElementById('loginForm')) {
        if ('scrollRestoration' in history) history.scrollRestoration = 'manual'; 
        window.scrollTo(0, 0);
    }

    document.addEventListener('click', function(e) {
        if (e.target.matches('.btn-primary, .btn-welcome')) {
            const btn = e.target;
            const ripple = document.createElement('span');
            ripple.style.cssText = `
                position: absolute; border-radius: 50%; background: rgba(255, 255, 255, 0.6);
                width: 100px; height: 100px; margin-top: -50px; margin-left: -50px;
                animation: ripple 0.6s; pointer-events: none;
            `;
            const rect = btn.getBoundingClientRect();
            ripple.style.left = (e.clientX - rect.left) + 'px';
            ripple.style.top = (e.clientY - rect.top) + 'px';
            btn.style.position = 'relative'; btn.style.overflow = 'hidden'; btn.appendChild(ripple);
            setTimeout(() => ripple.remove(), 600);
        }
    });

    if (!document.getElementById('ripple-style')) {
        const style = document.createElement('style');
        style.id = 'ripple-style';
        style.textContent = `@keyframes ripple { 0% { transform: scale(0); opacity: 1; } 100% { transform: scale(4); opacity: 0; } }`;
        document.head.appendChild(style);
    }

    document.querySelectorAll('.alert').forEach(alert => {
        setTimeout(() => {
            alert.style.transition = 'opacity 0.5s, transform 0.5s';
            alert.style.opacity = '0';
            alert.style.transform = 'translateY(-20px)';
            setTimeout(() => alert.remove(), 500);
        }, 5000);
        const closeBtn = document.createElement('button');
        closeBtn.innerHTML = '×';
        closeBtn.style.cssText = `
            position: absolute; right: 10px; top: 50%; transform: translateY(-50%);
            background: transparent; border: none; font-size: 1.5rem; cursor: pointer; opacity: 0.7;
        `;
        closeBtn.onclick = () => alert.remove();
        alert.style.position = 'relative'; alert.appendChild(closeBtn);
    });

    document.addEventListener('keydown', (e) => { if (e.key === 'Tab') document.body.classList.add('keyboard-nav'); });
    document.addEventListener('mousedown', () => document.body.classList.remove('keyboard-nav'));
    
    document.querySelectorAll('button[type="submit"]').forEach(btn => {
        btn.closest('form')?.addEventListener('submit', function() {
            setTimeout(() => {
                if(!btn.disabled && document.querySelectorAll('.is-invalid').length === 0) {
                    btn.disabled = true; btn.style.opacity = '0.6';
                    btn.dataset.originalText = btn.textContent; btn.textContent = 'Chargement...';
                    setTimeout(() => {
                        btn.disabled = false; btn.style.opacity = '1';
                        btn.textContent = btn.dataset.originalText;
                    }, 3000);
                }
            }, 50);
        });
    });
}