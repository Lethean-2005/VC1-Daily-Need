<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if (isset($_SESSION['user_id'])) : ?>
    <style>
        .bell-icon {
            font-size: 24px;
            cursor: pointer;
            position: relative;
        }

        .bell-icon.alert::after {
            display: none;
        }

        .bell-icon.ring {
            animation: ring 0.5s infinite;
        }

        .icon-ring {
            animation: ring 0.5s infinite;
        }

        @keyframes ring {
            0% { transform: rotate(0deg); }
            25% { transform: rotate(15deg); }
            50% { transform: rotate(0deg); }
            75% { transform: rotate(-15deg); }
            100% { transform: rotate(0deg); }
        }

        .search-small {
            max-width: 300px;
        }

        .notification-count {
            position: absolute;
            top: -2px;
            right: -2px;
            width: 18px;
            height: 18px;
            background-color: red;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            color: white;
            font-weight: bold;
        }

        .notification-count:empty {
            background-color: red;
            width: 16px;
            height: 16px;
        }

        .product-image {
            width: 50px;
            height: 50px;
            object-fit: contain;
            background-color: #f0f8ff;
            padding: 3px;
            border-radius: 6px;
            border: 1px solid #e0e0e0;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s ease;
        }

        .product-image:hover {
            transform: scale(1.05);
        }

        .product-image-fallback {
            width: 50px;
            height: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #f0f0f0;
            border-radius: 6px;
            border: 1px solid #e0e0e0;
            font-size: 12px;
            color: #777;
        }

        .modal-product-image {
            width: 40px;
            height: 40px;
            object-fit: contain;
            background-color: #f0f8ff;
            padding: 3px;
            border-radius: 6px;
            border: 1px solid #e0e0e0;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .table td, .table th {
            padding: 8px;
            vertical-align: middle;
        }

        /* Style for the category filter dropdown in the table header */
        .category-filter-header {
            background-color: #212529;
            color: white;
            border: none;
            padding: 5px;
            border-radius: 4px;
            font-size: 14px;
            cursor: pointer;
            width: 100%;
        }

        .category-filter-header:focus {
            outline: none;
            box-shadow: 0 0 0 2px rgba(255, 255, 255, 0.3);
        }

        .category-filter-header option {
            background-color: white;
            color: black;
        }

        /* Responsive styles */
        @media (max-width: 992px) {
            .search-small {
                max-width: 100%;
            }
            
            .table-responsive {
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
            }
            
            .d-flex.justify-content-between {
                flex-direction: column;
                gap: 1rem;
            }
            
            .btn-group {
                width: 100%;
                margin-bottom: 1rem;
            }
            
            .btn-group .btn {
                width: 50%;
            }
            
            #notification-bell {
                align-self: flex-end;
                margin-top: 1rem;
            }
        }
        
        @media (max-width: 768px) {
            .table td, .table th {
                padding: 6px;
                font-size: 14px;
            }
            
            .product-image, .product-image-fallback {
                width: 40px;
                height: 40px;
            }
            
            .dropdown-menu {
                position: absolute !important;
            }
        }
        
        @media (max-width: 576px) {
            .container {
                padding-left: 10px;
                padding-right: 10px;
            }
            
            .table td, .table th {
                padding: 4px;
                font-size: 13px;
            }
            
            .product-image, .product-image-fallback {
                width: 30px;
                height: 30px;
            }
            
            .bell-icon {
                font-size: 20px;
            }
            
            .btn {
                padding: 0.25rem 0.5rem;
                font-size: 0.875rem;
            }
        }
    </style>

<div class="container my-4">
    <!-- Top Bar with Buttons and Bell -->
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap">
        <div class="d-flex flex-wrap gap-2">
            <a href="/add-product" class="btn btn-primary">+ Add New Product</a>
            <button id="export-excel" class="btn btn-secondary">Export to Excel</button>
        </div>
        <span id="notification-bell" class="bell-icon ti ti-bell" title="Stock Alerts" data-bs-toggle="modal" data-bs-target="#lowStockModal">
            <span id="notification-count" class="notification-count"></span>
        </span>
    </div>

    <!-- Search Filter -->
    <div class="mb-3 search-small">
        <input type="text" id="search-input" class="form-control" placeholder="Search by Product or Category">
    </div>

    <!-- Product Table -->
    <div class="table-responsive">
        <table class="table table-bordered table-striped table-hover" id="product-table">
            <thead class="table-dark">
                <tr>
                    <th scope="col">Image</th>
                    <th scope="col">Product</th>
                    <th scope="col">
                        <?php
                        // Calculate category counts dynamically
                        $categoryCounts = [];
                        foreach ($products as $product) {
                            $category = $product['categories'];
                            if (!isset($categoryCounts[$category])) {
                                $categoryCounts[$category] = 0;
                            }
                            $categoryCounts[$category]++;
                        }

                        // Define the list of categories to display (removing duplicates)
                        $categories = array_unique(array_column($products, 'categories'));
                        sort($categories); // Sort categories alphabetically
                        ?>
                        <select id="category-filter" class="category-filter-header">
                            <option value="">All Categories</option>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?= htmlspecialchars($category) ?>">
                                    <?= htmlspecialchars($category) ?> (<?= $categoryCounts[$category] ?? 0 ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </th>
                    <th scope="col">Price</th>
                    <th scope="col">Stock</th>
                    <th scope="col" class="d-none d-md-table-cell">Description</th>
                    <th scope="col">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                // Define stock thresholds
                $lowStockThreshold = 10;
                $highStockThreshold = 100;

                foreach ($products as $product): 
                    $stock = $product['stockquantity'];
                    $isLowStock = $stock <= $lowStockThreshold;
                ?>
                    <tr class="<?= $isLowStock ? 'table-danger' : '' ?>" 
                        data-product-name="<?= htmlspecialchars($product['productname']) ?>" 
                        data-category="<?= htmlspecialchars($product['categories']) ?>">
                        <td class="text-center">
                            <?php if (!empty($product['imageURL']) && file_exists($product['imageURL'])): ?>
                                <img src="<?= htmlspecialchars($product['imageURL']) ?>" alt="<?= htmlspecialchars($product['productname']) ?>" class="product-image">
                            <?php else: ?>
                                <div class="product-image-fallback">No Image</div>
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($product['productname']) ?></td>
                        <td><?= htmlspecialchars($product['categories']) ?></td>
                        <td><?= htmlspecialchars($product['price']) ?>$</td>
                        <td>
                            <span class="stock-quantity"><?= htmlspecialchars($product['stockquantity']) ?></span>
                            <?php if ($isLowStock): ?>
                                <i class="ti ti-alert-triangle text-danger ms-2 icon-ring" title="Low Stock Alert"></i>
                            <?php endif; ?>
                        </td>
                        <td class="d-none d-md-table-cell"><?= htmlspecialchars($product['descriptions']) ?></td>
                        <td class="text-center">
                            <div class="dropdown">
                                <button class="btn border-0 p-1" type="button" id="dropdownMenuButton<?= $product['id'] ?>" data-bs-toggle="dropdown" data-bs-popper="static" aria-expanded="false">
                                    <i class="ti ti-dots-vertical"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton<?= $product['id'] ?>">
                                    <li><a class="dropdown-item" href="/products/view/<?= urlencode($product['id']) ?>">
                                        <i class="ti ti-eye"></i> View</a></li>
                                    <li><a class="dropdown-item" href="/products/edit/<?= urlencode($product['id']) ?>">
                                        <i class="ti ti-pencil"></i> Edit</a></li>
                                    <li><a class="dropdown-item text-danger" href="/products/delete/<?= urlencode($product['id']) ?>">
                                        <i class="ti ti-trash"></i> Delete</a></li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal for Low Stock Details -->
<div class="modal fade" id="lowStockModal" tabindex="-1" aria-labelledby="lowStockModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="lowStockModalLabel">Low Stock Alerts</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="lowStockDetails">
                <!-- Low stock items will be populated here by JavaScript -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript -->
<script>
// Real-time Search Functionality
document.getElementById('search-input').addEventListener('input', function() {
    const searchValue = this.value.toLowerCase().trim();
    const searchWords = searchValue.split(/\s+/).filter(word => word.length > 0);
    const categoryFilter = document.getElementById('category-filter').value.toLowerCase();
    const rows = document.querySelectorAll('#product-table tbody tr');

    rows.forEach(row => {
        const productName = row.getAttribute('data-product-name').toLowerCase();
        const category = row.getAttribute('data-category').toLowerCase();
        const matchesSearch = searchWords.every(word => 
            productName.includes(word) || category.includes(word)
        );
        const matchesCategory = categoryFilter === '' || category === categoryFilter;
        row.style.display = (matchesSearch && matchesCategory) ? '' : 'none';
    });
});

// Category Filter Functionality
document.getElementById('category-filter').addEventListener('change', function() {
    const categoryFilter = this.value.toLowerCase();
    const searchValue = document.getElementById('search-input').value.toLowerCase().trim();
    const searchWords = searchValue.split(/\s+/).filter(word => word.length > 0);
    const rows = document.querySelectorAll('#product-table tbody tr');

    rows.forEach(row => {
        const productName = row.getAttribute('data-product-name').toLowerCase();
        const category = row.getAttribute('data-category').toLowerCase();
        const matchesSearch = searchWords.every(word => 
            productName.includes(word) || category.includes(word)
        );
        const matchesCategory = categoryFilter === '' || category === categoryFilter;
        row.style.display = (matchesSearch && matchesCategory) ? '' : 'none';
    });
});

// Bell Notification Logic and Modal Population
document.addEventListener('DOMContentLoaded', function() {
    const lowStockRows = document.querySelectorAll('.table-danger');
    const bell = document.getElementById('notification-bell');
    const modalBody = document.getElementById('lowStockDetails');
    const notificationCount = document.getElementById('notification-count');
    const modal = document.getElementById('lowStockModal');

    let seenLowStockItems = JSON.parse(localStorage.getItem('seenLowStockItems')) || [];
    let currentLowStockItems = [];
    lowStockRows.forEach(row => {
        const productName = row.querySelector('td:nth-child(2)').textContent.trim();
        currentLowStockItems.push(productName);
    });

    const newLowStockItems = currentLowStockItems.filter(item => !seenLowStockItems.includes(item));
    const hasNewLowStock = newLowStockItems.length > 0;

    if (lowStockRows.length > 0) {
        if (hasNewLowStock) {
            bell.classList.add('ring');
        }
        bell.classList.add('alert');
        notificationCount.textContent = lowStockRows.length;

        let modalContent = '';
        lowStockRows.forEach(row => {
            const productName = row.querySelector('td:nth-child(2)').textContent.trim();
            const stockQty = row.querySelector('td:nth-child(5) .stock-quantity').textContent.trim();
            const imageSrc = row.querySelector('td:nth-child(1) img') ? row.querySelector('td:nth-child(1) img').getAttribute('src') : '';
            
            modalContent += `
                <div class="d-flex align-items-center mb-3">
                    ${imageSrc ? `<img src="${imageSrc}" alt="${productName}" class="modal-product-image me-3">` : `<div class="modal-product-image me-3">No Image</div>`}
                    <div>
                        <strong>${productName}</strong><br>
                        Stock Quantity: ${stockQty} units
                    </div>
                </div>
            `;
        });

        modalBody.innerHTML = modalContent || '<p>No low stock items found.</p>';
    } else {
        modalBody.innerHTML = '<p>No low stock items found.</p>';
        notificationCount.textContent = '';
    }

    modal.addEventListener('shown.bs.modal', function () {
        bell.classList.remove('ring', 'alert');
        notificationCount.textContent = '';
        seenLowStockItems = currentLowStockItems;
        localStorage.setItem('seenLowStockItems', JSON.stringify(seenLowStockItems));
    });
});

// Export to Excel Functionality
document.getElementById('export-excel').addEventListener('click', function() {
    const table = document.getElementById('product-table');
    const rows = table.querySelectorAll('tr');
    let csvContent = [];

    rows.forEach(row => {
        const cols = row.querySelectorAll('td, th');
        let rowData = [];
        
        cols.forEach((col, index) => {
            if (index !== 0 && index !== 6) { // Skip image and actions columns
                let text = '';
                if (index === 4) {
                    text = col.querySelector('.stock-quantity') ? col.querySelector('.stock-quantity').textContent.trim() : col.textContent.trim();
                } else if (index === 2) {
                    // For the category column, get the text content (not the dropdown)
                    text = col.textContent.trim();
                } else {
                    text = col.textContent.trim();
                }
                rowData.push(`"${text.replace(/"/g, '""')}"`);
            }
        });
        
        if (rowData.length > 0) {
            csvContent.push(rowData.join(','));
        }
    });

    const csvString = csvContent.join('\n');
    const blob = new Blob([csvString], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    const url = URL.createObjectURL(blob);
    
    link.setAttribute('href', url);
    link.setAttribute('download', 'products_export_' + new Date().toISOString().slice(0,10) + '.csv');
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
});
</script>

<?php 
else: 
    header("Location: /login"); 
    exit();
endif; 
?>