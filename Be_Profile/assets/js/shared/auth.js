function togglePass(inputId, btn) {
    const input = document.getElementById(inputId);
    const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
    input.setAttribute('type', type);
    btn.classList.toggle('active');
}
function goToPage(e, url) {
    e.preventDefault();
    document.body.classList.add('fade-out');
    setTimeout(() => {
        window.location.href = url;
    }, 300);
}
function openModal(id) {
    const modal = document.getElementById(id);
    if (modal) {
        modal.classList.add('show');
        document.body.style.overflow = 'hidden';
    }
}
function closeModal(id) {
    const modal = document.getElementById(id);
    if (modal) {
        modal.classList.remove('show');
        document.body.style.overflow = 'auto';
    }
}
