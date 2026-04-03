<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sauni — Settings</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/customer/profile.css">
</head>
<body>
<div class="page-wrap">
    <div class="header-title">
        <svg viewBox="0 0 24 24"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" y1="12" x2="3" y2="12"/></svg>
        <a href="profile.php" style="color: inherit; text-decoration: none;">Settings</a>
    </div>
    <div class="layout">
        <aside class="sidebar">
            <div class="sb-user">
                <div class="sb-av avatar">U</div>
                <div>
                    <div class="sb-uname username">User</div>
                    <div class="sb-urole">Customer</div>
                </div>
            </div>
            <nav class="sb-nav" id="settings-nav">
                <ul style="list-style: none; padding: 0; margin: 0;">
                    <li class="sb-link active" data-target="profile" style="cursor: pointer;">Edit Profile</li>
                    <li class="sb-link" data-target="password" style="cursor: pointer;">Password</li>
                    <div class="sb-divider"></div>
                    <li class="sb-link sb-danger" onclick="window.location.href='logout.php'" style="cursor: pointer;">Log Out</li>
                </ul>
            </nav>
        </aside>
        <main class="content">
            <div id="profile-section" class="form-section active tab-pane">
                <div class="content-header">
                    <h2>Edit Profile</h2>
                    <p class="subtitle">Update your personal information and delivery details.</p>
                </div>
                <div class="pane-layout">
                    <form id="profile-form" class="form-col" novalidate>
                        <div class="form-group">
                            <label>Full Name</label>
                            <input type="text" id="profile-name" name="name" class="f-input" placeholder="Loading..." required>
                            <span class="error-msg" id="profile-name-error"></span>
                        </div>
                        <div class="form-group">
                            <label>Email Address</label>
                            <input type="email" id="profile-email" name="email" class="f-input" placeholder="Loading..." disabled title="Email is locked">
                            <span class="f-desc">Your email address cannot be changed once verified.</span>
                            <span class="error-msg" id="profile-email-error"></span>
                        </div>
                        <div class="form-group">
                            <label>Phone Number</label>
                            <input type="tel" id="profile-phone" name="phone" class="f-input" placeholder="+977 98XXXX...">
                            <span class="error-msg" id="profile-phone-error"></span>
                        </div>
                        <div class="form-group">
                            <label>Default Delivery Address</label>
                            <input type="text" id="profile-address" name="address" class="f-input" placeholder="e.g. 14 Thamel Marg">
                            <span class="error-msg" id="profile-address-error"></span>
                        </div>
                        <div class="form-actions">
                            <button type="button" class="btn-cancel" onclick="resetForm('profile-form')">Cancel</button>
                            <button type="submit" class="btn-primary" style="padding: 0.75rem 1.5rem; border-radius: 8px; border: none; background: var(--primary, #ff3b00); color: white; cursor: pointer; font-family: inherit; font-weight: 500;">Update Profile</button>
                        </div>
                        <div id="profile-status" class="status-msg"></div>
                    </form>
                    <div class="info-col">
                        <div id="profile-info" class="info-card">
                            <div class="ic-head"><svg viewBox="0 0 24 24" width="20" height="20" style="margin-right: 8px;"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/><polyline points="3.27 6.96 12 12.01 20.73 6.96"/><line x1="12" y1="22.08" x2="12" y2="12"/></svg> Newsletter</div>
                            <div class="ic-desc">Keep your email updated to receive the latest authentic Nepali recipes and exclusive offers from our master chefs.</div>
                        </div>
                    </div>
                </div>
            </div>
            <div id="password-section" class="form-section tab-pane">
                <div class="content-header">
                    <h2>Password</h2>
                    <p class="subtitle">Please enter your current password to change your password.</p>
                </div>
                <div class="pane-layout">
                    <form id="password-form" class="form-col" novalidate>
                        <div class="form-group">
                            <label>Your Password</label>
                            <input type="password" id="current-password" name="current_password" class="f-input" placeholder="Current password" required>
                            <span class="error-msg" id="current-password-error"></span>
                        </div>
                        <div class="form-group">
                            <label>New Password</label>
                            <input type="password" id="new-password" name="new_password" class="f-input" placeholder="Enter your new password" minlength="8" required>
                            <span class="f-desc">Your new password must be at least 8 characters long.</span>
                            <span class="error-msg" id="new-password-error"></span>
                        </div>
                        <div class="form-group">
                            <label>Re-enter your new password</label>
                            <input type="password" id="confirm-password" name="confirm_password" class="f-input" placeholder="Confirm password" minlength="8" required>
                            <span class="error-msg" id="confirm-password-error"></span>
                        </div>
                        <div class="form-actions">
                            <button type="button" class="btn-cancel" onclick="resetForm('password-form')">Cancel</button>
                            <button type="submit" class="btn-primary" style="padding: 0.75rem 1.5rem; border-radius: 8px; border: none; background: var(--primary, #ff3b00); color: white; cursor: pointer; font-family: inherit; font-weight: 500;">Update Password</button>
                        </div>
                        <div id="password-status" class="status-msg"></div>
                    </form>
                    <div class="info-col">
                        <div id="password-info" class="info-card">
                            <div class="ic-head">
                                <svg viewBox="0 0 24 24" width="20" height="20" style="margin-right: 8px;"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                                Account Safety
                            </div>
                            <div class="ic-desc">We strongly recommend using a complex password featuring a mix of letters, numbers, and symbols to ensure your account remains secure.</div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>
<style>
    .sb-link { display: block; padding: 0.75rem 1rem; border-radius: 6px; margin-bottom: 0.5rem; transition: all 0.2s; color: var(--text-color, #333); }
    .sb-link:hover { background: var(--bg-color-hover, #f5f5f5); }
    .sb-link.active { background: #ffe4de; color: #ff3b00; font-weight: 600; }
    .sb-danger { color: #dc3545; }
    .form-section { display: none; }
    .form-section.active { display: block; }
    .status-msg { margin-top: 1rem; padding: 0.75rem; border-radius: 6px; display: none; font-size: 0.9rem; }
    .status-msg.success { background: #d4edda; color: #155724; display: block; }
    .status-msg.error { background: #f8d7da; color: #721c24; display: block; }
    .error-msg { color: #dc3545; font-size: 0.85rem; display: block; margin-top: 0.25rem; }
    .invalid .f-input { border-color: #dc3545; }
</style>
<script src="assets/js/customer/profile.js"></script>
</body>
</html>
