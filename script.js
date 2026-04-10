/*
  SAUNI MAIN SCRIPTS
  This file handles the interactivity like tab switching and popup windows.
*/

// 1. Function to switch between Different Tab sections (Account, Password, History)
function changeTab(tabName, clickedButton) {
    // Hide all tab content panes
    const allPanes = document.querySelectorAll('.tab-pane');
    allPanes.forEach(function(pane) {
        pane.classList.remove('active');
    });

    // Remove "active" color from all side menu buttons
    const allButtons = document.querySelectorAll('.nav-item');
    allButtons.forEach(function(btn) {
        btn.classList.remove('active');
    });

    // Show the specific tab the user clicked on
    const targetPane = document.getElementById('pane-' + tabName);
    if (targetPane) {
        targetPane.classList.add('active');
    }

    // Highlight the button that was clicked
    clickedButton.classList.add('active');
}

// 2. Helper function to show a Success or Error message on top of the page
function showMyAlert(messageText, messageType) {
    const messageArea = document.getElementById('messageDisplayArea');
    if (!messageArea) return;
    
    // Clear any previous messages
    messageArea.innerHTML = '';
    
    // Create a new message box
    const alertBox = document.createElement('div');
    alertBox.className = "alert alert-" + messageType;
    
    // Style the box based on type (green for success, red for error)
    alertBox.style.padding = '14px 18px';
    alertBox.style.borderRadius = '10px';
    alertBox.style.marginBottom = '2rem';
    alertBox.style.fontWeight = '500';
    alertBox.style.display = 'flex';
    alertBox.style.alignItems = 'center';
    
    if (messageType === 'success') {
        alertBox.style.background = '#dcfce7'; // Light green
        alertBox.style.color = '#166534';
        alertBox.style.borderLeft = '4px solid #22c55e';
    } else {
        alertBox.style.background = '#fee2e2'; // Light red
        alertBox.style.color = '#991b1b';
        alertBox.style.borderLeft = '4px solid #ef4444';
    }
    
    alertBox.textContent = messageText;
    messageArea.appendChild(alertBox);
    
    // Scroll to top so the user sees the message
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

// 3. Logic for the Profile Picture Popup window
const popupWindow = document.getElementById('photoPopup');
const openPopupBtn = document.getElementById('openPopupBtn');
const closePopupBtn = document.getElementById('closeWindow');
const triggerFileBtn = document.getElementById('chooseFileBtn');
const realFileInput = document.getElementById('fileInput');
const mainUploadForm = document.getElementById('uploadForm');
const previewArea = document.getElementById('previewArea');
const sidebarPicture = document.getElementById('sidebarPicture');

// Open the popup when the camera icon is clicked
if (openPopupBtn) {
    openPopupBtn.addEventListener('click', function() {
        popupWindow.classList.add('show');
    });
}

// Close the popup when "Cancel" is clicked
if (closePopupBtn) {
    closePopupBtn.addEventListener('click', function() {
        popupWindow.classList.remove('show');
    });
}

// Close the popup if user clicks outside the white box
window.addEventListener('click', function(event) {
    if (event.target === popupWindow) {
        popupWindow.classList.remove('show');
    }
});

// When "Upload New Photo" is clicked, hiddenly click the real File Input
if (triggerFileBtn && realFileInput) {
    triggerFileBtn.addEventListener('click', function() {
        realFileInput.click();
    });
}

// Show a preview of the image right after user selects a file
if (realFileInput) {
    realFileInput.addEventListener('change', function() {
        if (this.files && this.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const imageTag = '<img src="' + e.target.result + '" style="width:100%; height:100%; object-fit:cover; border-radius:50%">';
                
                // Show in popup preview area
                previewArea.innerHTML = imageTag;
                
                // Show in sidebar immediately
                if (sidebarPicture) {
                    sidebarPicture.innerHTML = imageTag;
                }
                
                // Wait a tiny bit then submit the form automatically
                setTimeout(function() {
                    mainUploadForm.submit();
                }, 800);
            }
            reader.readAsDataURL(this.files[0]);
        }
    });
}

// 4. Form Validation (Check if fields are empty before sending)
const accountForm = document.getElementById('accountForm');
if (accountForm) {
    accountForm.addEventListener('submit', function(event) {
        const nameInput = document.getElementById('name');
        const emailInput = document.getElementById('email');
        const phoneInput = document.getElementById('phone');
        const addressInput = document.getElementById('address');
        
        // Remove old error highlights
        const allInputs = accountForm.querySelectorAll('.input-box');
        allInputs.forEach(input => input.classList.remove('error'));
        
        // Simple checks
        if (!nameInput.value.trim()) {
            event.preventDefault();
            nameInput.classList.add('error');
            showMyAlert('Please enter your Full Name.', 'error');
            return;
        }
        if (!emailInput.value.trim()) {
            event.preventDefault();
            emailInput.classList.add('error');
            showMyAlert('Please enter your Email Address.', 'error');
            return;
        }
        if (!phoneInput.value.trim()) {
            event.preventDefault();
            phoneInput.classList.add('error');
            showMyAlert('Please enter your Phone Number.', 'error');
            return;
        }
    });
}

// Password Validation
const passwordForm = document.getElementById('passwordForm');
if (passwordForm) {
    passwordForm.addEventListener('submit', function(event) {
        const oldPass = document.getElementById('current_password').value;
        const newPass = document.getElementById('new_password').value;
        const confirmPass = document.getElementById('confirm_password').value;
        
        if (!oldPass) {
            event.preventDefault();
            showMyAlert('Please enter your Old Password.', 'error');
            return;
        }
        if (newPass.length < 8) {
            event.preventDefault();
            showMyAlert('New Password must be at least 8 characters long.', 'error');
            return;
        }
        if (newPass !== confirmPass) {
            event.preventDefault();
            showMyAlert('Passwords do not match!', 'error');
            return;
        }
    });
}
