<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Sauni Platform - Secure Login">
    <title>Sauni - Login</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700;900&family=Lato:wght@300;400;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/auth.css">
</head>
<body>

    <div class="split-layout">
        <div class="split-left">
            <div class="login-wrapper">
                <a href="index.php" class="brand">
                    <img src="assets/img/logo.svg" alt="Sauni Logo" style="height: 50px; margin-bottom: 20px;">
                </a>

                <div>
                    <h1>WELCOME BACK,</h1>
                    <p class="subtitle">Please login to your account.</p>

                    <div class="error-box" id="login-error" style="display: <?= !empty($error) ? 'block' : 'none' ?>;">
                        <?= htmlspecialchars($error ?? '') ?>
                    </div>

                    <form id="login-form" method="POST">
                        <div class="input-group">
                            <label>Username or Email</label>
                            <input type="text" name="username" placeholder="Enter your username" required autocomplete="username">
                        </div>

                        <div class="input-group">
                            <label>Password</label>
                            <div class="pwd-wrapper">
                                <input type="password" id="login-pass" name="password" placeholder="••••••••" required autocomplete="current-password"
                                    style="padding-right: 30px;">
                                <span class="eye-toggle" onclick="togglePass('login-pass', this)"
                                    aria-label="Toggle password visibility">
                                    <svg viewBox="0 0 24 24" style="width:18px;height:18px;stroke:currentColor;stroke-width:2;fill:none;stroke-linecap:round;stroke-linejoin:round;">
                                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                                        <circle cx="12" cy="12" r="3" />
                                    </svg>
                                </span>
                            </div>
                        </div>

                        <div class="form-options">
                            <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                                <input type="checkbox" style="accent-color: #111;"> Stay logged in
                            </label>
                            <a href="#" class="forgot">Forgot password?</a>
                        </div>

                        <div class="action-btns">
                            <button type="submit" class="btn-primary">Login</button>
                            <a href="signup.php" class="btn-outline">Sign up</a>
                        </div>
                    </form>
                </div>

                <p class="terms">By logging in, you agree to our 
                    <a onclick="openModal('termsModal')">Terms</a> and 
                    <a onclick="openModal('privacyModal')">Privacy Policy</a>.
                </p>
            </div>
        </div>

        <div class="split-right">
            <div class="slice" style="background-image: url('assets/img/foods/sekuwa.png');"></div>
            <div class="slice" style="background-image: url('assets/img/foods/kwantiDal.png');"></div>
            <div class="slice" style="background-image: url('assets/img/foods/thukpa.png');"></div>
            <div class="slice" style="background-image: url('assets/img/foods/Tharu/n9u3ktfj61h.webp');"></div>
        </div>
    </div>

    <div class="modal-backdrop" id="termsModal" onclick="if(event.target===this)closeModal('termsModal')">
        <div class="modal-content">
            <h3>Terms & Conditions</h3><br>
            <p style="font-size: 13px; color: #666; line-height: 1.6;">By using Sauni, you agree to our service terms. All payments are final once confirmed. Delivery times are estimates. Users must be at least 16 years old.</p>
            <button class="btn-primary" onclick="closeModal('termsModal')" style="margin-top: 20px; width: auto; padding: 10px 30px;">I Accept</button>
        </div>
    </div>

    <script src="assets/js/auth.js"></script>
</body>
</html>
