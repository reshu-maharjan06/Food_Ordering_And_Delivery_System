<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="csrf-token" content="<?= csrf_token() ?>">
    <meta charset="UTF-8">
    <title>Sauni — Settings</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;700;900&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/shared/global.css">
    <link rel="stylesheet" href="../assets/css/shared/nav.css">
    <link rel="stylesheet" href="../assets/css/customer/profile.css">
</head>
<body>
<?php $activeNav = 'profile'; include __DIR__ . '/../../includes/customer-nav.php'; ?>
<div class="page-wrap">
    <div class="header-title">
        <svg viewBox="0 0 24 24"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" y1="12" x2="3" y2="12"/></svg>
        Settings
    </div>
    <div class="layout">
        <aside class="sidebar">
            <div class="sb-user">
                <div class="sb-av-wrapper">
                    <?php if (!empty($user['profile_pic'])): ?>
                        <div class="sb-av" style="background-image:url('../<?= $user['profile_pic'] ?>'); background-size:cover; background-position:center; font-size:0;"></div>
                    <?php else: ?>
                        <div class="sb-av"><?= strtoupper(substr($user['name'] ?? 'U', 0, 1)) ?></div>
                    <?php endif; ?>
                    <button class="sb-av-edit" onclick="openAvModal()">
                        <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/><circle cx="12" cy="13" r="4"/></svg>
                    </button>
                </div>

                <!-- PROFESSIONAL MODAL -->
                <div class="modal-overlay" id="avModal">
                    <div class="modal-content">
                        <div class="modal-title">Profile Photo</div>
                        
                        <div class="modal-preview-wrap">
                            <?php if (!empty($user['profile_pic'])): ?>
                                <div class="modal-preview" style="background-image:url('../<?= $user['profile_pic'] ?>'); background-size:cover; background-position:center;"></div>
                            <?php else: ?>
                                <div class="modal-preview no-pic"><?= strtoupper(substr($user['name'] ?? 'U', 0, 1)) ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="modal-actions">
                            <label for="prof_upload" class="modal-btn primary">Change Photo</label>
                            <?php if (!empty($user['profile_pic'])): ?>
                                <button type="button" class="modal-btn-link danger" onclick="confirmDeletePic()">Remove Photo</button>
                            <?php endif; ?>
                        </div>
                        <div class="modal-close" onclick="closeAvModal()">Cancel</div>
                    </div>
                </div>

                <form id="delPicForm" method="POST" style="display:none;">
                    <input type="hidden" name="action_type" value="delete_pic">
                </form>
                <div>
                    <div class="sb-uname"><?= htmlspecialchars($user['name'] ?? 'Unknown User') ?></div>
                    <div class="sb-urole">Customer / Kathmandu</div>
                </div>
            </div>
            <nav class="sb-nav">
                <button class="sb-link active" onclick="switchTab('profile', this)">Account</button>
                <button class="sb-link" onclick="switchTab('password', this)">Password</button>
                <button class="sb-link" onclick="window.location.href='history.php'">Order History</button>
                <div class="sb-divider"></div>
                <button class="sb-link sb-danger" onclick="window.location.href='../logout.php'">Log Out</button>
            </nav>
        </aside>
        <main class="content">
            <?php if ($success): ?><div class="alert alert-success"><?= $success ?></div><?php endif; ?>
            <?php if ($error): ?><div class="alert alert-error"><?= $error ?></div><?php endif; ?>
            <div id="tab-profile" class="tab-pane active">
                <div class="content-header">
                    <h2>Account Information</h2>
                    <p class="subtitle">Update your personal information and profile picture.</p>
                </div>
                <div class="pane-layout">
                    <form method="POST" class="form-col" enctype="multipart/form-data" id="profileForm">
                        <?= csrf_field() ?>
                        <input type="file" id="prof_upload" name="profile_pic" style="display:none;" onchange="this.form.submit()">
                        <input type="hidden" name="action_type" value="profile">
                        <div class="form-group">
                            <label>Full Name</label>
                            <input type="text" name="name" class="f-input" value="<?= e($user['name'] ?? '') ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Email Address</label>
                            <input type="email" class="f-input" value="<?= e($user['email'] ?? '') ?>" disabled title="Email is locked">
                            <span class="f-desc">Your email address cannot be changed once verified.</span>
                        </div>
                        <div class="form-group">
                            <label>Phone Number</label>
                            <input type="tel" name="phone" class="f-input" placeholder="+977 98XXXX..." value="<?= e($user['phone'] ?? '') ?>">
                        </div>
                        <div class="form-group">
                            <label>Default Delivery Address</label>
                            <input type="text" name="address" class="f-input" placeholder="e.g. 14 Thamel Marg" value="<?= e($user['address'] ?? '') ?>">
                        </div>
                        <div class="form-actions">
                            <button type="button" class="btn-cancel" onclick="window.location.reload()">Cancel</button>
                            <button type="submit" class="btn-update">Update Profile</button>
                        </div>
                    </form>
                    <div class="info-col">
                        <div class="info-card">
                            <div class="ic-head"><svg viewBox="0 0 24 24"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/><polyline points="3.27 6.96 12 12.01 20.73 6.96"/><line x1="12" y1="22.08" x2="12" y2="12"/></svg> Newsletter</div>
                            <div class="ic-desc">Keep your email updated to receive the latest authentic Nepali recipes and exclusive offers from our master chefs.</div>
                        </div>
                    </div>
                </div>
            </div>
            <div id="tab-password" class="tab-pane">
                <div class="content-header">
                    <h2>Password</h2>
                    <p class="subtitle">Please enter your current password to change your password.</p>
                </div>
                <div class="pane-layout">
                    <form method="POST" class="form-col">
                        <?= csrf_field() ?>
                        <input type="hidden" name="action_type" value="password">
                        <div class="form-group">
                            <label>Your Password</label>
                            <input type="password" name="current_password" class="f-input" placeholder="Current password" required>
                        </div>
                        <div class="form-group">
                            <label>New Password</label>
                            <input type="password" name="new_password" class="f-input" placeholder="Enter your new password" minlength="6" required>
                            <span class="f-desc">Your new password must be at least 6 characters long.</span>
                        </div>
                        <div class="form-group">
                            <label>Re-enter your new password</label>
                            <input type="password" name="confirm_password" class="f-input" placeholder="Confirm password" minlength="6" required>
                        </div>
                        <div class="form-actions">
                            <button type="button" class="btn-cancel" onclick="document.forms[1].reset()">Cancel</button>
                            <button type="submit" class="btn-update">Update Password</button>
                        </div>
                    </form>
                    <div class="info-col">
                        <div class="info-card">
                            <div class="ic-head">
                                <svg viewBox="0 0 24 24"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
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
<script>
    function openAvModal() {
        document.getElementById('avModal').classList.add('show');
    }
    function closeAvModal() {
        document.getElementById('avModal').classList.remove('show');
    }
    function confirmDeletePic() {
        if (confirm("Permanently remove your profile picture?")) {
            document.getElementById('delPicForm').submit();
        }
    }
    window.onclick = function(event) {
        const modal = document.getElementById('avModal');
        if (event.target == modal) closeAvModal();
    }
</script>
<script src="../assets/js/customer/profile.js"></script>
<script src="../assets/js/sauni-cart.js"></script>
    <script src="../assets/js/shared/nav.js"></script>
</body>
</html>

