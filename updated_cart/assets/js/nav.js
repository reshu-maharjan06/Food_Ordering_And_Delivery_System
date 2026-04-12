
function togglePf(e) {
    if (e) e.stopPropagation();
    const drop = document.getElementById('pfDrop');
    if (drop) drop.classList.toggle('open');
}
function navGo(e, url) {
    if(e && (e.ctrlKey || e.metaKey)) return; 
    if(e) e.preventDefault();
    document.body.classList.add('page-out');
    setTimeout(() => window.location.href = url, 320);
}
document.addEventListener('click', function(e) {
    const drop = document.getElementById('pfDrop');
    const wrap = document.getElementById('pfWrap');
    if (drop && wrap && !wrap.contains(e.target)) {
        drop.classList.remove('open');
    }
});
window.addEventListener('pageshow', function (event) {
    if (event.persisted) {
        document.body.classList.remove('page-out');
    }
});
