<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dynamic Menu View</title>
    <style>
        body { font-family: sans-serif; padding: 2rem; max-width: 800px; margin: 0 auto; background: #f9f9f9; }
        .grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 1rem; }
        .card { background: #fff; padding: 1rem; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .card h3 { margin: 0 0 0.5rem 0; font-size: 1.1rem; }
        .card p { margin: 0; color: #666; font-size: 0.9rem; }
        .card .price { color: #e91e63; font-weight: bold; margin-top: 0.5rem; }
        .card .category { font-size: 0.8rem; color: #aaa; text-transform: uppercase; margin-bottom: 0.5rem; }
        #loading { font-size: 1.2rem; color: #007bff; text-align: center; padding: 2rem; font-weight: bold; }
        #empty { font-size: 1.2rem; color: #ff5722; text-align: center; padding: 2rem; display: none; background: #ffebee; border-radius: 8px;}
    </style>
</head>
<body>

    <h2>Restaurant Menu</h2>
    
    <div id="loading">Loading menu items... Please wait.</div>
    
    <div id="empty">No dishes found in the menu dataset.</div>
    
    <div class="grid" id="menu-container"></div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const loadingEl = document.getElementById('loading');
            const emptyEl = document.getElementById('empty');
            const container = document.getElementById('menu-container');

            fetch('api.php?action=menu')
                .then(response => response.json())
                .then(data => {
                    loadingEl.style.display = 'none';

                    if (!data || data.length === 0) {
                        emptyEl.style.display = 'block';
                        return;
                    }

                    let html = '';
                    data.forEach(item => {
                        html += `
                            <div class="card">
                                <div class="category">${item.category_name}</div>
                                <h3>${item.name}</h3>
                                <p>${item.description || ''}</p>
                                <div class="price">Rs. ${parseFloat(item.price).toFixed(2)}</div>
                            </div>
                        `;
                    });
                    container.innerHTML = html;
                })
                .catch(error => {
                    loadingEl.style.display = 'none';
                    emptyEl.style.display = 'block';
                    emptyEl.textContent = 'Failed to load menu data.';
                    console.error('Error fetching menu:', error);
                });
        });
    </script>
</body>
</html>
