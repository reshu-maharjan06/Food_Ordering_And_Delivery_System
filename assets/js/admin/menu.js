
let allItems = [], activeCat = 'all';

async function loadMenu() {
    const r = await fetch('../api.php?action=admin_menu');
    allItems = await r.json();
    buildFilterTabs();
    applyFilter();
}

function buildFilterTabs() {
    const cats = [...new Set(allItems.map(i => i.category))];
    const wrap = document.getElementById('filterTabs');
    if (!wrap) return;
    wrap.innerHTML = `<button class="filter-tab ${activeCat === 'all' ? 'active' : ''}" onclick="filterMenu('all',this)">All</button>`
        + cats.map(c => `
            <button class="filter-tab ${activeCat === c ? 'active' : ''}" onclick="filterMenu('${c}',this)">${c}</button>
        `).join('');
}

function filterMenu(cat, btn) {
    activeCat = cat;
    document.querySelectorAll('.filter-tab').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    applyFilter();
}

function searchMenu(q) {
    applyFilter();
}

function applyFilter() {
    const q = (document.getElementById('menuSearch')?.value || '').toLowerCase();
    let list = activeCat === 'all' ? allItems : allItems.filter(i => i.category === activeCat);
    if (q) list = list.filter(i => i.name.toLowerCase().includes(q) || (i.description || '').toLowerCase().includes(q));
    renderList(list);
}

function renderList(items) {
    const el = document.getElementById('menuList');
    if (!el) return;
    if (!items.length) {
        el.innerHTML = `<div class="empty-state"><p>No dishes found in this category.</p></div>`;
        return;
    }
    el.innerHTML = items.map(item => `
        <div class="menu-item-row" data-id="${item.id}">
            <img class="mi-img" src="${item.image_url ? '../' + item.image_url : '../assets/img/default-food.jpg'}" alt="${item.name}" onerror="this.src='../assets/img/default-food.jpg'">
            <div class="mi-info">
                <div class="mi-name">${item.name} ${item.is_popular ? '<span class="mi-popular">Popular</span>' : ''}</div>
                <div class="mi-cat">${item.category}</div>
            </div>
            <div class="mi-price">Rs. ${item.price}</div>
            <div class="mi-actions">
                <button class="btn btn-xs btn-outline" onclick="editItem(${item.id})" title="Edit">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                </button>
                <button class="btn btn-xs btn-outline btn-view-eye" onclick="viewItem(${item.id})" title="View">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                </button>
                <button class="btn btn-xs btn-danger" onclick="deleteItem(${item.id})" title="Delete">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/><path d="M9 6V4h6v2"/></svg>
                </button>
            </div>
        </div>
    `).join('');
}

function previewSelectedImg(input) {
    const prev = document.getElementById('imgPreview');
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            prev.innerHTML = `<img src="${e.target.result}" style="width:100%;height:100%;object-fit:cover;border-radius:10px;">`;
            prev.style.background = 'none';
        }
        reader.readAsDataURL(input.files[0]);
    }
}

function updateFormImgPreview(path) {
    const prev = document.getElementById('imgPreview');
    if (!prev) return;
    if (path) {
        prev.innerHTML = `<img src="../${path}" style="width:100%;height:100%;object-fit:cover;border-radius:10px;" onerror="updateFormImgPreview('')">`;
        prev.style.background = 'none';
    } else {
        prev.innerHTML = `
            <div class="img-preview-label">
              <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
              <span>Image preview</span>
            </div>
        `;
    }
}

function resetMenuForm() {
    const ids = ['m-name', 'm-cat', 'm-price', 'm-file', 'm-img-path', 'm-desc'];
    ids.forEach(id => {
        const el = document.getElementById(id);
        if (el) el.value = '';
    });
    const pop = document.getElementById('m-pop');
    if (pop) pop.checked = false;
    
    window.currentEditId = null;
    document.getElementById('form-title').textContent = 'Add New Dish';
    document.getElementById('btnText').textContent = 'Add to Catalog';
    document.getElementById('cancelBtn').style.display = 'none';
    updateFormImgPreview('');
}

function editItem(id) {
    const item = allItems.find(i => i.id == id);
    if (!item) return;
    window.currentEditId = id;
    document.getElementById('m-name').value = item.name;
    document.getElementById('m-cat').value = item.category;
    document.getElementById('m-price').value = item.price;
    document.getElementById('m-img-path').value = item.image_url || '';
    document.getElementById('m-desc').value = item.description || '';
    document.getElementById('m-pop').checked = !!item.is_popular;
    
    updateFormImgPreview(item.image_url);
    document.getElementById('form-title').textContent = 'Edit Dish Detail';
    document.getElementById('btnText').textContent = 'Update Dish';
    document.getElementById('cancelBtn').style.display = 'block';
    
    document.querySelector('.menu-form-wrap').scrollIntoView({ behavior: 'smooth' });
}

function viewItem(id) {
    const item = allItems.find(i => i.id == id);
    if (!item) return;
    const content = document.getElementById('viewContent');
    content.innerHTML = `
        <div style="display:grid;grid-template-columns:180px 1fr;gap:2rem">
            <img src="${item.image_url ? '../' + item.image_url : '../assets/img/default-food.jpg'}" style="width:180px;height:180px;object-fit:cover;border-radius:14px;box-shadow:var(--shadow-md)" onerror="this.src='../assets/img/default-food.jpg'">
            <div>
                <div style="font-size:1.5rem;font-weight:900;color:var(--text);margin-bottom:0.25rem">${item.name}</div>
                <div class="chip chip-prepared" style="margin-bottom:1.5rem">${item.category}</div>
                <div style="font-size:1.4rem;font-weight:800;color:var(--accent);margin-bottom:1.5rem">Rs. ${item.price}</div>
                <div style="color:var(--text2);line-height:1.6;font-size:0.95rem">${item.description || 'No description provided.'}</div>
            </div>
        </div>
    `;
    document.getElementById('vm-edit-btn').onclick = () => { closeView(); editItem(id); };
    document.getElementById('viewModal').classList.add('open');
}

function closeView() {
    document.getElementById('viewModal').classList.remove('open');
}

async function saveMenu() {
    const fd = new FormData();
    fd.append('id', window.currentEditId || '');
    fd.append('name', document.getElementById('m-name').value);
    fd.append('category', document.getElementById('m-cat').value);
    fd.append('price', document.getElementById('m-price').value);
    fd.append('description', document.getElementById('m-desc').value);
    fd.append('is_popular', document.getElementById('m-pop').checked ? 1 : 0);
    
    const fileInput = document.getElementById('m-file');
    if (fileInput.files.length > 0) {
        fd.append('image_file', fileInput.files[0]);
    } else {
        fd.append('existing_image', document.getElementById('m-img-path').value);
    }
    
    if (!fd.get('name') || !fd.get('price')) {
        alert('Please provide at least a name and price.');
        return;
    }
    
    const action = window.currentEditId ? 'edit_menu' : 'add_menu';
    const r = await fetch('../api.php?action=' + action, {
        method: 'POST',
        body: fd
    });
    
    const d = await r.json();
    if (d.success) {
        resetMenuForm();
        loadMenu();
    } else {
        alert(d.error || 'Failed to save');
    }
}

async function deleteItem(id) {
    if (!confirm('Permanent delete this dish from catalog?')) return;
    const r = await fetch('../api.php?action=delete_menu', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id })
    });
    const d = await r.json();
    if (d.success) loadMenu();
    else alert(d.error || 'Failed to delete');
}

document.addEventListener('DOMContentLoaded', loadMenu);
