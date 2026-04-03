/**
 * Sauni – Settings Page Logic
 * Handles sidebar navigation, form validation, and submission feedback.
 */

document.addEventListener('DOMContentLoaded', () => {
    // --- Navigation & View Switching ---
    const profileToggle = document.getElementById('profileDropdownToggle');
    const dropdownMenu = document.getElementById('dropdownMenu');
    const navItems = document.querySelectorAll('#settings-nav li');
    const sections = document.querySelectorAll('.form-section');
    const infoPanels = {
        profile: document.getElementById('profile-info'),
        password: document.getElementById('password-info')
    };

    if (profileToggle) {
        profileToggle.addEventListener('click', (e) => {
            dropdownMenu.classList.toggle('show');
            e.stopPropagation();
        });
        document.addEventListener('click', () => {
            dropdownMenu.classList.remove('show');
        });
    }

    function switchTab(targetId) {
        // Update Sidebar
        navItems.forEach(item => {
            item.classList.toggle('active', item.dataset.target === targetId);
        });

        // Update Main View Sections
        sections.forEach(sec => {
            sec.classList.toggle('active', sec.id === `${targetId}-section`);
        });

        // Update Side Info Panels
        if (infoPanels.profile) infoPanels.profile.style.display = (targetId === 'profile' ? 'block' : 'none');
        if (infoPanels.password) infoPanels.password.style.display = (targetId === 'password' ? 'block' : 'none');
    }

    // Handle initial view based on URL parameter or default
    const urlParams = new URLSearchParams(window.location.search);
    const initialTab = urlParams.get('tab') || 'profile';
    switchTab(initialTab);

    // Sidebar navigation click handler
    navItems.forEach(item => {
        if (item.dataset.target && item.dataset.target !== 'history') {
            item.addEventListener('click', () => {
                switchTab(item.dataset.target);
                // Update URL for state persistence
                window.history.replaceState(null, '', `?tab=${item.dataset.target}`);
            });
        }
    });

    // --- Validation Logic ---

    /**
     * Rules defined by input ID.
     * Returns error message string or empty if valid.
     */
    const validationRules = {
        'profile-name': (val) => val.trim() !== '' ? '' : 'Full name cannot be empty.',
        'profile-email': (val) => {
            if (!val.trim()) return 'Email address cannot be empty.';
            return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(val.trim()) ? '' : 'Please enter a valid email address (e.g. user@example.com).';
        },
        'profile-phone': (val) => {
            if (!val.trim()) return 'Phone number cannot be empty.';
            return /^[\+]?[0-9\s-]{7,15}$/.test(val.trim()) ? '' : 'Please enter a valid phone number (at least 7 digits).';
        },
        'profile-address': (val) => val.trim().length >= 5 ? '' : 'Please provide a more detailed address (at least 5 characters).',
        'current-password': (val) => val.length > 0 ? '' : 'Please enter your current password.',
        'new-password': (val) => val.length >= 8 ? '' : 'New password must be at least 8 characters long.',
        'confirm-password': (val, form) => {
            const newPass = form.querySelector('#new-password').value;
            if (!val) return 'Please confirm your new password.';
            return val === newPass ? '' : 'Passwords do not match.';
        }
    };

    /**
     * Validates a single input element and updates the UI state.
     */
    function validateField(input) {
        const form = input.closest('form');
        const rule = validationRules[input.id];
        const errorMsg = rule ? rule(input.value, form) : '';
        const group = input.closest('.form-group');
        const errorSpan = document.getElementById(`${input.id}-error`);

        if (errorMsg) {
            group.classList.add('invalid');
            if (errorSpan) errorSpan.textContent = errorMsg;
            return false;
        } else {
            group.classList.remove('invalid');
            if (errorSpan) errorSpan.textContent = '';
            return true;
        }
    }

    // Initialize listeners for both forms
    ['profile-form', 'password-form'].forEach(formId => {
        const form = document.getElementById(formId);
        if (!form) return;

        form.querySelectorAll('input, textarea').forEach(input => {
            // Ignore read-only fields
            if (input.readOnly) return;

            // Trigger on Blur: 'When we leave a box' as requested
            input.addEventListener('blur', () => validateField(input));

            // Clear errors on input as they correct them
            input.addEventListener('input', () => {
                if (input.closest('.form-group').classList.contains('invalid')) {
                    validateField(input);
                }
            });
        });

        // Form submission handling
        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            
            // Check all visible fields before final submission
            let isFormValid = true;
            form.querySelectorAll('input, textarea').forEach(input => {
                if (!input.readOnly && !validateField(input)) {
                    isFormValid = false;
                }
            });

            if (!isFormValid) return; // Halt if any info is incorrect

            const submitBtn = form.querySelector('.btn-primary');
            const statusDiv = document.getElementById(`${formId.split('-')[0]}-status`);
            const originalText = submitBtn.textContent;

            // Loading State: Visual feedback during "save"
            submitBtn.textContent = 'Saving Changes...';
            submitBtn.disabled = true;
            statusDiv.className = 'status-msg';
            statusDiv.textContent = '';

            // Simulate server-side processing
            setTimeout(() => {
                submitBtn.textContent = originalText;
                submitBtn.disabled = false;
                
                // Final success feedback
                statusDiv.textContent = formId === 'profile-form' ? 
                    'Profile updated successfully!' : 
                    'Password has been reset successfully!';
                statusDiv.className = 'status-msg success';

                // Fade out status message after a while
                setTimeout(() => {
                    statusDiv.className = 'status-msg';
                }, 4000);
            }, 1200);
        });
    });

    // Exposed Reset Function for "Cancel" buttons
    window.resetForm = (formId) => {
        const form = document.getElementById(formId);
        if (!form) return;
        
        form.reset();
        // Clear all UI error markers
        form.querySelectorAll('.form-group').forEach(group => group.classList.remove('invalid'));
        form.querySelectorAll('.error-msg').forEach(msg => msg.textContent = '');
        
        const statusDiv = document.getElementById(`${formId.split('-')[0]}-status`);
        if (statusDiv) statusDiv.className = 'status-msg';
    };
});
