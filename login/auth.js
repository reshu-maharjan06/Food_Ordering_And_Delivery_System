
function togglePass(id, btn) {
  const input = document.getElementById(id);
  const icon = btn.querySelector("svg");

  if (input.type === "password") {
    input.type = "text";
    icon.innerHTML =
      '<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24M1 1l22 22"/>';
  } else {
    input.type = "password";
    icon.innerHTML =
      '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>';
  }
}


function openModal(id) {
  document.getElementById(id).classList.add("open");
  document.body.style.overflow = "hidden";
}

function closeModal(id) {
  document.getElementById(id).classList.remove("open");
  document.body.style.overflow = "";
}

document.addEventListener("keydown", (e) => {
  if (e.key === "Escape") {
    document
      .querySelectorAll(".modal-backdrop.open")
      .forEach((m) => m.classList.remove("open"));
    document.body.style.overflow = "";
  }
});

function showError(msg) {
  const box = document.getElementById("error-box");
  if (!box) return;
  box.textContent = msg;
  box.style.display = "block";
  document.getElementById("success-box") &&
    (document.getElementById("success-box").style.display = "none");
}

function showSuccess(msg) {
  const box = document.getElementById("success-box");
  if (!box) return;
  box.textContent = msg;
  box.style.display = "block";
  document.getElementById("error-box") &&
    (document.getElementById("error-box").style.display = "none");
}

function clearMessages() {
  document.getElementById("error-box") &&
    (document.getElementById("error-box").style.display = "none");
  document.getElementById("success-box") &&
    (document.getElementById("success-box").style.display = "none");
}

const loginForm = document.getElementById("login-form");
if (loginForm) {
  // Clear errors while typing
  loginForm
    .querySelectorAll("input")
    .forEach((i) => i.addEventListener("input", clearMessages));

  loginForm.addEventListener("submit", async (e) => {
    e.preventDefault();

    const username = document.getElementById("username").value.trim();
    const password = document.getElementById("login-pass").value;

    if (!username || !password) {
      showError("Please enter your username and password.");
      return;
    }

    showError("No backend connected yet. Add your API endpoint in auth.js.");
  });
}

const signupForm = document.getElementById("signup-form");
if (signupForm) {
  // Clear errors while typing
  signupForm
    .querySelectorAll("input")
    .forEach((i) => i.addEventListener("input", clearMessages));

  signupForm.addEventListener("submit", async (e) => {
    e.preventDefault();

    const name = document.getElementById("name").value.trim();
    const email = document.getElementById("email").value.trim();
    const username = document.getElementById("username").value.trim();
    const password = document.getElementById("reg-pass").value;
    const confirmPassword = document.getElementById("reg-cpass").value;

    // Client-side validation (mirrors the PHP checks)
    if (!name || !email || !username || !password) {
      showError("Please fill in all fields.");
      return;
    }
    if (password !== confirmPassword) {
      showError("Passwords do not match! Please try again.");
      return;
    }
    if (password.length < 6) {
      showError("Password must be at least 6 characters.");
      return;
    }

    showError("No backend connected yet. Add your API endpoint in auth.js.");
  });
}


window.addEventListener("pageshow", (e) => {
  if (e.persisted) document.body.classList.remove("page-out");
});
