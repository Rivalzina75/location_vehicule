/**
 * MACHINA - Scripts JavaScript (Optimisé)
 * Version 3.1 - Nettoyé et allégé
 */

import './bootstrap';

// ===================================
// MAIN INITIALIZATION
// ===================================

document.addEventListener('DOMContentLoaded', function () {
    // Core security modules
    initLoginLockout();
    initLockoutCountdown();
    initRegisterValidation();
    initFormValidation();

    // UI Enhancements
    initPasswordToggle();
    initAutoFormatting();
    initLangSwitcher();
    initAlerts();
    initRippleEffect();
    initForgotPasswordTransfer();
    initPageTransitions();
    initThemeToggle();
    initLogoutModal();

    // Dashboard
    initMobileDashboard();

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
 * Initialize lockout countdown timer
 */
function initLockoutCountdown() {
    const lockoutInput = document.getElementById('lockout-until');
    if (!lockoutInput) return;

    const lockoutUntil = parseInt(lockoutInput.value) || 0;
    const currentTime = Math.floor(Date.now() / 1000);

    if (lockoutUntil <= 0 || lockoutUntil <= currentTime) return;

    const translationsEl = document.getElementById('lockout-translations');
    const translations = {
        countdown: translationsEl?.dataset.countdown || 'Time remaining: :seconds seconds.',
        complete: translationsEl?.dataset.complete || 'You can now try again.'
    };

    const countdownElement = document.getElementById('countdown-timer');
    const submitButton = document.getElementById('login-btn');
    const emailInput = document.getElementById('email');

    if (!countdownElement) return;

    let remainingSeconds = lockoutUntil - currentTime;

    countdownElement.style.display = 'block';

    if (emailInput) emailInput.classList.add('is-invalid');

    if (submitButton) {
        submitButton.disabled = true;
        submitButton.style.opacity = '0.5';
        submitButton.style.cursor = 'not-allowed';
    }

    function updateCountdown() {
        if (remainingSeconds > 0) {
            const message = translations.countdown.replace(':seconds', Math.floor(remainingSeconds));
            countdownElement.textContent = message;
            countdownElement.classList.remove('countdown-complete');
        } else {
            countdownElement.textContent = translations.complete;
            countdownElement.classList.add('countdown-complete');
            countdownElement.style.color = '#00d9a5';
            countdownElement.style.backgroundColor = '#e6fff9';
            countdownElement.style.borderColor = '#00d9a5';

            if (submitButton) {
                submitButton.disabled = false;
                submitButton.style.opacity = '1';
                submitButton.style.cursor = 'pointer';
            }

            if (emailInput) emailInput.classList.remove('is-invalid');
        }
    }

    updateCountdown();

    const interval = setInterval(() => {
        remainingSeconds--;
        updateCountdown();

        if (remainingSeconds <= 0) {
            clearInterval(interval);
        }
    }, 1000);
}

/**
 * Registration form validation with password strength checking
 */
function initRegisterValidation() {
    const form = document.getElementById('registerForm') || document.getElementById('resetPasswordForm');
    if (!form) return;

    const passwordInput = form.querySelector('input[name="password"]');
    const passwordConfirm = form.querySelector('input[name="password_confirmation"]');

    if (!passwordInput) return;

    createPasswordStrengthUI(passwordInput);

    passwordInput.addEventListener('input', function () {
        updatePasswordStrength(this);
        if (passwordConfirm) {
            clearFieldError(passwordConfirm);
            passwordConfirm.classList.remove('is-invalid');
        }
    });

    passwordInput.addEventListener('focus', function () {
        if (this.value.length > 0) {
            updatePasswordStrength(this);
        }
    });

    if (passwordConfirm) {
        passwordConfirm.addEventListener('input', function () {
            clearFieldError(this);
            this.classList.remove('is-invalid');
        });
    }

    form.addEventListener('submit', function (e) {
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
            showFieldError(passwordConfirm, getTranslation('passwords_no_match'));
        }

        if (hasError) {
            const firstError = form.querySelector('.is-invalid');
            if (firstError) {
                firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                firstError.focus();
            }
        }
    });
}

/**
 * Get translation based on current page language
 */
function getTranslation(key) {
    const lang = document.documentElement.lang || 'en';
    const translations = {
        'fr': {
            'passwords_no_match': 'Les mots de passe ne correspondent pas.',
            'missing': 'Il vous manque :',
            'min_chars': '14 caractères min.',
            'uppercase': 'Une majuscule',
            'lowercase': 'Une minuscule',
            'number': 'Un chiffre',
            'special': 'Caractère spécial',
            'weak': 'Faible',
            'medium': 'Moyen',
            'strong': 'Fort',
            'excellent': 'Excellent'
        },
        'en': {
            'passwords_no_match': 'Passwords do not match.',
            'missing': 'Missing:',
            'min_chars': '14 characters min.',
            'uppercase': 'One uppercase',
            'lowercase': 'One lowercase',
            'number': 'One number',
            'special': 'Special character',
            'weak': 'Weak',
            'medium': 'Medium',
            'strong': 'Strong',
            'excellent': 'Excellent'
        }
    };

    const currentLang = lang.startsWith('fr') ? 'fr' : 'en';
    return translations[currentLang][key] || translations['en'][key];
}

/**
 * Creates the password strength UI elements
 */
function createPasswordStrengthUI(input) {
    const wrapper = input.closest('.password-wrapper') || input.parentElement;
    const formGroup = wrapper.closest('.form-group') || wrapper.parentElement;

    if (formGroup.querySelector('.password-strength')) return;

    const strengthContainer = document.createElement('div');
    strengthContainer.className = 'password-strength';
    strengthContainer.innerHTML = `
        <div class="strength-bar">
            <div class="strength-fill"></div>
        </div>
        <span class="strength-text"></span>
    `;

    const requirementsDiv = document.createElement('div');
    requirementsDiv.className = 'password-requirements';
    requirementsDiv.style.display = 'none';
    requirementsDiv.innerHTML = `
        <p>⚠ ${getTranslation('missing')}</p>
        <ul>
            <li data-req="length">${getTranslation('min_chars')}</li>
            <li data-req="uppercase">${getTranslation('uppercase')}</li>
            <li data-req="lowercase">${getTranslation('lowercase')}</li>
            <li data-req="number">${getTranslation('number')}</li>
            <li data-req="special">${getTranslation('special')}</li>
        </ul>
    `;

    wrapper.after(strengthContainer);
    strengthContainer.after(requirementsDiv);
}

/**
 * Check password requirements
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
    const formGroup = wrapper.closest('.form-group') || wrapper.parentElement;

    const strengthContainer = formGroup.querySelector('.password-strength');
    const requirementsDiv = formGroup.querySelector('.password-requirements');

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

    let color, label;
    if (result.percentage <= 40) {
        color = '#ff4757';
        label = getTranslation('weak');
    } else if (result.percentage <= 60) {
        color = '#ffa502';
        label = getTranslation('medium');
    } else if (result.percentage <= 80) {
        color = '#2ed573';
        label = getTranslation('strong');
    } else {
        color = '#00d9a5';
        label = getTranslation('excellent');
    }

    fill.style.width = result.percentage + '%';
    fill.style.background = color;
    text.textContent = label;
    text.style.color = color;

    const items = requirementsDiv.querySelectorAll('li');
    items.forEach(item => {
        const req = item.dataset.req;
        item.classList.toggle('valid', result.requirements[req]);
    });

    requirementsDiv.style.display = result.valid ? 'none' : 'block';
    if (result.valid) input.classList.remove('is-invalid');
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
            input.addEventListener('blur', function () {
                if (this.value.trim() === '') {
                    this.classList.add('is-invalid');
                } else {
                    this.classList.remove('is-invalid');
                }
            });

            input.addEventListener('input', function () {
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

    const wrapper = field.closest('.password-wrapper');
    if (wrapper) {
        wrapper.insertAdjacentElement('afterend', errorDiv);
    } else {
        field.insertAdjacentElement('afterend', errorDiv);
    }
}

/**
 * Clears error message for a field
 */
function clearFieldError(field) {
    let errorElement = field.nextElementSibling;
    while (errorElement) {
        if (errorElement.classList.contains('dynamic-error')) {
            errorElement.remove();
            return;
        }
        errorElement = errorElement.nextElementSibling;
    }

    const wrapper = field.closest('.password-wrapper');
    if (wrapper) {
        errorElement = wrapper.nextElementSibling;
        while (errorElement) {
            if (errorElement.classList.contains('dynamic-error')) {
                errorElement.remove();
                return;
            }
            errorElement = errorElement.nextElementSibling;
        }
    }

    const existingError = field.parentElement.querySelector('.dynamic-error');
    if (existingError) existingError.remove();
}

// ===================================
// UI ENHANCEMENTS
// ===================================

// Page transitions with hierarchy
function initPageTransitions() {
    const menuItems = document.querySelectorAll('.menu-item[data-page-index]');
    const currentPage = document.querySelector('.page-transition[data-page-index]');

    if (!currentPage) return;

    const currentIndex = parseInt(currentPage.dataset.pageIndex);

    // Store current page index in sessionStorage
    sessionStorage.setItem('currentPageIndex', currentIndex);

    menuItems.forEach(item => {
        item.addEventListener('click', function (e) {
            const href = this.getAttribute('href');
            if (!href) return;

            // Allow new tab / modifier clicks
            if (e.metaKey || e.ctrlKey || e.shiftKey || e.altKey || this.target === '_blank') {
                return;
            }

            e.preventDefault();
            const targetIndex = parseInt(this.dataset.pageIndex);
            const previousIndex = parseInt(sessionStorage.getItem('currentPageIndex') || '0');

            // Determine slide direction based on hierarchy
            if (targetIndex > previousIndex) {
                sessionStorage.setItem('slideDirection', 'forward');
            } else if (targetIndex < previousIndex) {
                sessionStorage.setItem('slideDirection', 'backward');
            }

            // Update stored index
            sessionStorage.setItem('currentPageIndex', targetIndex);

            // Play exit animation before navigation
            currentPage.classList.remove('slide-in-left', 'slide-in-right');
            if (targetIndex > previousIndex) {
                currentPage.classList.add('slide-out-right');
            } else if (targetIndex < previousIndex) {
                currentPage.classList.add('slide-out-left');
            }

            setTimeout(() => {
                window.location.href = href;
            }, 280);
        });
    });

    // Apply entrance animation based on previous navigation
    const slideDirection = sessionStorage.getItem('slideDirection');
    if (slideDirection === 'forward') {
        currentPage.classList.add('slide-in-left');
    } else if (slideDirection === 'backward') {
        currentPage.classList.add('slide-in-right');
    }

    // Clear direction after animation
    sessionStorage.removeItem('slideDirection');
}

// Theme toggle (light/dark)
const THEME_KEY = 'theme';

function toggleTheme() {
    const current = document.documentElement.classList.contains('theme-dark') ? 'dark' : 'light';
    const next = current === 'dark' ? 'light' : 'dark';
    applyTheme(next);
}

function applyTheme(theme) {
    const next = theme === 'dark' ? 'theme-dark' : 'theme-light';
    const root = document.documentElement;
    const body = document.body;

    root.classList.remove('theme-dark', 'theme-light');
    body.classList.remove('theme-dark', 'theme-light');
    root.classList.add(next);
    body.classList.add(next);

    const btn = document.getElementById('theme-toggle');
    if (btn) {
        const icon = btn.querySelector('.theme-icon');
        const label = btn.querySelector('.theme-label');
        if (theme === 'dark') {
            if (icon) icon.textContent = '🌞';
            if (label) label.textContent = 'Light';
        } else {
            if (icon) icon.textContent = '🌙';
            if (label) label.textContent = 'Dark';
        }
    }

    try {
        localStorage.setItem(THEME_KEY, theme);
    } catch (e) {
        /* ignore storage errors */
    }
}

function initThemeToggle() {
    const btn = document.getElementById('theme-toggle');
    if (!btn) return;

    let initial = 'light';
    try {
        const stored = localStorage.getItem(THEME_KEY);
        const prefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
        initial = stored || (prefersDark ? 'dark' : 'light');
    } catch (e) {
        /* ignore */
    }

    // Always apply once to sync html/body classes with stored preference
    applyTheme(initial);

    btn.addEventListener('click', (e) => {
        e.preventDefault();
        toggleTheme();
    });

    // Fallback global handler (for inline onclick if needed)
    window.toggleTheme = toggleTheme;
}

/**
 * Initialize password visibility toggle
 */
function initPasswordToggle() {
    const passwordFields = document.querySelectorAll('input[type="password"]');

    passwordFields.forEach(field => {
        if (field.dataset.toggleInitialized) return;
        field.dataset.toggleInitialized = 'true';

        let wrapper = field.closest('.password-wrapper');

        if (!wrapper) {
            wrapper = document.createElement('div');
            wrapper.className = 'password-wrapper';
            field.parentNode.insertBefore(wrapper, field);
            wrapper.appendChild(field);
        }

        if (wrapper.querySelector('.password-toggle-btn')) return;

        const toggleBtn = document.createElement('button');
        toggleBtn.type = 'button';
        toggleBtn.className = 'password-toggle-btn';
        toggleBtn.setAttribute('aria-label', 'Toggle password visibility');
        toggleBtn.setAttribute('tabindex', '-1');

        const eyeOpenSVG = `<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>`;
        const eyeClosedSVG = `<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line></svg>`;

        toggleBtn.innerHTML = eyeOpenSVG;

        toggleBtn.addEventListener('click', function (e) {
            e.preventDefault();
            e.stopPropagation();
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
    const phoneInputs = document.querySelectorAll('input[name="phone_number"], input[name="telephone"]');

    phoneInputs.forEach(input => {
        input.addEventListener('input', (e) => {
            let v = e.target.value;
            const hasPlus = v.startsWith('+');
            v = v.replace(/[^\d]/g, '');
            if (hasPlus) v = '+' + v;
            if (v.length > 15) v = v.substring(0, 15);
            e.target.value = v;
        });
    });

    const postalInput = document.querySelector('#postal_code, #profile_postal');
    if (postalInput) {
        postalInput.addEventListener('input', function (e) {
            e.target.value = e.target.value.replace(/[^\d]/g, '').substring(0, 5);
        });
    }

    const dateInput = document.querySelector('#date_of_birth, #profile_dob');
    if (dateInput) {
        dateInput.addEventListener('input', function (e) {
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

    toggleBtn.addEventListener('click', function (e) {
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

    document.addEventListener('click', function (e) {
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

/**
 * Transfer email from login to forgot password page
 */
function initForgotPasswordTransfer() {
    const loginEmailInput = document.getElementById('email');
    const forgotLink = document.getElementById('forgot-password-link');

    if (loginEmailInput && forgotLink) {
        forgotLink.addEventListener('click', function (e) {
            if (loginEmailInput.value) {
                e.preventDefault();
                const url = new URL(this.href);
                url.searchParams.set('email', loginEmailInput.value);
                window.location.href = url.toString();
            }
        });
    }
}

// ===================================
// DASHBOARD MOBILE
// ===================================

/**
 * Initialize mobile menu for dashboard
 */
function initMobileDashboard() {
    const mobileToggle = document.getElementById('mobileMenuToggle');
    const sidebar = document.querySelector('.sidebar-pro');

    if (!mobileToggle || !sidebar) return;

    mobileToggle.addEventListener('click', function () {
        sidebar.classList.toggle('open');
    });

    document.addEventListener('click', function (e) {
        if (window.innerWidth <= 768) {
            if (!sidebar.contains(e.target) && !mobileToggle.contains(e.target)) {
                sidebar.classList.remove('open');
            }
        }
    });

}

// Initialize global UI behaviors
document.addEventListener('DOMContentLoaded', () => {
    // Remove duplicate initThemeToggle
    initThemeToggle();
});

// ===================================
// LOGOUT CONFIRMATION MODAL
// ===================================

function initLogoutModal() {
    const logoutBtn = document.querySelector('.logout-nav');
    if (!logoutBtn) return;

    const modal = document.createElement('div');
    modal.className = 'logout-modal';
    modal.id = 'logout-modal';
    modal.innerHTML = `
        <div class="logout-modal-content">
            <span class="logout-modal-icon">👋</span>
            <h2 class="logout-modal-title">Confirmer la déconnexion</h2>
            <p class="logout-modal-text">Êtes-vous sûr de vouloir vous déconnecter ?</p>
            <div class="logout-modal-buttons">
                <button type="button" class="logout-modal-btn logout-modal-btn-cancel" onclick="document.getElementById('logout-modal').classList.remove('show');">
                    Annuler
                </button>
                <button type="button" class="logout-modal-btn logout-modal-btn-confirm" onclick="document.getElementById('logout-form-nav').submit();">
                    Se déconnecter
                </button>
            </div>
        </div>
    `;
    document.body.appendChild(modal);

    logoutBtn.addEventListener('click', (e) => {
        e.preventDefault();
        modal.classList.add('show');
    });

    modal.addEventListener('click', (e) => {
        if (e.target === modal) {
            modal.classList.remove('show');
        }
    });

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && modal.classList.contains('show')) {
            modal.classList.remove('show');
        }
    });
}

// ===================================
// ACCESSIBILITY
// ===================================

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