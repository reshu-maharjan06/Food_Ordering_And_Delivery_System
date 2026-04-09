<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="csrf-token" content="<?= csrf_token() ?>">
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Sauni Admin — Menu</title>
<link href="https://fonts.googleapis.com/css2?family=Lato:wght@300;400;700;900&family=Playfair+Display:ital,wght@0,400..900;1,400..900&display=swap" rel="stylesheet">
<link rel="stylesheet" href="../assets/css/admin/shared.css">
<link rel="stylesheet" href="../assets/css/admin/menu.css">
</head>
<body>
<div class="shell">
<aside class="sb">
  <div class="sb-brand">
    <div class="sb-brand-icon">S</div>
    <div class="sb-brand-name">Sau<span>ni</span></div>
  </div>
  <div class="sb-body">
    <div class="sb-section-label">Main</div>

    <a href="orders.php" class="sb-link">
      <div class="sb-icon"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2"/><rect x="9" y="3" width="6" height="4" rx="2"/></svg></div>
      Orders
    </a>
    <a href="menu.php" class="sb-link active">
      <div class="sb-icon"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 2v7c0 1.1.9 2 2 2h4a2 2 0 0 0 2-2V2"/><path d="M18 2v20"/><path d="M21 2v5c0 1.1-.9 2-2 2h-1"/></svg></div>
      Menu
    </a>
    <div class="sb-section-label" style="margin-top:0.5rem">Operations</div>
    <a href="delivery_staff.php" class="sb-link">
      <div class="sb-icon"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="3" width="15" height="13"/><polyline points="16 8 20 8 23 11 23 16 16 16"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg></div>
      Drivers &amp; Map
    </a>
    <a href="ratings.php" class="sb-link">
      <div class="sb-icon"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg></div>
      Reviews
    </a>
  </div>
  <div class="sb-footer">
    <div class="sb-user-wrap">
      <?php if (!empty($_SESSION['profile_pic'])): ?>
        <div class="sb-av" style="background-image:url('../<?= $_SESSION['profile_pic'] ?>'); background-size:cover; background-position:center; font-size:0; border:2px solid #fff;"></div>
      <?php else: ?>
        <div class="sb-av"><?= strtoupper(substr($_SESSION['username'] ?? 'A', 0, 1)) ?></div>
      <?php endif; ?>
      <div><div class="sb-uname"><?= htmlspecialchars($_SESSION['username'] ?? 'Admin') ?></div><div class="sb-urole">Menu Engine</div></div>
    </div>
    <a href="../logout.php" class="sb-logout">↩ Sign out</a>
  </div>
</aside>
<div class="main">
  <div class="topbar">
    <div class="topbar-left">
      <div class="topbar-title">Menu Engine</div>
      <div class="topbar-sub" id="menuCount">Catalog Management</div>
    </div>
    <div class="topbar-right">
      <div class="topbar-identity" style="display:flex; align-items:center; gap:0.6rem; background:#f9fafb; padding:0.4rem 1rem; border-radius:50px; border:1px solid #e5e7eb; margin-right: 1rem;">
        <svg width="20" height="24" viewBox="0 0 457 547" fill="none"><path d="M0 547H456.551L170.838 273.5H380.459L0 0V547Z" fill="#DC143C"/><path d="M0 547H456.551L170.838 273.5H380.459L0 0V547Z" stroke="#003594" stroke-width="30"/><circle cx="106.333" cy="413.667" r="69" fill="white"/><path d="M38.3333 154.333C38.3333 154.333 73 203.667 110.333 203.667C147.667 203.667 182.333 154.333 182.333 154.333C182.333 154.333 166.333 167 110.333 167C54.3333 167 38.3333 154.333 38.3333 154.333Z" fill="white"/></svg>
        <span style="font-size:0.75rem; font-weight:800; color:#111; letter-spacing:0.5px;">THAMEL, NEPAL</span>
      </div>
      <button class="btn btn-primary" onclick="resetMenuForm()">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        New Dish
      </button>
    </div>
  </div>
  <div class="content">
    <div class="menu-layout">
      <div class="card menu-form-wrap">
        <div class="card-head"><div class="card-title" id="form-title">Add New Dish</div></div>
        <div class="card-pad">
          <div id="imgPreview" class="img-preview">
            <div class="img-preview-label">
              <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
              <span>Image preview</span>
            </div>
          </div>
          <div class="form-group">
            <label class="form-label">Dish Name</label>
            <input class="form-control" id="m-name" type="text" placeholder="e.g. Steamed Momo">
          </div>
          <div class="form-group">
            <label class="form-label">Category</label>
            <input class="form-control" id="m-cat" type="text" placeholder="e.g. Appetizers">
          </div>
          <div class="form-group">
            <label class="form-label">Price (Rs.)</label>
            <input class="form-control" id="m-price" type="number" placeholder="450">
          </div>
          <div class="form-group">
            <label class="form-label">Dish Image</label>
            <input class="form-control" id="m-file" type="file" accept="image/*" onchange="previewSelectedImg(this)">
            <input type="hidden" id="m-img-path">
          </div>
          <div class="form-group">
            <label class="form-label">Description</label>
            <textarea class="form-control" id="m-desc" placeholder="Juicy steamed momo with spicy sauce..."></textarea>
          </div>
          <label class="checkbox-row" style="margin-bottom:1.25rem">
            <input type="checkbox" id="m-pop">
            <span class="checkbox-label">⭐ Mark as Popular</span>
          </label>
          <button class="btn btn-primary" style="width:100%" id="saveBtn" onclick="saveMenu()">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
            <span id="btnText">Add to Catalog</span>
          </button>
          <button class="btn btn-outline" style="width:100%; margin-top:0.6rem; display:none;" id="cancelBtn" onclick="resetMenuForm()">
            Cancel Edit
          </button>
        </div>
      </div>
      <div class="card menu-table-wrap">
        <div class="filter-tabs" id="filterTabs">
          <button class="filter-tab active" onclick="filterMenu('all',this)">All</button>
        </div>
        <div class="search-bar">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
          <input type="text" id="menuSearch" placeholder="Search dishes..." oninput="searchMenu(this.value)">
        </div>
        <div id="menuList"></div><div id="paginationWrap" style="margin-top:1.5rem; display:flex; align-items:center; justify-content:center; gap:1rem; border-top:1px solid var(--border); padding-top:1.2rem;"></div>
      </div>
    </div>
  </div>
</div>
<div class="modal-overlay" id="viewModal">
  <div class="modal-box" style="max-width: 600px;">
    <div class="modal-head">
      <div class="modal-title">Dish Inspection</div>
      <div class="modal-sub">Viewing standard catalog item</div>
    </div>
    <div class="view-modal-content" id="viewContent">
    </div>
    <div style="display:flex; gap:0.75rem; border-top:1px solid var(--border); padding-top:1.5rem; margin-top:1.5rem">
      <button class="btn btn-outline" onclick="closeView()" style="flex:1">Dismiss</button>
      <button class="btn btn-primary" id="vm-edit-btn" style="flex:1">Edit Details</button>
    </div>
  </div>
</div>
</div>
<script src="../assets/js/admin/menu.js"></script>
</body>
</html>

