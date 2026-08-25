<?php
try {
    $conn = Database::connect();

    $todayStart = date('Y-m-d H:i:s', strtotime('-24 hours'));

    $totalStmt = $conn->prepare("SELECT COUNT(*) FROM orders WHERE orderdate >= :todayStart");
    $totalStmt->bindValue(':todayStart', $todayStart);
    $totalStmt->execute();
    $totalOrders = $totalStmt->fetchColumn();

    $stmt = $conn->prepare("
        SELECT o.*, u.username, u.profile AS user_profile, u.phone
        FROM orders o
        LEFT JOIN users u ON o.user_id = u.id
        WHERE o.orderdate >= :todayStart
        ORDER BY o.orderdate ASC
    ");
    $stmt->bindValue(':todayStart', $todayStart);
    $stmt->execute();
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    $orders = [];
    $totalOrders = 0;
}
?>

<style>
    .user-link {
        cursor: pointer;
        color: #007bff;
        text-decoration: none;
    }
                
    .user-link:hover {
        text-decoration: underline;
    }
    
    .popover {
        max-width: 300px;
    }

    /* Search styles */
    .search-container {
        position: relative;
        width: 100%;
        max-width: 400px;
    }

    .search-input {
        width: 100%;
        padding: 8px 40px 8px 16px;
        border: 1px solid #ddd;
        border-radius: 50px;
        outline: none;
        font-size: 14px;
        color: black;
        background-color: #fff;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        transition: border-color 0.2s ease-in-out;
    }

    .search-input:focus {
        border-color: #007bff;
        box-shadow: 0 2px 8px rgba(0, 123, 255, 0.2);
    }

    .search-icon {
        position: absolute;
        top: 50%;
        left: 12px;
        transform: translateY(-50%);
        color: black;
        font-size: 16px;
    }

    #suggestions {
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        background-color: white;
        border: 1px solid #ddd;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        max-height: 200px;
        overflow-y: auto;
        z-index: 1000;
        display: none;
        margin-top: 4px;
    }

    #suggestions a {
        display: block;
        padding: 10px 16px;
        color: black;
        text-decoration: none;
        font-size: 14px;
        border-bottom: 1px solid #eee;
        transition: background-color 0.2s ease-in-out;
    }

    #suggestions a:hover {
        background-color: #f0f0f0;
    }

    #suggestions .text-muted {
        padding: 10px 16px;
        color: #6c757d;
        font-size: 14px;
    }

    @media (max-width: 768px) {
        .search-container {
            max-width: 100%;
        }

        .search-input {
            font-size: 13px;
            padding: 8px 36px 8px 12px;
        }

        #suggestions {
            max-height: 150px;
        }

        #suggestions a {
            font-size: 13px;
            padding: 8px 12px;
        }
    }

    /* Profile dropdown */
    .dn-profile-trigger { display: flex; align-items: center; }
    .dn-profile-avatar {
        width: 34px; height: 34px; border-radius: 50%; object-fit: cover;
        border: 2px solid #eee;
    }
    .dn-profile-avatar-lg { width: 42px; height: 42px; }
    .dn-profile-menu {
        width: 280px;
        padding: 0;
        border-radius: 12px;
        border: 1px solid #eee;
        overflow: hidden;
        box-shadow: 0 12px 30px rgba(0,0,0,.12);
    }
    .dn-profile-menu-header {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 14px 16px;
    }
    .dn-profile-menu-id { flex: 1; min-width: 0; }
    .dn-profile-name { margin: 0; font-size: .88rem; font-weight: 700; color: #1f2a1f; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .dn-profile-email { margin: 0; font-size: .75rem; color: #999; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .dn-role-badge {
        flex-shrink: 0;
        padding: 2px 9px;
        border-radius: 20px;
        font-size: .68rem;
        font-weight: 600;
        text-transform: capitalize;
    }
    .dn-role-admin { background: #eef2ff; color: #4338ca; }
    .dn-role-users { background: #f4f3ef; color: #555; }
    .dn-profile-menu-section { padding: 6px 8px; border-top: 1px dashed #eee; }
    .dn-profile-menu-item {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 8px 8px;
        border-radius: 8px;
        font-size: .85rem;
        color: #333;
        text-decoration: none;
    }
    .dn-profile-menu-item:hover { background: #f7f6f3; color: #333; }
    .dn-profile-menu-item .flex-1 { flex: 1; }
    .dn-profile-menu-item i:first-child { color: #999; font-size: 1rem; }
    .dn-profile-menu-item .dn-chevron { color: #ccc; font-size: .85rem; }
    .dn-profile-menu-item-danger { color: #dc2626; }
    .dn-profile-menu-item-danger:hover { background: #fef2f2; color: #dc2626; }
    .dn-profile-menu-item-danger i { color: #dc2626 !important; }
    .dn-profile-menu-footer {
        border-top: 1px dashed #eee;
        padding: 10px 16px;
        text-align: center;
        font-size: .7rem;
        color: #aaa;
    }

    /* Sidebar â€” dark reskin via the theme's own CSS variables, floated with a margin */
    .dn-sidebar {
        --pc-sidebar-background: #0f172a;
        --pc-sidebar-color: #cbd5e1;
        --pc-sidebar-color-rgb: 203, 213, 225;
        --pc-sidebar-active-color: #6366f1;
        --pc-sidebar-caption-color: #64748b;
        --pc-sidebar-border: rgba(255, 255, 255, 0.08);
        --pc-sidebar-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        left: 1rem !important;
        top: 1rem !important;
        bottom: 1rem !important;
        height: calc(100vh - 2rem) !important;
        border-radius: 16px !important;
        overflow: hidden !important;
    }
    .dn-sidebar-header,
    .dn-sidebar-content { background: transparent !important; box-shadow: none !important; }
    .dn-sidebar-content.navbar-content { height: calc(100vh - 2rem - 60px - 66px) !important; }
    .pc-header { left: calc(260px + 2rem) !important; }
    .pc-container,
    .pc-footer { margin-left: calc(260px + 2rem) !important; }
    .pc-sidebar.pc-sidebar-hide ~ .pc-header { left: 0 !important; }
    .pc-sidebar.pc-sidebar-hide ~ .pc-footer,
    .pc-sidebar.pc-sidebar-hide ~ .pc-container { margin-left: 0 !important; }
    .dn-sidebar-brand { display: flex; align-items: center; gap: 10px; text-align: left !important; }
    .dn-sidebar-brand-icon {
        display: flex; align-items: center; justify-content: center;
        width: 36px; height: 36px; border-radius: 10px;
        background: #6366f1; color: #fff; font-size: 1.1rem; flex-shrink: 0;
    }
    .dn-sidebar-brand-text { display: flex; flex-direction: column; line-height: 1.2; }
    .dn-sidebar-brand-name { font-size: .88rem; font-weight: 700; color: #fff; }
    .dn-sidebar-brand-sub { font-size: .72rem; color: #94a3b8; }
    .dn-sidebar .pc-caption label {
        font-size: .68rem;
        font-weight: 700;
        letter-spacing: .05em;
        text-transform: uppercase;
    }
    .dn-sidebar .pc-link { border-radius: 8px; margin: 0 12px; }
    .dn-sidebar-footer {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 14px 16px;
        border-top: 1px solid rgba(255, 255, 255, 0.08);
    }
    .dn-sidebar-footer-avatar { width: 32px; height: 32px; border-radius: 50%; object-fit: cover; flex-shrink: 0; }
    .dn-sidebar-footer-id { flex: 1; min-width: 0; }
    .dn-sidebar-footer-name { margin: 0; font-size: .8rem; font-weight: 600; color: #fff; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .dn-sidebar-footer-role { margin: 0; font-size: .7rem; color: #94a3b8; text-transform: capitalize; }
    .dn-sidebar-footer-logout { color: #94a3b8; flex-shrink: 0; }
    .dn-sidebar-footer-logout:hover { color: #fff; }
</style>

<div class="loader-bg">
    <div class="loader-track">
        <div class="loader-fill"></div>
    </div>
</div>

<!-- [ Sidebar Menu ] start -->
<nav class="pc-sidebar dn-sidebar">
    <div class="navbar-wrapper dn-sidebar-header">
        <div class="m-header">
            <a href="/admin" class="b-brand dn-sidebar-brand">
                <span class="dn-sidebar-brand-icon"><i class="ti ti-building-warehouse"></i></span>
                <span class="dn-sidebar-brand-text">
                    <span class="dn-sidebar-brand-name">Daily Needs</span>
                    <span class="dn-sidebar-brand-sub">Admin</span>
                </span>
            </a>
        </div>
    </div>
    <div class="navbar-content dn-sidebar-content">
        <ul class="pc-navbar">
            <li class="pc-item pc-caption"><label>Main Menu</label></li>
            <li class="pc-item">
                <a href="/admin" class="pc-link">
                    <span class="pc-micon"><i class="ti ti-dashboard"></i></span>
                    <span class="pc-mtext">Dashboard</span>
                </a>
            </li>
            <li class="pc-item">
                <a href="/somepage" class="pc-link">
                    <span class="pc-micon"><i class="ti ti-brand-chrome"></i></span>
                    <span class="pc-mtext">Sample Page</span>
                </a>
            </li>

            <li class="pc-item pc-caption"><label>Catalog</label></li>
            <li class="pc-item pc-hasmenu">
                <a href="#!" class="pc-link">
                    <span class="pc-micon"><i class="ti ti-box"></i></span>
                    <span class="pc-mtext">Product Management</span>
                    <span class="pc-arrow"><i class="ti ti-chevron-right"></i></span>
                </a>
                <ul class="pc-submenu" id="order-submenu" style="display: none;">
                    <li class="pc-item">
                        <a class="pc-link" href="/products">
                            <i class="ti ti-package"></i> All Product
                        </a>
                    </li>
                    <li class="pc-item">
                        <a class="pc-link" href="/products/add-discount">
                            <i class="ti ti-discount"></i> Add Discount
                        </a>
                    </li>
                </ul>
            </li>
            <li class="pc-item pc-hasmenu">
                <a href="#!" class="pc-link">
                    <span class="pc-micon"><i class="ti ti-transfer-in"></i></span>
                    <span class="pc-mtext">Stock Management</span>
                    <span class="pc-arrow"><i class="ti ti-chevron-right"></i></span>
                </a>
                <ul class="pc-submenu" id="order-submenu" style="display: none;">
                    <li class="pc-item">
                        <a class="pc-link" href="/stock">
                            <i class="ti ti-box"></i> All Stocks
                        </a>
                    </li>
                    <li class="pc-item">
                        <a class="pc-link" href="/stock/in">
                            <i class="ti ti-arrow-down-circle"></i> Stock In
                        </a>
                    </li>
                    <li class="pc-item">
                        <a class="pc-link" href="/stock/out">
                            <i class="ti ti-arrow-up-circle"></i> Stock Out
                        </a>
                    </li>
                </ul>
            </li>
            <li class="pc-item">
                <a href="/salesreport" class="pc-link">
                    <span class="pc-micon"><i class="ti ti-report"></i></span>
                    <span class="pc-mtext">Sale Report</span>
                </a>
            </li>

            <li class="pc-item pc-caption"><label>Management</label></li>
            <li class="pc-item pc-hasmenu">
                <a href="#!" class="pc-link">
                    <span class="pc-micon"><i class="ti ti-users"></i></span>
                    <span class="pc-mtext">User Management</span>
                    <span class="pc-arrow"><i class="ti ti-chevron-right"></i></span>
                </a>
                <ul class="pc-submenu" id="order-submenu" style="display: none;">
                    <li class="pc-item">
                        <a class="pc-link" href="/users">
                            <i class="ti ti-users"></i> All Users
                        </a>
                    </li>
                    <li class="pc-item">
                        <a class="pc-link" href="/users/active">
                            <i class="ti ti-user-check"></i> Active User
                        </a>
                    </li>
                    <li class="pc-item">
                        <a class="pc-link" href="/users/trash">
                            <i class="ti ti-trash"></i> Trash
                        </a>
                    </li>
                </ul>
            </li>
            <li class="pc-item pc-hasmenu">
                <a href="#!" class="pc-link">
                    <span class="pc-micon"><i class="ti ti-clipboard-list"></i></span>
                    <span class="pc-mtext">Order Management</span>
                    <span class="pc-arrow"><i class="ti ti-chevron-right"></i></span>
                </a>
                <ul class="pc-submenu" id="order-submenu" style="display: none;">
                    <li class="pc-item"><a class="pc-link" href="/All_order">All Orders</a></li>
                    <li class="pc-item"><a class="pc-link" href="/recent_order">Recent Orders</a></li>
                    <li class="pc-item"><a class="pc-link" href="/order_history">Order History</a></li>
                    <li class="pc-item"><a class="pc-link" href="/order_pending">Pending Orders</a></li>
                    <li class="pc-item"><a class="pc-link" href="/old_order">Older Orders</a></li>
                </ul>
            </li>
        </ul>
    </div>
    <?php if (isset($_SESSION['user_id'])): ?>
        <div class="dn-sidebar-footer">
            <img
                src="<?= htmlspecialchars($_SESSION['user_profile'] ?? '') ?>"
                onerror="this.onerror=null; this.src='/assets/images/userPlaceHolder.png';"
                alt=""
                class="dn-sidebar-footer-avatar">
            <div class="dn-sidebar-footer-id">
                <p class="dn-sidebar-footer-name"><?= htmlspecialchars($_SESSION['user_name'] ?? 'User') ?></p>
                <p class="dn-sidebar-footer-role"><?= htmlspecialchars($_SESSION['user_role'] ?? '') ?></p>
            </div>
            <a href="/logout" class="dn-sidebar-footer-logout" aria-label="Sign out">
                <i class="ti ti-power"></i>
            </a>
        </div>
    <?php endif; ?>
</nav>
<!-- [ Sidebar Menu ] end --> 

<!-- [ Header Topbar ] start -->
<header class="pc-header" style="background:white; box-shadow: 0px 2px 10px rgba(0, 0, 0, 0.1);">
    <div class="header-wrapper">
        <div class="me-auto pc-mob-drp">
            <ul class="list-unstyled">
                <li class="pc-h-item pc-sidebar-collapse">
                    <a href="#" class="pc-head-link ms-0" id="sidebar-hide"><i class="ti ti-menu-2" style="color:black;"></i></a>
                </li>
                <li class="pc-h-item pc-sidebar-popup">
                    <a href="#" class="pc-head-link ms-0" id="mobile-collapse"><i class="ti ti-menu-2" style="color:black;"></i></a>
                </li>
                <li class="pc-h-item">
                    <div class="search-container">
                        <input type="search" id="globalSearch" class="search-input" placeholder="Search here...">
                        <div id="suggestions" class="list-group"></div>
                    </div>
                </li>
            </ul>
        </div>
        <div class="ms-auto">
            <ul class="list-unstyled">
                <li class="dropdown pc-h-item">
                    <a
                        class="pc-head-link dropdown-toggle arrow-none me-0"
                        data-bs-toggle="dropdown"
                        href="/recent_order"
                        role="button"
                        aria-haspopup="false"
                        aria-expanded="false"
                    >
                        <i class="ti ti-bell" style="color:black;"></i>
                        <span class="badge bg-success pc-h-badge"><?php echo $totalOrders; ?></span>
                    </a>
                    <div class="dropdown-menu dropdown-notification dropdown-menu-end pc-h-dropdown" style="background:white; box-shadow: 0px 4px 12px rgba(0, 0, 0, 0.15);">
                        <div class="dropdown-header d-flex align-items-center justify-content-between">
                            <h3 class="m-0" style="color:black;">Notifications (Recent Orders)</h3>
                            <a href="/recent_order" class="pc-head-link bg-transparent"><i class="ti ti-circle-check text-success"></i></a>
                        </div>
                        <div class="dropdown-divider"></div>
                        <div class="dropdown-header px-0 text-wrap header-notification-scroll position-relative" style="max-height: calc(100vh - 215px); color: black;">
                            <div class="list-group list-group-flush w-100">
                                <?php if (empty($orders)): ?>
                                    <a class="list-group-item list-group-item-action" style="color: black;">
                                        <div class="d-flex">
                                            <div class="flex-grow-1 ms-1">
                                                <p class="text-body mb-1" style="color:black;">No recent orders in the last 24 hours.</p>
                                            </div>
                                        </div>
                                    </a>
                                <?php else: ?>
                                    <?php foreach ($orders as $order): ?>
                                        <a class="list-group-item list-group-item-action" href="/recent_order?id=<?php echo $order['id']; ?>" style="color:black;">
                                            <div class="d-flex">
                                                <div class="flex-shrink-0">
                                                    <div class="user-avtar bg-light-success">
                                                        <i class="ti ti-shopping-cart" style="color:white;"></i>
                                                    </div>
                                                </div>
                                                <div class="flex-grow-1 ms-1">
                                                    <span class="float-end text-muted" style="color:grey;"><?php echo date('H:i', strtotime($order['orderdate'])); ?></span>
                                                    <p class="text-body mb-1" style="color:black;">
                                                        New order from
                                                        <b
                                                            class="user-link"
                                                            data-bs-toggle="popover"
                                                            data-bs-trigger="hover focus"
                                                            data-bs-placement="bottom"
                                                            data-bs-html="true"
                                                            data-bs-content="
                                                                <div>
                                                                    <strong>Username:</strong> <?php echo htmlspecialchars($order['username'] ?? 'Unknown'); ?><br>
                                                                    <strong>Phone:</strong> <?php echo htmlspecialchars($order['phone'] ?? 'N/A'); ?><br>
                                                                    <strong>User ID:</strong> <?php echo htmlspecialchars($order['user_id'] ?? 'N/A'); ?>
                                                                </div>"
                                                            style="color:black;"
                                                        >
                                                            <?php echo htmlspecialchars($order['username'] ?? 'Unknown Customer'); ?>
                                                        </b>
                                                        - $<?php echo number_format($order['totalprice'], 2); ?>
                                                    </p>
                                                    <span class="text-muted" style="color:grey;"><?php echo htmlspecialchars(date('M d, Y H:i', strtotime($order['orderdate']))); ?></span>
                                                </div>
                                            </div>
                                        </a>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="dropdown-divider"></div>
                        <div class="text-center py-2">
                            <a href="/recent_order" class="link-primary">View all</a>
                        </div>
                    </div>
                </li>
                <?php $conn = null; ?>
                <li class="dropdown pc-h-item header-user-profile">
                    <a
                        class="dn-profile-trigger dropdown-toggle arrow-none me-0"
                        data-bs-toggle="dropdown"
                        href="#"
                        role="button"
                        aria-haspopup="false"
                        data-bs-auto-close="outside"
                        aria-expanded="false"
                    >
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <?php $profileImage = !empty($_SESSION['user_profile']) ? $_SESSION['user_profile'] : ''; ?>
                            <img
                                src="<?= htmlspecialchars($profileImage) ?>"
                                onerror="this.onerror=null; this.src='/assets/images/userPlaceHolder.png';"
                                alt="user-image"
                                class="dn-profile-avatar">
                        <?php endif; ?>
                    </a>
                    <div class="dn-profile-menu dropdown-menu dropdown-menu-end pc-h-dropdown">
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <?php $profileImage = !empty($_SESSION['user_profile']) ? $_SESSION['user_profile'] : ''; ?>
                            <div class="dn-profile-menu-header">
                                <img
                                    src="<?= htmlspecialchars($profileImage) ?>"
                                    onerror="this.onerror=null; this.src='/assets/images/userPlaceHolder.png';"
                                    alt="user-image"
                                    class="dn-profile-avatar dn-profile-avatar-lg">
                                <div class="dn-profile-menu-id">
                                    <p class="dn-profile-name"><?= htmlspecialchars($_SESSION['user_name'] ?? 'User') ?></p>
                                    <p class="dn-profile-email"><?= htmlspecialchars($_SESSION['user_email'] ?? '') ?></p>
                                </div>
                                <span class="dn-role-badge dn-role-<?= htmlspecialchars($_SESSION['user_role'] ?? 'users') ?>">
                                    <?= htmlspecialchars($_SESSION['user_role'] ?? 'users') ?>
                                </span>
                            </div>

                            <div class="dn-profile-menu-section">
                                <a href="#!" class="dn-profile-menu-item" data-bs-toggle="modal" data-bs-target="#editProfileModal">
                                    <i class="ti ti-edit-circle"></i>
                                    <span class="flex-1">Edit Profile</span>
                                    <i class="ti ti-chevron-right dn-chevron"></i>
                                </a>
                                <a href="/admin" class="dn-profile-menu-item">
                                    <i class="ti ti-dashboard"></i>
                                    <span class="flex-1">Dashboard</span>
                                    <i class="ti ti-chevron-right dn-chevron"></i>
                                </a>
                                <?php if (($_SESSION['user_role'] ?? '') === 'admin'): ?>
                                    <a href="/users" class="dn-profile-menu-item">
                                        <i class="ti ti-users"></i>
                                        <span class="flex-1">Manage Users</span>
                                        <i class="ti ti-chevron-right dn-chevron"></i>
                                    </a>
                                <?php endif; ?>
                            </div>

                            <div class="dn-profile-menu-section">
                                <a href="/logout" class="dn-profile-menu-item dn-profile-menu-item-danger">
                                    <i class="ti ti-power"></i>
                                    Sign out
                                </a>
                            </div>

                            <div class="dn-profile-menu-footer">v1.0.0 &middot; Daily Needs Admin</div>
                        <?php endif; ?>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</header>
<!-- [ Header ] end -->

<!-- Profile Edit Modal -->
<div class="modal fade" id="editProfileModal" tabindex="-1" aria-labelledby="editProfileModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content p-4">
            <div class="modal-header border-0">
                <h5 class="modal-title" id="editProfileModalLabel">Edit Profile</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <?php if(isset($_SESSION['user_id'])): ?>
                <div class="position-relative d-inline-block">
                    <img id="profileImage" src="<?= $_SESSION['user_profile'] ?>" alt="Profile Image" class="rounded-circle border shadow" width="100" height="100">
                    <label for="profileUpload" class="position-absolute bottom-0 end-0 bg-primary text-white rounded-circle p-1" style="cursor: pointer;">
                        <i class="ti ti-camera"></i>
                    </label>
                    <input type="file" id="profileUpload" class="d-none" accept="image/*" onchange="previewImage(event)">
                </div>
                <form action="/users/update/<?= $_SESSION['user_id']?>" method="POST" enctype="multipart/form-data" class="mt-3">
                    <div class="mb-3">
                        <label class="form-label">Username</label>
                        <input type="text" class="form-control" value="<?= $_SESSION['user_name'] ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" value="<?= $_SESSION['user_email'] ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Phone</label>
                        <input type="text" class="form-control" value="<?= $_SESSION['user_phone'] ?>">
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Update</button>
                </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
    // Profile image preview
    function previewImage(event) {
        var reader = new FileReader();
        reader.onload = function() {
            var output = document.getElementById('profileImage');
            output.src = reader.result;
        }
        reader.readAsDataURL(event.target.files[0]);
    }

    // Initialize Bootstrap Popovers
    document.addEventListener('DOMContentLoaded', function () {
        var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
        var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
            return new bootstrap.Popover(popoverTriggerEl);
        });
    });

    // Search functionality
    const pages = [
        { name: "Dashboard", path: "/admin" },
        { name: "All Users", path: "/users" },
        { name: "Active Users", path: "/users/active" },
        { name: "Trash Users", path: "/users/trash" },
        { name: "All Products", path: "/products" },
        { name: "Add Discount", path: "/products/add-discount" },
        { name: "All Stocks", path: "/stock" },
        { name: "Stock In", path: "/stock/in" },
        { name: "Stock Out", path: "/stock/out" },
        { name: "Sales Report", path: "/salesreport" },
        { name: "All Orders", path: "/All_order" },
        { name: "Recent Orders", path: "/recent_order" },
        { name: "Order History", path: "/order_history" },
        { name: "Pending Orders", path: "/order_pending" },
        { name: "Older Orders", path: "/old_order" },
        { name: "Sample Page", path: "/somepage" }
    ];

    const searchInput = document.getElementById("globalSearch");
    const suggestionsBox = document.getElementById("suggestions");

    searchInput.addEventListener("input", function () {
        const query = this.value.toLowerCase().trim();
        suggestionsBox.innerHTML = '';

        if (!query) {
            suggestionsBox.style.display = "none";
            return;
        }

        const filteredPages = pages.filter(page => page.name.toLowerCase().includes(query));

        if (filteredPages.length === 0) {
            suggestionsBox.innerHTML = `<div class="list-group-item text-muted">No results found</div>`;
        } else {
            filteredPages.forEach(page => {
                const item = document.createElement("a");
                item.className = "list-group-item list-group-item-action";
                item.textContent = page.name;
                item.href = page.path;
                suggestionsBox.appendChild(item);
            });
        }

        suggestionsBox.style.display = "block";
    });

    document.addEventListener("click", function (e) {
        if (!searchInput.contains(e.target) && !suggestionsBox.contains(e.target)) {
            suggestionsBox.style.display = "none";
        }
    });

    // Ensure Feather icons are rendered
    feather.replace();
</script>

<!-- [ Main Content ] start -->
<div class="pc-container">
    <div class="pc-content">