import './bootstrap';

/**
 * MACHINA - Application de Location de Véhicules
 * Scripts JavaScript pour améliorer l'expérience utilisateur
 */

// ===================================
// Initialisation au chargement du DOM
// ===================================
document.addEventListener('DOMContentLoaded', function() {

    // Initialiser toutes les fonctionnalités
    initFormValidation();
    initPasswordToggle();
    initSmoothAnimations();
    initAutoFormatting();

    console.log('✓ Machina scripts loaded successfully');
});

// ===================================
// Validation de formulaire en temps réel
// ===================================
function initFormValidation() {
    const forms = document.querySelectorAll('form');

    forms.forEach(form => {
        const inputs = form.querySelectorAll('input[required]');

        inputs.forEach(input => {
            input.addEventListener('blur', function() {
                validateField(this);
            });

            input.addEventListener('input', function() {
                if (this.classList.contains('is-invalid')) {
                    validateField(this);
                }
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

// ===================================
// Toggle Password Visibility
// ===================================
function initPasswordToggle() {
    // Ajouter un bouton pour afficher/masquer le mot de passe
    const passwordFields = document.querySelectorAll('input[type="password"]');

    passwordFields.forEach(field => {
        const wrapper = document.createElement('div');
        wrapper.style.position = 'relative';

        field.parentNode.insertBefore(wrapper, field);
        wrapper.appendChild(field);

        const toggleBtn = document.createElement('button');
        toggleBtn.type = 'button';
        toggleBtn.innerHTML = '👁️';
        toggleBtn.style.cssText = `
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            background: transparent;
            border: none;
            cursor: pointer;
            font-size: 1.2rem;
            opacity: 0.6;
            transition: opacity 0.3s;
        `;

        toggleBtn.addEventListener('mouseenter', function() {
            this.style.opacity = '1';
        });

        toggleBtn.addEventListener('mouseleave', function() {
            this.style.opacity = '0.6';
        });

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
    });
}

// ===================================
// Animations au scroll
// ===================================
function initSmoothAnimations() {
    // Détecter les éléments qui apparaissent à l'écran
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, observerOptions);

    // Observer les cartes d'authentification
    const cards = document.querySelectorAll('.auth-container, .content-card, .welcome-container');
    cards.forEach(card => {
        observer.observe(card);
    });
}

// ===================================
// Auto-formatting des champs
// ===================================
function initAutoFormatting() {
    // Format du numéro de téléphone
    const phoneInput = document.getElementById('phone_number');
    if (phoneInput) {
        phoneInput.addEventListener('input', function(e) {
            let value = e.target.value.replace(/[^\d]/g, '');

            if (value.length > 0) {
                let formatted = value.match(/.{1,2}/g)?.join(' ') || value;
                e.target.value = formatted.substring(0, 14); // 10 chiffres + 4 espaces
            }
        });
    }

    // Format du code postal (5 chiffres max)
    const postalCodeInput = document.getElementById('postal_code');
    if (postalCodeInput) {
        postalCodeInput.addEventListener('input', function(e) {
            e.target.value = e.target.value.replace(/[^\d]/g, '').substring(0, 5);
        });
    }
}

// ===================================
// Feedback visuel pour les boutons
// ===================================
document.addEventListener('click', function(e) {
    if (e.target.matches('.btn-primary, .btn-welcome')) {
        const btn = e.target;

        // Effet de ripple
        const ripple = document.createElement('span');
        ripple.style.cssText = `
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.6);
            width: 100px;
            height: 100px;
            margin-top: -50px;
            margin-left: -50px;
            animation: ripple 0.6s;
            pointer-events: none;
        `;

        const rect = btn.getBoundingClientRect();
        ripple.style.left = (e.clientX - rect.left) + 'px';
        ripple.style.top = (e.clientY - rect.top) + 'px';

        btn.style.position = 'relative';
        btn.style.overflow = 'hidden';
        btn.appendChild(ripple);

        setTimeout(() => ripple.remove(), 600);
    }
});

// Ajouter l'animation CSS pour le ripple effect
const style = document.createElement('style');
style.textContent = `
    @keyframes ripple {
        0% {
            transform: scale(0);
            opacity: 1;
        }
        100% {
            transform: scale(4);
            opacity: 0;
        }
    }
`;
document.head.appendChild(style);

// ===================================
// Gestion des alertes auto-dismiss
// ===================================
const alerts = document.querySelectorAll('.alert');
alerts.forEach(alert => {
    // Auto-dismiss après 5 secondes
    setTimeout(() => {
        alert.style.transition = 'opacity 0.5s, transform 0.5s';
        alert.style.opacity = '0';
        alert.style.transform = 'translateY(-20px)';

        setTimeout(() => alert.remove(), 500);
    }, 5000);

    // Ajouter un bouton de fermeture
    const closeBtn = document.createElement('button');
    closeBtn.innerHTML = '×';
    closeBtn.style.cssText = `
        position: absolute;
        right: 10px;
        top: 50%;
        transform: translateY(-50%);
        background: transparent;
        border: none;
        font-size: 1.5rem;
        cursor: pointer;
        opacity: 0.7;
        transition: opacity 0.3s;
    `;

    closeBtn.addEventListener('click', function() {
        alert.style.transition = 'opacity 0.5s, transform 0.5s';
        alert.style.opacity = '0';
        alert.style.transform = 'translateY(-20px)';
        setTimeout(() => alert.remove(), 500);
    });

    closeBtn.addEventListener('mouseenter', function() {
        this.style.opacity = '1';
    });

    closeBtn.addEventListener('mouseleave', function() {
        this.style.opacity = '0.7';
    });

    alert.style.position = 'relative';
    alert.style.paddingRight = '40px';
    alert.appendChild(closeBtn);
});

// ===================================
// Amélioration de l'accessibilité
// ===================================

// Focus visible pour navigation au clavier
document.addEventListener('keydown', function(e) {
    if (e.key === 'Tab') {
        document.body.classList.add('keyboard-nav');
    }
});

document.addEventListener('mousedown', function() {
    document.body.classList.remove('keyboard-nav');
});

// Ajouter les styles pour le focus
const focusStyle = document.createElement('style');
focusStyle.textContent = `
    body.keyboard-nav *:focus {
        outline: 3px solid #3498db !important;
        outline-offset: 2px;
    }
`;
document.head.appendChild(focusStyle);

// ===================================
// Prévention de la double soumission
// ===================================
const submitButtons = document.querySelectorAll('button[type="submit"]');
submitButtons.forEach(btn => {
    btn.closest('form')?.addEventListener('submit', function() {
        btn.disabled = true;
        btn.style.opacity = '0.6';

        const originalText = btn.textContent;
        btn.textContent = 'Chargement...';

        // Réactiver après 3 secondes en cas d'erreur
        setTimeout(() => {
            btn.disabled = false;
            btn.style.opacity = '1';
            btn.textContent = originalText;
        }, 3000);
    });
});

// ===================================
// Console branding
// ===================================
console.log('%c🚗 MACHINA - Location de Véhicules', 'color: #3498db; font-size: 20px; font-weight: bold;');
console.log('%cVersion 1.0.0', 'color: #7f8c8d; font-size: 12px;');
