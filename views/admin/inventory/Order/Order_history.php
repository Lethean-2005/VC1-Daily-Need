<?php
// Handle AJAX requests
if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
    try {
        $conn = Database::connect();

        // Handle delete request
        if (isset($_POST['action']) && $_POST['action'] === 'delete_order') {
            $orderId = (int)$_POST['order_id'];
            $deleteStmt = $conn->prepare("DELETE FROM orders WHERE id = :id");
            $deleteStmt->bindValue(':id', $orderId, PDO::PARAM_INT);
            $deleteStmt->execute();
            echo json_encode(['success' => true, 'message' => 'Order deleted successfully']);
            exit;
        }

        // Handle edit request
        if (isset($_POST['action']) && $_POST['action'] === 'edit_order') {
            $orderId = (int)$_POST['order_id'];
            $status = $_POST['order_status'];
            $updateStmt = $conn->prepare("UPDATE orders SET orderstatus = :status WHERE id = :id");
            $updateStmt->bindValue(':status', $status);
            $updateStmt->bindValue(':id', $orderId, PDO::PARAM_INT);
            $updateStmt->execute();
            echo json_encode(['success' => true, 'message' => 'Order updated successfully', 'new_status' => $status]);
            exit;
        }

    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        exit;
    }
}

try {
    $conn = Database::connect();

    $itemsPerPage = 10;
    $currentPage = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
    $offset = ($currentPage - 1) * $itemsPerPage;

    $startDate = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-d', strtotime('-1 month'));
    $endDate = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-d');
    $dateFilter = "WHERE o.orderdate BETWEEN :start_date AND :end_date";

    $tabs = [
        'all' => "",
        'completed' => "AND o.orderstatus IN ('Delivered', 'Collected')",
        'cancelled' => "AND o.orderstatus = 'Cancelled'"
    ];

    $currentTab = isset($_GET['tab']) ? $_GET['tab'] : 'all';
    $tabFilter = $tabs[$currentTab] ?? "";

    $totalStmt = $conn->prepare("SELECT COUNT(*) FROM orders o LEFT JOIN users u ON o.user_id = u.id $dateFilter $tabFilter");
    $totalStmt->bindValue(':start_date', $startDate);
    $totalStmt->bindValue(':end_date', $endDate . ' 23:59:59');
    $totalStmt->execute();
    $totalOrders = $totalStmt->fetchColumn();
    $totalPages = ceil($totalOrders / $itemsPerPage);

    $stmt = $conn->prepare("
        SELECT o.*, u.username, u.profile AS user_profile
        FROM orders o
        LEFT JOIN users u ON o.user_id = u.id
        $dateFilter $tabFilter
        ORDER BY o.orderdate DESC
        LIMIT :limit OFFSET :offset
    ");
    $stmt->bindValue(':start_date', $startDate);
    $stmt->bindValue(':end_date', $endDate . ' 23:59:59');
    $stmt->bindValue(':limit', $itemsPerPage, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $summaryStmt = $conn->prepare("
        SELECT 
            COUNT(*) as total_orders,
            SUM(CASE WHEN o.orderstatus = 'Delivered' THEN 1 ELSE 0 END) as delivered,
            SUM(CASE WHEN o.orderstatus = 'Collected' THEN 1 ELSE 0 END) as collected,
            SUM(CASE WHEN o.orderstatus = 'Cancelled' THEN 1 ELSE 0 END) as cancelled,
            SUM(CASE WHEN o.orderstatus IN ('Delivered', 'Collected') THEN o.totalprice ELSE 0 END) as total_revenue,
            COUNT(CASE WHEN o.orderstatus IN ('Delivered', 'Collected') THEN 1 END) as completed_count,
            SUM(CASE WHEN o.payments = 'Cash' THEN 1 ELSE 0 END) as cash_payments,
            SUM(CASE WHEN o.payments = 'Paid' THEN 1 ELSE 0 END) as paid_payments
        FROM orders o
        $dateFilter
    ");
    $summaryStmt->bindValue(':start_date', $startDate);
    $summaryStmt->bindValue(':end_date', $endDate . ' 23:59:59');
    $summaryStmt->execute();
    $summary = $summaryStmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    $orders = [];
    $totalPages = 1;
    $currentPage = 1;
    $summary = ['total_orders' => 0, 'delivered' => 0, 'collected' => 0, 'cancelled' => 0, 'total_revenue' => 0, 'completed_count' => 0, 'cash_payments' => 0, 'paid_payments' => 0];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order History</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <style>
        .table-container {
            max-height: 55vh;
            overflow-y: auto;
        }

        .sticky-header {
            position: sticky;
            top: 0;
            z-index: 10;
            background-color: #2C4A6B;
        }

        .table-container::-webkit-scrollbar {
            display: none;
        }

        .table-md-text th,
        .table-md-text td {
            padding: 0.75rem 1rem;
            font-size: 0.9375rem;
        }

        .status-badge {
            padding: 0.375rem 0.75rem;
            font-size: 0.8125rem;
        }

        .dropdown-item {
            font-size: 0.875rem;
        }

        .user-avatar {
            width: 36px;
            height: 36px;
        }

        .tab-content {
            margin-top: 1rem;
        }

        .table-row:hover {
            background-color: #f8fafc;
        }

        /* Modal styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 50;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
        }

        .modal-content {
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            width: 90%;
            max-width: 400px;
            position: relative;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .modal-close {
            position: absolute;
            top: 10px;
            right: 10px;
            font-size: 24px;
            cursor: pointer;
            color: #333;
        }

        .modal-content img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            margin-right: 10px;
        }

        .status-pending {
            color: #D97706;
        }

        .status-delivered {
            color: #16A34A;
        }

        .status-collected {
            color: #16A34A;
        }

        .status-cancelled {
            color: #DC2626;
        }
    </style>
</head>

<body class="bg-gray-100">
    <div class="container mx-auto my-6 px-4">
        <div class="bg-white rounded-lg p-6 shadow border border-[#2C4A6B]">
            <h4 class="mb-4 font-semibold text-[#2C4A6B] text-xl">Order History</h4>

            <div class="tab-content" id="orderTabsContent">
                <div class="<?php echo $currentTab === 'all' ? 'block' : 'hidden'; ?>" id="all" role="tabpanel">
                    <div class="table-container">
                        <table class="w-full border-collapse">
                            <thead>
                                <tr class="bg-[#2C4A6B] text-white sticky-header">
                                    <th class="p-3 text-left">NO</th>
                                    <th class="p-3 text-left">Name</th>
                                    <th class="p-3 text-left">Payment</th>
                                    <th class="p-3 text-left">Date & Time</th>
                                    <th class="p-3 text-left">Status</th>
                                    <th class="p-3 text-left">Total</th>
                                    <th class="p-3 text-left">Action</th>
                                </tr>
                            </thead>
                            <tbody id="order-table-body">
                                <?php
                                $counter = ($currentPage - 1) * $itemsPerPage + 1;
                                foreach ($orders as $order): ?>
                                    <tr id="order-row-<?php echo $order['id']; ?>" class="hover:bg-gray-50 transition-colors border-b border-gray-200" data-order-id="<?php echo $order['id']; ?>">
                                        <td class="p-3 font-medium text-blue-500"><?php echo $counter++; ?></td>
                                        <td class="p-3">
                                            <div class="flex items-center">
                                                <img src="<?php echo htmlspecialchars($order['user_profile'] ?: 'https://via.placeholder.com/40'); ?>"
                                                    class="rounded-full mr-3 user-avatar"
                                                    alt="avatar">
                                                <span class="font-medium"><?php echo htmlspecialchars($order['username'] ?? 'Unknown'); ?></span>
                                            </div>
                                        </td>
                                        <td class="p-3 text-green-600 font-medium"><?php echo htmlspecialchars($order['payments'] ?? 'N/A'); ?></td>
                                        <td class="p-3"><?php echo htmlspecialchars(date('M j, Y H:i', strtotime($order['orderdate']))); ?></td>
                                        <td class="p-3">
                                            <span id="status-<?php echo $order['id']; ?>" class="status-cell status-badge rounded-full font-medium <?php
                                                if ($order['orderstatus'] === 'Pending') {
                                                    echo 'bg-yellow-200 text-yellow-600';
                                                } elseif ($order['orderstatus'] === 'Delivered') {
                                                    echo 'bg-green-200 text-green-600';
                                                } elseif ($order['orderstatus'] === 'Cancelled') {
                                                    echo 'bg-pink-200 text-pink-600';
                                                } elseif ($order['orderstatus'] === 'Collected') {
                                                    echo 'bg-green-200 text-green-600';
                                                } else {
                                                    echo 'bg-pink-200 text-pink-600';
                                                }
                                            ?>">
                                                <?php echo htmlspecialchars($order['orderstatus']); ?>
                                            </span>
                                        </td>
                                        <td class="p-3 font-medium text-purple-600">$<?php echo number_format($order['totalprice'], 2); ?></td>
                                        <td class="p-3">
                                            <div class="relative inline-block">
                                                <button class="text-gray-500 hover:text-gray-700 p-2" onclick="toggleDropdown('dropdown-<?php echo $order['id']; ?>')">
                                                    <i class="ti ti-dots-vertical"></i>
                                                </button>
                                                <div id="dropdown-<?php echo $order['id']; ?>" class="hidden absolute right-0 z-10 mt-2 w-52 origin-top-right rounded-md bg-white shadow-lg ring-1 ring-black ring-opacity-5">
                                                    <button onclick="showDetails('<?php echo $order['id']; ?>', '<?php echo htmlspecialchars($order['username'] ?? 'Unknown'); ?>', 
                                                        '<?php echo htmlspecialchars($order['user_profile'] ?? 'https://via.placeholder.com/40'); ?>', 
                                                        '<?php echo htmlspecialchars($order['payments'] ?? 'N/A'); ?>', 
                                                        '<?php echo htmlspecialchars(date('M j, Y H:i', strtotime($order['orderdate']))); ?>', 
                                                        '<?php echo htmlspecialchars($order['ordertype'] ?? 'N/A'); ?>', 
                                                        '<?php echo htmlspecialchars($order['orderstatus']); ?>', 
                                                        '<?php echo number_format($order['totalprice'], 2); ?>')"
                                                        class="block px-3 py-2 hover:bg-gray-100 dropdown-item w-full text-left">
                                                        <i class="ti ti-eye text-blue-600 mr-2"></i> View
                                                    </button>
                                                    <button onclick="showEdit('<?php echo $order['id']; ?>', '<?php echo htmlspecialchars($order['orderstatus']); ?>')"
                                                        class="block px-3 py-2 hover:bg-gray-100 dropdown-item w-full text-left">
                                                        <i class="ti ti-edit text-green-600 mr-2"></i> Edit
                                                    </button>
                                                    <!-- <button onclick="showMessage('<?php echo $order['id']; ?>')"
                                                        class="block px-3 py-2 hover:bg-gray-100 dropdown-item w-full text-left">
                                                        <i class="ti ti-mail text-purple-600 mr-2"></i> Message
                                                    </button> -->
                                                    <button onclick="showDelete('<?php echo $order['id']; ?>')"
                                                        class="block px-3 py-2 hover:bg-gray-100 dropdown-item w-full text-left">
                                                        <i class="ti ti-trash text-red-600 mr-2"></i> Delete
                                                    </button>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <?php if ($currentTab !== 'summary' && $totalPages > 1): ?>
                        <nav class="mt-4">
                            <ul class="flex justify-center space-x-2">
                                <li class="<?php echo $currentPage <= 1 ? 'pointer-events-none opacity-50' : ''; ?>">
                                    <a class="px-3 py-1.5 border border-[#2C4A6B] rounded text-base"
                                        href="?tab=<?php echo $currentTab; ?>&page=<?php echo $currentPage - 1; ?>&start_date=<?php echo $startDate; ?>&end_date=<?php echo $endDate; ?>">Previous</a>
                                </li>
                                <li class="px-3 py-1.5 border border-[#2C4A6B] rounded text-base">Page <?php echo $currentPage; ?> of <?php echo $totalPages; ?></li>
                                <li class="<?php echo $currentPage >= $totalPages ? 'pointer-events-none opacity-50' : ''; ?>">
                                    <a class="px-3 py-1.5 border border-[#2C4A6B] rounded text-base"
                                        href="?tab=<?php echo $currentTab; ?>&page=<?php echo $currentPage + 1; ?>&start_date=<?php echo $startDate; ?>&end_date=<?php echo $endDate; ?>">Next</a>
                                </li>
                            </ul>
                        </nav>
                    <?php endif; ?>
                </div>

                <div class="<?php echo $currentTab === 'summary' ? 'block' : 'hidden'; ?>" id="summary" role="tabpanel">
                    <div class="p-4 border border-[#2C4A6B] rounded-lg">
                        <h5 class="mb-4 font-semibold text-[#2C4A6B] text-lg">Order Summary (<?php echo date('m/d/Y', strtotime($startDate)); ?> to <?php echo date('m/d/Y', strtotime($endDate)); ?>)</h5>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <h6 class="text-gray-500 mb-3 text-sm">Order Statistics</h6>
                                <ul class="divide-y border border-[#2C4A6B] rounded-lg mb-4 text-base">
                                    <li class="flex justify-between items-center p-3">
                                        Total Orders
                                        <span class="bg-[#2C4A6B] text-white rounded-full px-3 py-1 text-sm"><?php echo $summary['total_orders']; ?></span>
                                    </li>
                                    <li class="flex justify-between items-center p-3">
                                        Delivered
                                        <span class="bg-green-100 text-green-800 rounded-full px-3 py-1 text-sm"><?php echo $summary['delivered']; ?></span>
                                    </li>
                                    <li class="flex justify-between items-center p-3">
                                        Collected
                                        <span class="bg-green-100 text-green-800 rounded-full px-3 py-1 text-sm"><?php echo $summary['collected']; ?></span>
                                    </li>
                                    <li class="flex justify-between items-center p-3">
                                        Cancelled
                                        <span class="bg-pink-100 text-pink-800 rounded-full px-3 py-1 text-sm"><?php echo $summary['cancelled']; ?></span>
                                    </li>
                                </ul>
                            </div>
                            <div>
                                <h6 class="text-gray-500 mb-3 text-sm">Financial Overview</h6>
                                <ul class="divide-y border border-[#2C4A6B] rounded-lg mb-4 text-base">
                                    <li class="flex justify-between items-center p-3">
                                        Total Revenue (Completed Orders)
                                        <span class="text-[#2C4A6B] font-bold">$<?php echo number_format($summary['total_revenue'], 2); ?></span>
                                    </li>
                                    <li class="flex justify-between items-center p-3">
                                        Average Order Value (Completed)
                                        <span class="text-[#2C4A6B] font-bold">$<?php echo $summary['completed_count'] > 0 ? number_format($summary['total_revenue'] / $summary['completed_count'], 2) : '0.00'; ?></span>
                                    </li>
                                </ul>
                                <h6 class="text-gray-500 mb-3 text-sm">Payment Methods</h6>
                                <ul class="divide-y border border-[#2C4A6B] rounded-lg text-base">
                                    <li class="flex justify-between items-center p-3">
                                        Cash Payments
                                        <span class="bg-gray-200 text-gray-800 rounded-full px-3 py-1 text-sm"><?php echo $summary['cash_payments']; ?></span>
                                    </li>
                                    <li class="flex justify-between items-center p-3">
                                        Paid (Online/Card)
                                        <span class="bg-gray-200 text-gray-800 rounded-full px-3 py-1 text-sm"><?php echo $summary['paid_payments']; ?></span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Container for View Details -->
    <div id="orderModal" class="modal">
        <div class="modal-content">
            <span class="modal-close" onclick="closeModal('orderModal')">×</span>
            <h2 class="text-xl font-bold mb-4">Order Details</h2>
            <div class="flex items-center mb-4">
                <img id="modalProfile" src="" alt="Profile">
                <div>
                    <p id="modalUsername" class="font-semibold"></p>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-2 mb-4">
                <div>
                    <p class="text-gray-600 text-sm">Order ID:</p>
                    <p id="modalOrderId" class="font-medium"></p>
                </div>
                <div>
                    <p class="text-gray-600 text-sm">Date:</p>
                    <p id="modalOrderDate" class="font-medium"></p>
                </div>
                <div>
                    <p class="text-gray-600 text-sm">Payment:</p>
                    <p id="modalPayment" class="font-medium text-green-600"></p>
                </div>
                <div>
                    <p class="text-gray-600 text-sm">Type:</p>
                    <p id="modalOrderType" class="font-medium"></p>
                </div>
                <div>
                    <p class="text-gray-600 text-sm">Status:</p>
                    <p id="modalStatus" class="font-medium"></p>
                </div>
            </div>
            <div>
                <p class="text-gray-600 text-sm">Total:</p>
                <p id="modalTotal" class="font-semibold text-lg text-purple-600"></p>
            </div>
        </div>
    </div>

    <!-- Modal Container for Edit Order -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <span class="modal-close" onclick="closeModal('editModal')">×</span>
            <h2 class="text-xl font-bold mb-4">Edit Order</h2>
            <div class="mb-4">
                <label for="editOrderStatus" class="block text-gray-600 text-sm mb-2">Order Status</label>
                <select id="editOrderStatus" class="w-full border border-gray-300 rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-teal-300">
                    <option value="Pending">Pending</option>
                    <option value="Delivered">Delivered</option>
                    <option value="Canceled">Cancelled</option>
                </select>
            </div>
            <button onclick="updateOrder()" class="w-full bg-green-500 text-white py-2 rounded-lg hover:bg-green-600 transition">Save Changes</button>
        </div>
    </div>

    <!-- Modal Container for Send Message -->
    <div id="messageModal" class="modal">
        <div class="modal-content">
            <span class="modal-close" onclick="closeModal('messageModal')">×</span>
            <h2 class="text-xl font-bold mb-4">Send Message</h2>
            <form id="sendMessageForm">
                <input type="hidden" id="messageOrderId" name="orderId">
                <div class="mb-4">
                    <label for="messageContent" class="block text-gray-600 text-sm mb-2">Message</label>
                    <textarea id="messageContent" name="messageContent" class="w-full border border-gray-300 rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-teal-300" rows="4" placeholder="Type your message here..."></textarea>
                </div>
                <button type="submit" class="w-full bg-purple-500 text-white py-2 rounded-lg hover:bg-purple-600 transition">Send Message</button>
            </form>
        </div>
    </div>

    <!-- Modal Container for Delete Confirmation -->
    <div id="deleteModal" class="modal">
        <div class="modal-content">
            <span class="modal-close" onclick="closeModal('deleteModal')">×</span>
            <h2 class="text-xl font-bold mb-4">Delete Order</h2>
            <p class="text-gray-700 mb-4" id="deleteMessage"></p>
            <div>
                <button onclick="deleteOrder()" class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition">Yes, Delete</button>
                <button onclick="closeModal('deleteModal')" class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition ml-2">Cancel</button>
            </div>
        </div>
    </div>

    <script>
        let currentOrderId = null;

        function toggleDropdown(id) {
            const dropdown = document.getElementById(id);
            const allDropdowns = document.querySelectorAll('[id^="dropdown-"]');
            allDropdowns.forEach(d => {
                if (d.id !== id) d.classList.add('hidden');
            });
            dropdown.classList.toggle('hidden');
        }

        document.addEventListener('click', function(event) {
            if (!event.target.matches('[onclick^="toggleDropdown"]') && !event.target.closest('[onclick^="toggleDropdown"]')) {
                const dropdowns = document.querySelectorAll('[id^="dropdown-"]');
                dropdowns.forEach(dropdown => dropdown.classList.add('hidden'));
            }
        });

        function showDetails(id, username, profile, payments, orderdate, ordertype, orderstatus, totalprice) {
            const modal = document.getElementById("orderModal");
            document.getElementById("modalProfile").src = profile;
            document.getElementById("modalUsername").textContent = username;
            document.getElementById("modalOrderId").textContent = id;
            document.getElementById("modalOrderDate").textContent = orderdate;
            document.getElementById("modalPayment").textContent = payments;
            document.getElementById("modalOrderType").textContent = ordertype;
            document.getElementById("modalStatus").textContent = orderstatus;
            document.getElementById("modalTotal").textContent = `$ ${totalprice}`;

            const modalStatus = document.getElementById("modalStatus");
            modalStatus.classList.remove("status-pending", "status-delivered", "status-collected", "status-cancelled");
            if (orderstatus.toLowerCase() === "pending") modalStatus.classList.add("status-pending");
            else if (orderstatus.toLowerCase() === "delivered") modalStatus.classList.add("status-delivered");
            else if (orderstatus.toLowerCase() === "canceled") modalStatus.classList.add("status-cancelled");

            modal.style.display = "flex";
        }

        function showEdit(id, orderstatus) {
            currentOrderId = id;
            const modal = document.getElementById("editModal");
            const statusSelect = document.getElementById("editOrderStatus");
            statusSelect.value = orderstatus;
            modal.style.display = "flex";
        }

        function updateOrder() {
            const status = document.getElementById("editOrderStatus").value;
            const formData = new FormData();
            formData.append('action', 'edit_order');
            formData.append('order_id', currentOrderId);
            formData.append('order_status', status);

            fetch('', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const statusCell = document.getElementById(`status-${currentOrderId}`);
                    statusCell.textContent = data.new_status;
                    statusCell.className = `status-cell status-badge rounded-full font-medium ${
                        data.new_status === 'Pending' ? 'bg-yellow-200 text-yellow-600' :
                        (data.new_status === 'Delivered' || data.new_status === 'Collected') ? 'bg-green-200 text-green-600' : 'bg-pink-200 text-pink-600'
                    }`;
                    alert(data.message);
                    closeModal('editModal');
                    // Remove row if it no longer matches the current tab filter
                    if ((currentTab === 'completed' && data.new_status !== 'Delivered' && data.new_status !== 'Collected') ||
                        (currentTab === 'cancelled' && data.new_status !== 'Cancelled')) {
                        document.getElementById(`order-row-${currentOrderId}`).remove();
                        checkEmptyTable();
                    }
                } else {
                    alert(data.message);
                }
            })
            .catch(error => {
                // console.error('Error:', error);
                // alert('An error occurred while updating the order.');
            });
        }

        function showDelete(id) {
            currentOrderId = id;
            const modal = document.getElementById("deleteModal");
            document.getElementById("deleteMessage").textContent = `Are you sure you want to delete Order #${id}?`;
            modal.style.display = "flex";
        }

        function deleteOrder() {
            const formData = new FormData();
            formData.append('action', 'delete_order');
            formData.append('order_id', currentOrderId);

            fetch('', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById(`order-row-${currentOrderId}`).remove();
                    alert(data.message);
                    closeModal('deleteModal');
                    checkEmptyTable();
                } else {
                    alert(data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while deleting the order.');
            });
        }

        function checkEmptyTable() {
            const tbody = document.getElementById('order-table-body');
            if (tbody.children.length === 0) {
                tbody.innerHTML = `
                    <tr class="hover:bg-gray-50 transition-colors border-b border-gray-200">
                        <td colspan="8" class="p-3 text-center text-gray-600">No orders available for this filter.</td>
                    </tr>
                `;
            }
        }

        function showMessage(id) {
            const modal = document.getElementById("messageModal");
            document.getElementById("messageOrderId").value = id;
            modal.style.display = "flex";
        }

        document.getElementById("sendMessageForm").addEventListener("submit", function(event) {
            event.preventDefault();
            const orderId = document.getElementById("messageOrderId").value;
            const messageContent = document.getElementById("messageContent").value;

            if (!messageContent.trim()) {
                alert("Please enter a message.");
                return;
            }

            // Placeholder for message sending logic (can be extended later)
            alert(`Message for Order #${orderId}: ${messageContent}`);
            closeModal('messageModal');
            document.getElementById("messageContent").value = "";
        });

        function closeModal(modalId) {
            document.getElementById(modalId).style.display = "none";
        }

        window.onclick = function(event) {
            const modals = ['orderModal', 'editModal', 'messageModal', 'deleteModal'];
            modals.forEach(modalId => {
                const modal = document.getElementById(modalId);
                if (event.target === modal) {
                    modal.style.display = "none";
                }
            });
        }
    </script>

<?php $conn = null; ?>
</body>
</html>