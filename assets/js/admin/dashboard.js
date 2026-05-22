
function updateTime() {
  document.getElementById('tbTime').textContent = new Date().toLocaleString('en-US',{weekday:'long',month:'long',day:'numeric',hour:'2-digit',minute:'2-digit'});
}
updateTime(); setInterval(updateTime,30000);
let lineChartInstance = null, barChartInstance = null, donutInstance = null;
const TARGET = 10000;
function loadDash() {
  fetch('../api.php?action=admin_dashboard').then(r=>r.json()).then(d=>{
    document.getElementById('kpi-revenue').textContent = 'Rs. '+(d.revenue_today||0);
    document.getElementById('kpi-active').textContent = d.active_orders ?? '0';
    document.getElementById('kpi-pending-delta').innerHTML = `<span class="trend-n">${d.pending_count||0} awaiting prep</span>`;
    document.getElementById('kpi-today').textContent = d.delivered_today ?? '0';
    document.getElementById('kpi-drivers').textContent = d.drivers_online ?? '0';
    const rev = d.revenue_today || 0;
    const pct = Math.min(100, Math.round((rev / TARGET) * 100));
    document.getElementById('donutPct').textContent = pct + '%';
    document.getElementById('targetAmount').textContent = 'Rs. ' + rev.toLocaleString();
    if(!donutInstance) {
      const dc = document.getElementById('donutChart').getContext('2d');
      donutInstance = new Chart(dc, {
        type:'doughnut',
        data:{labels:['Achieved','Remaining'],datasets:[{data:[pct,100-pct],backgroundColor:['#ff3b00','#f0f2f8'],borderWidth:0,hoverOffset:4}]},
        options:{cutout:'75%',responsive:true,maintainAspectRatio:true,plugins:{legend:{display:false},tooltip:{enabled:false}}}
      });
    } else {
      donutInstance.data.datasets[0].data = [pct, 100-pct];
      donutInstance.update('none');
    }
    fetch('../api.php?action=admin_menu').then(r=>r.json()).then(items=>{
      const trending = items.sort((a,b)=>(b.is_popular||0)-(a.is_popular||0)).slice(0,4);
      document.getElementById('trendingMenus').innerHTML = trending.length ? trending.map((item,i)=>{
        const imgPath = (item.image_url && !item.image_url.startsWith('http') && !item.image_url.startsWith('../')) ? '../' + item.image_url : (item.image_url || '../assets/img/default-food.jpg');
        return `
        <div class="trending-item">
          <div class="trending-num">#${i+1}</div>
          <img src="${imgPath}" class="trending-img" onerror="this.src='../assets/img/default-food.jpg'">
          <div style="flex:1;min-width:0">
            <div class="trending-name">${item.name}</div>
            <div class="trending-meta">Rs. ${item.price}</div>
          </div>
          <div class="trending-badge">⭐ Popular</div>
        </div>`;
      }).join('') : '<div class="empty-state"><p>No items</p></div>';
    });
    const orders = d.recent_orders || [];
    const tbody = document.getElementById('recentOrders');
    if(!orders.length) {
      tbody.innerHTML = '<tr><td colspan="4"><div class="empty-state"><p>No orders yet</p></div></td></tr>';
    } else {
      tbody.innerHTML = orders.slice(0,8).map(o=>`
        <tr>
          <td><div style="display:flex;align-items:center;gap:0.6rem">
            <div style="width:32px;height:32px;border-radius:8px;background:var(--accent-light);display:flex;align-items:center;justify-content:center;font-size:0.7rem;font-weight:700;color:var(--accent)">#${o.id}</div>
            <div><div style="font-weight:700;font-size:0.83rem">#${o.id}</div><div style="font-size:0.75rem;color:var(--text3)">${new Date(o.created_at).toLocaleTimeString([],{hour:'2-digit',minute:'2-digit'})}</div></div>
          </div></td>
          <td>${o.customer_name||'—'}</td>
          <td><b>Rs. ${o.total_amount}</b></td>
          <td><span class="chip chip-${o.status}">${o.status.replace('_',' ')}</span></td>
        </tr>`).join('');
    }
    const act = orders.slice(0,8);
    const colors = {pending:'#ff9e03',prepared:'#8b5cf6',assigned:'#3b82f6',picked_up:'#0ea5e9',delivered:'#10b981'};
    document.getElementById('activityFeed').innerHTML = act.length ? act.map(o=>{
      const t = new Date(o.created_at).toLocaleTimeString([],{hour:'2-digit',minute:'2-digit'});
      const col = colors[o.status]||'#9ca3af';
      return `<div class="feed-row">
        <div class="feed-dot" style="background:${col}"></div>
        <div class="feed-text">Order <b>#${o.id}</b> — ${o.customer_name||'Customer'} — <span class="chip chip-${o.status}" style="font-size:0.7rem">${o.status.replace('_',' ')}</span></div>
        <div class="feed-time">${t}</div>
      </div>`;
    }).join('') : '<div class="empty-state"><p>No activity</p></div>';
    const weekly = d.weekly_data || [];
    const days = weekly.map(w=>w.day_name||'');
    const counts = weekly.map(w=>w.count||0);
    const revenues = weekly.map(w=>w.revenue||0);
    const expenses = counts.map(c=>Math.round(c*0.4));
    if(!lineChartInstance) {
      const ctx1 = document.getElementById('lineChart').getContext('2d');
      const grad = ctx1.createLinearGradient(0,0,0,200);
      grad.addColorStop(0,'rgba(255,59,0,0.2)');
      grad.addColorStop(1,'rgba(255,59,0,0)');
      lineChartInstance = new Chart(ctx1,{
        type:'line',
        data:{labels:days,datasets:[{label:'Revenue (Rs.)',data:revenues,borderColor:'#ff3b00',backgroundColor:grad,fill:true,tension:0.45,pointBackgroundColor:'#ff3b00',pointRadius:4,pointHoverRadius:6,borderWidth:2.5},{label:'Orders',data:counts,borderColor:'#3b82f6',backgroundColor:'transparent',tension:0.45,pointBackgroundColor:'#3b82f6',pointRadius:4,pointHoverRadius:6,borderWidth:2,borderDash:[6,3]}]},
        options:{responsive:true,maintainAspectRatio:false,plugins:{legend:{labels:{font:{family:'Lato',size:11},usePointStyle:true}},tooltip:{mode:'index',intersect:false,backgroundColor:'rgba(255,255,255,0.95)',borderColor:'#e8eaf0',borderWidth:1,titleColor:'#1a1d27',bodyColor:'#6b7280'}},scales:{x:{grid:{display:false},ticks:{font:{family:'Lato',size:11}}},y:{grid:{color:'#f0f2f8'},ticks:{font:{family:'Lato',size:11}}}}}
      });
    } else {
      lineChartInstance.data.labels = days;
      lineChartInstance.data.datasets[0].data = revenues;
      lineChartInstance.data.datasets[1].data = counts;
      lineChartInstance.update('none');
    }
    if(!barChartInstance) {
      const ctx2 = document.getElementById('barChart').getContext('2d');
      barChartInstance = new Chart(ctx2,{
        type:'bar',
        data:{labels:days,datasets:[
          {label:'Orders',data:counts,backgroundColor:'#ff3b00',borderRadius:6,borderSkipped:false,maxBarThickness:30},
          {label:'Expenses (est.)',data:expenses,backgroundColor:'#94a3b8',borderRadius:6,borderSkipped:false,maxBarThickness:30}
        ]},
        options:{responsive:true,maintainAspectRatio:false,plugins:{legend:{display:false}},scales:{x:{grid:{display:false},ticks:{font:{family:'Lato',size:11}}},y:{grid:{color:'#f0f2f8'},ticks:{font:{family:'Lato',size:11}}}}}
      });
    } else {
      barChartInstance.data.labels = days;
      barChartInstance.data.datasets[0].data = counts;
      barChartInstance.data.datasets[1].data = expenses;
      barChartInstance.update('none');
    }
  });
}
loadDash();
setInterval(loadDash,8000);
