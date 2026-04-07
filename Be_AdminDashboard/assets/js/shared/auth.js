
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
    const el = document.getElementById(id);
    if (el) {
        el.classList.add("open");
        document.body.style.overflow = "hidden";
    }
}

function closeModal(id) {
    const el = document.getElementById(id);
    if (el) {
        el.classList.remove("open");
        document.body.style.overflow = "";
    }
}

document.addEventListener("keydown", (e) => {
    if (e.key === "Escape") {
        document
            .querySelectorAll(".modal-backdrop.open")
            .forEach((m) => m.classList.remove("open"));
        document.body.style.overflow = "";
    }
});

function goToPage(e, url) {
    if (e) e.preventDefault();
    document.body.classList.add("page-out");
    setTimeout(() => {
        window.location.href = url;
    }, 400);
}

window.addEventListener("pageshow", (e) => {
    if (e.persisted) document.body.classList.remove("page-out");
});
