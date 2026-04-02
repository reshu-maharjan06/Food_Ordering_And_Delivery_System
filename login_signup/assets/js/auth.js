document.addEventListener('DOMContentLoaded', () => {
    window.togglePass = (id, btn) => {
        const input = document.getElementById(id);
        const icon = btn.querySelector("svg");

        if (input.type === "password") {
            input.type = "text";
            icon.innerHTML = '<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24M1 1l22 22"/>';
        } else {
            input.type = "password";
            icon.innerHTML = '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>';
        }
    };

    const loginForm = document.getElementById('login-form');
    if (loginForm) {
        loginForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = new FormData(loginForm);
            formData.append('ajax', 'true');

            const errorBox = document.getElementById('login-error');
            const submitBtn = loginForm.querySelector('button[type="submit"]');
            
            if(errorBox) { errorBox.style.display = 'none'; errorBox.innerText = ''; }
            submitBtn.disabled = true;
            submitBtn.innerText = 'Verifying...';

            try {
                const response = await fetch('index.php', {
                    method: 'POST',
                    body: formData,
                    headers: { 
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                });

                const text = await response.text();
                console.log('Server response text:', text);

                if (!response.ok) {
                   throw new Error(`Server returned status ${response.status}: ${text}`);
                }

                let data;
                try {
                    data = JSON.parse(text);
                } catch (jsonErr) {
                    console.error('SERVER RESPONDED WITH NON-JSON:', text);
                    if(errorBox) {
                        errorBox.innerText = "Invalid response format. Check the console for details.";
                        errorBox.style.display = 'block';
                    }
                    submitBtn.disabled = false;
                    submitBtn.innerText = 'Login';
                    return;
                }

                if (data.ok) {
                    submitBtn.innerText = 'Success! Redirecting...';
                    window.location.href = data.redirect;
                } else {
                    if(errorBox) {
                        errorBox.innerText = data.message;
                        errorBox.style.display = 'block';
                    }
                    submitBtn.disabled = false;
                    submitBtn.innerText = 'Login';
                }
            } catch (err) {
                console.error('Fetch/Login Error:', err);
                if(errorBox) {
                    errorBox.innerText = 'An unexpected error occurred: ' + err.message;
                    errorBox.style.display = 'block';
                }
                submitBtn.disabled = false;
                submitBtn.innerText = 'Login';
            }
        });
    }

    const signupForm = document.getElementById('signup-form');
    if (signupForm) {
        signupForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = new FormData(signupForm);
            formData.append('ajax', 'true');

            const errorBox = document.getElementById('signup-error');
            const submitBtn = signupForm.querySelector('button[type="submit"]');

            if(errorBox) { errorBox.style.display = 'none'; errorBox.innerText = ''; }
            submitBtn.disabled = true;
            submitBtn.innerText = 'Creating account...';

            try {
                const response = await fetch('signup.php', {
                    method: 'POST',
                    body: formData,
                    headers: { 
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                });

                const text = await response.text();
                console.log('Server response text:', text);

                if (!response.ok) {
                    throw new Error(`Server returned status ${response.status}: ${text}`);
                 }

                let data;
                try {
                    data = JSON.parse(text);
                } catch (jsonErr) {
                    console.error('SERVER RESPONDED WITH NON-JSON:', text);
                    if(errorBox) {
                        errorBox.innerText = "Invalid response format. Please try again.";
                        errorBox.style.display = 'block';
                    }
                    submitBtn.disabled = false;
                    submitBtn.innerText = 'Sign Up';
                    return;
                }

                if (data.ok) {
                    const wrapper = signupForm.closest('.login-wrapper');
                    if (wrapper) {
                        wrapper.innerHTML = `
                            <div class="success-state">
                                <div class="icon-circle">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                                        <polyline points="20 6 9 17 4 12"/>
                                    </svg>
                                </div>
                                <h1>Account Created!</h1>
                                <p>${data.message}</p>
                                <a href="index.php" class="btn-primary" style="display: block; text-decoration: none; text-align: center; margin-top: 20px;">Go to Login</a>
                            </div>
                        `;
                    }
                } else {
                    if(errorBox) {
                        errorBox.innerText = data.message;
                        errorBox.style.display = 'block';
                    }
                    submitBtn.disabled = false;
                    submitBtn.innerText = 'Sign Up';
                }
            } catch (err) {
                console.error('Signup Fetch/Auth Error:', err);
                if(errorBox) {
                    errorBox.innerText = 'Registration failed: ' + err.message;
                    errorBox.style.display = 'block';
                }
                submitBtn.disabled = false;
                submitBtn.innerText = 'Sign Up';
            }
        });
    }

    window.openModal = (id) => {
        const el = document.getElementById(id);
        if (el) { el.classList.add("open"); document.body.style.overflow = "hidden"; }
    };

    window.closeModal = (id) => {
        const el = document.getElementById(id);
        if (el) { el.classList.remove("open"); document.body.style.overflow = ""; }
    };
});
