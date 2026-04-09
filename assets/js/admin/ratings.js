
function stars(n, max=5) {
  return Array.from({length:max},(_,i)=>`<span class="star ${i<n?'on':'off'}">${i<n?'★':'☆'}</span>`).join('');
}
async function loadReviews() {
  const res = await fetch('../api.php?action=admin_ratings');
  const data = await res.json();
  const box = document.getElementById('revGrid');
  if(!data.length) {
    box.innerHTML='<div class="empty-state" style="grid-column:1/-1"><svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg><p>No reviews yet</p></div>';
    return;
  }
  const avgF = (data.reduce((s,r)=>s+(r.food_rating||r.rating||0),0)/data.length).toFixed(1);
  const avgD = (data.reduce((s,r)=>s+(r.driver_rating||r.rating||0),0)/data.length).toFixed(1);
  document.getElementById('avgFood').textContent = avgF;
  document.getElementById('avgDriver').textContent = avgD;
  document.getElementById('totalReviews').textContent = data.length;
  document.getElementById('avgRating').textContent = ((+avgF + +avgD)/2).toFixed(1) + ' / 5';
  document.getElementById('reviewSub').textContent = `${data.length} reviews · Avg ${((+avgF + +avgD)/2).toFixed(1)}/5`;
  box.innerHTML = data.map(r=>{
    const date = new Date(r.created_at).toLocaleDateString('en-US',{month:'short',day:'numeric',year:'numeric'});
    const fr = r.food_rating||r.rating||0;
    const dr = r.driver_rating||r.rating||0;
    const initials = (r.username||'?')[0].toUpperCase();
    return `
      <div class="review-card">
        <div class="rc-header">
          <div class="rc-user">
            <div class="avatar" style="width:40px;height:40px;font-size:0.9rem">${initials}</div>
            <div><div class="rc-uname">${r.username||'Anonymous'}</div><div class="rc-date">${date}</div></div>
          </div>
          <div class="rc-order">Order #${r.order_id}</div>
        </div>
        <div class="rc-stars-row">
          <div class="star-group">
            <div class="star-label">Food</div>
            <div class="star-display">${stars(fr)}</div>
          </div>
          <div class="star-group">
            <div class="star-label">Driver</div>
            <div class="star-display">${stars(dr)}</div>
          </div>
          <div class="star-group" style="margin-left:auto;text-align:right">
            <div class="star-label">Order Value</div>
            <div style="font-weight:700;font-size:0.85rem;color:var(--text)">Rs. ${r.total_amount||0}</div>
          </div>
        </div>
        <div class="rc-comment">${r.comment||'No comment provided.'}</div>
      </div>`;
  }).join('');
}
loadReviews();
