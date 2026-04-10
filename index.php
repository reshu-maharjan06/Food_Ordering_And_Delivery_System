<?php
/**
 * Sauni - User Settings Page
 * This is the main page where users can update their profile and password.
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings - Sauni</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700;800&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Main Style Sheet (Standardized name) -->
    <link rel="stylesheet" href="style.css?v=<?= time() ?>">
</head>
<body>

<!-- Popup box to change profile picture -->
<div class="modal-overlay" id="photoPopup">
    <div class="modal-content">
        <div class="modal-title">Profile Picture</div>

        <div class="modal-preview-wrap">
            <div class="modal-preview <?= empty($user['profile_pic']) ? 'no-pic' : '' ?>" id="previewArea">
                <?php if (!empty($user['profile_pic'])): ?>
                    <img src="../<?= $user['profile_pic'] ?>" style="width:100%; height:100%; object-fit:cover; border-radius:50%">
                <?php else: ?>
                    <?= strtoupper(substr($user['name'] ?? 'A', 0, 1)) ?>
                <?php endif; ?>
            </div>
        </div>

        <div class="modal-actions">
            <!-- Form to upload a new photo -->
            <form method="POST" enctype="multipart/form-data" id="uploadForm">
                <input type="hidden" name="action_type" value="profile">
                <input type="hidden" name="csrf_token" id="csrfUpload" value="<?= $csrf_token ?? '' ?>">
                
                <input type="file" id="fileInput" name="profile_pic" accept=".jpg,.jpeg,.png,.webp" style="display:none">

                <button type="button" class="modal-btn primary" id="chooseFileBtn">
                    Upload New Photo
                </button>
            </form>

            <!-- Form to remove current photo -->
            <form method="POST" id="removePhotoForm">
                <input type="hidden" name="action_type" value="delete_pic">
                <input type="hidden" name="csrf_token" id="csrfDelete" value="<?= $csrf_token ?? '' ?>">
                <button type="submit" class="modal-btn-link danger">
                    Remove Current Photo
                </button>
            </form>
        </div>

        <div class="modal-close" id="closeWindow">Cancel</div>
    </div>
</div>

<div class="page-init">
    <div class="page-container">

        <!-- Top Header with Back Arrow -->
        <div class="title-section">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M5 12H19M19 12L13 6M19 12L13 18" stroke="#ff3b00" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            <h1>Settings</h1>
        </div>

        <div class="main-wrapper">

            <!-- Sidebar with Profile Summary -->
            <aside class="sidebar">
                
                <div class="profile-card">
                    <div class="avatar-container">
                        <div class="avatar" id="sidebarPicture">
                            <?php if (!empty($user['profile_pic'])): ?>
                                <img src="../<?= $user['profile_pic'] ?>" alt="Profile">
                            <?php else: ?>
                                <?= strtoupper(substr($user['name'] ?? 'A', 0, 1)) ?>
                            <?php endif; ?>
                        </div>
                        <!-- Click this to open the popup -->
                        <div class="camera-trigger" id="openPopupBtn" title="Update Photo">
                            <span class="camera-label">Update Photo</span>
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/><circle cx="12" cy="13" r="4"/>
                            </svg>
                        </div>
                    </div>

                    <div class="user-meta">
                        <h3 id="sidebarUserName"><?= htmlspecialchars($user['name'] ?? 'User') ?></h3>
                        <p>Customer / Kathmandu</p>
                    </div>
                </div>

                <!-- Left Navigation Menu -->
                <nav class="side-nav">
                    <button class="nav-item active" id="tabBtnAccount" onclick="changeTab('account', this)">Account</button>
                    <button class="nav-item" id="tabBtnPassword" onclick="changeTab('password', this)">Password</button>
                    <button class="nav-item" id="tabBtnHistory" onclick="changeTab('history', this)">Order History</button>
                    
                    <div class="nav-divider"></div>
                    
                    <a href="../logout.php" class="nav-logout">Log Out</a>
                </nav>

            </aside>

            <!-- Main Form Content -->
            <main class="content-area">

                <!-- This area shows messages (Success or Error) -->
                <div id="messageDisplayArea"></div>

                <!-- Section: ACCOUNT -->
                <div class="tab-pane active" id="pane-account">
                    <div class="section-head">
                        <h2>Account Information</h2>
                        <p>Update your personal details here.</p>
                    </div>

                    <div class="pane-flex">
                        <div class="form-container">
                            <form method="POST" id="accountForm">
                                <input type="hidden" name="action_type" value="profile">
                                <input type="hidden" name="csrf_token" value="<?= $csrf_token ?? '' ?>">

                                <div class="form-field">
                                    <label class="field-label" for="name">Full Name</label>
                                    <input type="text" id="name" name="name" class="input-box" 
                                           value="<?= htmlspecialchars($user['name'] ?? '') ?>" 
                                           placeholder="e.g. Shreya Joshi">
                                </div>

                                <div class="form-field">
                                    <label class="field-label" for="email">Email Address</label>
                                    <input type="email" id="email" name="email" class="input-box" 
                                           value="<?= htmlspecialchars($user['email'] ?? '') ?>" 
                                           placeholder="e.g. shreya@example.com">
                                </div>


                                <div class="form-field">
                                    <label class="field-label" for="phone">Phone Number</label>
                                    <input type="text" id="phone" name="phone" class="input-box" 
                                           value="<?= htmlspecialchars($user['phone'] ?? '') ?>" 
                                           placeholder="+977 98XXXX...">
                                </div>

                                <div class="form-field">
                                    <label class="field-label" for="address">Delivery Address</label>
                                    <input type="text" id="address" name="address" class="input-box" 
                                           value="<?= htmlspecialchars($user['address'] ?? '') ?>" 
                                           placeholder="e.g. 14 Thamel Marg">
                                </div>

                                <div class="form-footer">
                                    <button type="reset" class="btn-ghost">Reset</button>
                                    <button type="submit" class="btn-primary">Save Changes</button>
                                </div>
                            </form>
                        </div>

                        <!-- Extra Info Column -->
                        <div class="side-info">
                            <div class="card-lite">
                                <div class="card-icon">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M21 16V8L12 3L3 8V16L12 21L21 16Z" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M12 21V12" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M12 12L21 8" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M12 12L3 8" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </div>
                                <h4>Stay Updated</h4>
                                <p>Make sure your email is correct to receive the latest news and discounts.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section: PASSWORD -->
                <div class="tab-pane" id="pane-password">
                    <div class="section-head">
                        <h2>Security</h2>
                        <p>Change your password here.</p>
                    </div>
                    
                    <div class="pane-flex">
                        <div class="form-container">
                            <form method="POST" id="passwordForm">
                                <input type="hidden" name="action_type" value="password">
                                <input type="hidden" name="csrf_token" value="<?= $csrf_token ?? '' ?>">

                                <div class="form-field">
                                    <label class="field-label" for="current_password">Old Password</label>
                                    <input type="password" id="current_password" name="current_password" class="input-box" placeholder="••••••••">
                                </div>

                                <div class="form-field">
                                    <label class="field-label" for="new_password">New Password</label>
                                    <input type="password" id="new_password" name="new_password" class="input-box" placeholder="Min. 8 characters">
                                </div>

                                <div class="form-field">
                                    <label class="field-label" for="confirm_password">Confirm New Password</label>
                                    <input type="password" id="confirm_password" name="confirm_password" class="input-box" placeholder="••••••••">
                                </div>

                                <div class="form-footer">
                                    <button type="reset" class="btn-ghost">Reset</button>
                                    <button type="submit" class="btn-primary">Update Password</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Section: HISTORY -->
                <div class="tab-pane" id="pane-history">
                    <div class="section-head">
                        <h2>Order History</h2>
                        <p>Orders you made in the past.</p>
                    </div>
                    <div class="card-lite" style="text-align:center; padding:4rem 2rem;">
                        <p style="color:#999;">You haven't ordered anything yet!</p>
                    </div>
                </div>

            </main>
        </div>
    </div>
</div>

<!-- Main Script (Standardized name) -->
<script src="script.js?v=<?= time() ?>"></script>
</body>
</html>
