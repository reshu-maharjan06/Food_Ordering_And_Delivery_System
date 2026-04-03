document.addEventListener('DOMContentLoaded', () => {
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
        navItems.forEach(item => {
            item.classList.toggle('active', item.dataset.target === targetId);
        });
        sections.forEach(sec => {
            sec.classList.toggle('active', sec.id === `${targetId}-section`);
        });
        if (infoPanels.profile) infoPanels.profile.style.display = (targetId === 'profile' ? 'block' : 'none');
        if (infoPanels.password) infoPanels.password.style.display = (targetId === 'password' ? 'block' : 'none');
    }
    const urlParams = new URLSearchParams(window.location.search);
    const initialTab = urlParams.get('tab') || 'profile';
    switchTab(initialTab);
    navItems.forEach(item => {
        if (item.dataset.target && item.dataset.target !== 'history') {
            item.addEventListener('click', () => {
                switchTab(item.dataset.target);
                window.history.replaceState(null, '', `?tab=${item.dataset.target}`);
            });
        }
    });
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
    const statusDivs = {
        profile: document.getElementById('profile-status'),
        password: document.getElementById('password-status')
    };
    async function fetchUserDetails() {
        try {
            const response = await fetch('api/profile_api.php');
            const result = await response.json();
            if (result.success) {
                const user = result.data;
                document.getElementById('profile-name').value = user.name || '';
                document.getElementById('profile-email').value = user.email || '';
                document.getElementById('profile-phone').value = user.phone || '';
                document.getElementById('profile-address').value = user.address || '';
                const avatar = (user.name || 'U').charAt(0).toUpperCase();
                document.querySelectorAll('.avatar, .settings-avatar').forEach(el => el.textContent = avatar);
                document.querySelectorAll('.username, .settings-user-meta h4').forEach(el => {
                    if (el.classList.contains('username')) {
                        el.innerHTML = `${user.name || 'User'}&nbsp;<i class="fa-solid fa-chevron-down fa-xs"></i>`;
                    } else {
                        el.textContent = user.name || 'User';
                    }
                });
            } else {
                console.error('Failed to fetch user details:', result.error);
                if (result.error === 'Unauthorized access. Please log in.') {
                    window.location.href = 'login.php';
                }
            }
        } catch (error) {
            console.error('Error fetching user details:', error);
        }
    }
    fetchUserDetails();
    ['profile-form', 'password-form'].forEach(formId => {
        const form = document.getElementById(formId);
        if (!form) return;
        form.querySelectorAll('input, textarea').forEach(input => {
            if (input.readOnly) return;
            input.addEventListener('blur', () => validateField(input));
            input.addEventListener('input', () => {
                if (input.closest('.form-group').classList.contains('invalid')) {
                    validateField(input);
                }
            });
        });
        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            let isFormValid = true;
            form.querySelectorAll('input, textarea').forEach(input => {
                if (!input.readOnly && !validateField(input)) {
                    isFormValid = false;
                }
            });
            if (!isFormValid) return;
            const submitBtn = form.querySelector('.btn-primary');
            const type = formId.split('-')[0];
            const statusDiv = statusDivs[type];
            const originalText = submitBtn.textContent;
            submitBtn.textContent = 'Saving Changes...';
            submitBtn.disabled = true;
            statusDiv.className = 'status-msg';
            statusDiv.textContent = '';
            const formData = new FormData(form);
            formData.append('action', type === 'profile' ? 'update_profile' : 'update_password');
            try {
                const response = await fetch('api/profile_api.php', {
                    method: 'POST',
                    body: formData
                });
                const result = await response.json();
                submitBtn.textContent = originalText;
                submitBtn.disabled = false;
                if (result.success) {
                    statusDiv.textContent = result.message;
                    statusDiv.className = 'status-msg success';
                    if (type === 'profile') fetchUserDetails();
                } else {
                    statusDiv.textContent = result.error;
                    statusDiv.className = 'status-msg error';
                }
                setTimeout(() => {
                    statusDiv.className = 'status-msg';
                }, 4000);
            } catch (error) {
                console.error('Error submitting form:', error);
                submitBtn.textContent = originalText;
                submitBtn.disabled = false;
                statusDiv.textContent = 'An unexpected error occurred.';
                statusDiv.className = 'status-msg error';
            }
        });
    });
    window.resetForm = (formId) => {
        const form = document.getElementById(formId);
        if (!form) return;
        form.reset();
        form.querySelectorAll('.form-group').forEach(group => group.classList.remove('invalid'));
        form.querySelectorAll('.error-msg').forEach(msg => msg.textContent = '');
        const statusDiv = document.getElementById(`${formId.split('-')[0]}-status`);
        if (statusDiv) statusDiv.className = 'status-msg';
    };
});
