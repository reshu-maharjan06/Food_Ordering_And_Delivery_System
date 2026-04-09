<!DOCTYPE html>
<html lang="en">

<head>
    <meta name="csrf-token" content="<?= csrf_token() ?>">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sauni - Create Account</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700;900&family=Lato:wght@300;400;700&family=Outfit:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/pages/auth.css">
</head>

<body>

    <div class="split-layout">

        <!-- VISUAL COLLAGE -->
        <div class="split-right">
            <div class="slice" style="background-image: url('assets/img/foods/sekuwa.png');">
            </div>
            <div class="slice" style="background-image: url('assets/img/chefs/chef2.png');">
            </div>
            <div class="slice" style="background-image: url('assets/img/foods/kwantiDal.png');">
            </div>
            <div class="slice" style="background-image: url('assets/img/chefs/chef4.png');">
            </div>
        </div>

        <!-- SIGNUP FORM SIDE -->
        <div class="split-left">
            <div class="login-wrapper">
                <a href="landing.php" class="brand brand--signup">sauni</a>

                <?php if($success): ?>
                <div class="success-state">
                    <div class="icon-circle">
                        <svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
                    </div>
                    <h1>Account Created!</h1>
                    <p>Your account has been successfully created.<br>You can now log in and start ordering.</p>
                    <a href="index.php" class="btn-primary" style="display: block; text-decoration: none; text-align: center; margin-top: 20px;">Go to Login</a>
                </div>
                <?php else: ?>
                <div>
                    <h1>CREATE ACCOUNT</h1>
                    <p class="subtitle">Join us to explore authentic foods.</p>

                    <?php if($error): ?><div class="error-box"><?= e($error) ?></div><?php endif; ?>

                    <form method="POST" action="">
                        <?= csrf_field() ?>
                        <div class="input-group">
                            <label>Full Name</label>
                            <input type="text" name="name" placeholder="Enter your full name" required>
                        </div>

                        <div class="row">
                            <div class="input-group">
                                <label>Email Address</label>
                                <input type="email" name="email" placeholder="example@mail.com" required>
                            </div>
                            <div class="input-group">
                                <label>Username</label>
                                <input type="text" name="username" placeholder="Pick a username" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="input-group">
                                <label>Password</label>
                                <div class="pwd-wrapper">
                                    <input type="password" name="password" id="reg-pass" placeholder="••••••••" minlength="6" required style="padding-right: 30px;">
                                    <span class="eye-toggle" onclick="togglePass('reg-pass', this)" aria-label="Toggle password visibility">
                                        <svg viewBox="0 0 24 24" class="eye-icon"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                    </span>
                                </div>
                            </div>
                            <div class="input-group">
                                <label>Confirm Password</label>
                                <div class="pwd-wrapper">
                                    <input type="password" name="confirm_password" id="reg-cpass" placeholder="••••••••" minlength="6" required style="padding-right: 30px;">
                                    <span class="eye-toggle" onclick="togglePass('reg-cpass', this)" aria-label="Toggle password visibility">
                                        <svg viewBox="0 0 24 24" class="eye-icon"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="action-btns action-btns--signup">
                            <button type="submit" class="btn-primary">Sign Up</button>
                            <button type="button" class="btn-outline" onclick="goToPage(event, 'index.php')">Login</button>
                        </div>
                    </form>
                </div>
                <?php endif; ?>

                <p class="terms">By signing up, you agree to our <a onclick="openModal('termsModal')">Terms</a> and <a onclick="openModal('privacyModal')">Privacy Policy</a>.</p>
            </div>
        </div>

    </div>

    <!-- Modals (Sharing the same design as login) -->
    <div class="modal-backdrop" id="termsModal" onclick="if(event.target===this)closeModal('termsModal')">
        <div class="modal-content">
            <div class="modal-head">
                <h3>Terms & Conditions</h3>
                <button class="modal-close" onclick="closeModal('termsModal')"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg></button>
            </div>
            <div class="modal-body">
                <p>Welcome to <strong>Sauni</strong>. By using our platform, you agree to the following terms.</p>
                <h4>1. Use of Service</h4>
                <p>You must be at least 16 years old to use this service. You are responsible for maintaining the confidentiality of your account credentials.</p>
                <h4>2. Orders & Payments</h4>
                <p>All payments are final once an order is confirmed. Cancellations must be made within 5 minutes of placing the order.</p>
                <h4>3. Delivery</h4>
                <p>Delivery times are estimates and may vary. Sauni is not liable for delays beyond its reasonable control.</p>
            </div>
            <div class="modal-foot">
                <button class="btn-accept" onclick="closeModal('termsModal')">I Accept</button>
            </div>
        </div>
    </div>

    <div class="modal-backdrop" id="privacyModal" onclick="if(event.target===this)closeModal('privacyModal')">
        <div class="modal-content">
             <div class="modal-head">
                <h3>Privacy Policy</h3>
                <button class="modal-close" onclick="closeModal('privacyModal')"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg></button>
            </div>
            <div class="modal-body">
                <p>Your privacy is important to us. This policy explains what information we collect and how we use it. </p>
                <h4>1. Information We Collect</h4>
                <ul>
                    <li>Name, email, and username at registration</li>
                    <li>Order history and preferences</li>
                    <li>Delivery location data</li>
                </ul>
            </div>
            <div class="modal-foot">
                <button class="btn-accept" onclick="closeModal('privacyModal')">Understood</button>
            </div>
        </div>
    </div>

    <script src="assets/js/shared/auth.js"></script>
</body>

</html>

