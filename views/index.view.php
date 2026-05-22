<!DOCTYPE html>
<html lang="en">

<head>
    <meta name="csrf-token" content="<?= csrf_token() ?>">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sauni - Login</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700;900&family=Lato:wght@300;400;700&family=Outfit:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/pages/auth.css">
</head>

<body>

    <div class="split-layout">

        <!-- LOGIN FORM SIDE -->
        <div class="split-left">
            <div class="login-wrapper">
                <a href="landing.php" class="brand brand--login" style="font-family:'Outfit',sans-serif; font-size:1.5rem; font-weight:900; letter-spacing:0; word-spacing:0; color:#ff3b00; display:inline-flex; align-items:center; text-decoration:none;">S<span style="font-size:1em; line-height:1; display:inline-block; margin:0 -0.15em; vertical-align:-0.05em;">🔥</span>UNI</a>

                <div>
                    <h1>WELCOME BACK,</h1>
                    <p class="subtitle">Please login to your account.</p>

                    <?php if($error): ?><div class="error-box"><?= e($error) ?></div><?php endif; ?>

                    <form method="POST" action="">
                        <?= csrf_field() ?>
                        <div class="input-group">
                            <label>Username</label>
                            <input type="text" id="username" name="username" placeholder="Enter your username" required>
                        </div>

                        <div class="input-group">
                            <label>Password</label>
                            <div class="pwd-wrapper">
                                <input type="password" id="login-pass" name="password" placeholder="••••••••" required
                                    style="padding-right: 30px;">
                                <span class="eye-toggle" onclick="togglePass('login-pass', this)"
                                    aria-label="Toggle password visibility">
                                    <svg viewBox="0 0 24 24" class="eye-icon">
                                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                                        <circle cx="12" cy="12" r="3" />
                                    </svg>
                                </span>
                            </div>
                        </div>

                        <div class="form-options">
                            <label class="checkbox-label">
                                <input type="checkbox" id="stay-logged-in"> Stay logged in
                            </label>
                        </div>

                        <div class="action-btns">
                            <button type="submit" class="btn-primary">Login</button>
                            <button type="button" class="btn-outline" onclick="location.href='signup.php'">Sign up</button>
                        </div>
                    </form>
                </div>

                <p class="terms">By logging in, you agree to our <a onclick="openModal('termsModal')">Terms</a> and <a onclick="openModal('privacyModal')">Privacy Policy</a>.</p>
            </div>
        </div>

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

    </div>

    <!-- TERMS MODAL -->
    <div class="modal-backdrop" id="termsModal" onclick="if(event.target===this)closeModal('termsModal')">
        <div class="modal-content">
            <div class="modal-head">
                <h3>Terms &amp; Conditions</h3>
                <button class="modal-close" onclick="closeModal('termsModal')">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                        stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>
            </div>
            <div class="modal-body">
                <p>Welcome to <strong>Sauni</strong>. By using our platform, you agree to the following terms.</p>
                <h4>1. Use of Service</h4>
                <p>You must be at least 16 years old to use this service. You are responsible for maintaining the
                    confidentiality of your account credentials.</p>
                <h4>2. Orders &amp; Payments</h4>
                <p>All payments are final once an order is confirmed. Cancellations must be made within 5 minutes of
                    placing the order.</p>
                <h4>3. Delivery</h4>
                <p>Delivery times are estimates and may vary. Sauni is not liable for delays beyond its reasonable
                    control.</p>
                <h4>4. Ratings &amp; Reviews</h4>
                <p>Reviews must be honest. False reviews may result in account suspension.</p>
                <h4>5. Prohibited Use</h4>
                <ul>
                    <li>Do not use Sauni for any illegal purpose</li>
                    <li>Do not attempt to circumvent security measures</li>
                    <li>Do not post offensive or false reviews</li>
                </ul>
                <h4>6. Changes</h4>
                <p>Sauni reserves the right to modify these terms at any time.</p>
            </div>
            <div class="modal-foot">
                <button class="btn-accept" onclick="closeModal('termsModal')">I Accept</button>
            </div>
        </div>
    </div>

    <!-- PRIVACY MODAL -->
    <div class="modal-backdrop" id="privacyModal" onclick="if(event.target===this)closeModal('privacyModal')">
        <div class="modal-content">
            <div class="modal-head">
                <h3>Privacy Policy</h3>
                <button class="modal-close" onclick="closeModal('privacyModal')">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                        stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>
            </div>
            <div class="modal-body">
                <p>Your privacy is important to us. This policy explains what information we collect and how we use it.
                </p>
                <h4>1. Information We Collect</h4>
                <ul>
                    <li>Name, email, and username at registration</li>
                    <li>Order history and preferences</li>
                    <li>Delivery location data</li>
                    <li>Usage data and session information</li>
                </ul>
                <h4>2. How We Use Your Data</h4>
                <p>We use your data to process orders and improve our service. We never sell your personal information.
                </p>
                <h4>3. Data Security</h4>
                <p>Passwords are hashed and never stored in plain text.</p>
                <h4>4. Your Rights</h4>
                <p>Request data deletion at privacy@sauni.com.</p>
            </div>
            <div class="modal-foot">
                <button class="btn-accept" onclick="closeModal('privacyModal')">Understood</button>
            </div>
        </div>
    </div>

    <script src="assets/js/shared/auth.js"></script>
</body>

</html>

