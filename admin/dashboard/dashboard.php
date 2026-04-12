<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sauni Admin — Dashboard</title>
    <link
        href="https://fonts.googleapis.com/css2?family=Lato:wght@300;400;700;900&family=Playfair+Display:ital,wght@0,400..900;1,400..900&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="../admin-style.css">
    <link rel="stylesheet" href="../dashboard/dashboard.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
</head>

<body>

    <div class="shell">

        <!-- ── SIDEBAR ── -->
        <aside class="sb">
            <div class="sb-brand">
                <div class="sb-brand-icon">S</div>
                <div class="sb-brand-name">Sau<span>ni</span></div>
                <img src="../images/logo.png" onerror="this.style.display='none'" alt="Sauni Logo"
                    style="height:35px;width:auto;border-radius:8px;">
            </div>

            <div class="sb-body">
                <div class="sb-section-label">Main</div>

                <a href="../dashboard/dashboard.php" class="sb-link active">
                    <div class="sb-icon">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="3" width="7" height="7" />
                            <rect x="14" y="3" width="7" height="7" />
                            <rect x="14" y="14" width="7" height="7" />
                            <rect x="3" y="14" width="7" height="7" />
                        </svg>
                    </div>
                    Dashboard
                </a>

                <a href="orders.html" class="sb-link">
                    <div class="sb-icon">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2" />
                            <rect x="9" y="3" width="6" height="4" rx="2" />
                        </svg>
                    </div>
                    Orders
                </a>

                <a href="../menu/menu.php" class="sb-link">
                    <div class="sb-icon">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M3 2v7c0 1.1.9 2 2 2h4a2 2 0 0 0 2-2V2" />
                            <path d="M18 2v20" />
                            <path d="M21 2v5c0 1.1-.9 2-2 2h-1" />
                        </svg>
                    </div>
                    Menu
                </a>

                <div class="sb-section-label" style="margin-top:0.5rem">Operations</div>

                <a href="delivery-staff.html" class="sb-link">
                    <div class="sb-icon">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="1" y="3" width="15" height="13" />
                            <polyline points="16 8 20 8 23 11 23 16 16 16" />
                            <circle cx="5.5" cy="18.5" r="2.5" />
                            <circle cx="18.5" cy="18.5" r="2.5" />
                        </svg>
                    </div>
                    Drivers &amp; Map
                </a>

                <a href="ratings.html" class="sb-link">
                    <div class="sb-icon">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polygon
                                points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2" />
                        </svg>
                    </div>
                    Reviews
                </a>
            </div>

            <div class="sb-footer">
                <div class="sb-user-wrap">
                    <div class="sb-av" id="sb-av-initial">A</div>
                    <div>
                        <div class="sb-uname" id="sb-uname">Admin</div>
                        <div class="sb-urole">Platform Manager</div>
                    </div>
                </div>
                <a href="#" class="sb-logout" id="logout-link">↩ Sign out</a>
            </div>
        </aside>

        <!-- ── MAIN ── -->
        <div class="main">

            <!-- TOPBAR -->
            <div class="topbar">
                <div class="topbar-left">
                    <div class="topbar-title">Dashboard</div>
                    <div class="topbar-sub" id="tbTime">—</div>
                </div>
                <div class="topbar-right">
                    <div class="topbar-identity"
                        style="display:flex;align-items:center;gap:0.6rem;background:#f9fafb;padding:0.4rem 1rem;border-radius:50px;border:1px solid #e5e7eb;">
                        <span style="font-size:0.75rem;font-weight:800;color:#111;letter-spacing:0.5px;">NEPAL
                            STACK</span>
                    </div>
                    <div class="topbar-live">
                        <div class="live-pulse"></div> Live
                    </div>
                </div>
            </div>

            <!-- CONTENT -->
            <div class="content">

                <!-- KPI CARDS -->
                <div class="kpi-grid">
                    <div class="kpi-card">
                        <div class="kpi-top">
                            <div class="kpi-label">Daily Revenue</div>
                            <div class="kpi-icon kpi-icon-0">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <line x1="12" y1="1" x2="12" y2="23" />
                                    <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6" />
                                </svg>
                            </div>
                        </div>
                        <div class="kpi-val" id="kpi-revenue">—</div>
                        <div class="kpi-trend trend-up" id="kpi-revenue-trend">↑ Today's earnings</div>
                    </div>

                    <div class="kpi-card">
                        <div class="kpi-top">
                            <div class="kpi-label">Total Orders</div>
                            <div class="kpi-icon kpi-icon-1">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path
                                        d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2" />
                                    <rect x="9" y="3" width="6" height="4" rx="2" />
                                </svg>
                            </div>
                        </div>
                        <div class="kpi-val" id="kpi-active">—</div>
                        <div class="kpi-trend" id="kpi-pending-delta"><span class="trend-n">— active orders</span></div>
                    </div>

                    <div class="kpi-card">
                        <div class="kpi-top">
                            <div class="kpi-label">Delivered Today</div>
                            <div class="kpi-icon kpi-icon-2">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="20 6 9 17 4 12" />
                                </svg>
                            </div>
                        </div>
                        <div class="kpi-val" id="kpi-today">—</div>
                        <div class="kpi-trend trend-up">↑ Daily completions</div>
                    </div>

                    <div class="kpi-card">
                        <div class="kpi-top">
                            <div class="kpi-label">Riders Online</div>
                            <div class="kpi-icon kpi-icon-3">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="1" y="3" width="15" height="13" />
                                    <polyline points="16 8 20 8 23 11 23 16 16 16" />
                                    <circle cx="5.5" cy="18.5" r="2.5" />
                                    <circle cx="18.5" cy="18.5" r="2.5" />
                                </svg>
                            </div>
                        </div>
                        <div class="kpi-val" id="kpi-drivers">—</div>
                        <div class="kpi-trend trend-up">↑ GPS active</div>
                    </div>
                </div>

                <!-- CHARTS ROW -->
                <div class="dash-layout">
                    <div class="card">
                        <div class="card-head">
                            <div class="card-title">Daily Target Income</div>
                        </div>
                        <div class="target-card">
                            <div class="donut-wrap">
                                <canvas id="donutChart" class="donut-chart-wrap"></canvas>
                                <div class="donut-center">
                                    <div class="donut-pct" id="donutPct">0%</div>
                                    <div class="donut-lbl">of target</div>
                                </div>
                            </div>
                            <div class="target-amount" id="targetAmount">Rs. 0</div>
                            <div class="target-sub">from Rs. 10,000 target</div>
                            <a href="orders.html" class="btn btn-outline" style="width:100%;justify-content:center">More
                                Details →</a>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-head">
                            <div class="card-title">Order Volume — Last 7 Days</div>
                            <div style="display:flex;gap:0.75rem;align-items:center">
                                <div
                                    style="display:flex;align-items:center;gap:0.4rem;font-size:0.75rem;font-weight:600;color:var(--text3)">
                                    <div style="width:10px;height:10px;border-radius:2px;background:var(--accent)">
                                    </div>Orders
                                </div>
                                <div
                                    style="display:flex;align-items:center;gap:0.4rem;font-size:0.75rem;font-weight:600;color:var(--text3)">
                                    <div style="width:10px;height:10px;border-radius:2px;background:#94a3b8"></div>
                                    Expenses
                                </div>
                            </div>
                        </div>
                        <div class="card-pad">
                            <div class="chart-container" style="height:260px"><canvas id="barChart"></canvas></div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-head">
                            <div class="card-title">Daily Trending Menus</div>
                        </div>
                        <div id="trendingMenus"></div>
                    </div>
                </div>

                <!-- BOTTOM ROW -->
                <div class="dash-bottom">
                    <div class="card">
                        <div class="card-head">
                            <div class="card-title">Revenue — Last 7 Days</div>
                            <div class="topbar-live" style="font-size:0.75rem;padding:0.25rem 0.7rem">
                                <div class="live-pulse"></div> Auto-refresh
                            </div>
                        </div>
                        <div class="card-pad">
                            <div class="chart-container"><canvas id="lineChart"></canvas></div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-head">
                            <div class="card-title">Activity Feed</div>
                        </div>
                        <div class="feed-wrap" id="activityFeed"></div>
                    </div>
                </div>

                <!-- RECENT ORDERS TABLE -->
                <div class="card" style="margin-bottom:1.25rem">
                    <div class="card-head">
                        <div class="card-title">Recent Order Requests</div>
                        <a href="orders.html" class="card-action">Mission Control →</a>
                    </div>
                    <table>
                        <thead>
                            <tr>
                                <th>Order</th>
                                <th>Customer</th>
                                <th>Amount</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody id="recentOrders">
                            <tr>
                                <td colspan="4">
                                    <div class="empty-state">
                                        <p>Loading...</p>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>

    <script src="../dashboard/dashboard.js"></script>
</body>

</html>