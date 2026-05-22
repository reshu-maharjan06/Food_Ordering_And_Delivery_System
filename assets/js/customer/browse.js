
let allItems=[], activeCat='all';
fetch('../api.php?action=menu').then(r=>r.json()).then(data=>{
    allItems=data;
    const cats={};
    data.forEach(i=>{cats[i.category]=(cats[i.category]||0)+1;});
    document.getElementById('cnt-all').textContent=data.length;
    const cf=document.getElementById('catFilters');
    cf.innerHTML=Object.entries(cats).map(([c,n])=>
        `<button class="cat-filter" data-cat="${c}" onclick="setCat('${c}',this)">${c}<span class="cf-cnt">${n}</span></button>`
    ).join('');
    filterItems();
});
function setCat(c,btn){
    activeCat=c;
    document.querySelectorAll('.cat-filter').forEach(b=>b.classList.remove('on'));
    btn.classList.add('on');
    filterItems();
}
function filterItems(){
    const q=document.getElementById('searchInput').value.toLowerCase();
    let f=activeCat==='all'?allItems:allItems.filter(i=>i.category===activeCat);
    if(q) f=f.filter(i=>i.name.toLowerCase().includes(q)||(i.description||'').toLowerCase().includes(q));
    document.getElementById('gridInfo').textContent=`${f.length} dish${f.length!==1?'es':''} found`;
    renderGrid(f);
}
function renderGrid(items){
    const g=document.getElementById('menuGrid');
    if(!items.length){g.innerHTML='<div style="grid-column:1/-1;text-align:center;padding:4rem;color:#9ca3af">No dishes found.</div>';return;}
    g.innerHTML=items.map(d=>{
        const imgPath = (d.image_url && !d.image_url.startsWith('http') && !d.image_url.startsWith('../')) ? '../' + d.image_url : (d.image_url || '../assets/img/default-food.jpg');
        return `
        <div class="card">
            <img src="${imgPath}" class="card-img" alt="${d.name}" onerror="this.src='../assets/img/default-food.jpg'">
            <div class="card-body">
                <div class="card-cat">${d.category}</div>
                <div class="card-name">${d.name}</div>
                <div class="card-desc">${d.description||''}</div>
                <div class="card-foot">
                    <div class="card-price">Rs. ${d.price}</div>
                    <button class="card-add" id="add-${d.id}" onclick='addItem(${JSON.stringify(d).replace(/'/g,"&#39;")},this)' title="Add to Cart">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                    </button>
                </div>
            </div>
        </div>`;
    }).join('');
}
function addItem(item,btn){
    SauniCart.add(item,btn);
}
