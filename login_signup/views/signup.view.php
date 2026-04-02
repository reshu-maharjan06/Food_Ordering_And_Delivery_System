<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Sauni Platform - Join us today">
    <title>Sauni - Create Account</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700;900&family=Lato:wght@300;400;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/auth.css">
</head>
<body>

    <div class="split-layout">
        <!-- VISUAL COLLAGE -->
        <div class="split-right">
            <div class="slice" style="background-image: url('assets/img/foods/sekuwa.png');"></div>
            <div class="slice" style="background-image: url('assets/img/foods/kwantiDal.png');"></div>
            <div class="slice" style="background-image: url('assets/img/foods/thukpa.png');"></div>
            <div class="slice" style="background-image: url('assets/img/foods/Tharu/n9u3ktfj61h.webp');"></div>
        </div>

        <!-- SIGNUP FORM SIDE -->
        <div class="split-left">
            <div class="login-wrapper">
                <a href="index.php" class="brand">
                    <img src="assets/img/logo.svg" alt="Sauni" style="height: 50px; margin-bottom: 20px;">
                </a>

                <?php if (!empty($success)): ?>
                    <div class="success-state">
                        <div class="icon-circle">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="20 6 9 17 4 12"/>
                            </svg>
                        </div>
                        <h1>Account Created!</h1>
                        <p><?= htmlspecialchars($success) ?></p>
                        <a href="index.php" class="btn-primary" style="display: block; text-decoration: none; text-align: center; margin-top: 20px;">Go to Login</a>
                    </div>
                <?php else: ?>
                    <div>
                        <h1>CREATE ACCOUNT</h1>
                        <p class="subtitle">Join Sauni to explore authentic foods.</p>

                        <div class="error-box" id="signup-error" style="display: <?= !empty($error) ? 'block' : 'none' ?>;">
                            <?= htmlspecialchars($error ?? '') ?>
                        </div>

                        <form id="signup-form" method="POST">
                            <div class="input-group">
                                <label>Full Name</label>
                                <input type="text" name="name" placeholder="Enter your full name" required autocomplete="name">
                            </div>

                            <div class="row">
                                <div class="input-group">
                                    <label>Email Address</label>
                                    <input type="email" name="email" placeholder="example@mail.com" required autocomplete="email">
                                </div>
                                <div class="input-group">
                                    <label>Username</label>
                                    <input type="text" name="username" placeholder="Pick a username" required autocomplete="username">
                                </div>
                            </div>

                            <div class="row">
                                <div class="input-group">
                                    <label>Password</label>
                                    <div class="pwd-wrapper">
                                        <input type="password" id="reg-pass" name="password" placeholder="••••••••" minlength="6" required autocomplete="new-password">
                                        <span class="eye-toggle" onclick="togglePass('reg-pass', this)" aria-label="Toggle password visibility">
                                            <svg viewBox="0 0 24 24" style="width:18px;height:18px;stroke:currentColor;stroke-width:2;fill:none;stroke-linecap:round;stroke-linejoin:round;">
                                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>
                                            </svg>
                                        </span>
                                    </div>
                                </div>
                                <div class="input-group">
                                    <label>Confirm Password</label>
                                    <div class="pwd-wrapper">
                                        <input type="password" id="reg-cpass" name="confirm_password" placeholder="••••••••" minlength="6" required autocomplete="new-password">
                                        <span class="eye-toggle" onclick="togglePass('reg-cpass', this)" aria-label="Toggle password visibility">
                                            <svg viewBox="0 0 24 24" style="width:18px;height:18px;stroke:currentColor;stroke-width:2;fill:none;stroke-linecap:round;stroke-linejoin:round;">
                                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>
                                            </svg>
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div class="action-btns">
                                <button type="submit" class="btn-primary">Sign Up</button>
                                <a href="index.php" class="btn-outline">Login</a>
                            </div>
                        </form>
                    </div>
                <?php endif; ?>

                <p class="terms">By signing up, you agree to our 
                    <a onclick="openModal('termsModal')">Terms</a> and 
                    <a onclick="openModal('privacyModal')">Privacy Policy</a>.
                </p>
            </div>
        </div>
    </div>

    <!-- Modals -->
    <div class="modal-backdrop" id="termsModal" onclick="if(event.target===this)closeModal('termsModal')">
        <div class="modal-content">
            <h3>Terms & Conditions</h3><br>
            <p style="font-size: 13px; color: #666; line-height: 1.6;">Welcome to Sauni. By joining, you are joining a community focused on authentic experiences. Your data is handled securely and we never share your credentials with third parties.</p>
            <button class="btn-primary" onclick="closeModal('termsModal')" style="margin-top: 20px; width: auto; padding: 10px 30px;">I Understand</button>
        </div>
    </div>

    <script src="assets/js/auth.js"></script>
</body>
</html>
