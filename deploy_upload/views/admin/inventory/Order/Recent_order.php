<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "dailyneed_db";

// Handle AJAX requests
if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
    try {
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

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

// Normal page load (non-AJAX)
try {
    $conn = new PDO("mysql:host=$servername; dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Handle message request (for simplicity, we'll just echo it; in practice, you'd store or send it)
    if (isset($_POST['send_message'])) {
        $orderId = (int)$_POST['order_id'];
        $message = htmlspecialchars($_POST['message']);
        // Here you could implement actual messaging logic (e.g., save to DB or send email)
        echo "<script>alert('Message for Order #$orderId: $message');</script>";
    }

    // Pagination settings
    $itemsPerPage = 10;
    $currentPage = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
    $offset = ($currentPage - 1) * $itemsPerPage;

    // Define "today" range (last 24 hours)
    $todayStart = date('Y-m-d H:i:s', strtotime('-24 hours'));

    // Get total number of recent orders (within 24 hours)
    $totalStmt = $conn->prepare("SELECT COUNT(*) FROM orders WHERE orderdate >= :todayStart");
    $totalStmt->bindValue(':todayStart', $todayStart);
    $totalStmt->execute();
    $totalOrders = $totalStmt->fetchColumn();
    $totalPages = ceil($totalOrders / $itemsPerPage);

    // Fetch recent orders for the current page (sorted by oldest first)
    $stmt = $conn->prepare("
        SELECT o.*, u.username, u.profile AS user_profile, u.phone
        FROM orders o
        LEFT JOIN users u ON o.user_id = u.id
        WHERE o.orderdate >= :todayStart
        ORDER BY o.orderdate ASC
        LIMIT :limit OFFSET :offset
    ");
    $stmt->bindValue(':todayStart', $todayStart);
    $stmt->bindValue(':limit', $itemsPerPage, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    $orders = [];
    $totalPages = 1;
    $currentPage = 1;
}
?>

<!DOCTYPE html>
<html>
<head>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .table-container::-webkit-scrollbar {
            width: 40px;
            height: 8px;
        }

        .sticky-header thead th {
            position: sticky;
            top: 0;
            z-index: 10;
            background-color: #2C4A6B;
        }

        .table-container {
            max-height: 60vh;
            overflow-y: auto;
            border: none;
            outline: none;
            padding: 0;
            margin: 0;
        }

        .relative {
            z-index: 20;
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 30;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
        }

        .modal-content {
            background-color: white;
            margin: 15% auto;
            padding: 20px;
            border-radius: 8px;
            width: 90%;
            max-width: 500px;
            position: relative;
        }

        .close {
            position: absolute;
            right: 20px;
            top: 10px;
            font-size: 24px;
            cursor: pointer;
        }

        /* Add this to your <style> section */
        .dots-hidden .fas.fa-ellipsis-v {
            visibility: hidden !important;
        }

        .dropdown-active {
            display: block !important; /* Ensure the dropdown is visible */
            z-index: 30; /* Higher z-index to ensure it appears above the button */
            background-color: white; /* Ensure the background is solid */
        }
    </style>
</head>

<body class="bg-gradient-to-r from-blue-100 to-purple-100 min-h-screen">
    <div class="container mx-auto px-4 py-8">
        <div class="bg-white rounded-xl shadow-2xl overflow-hidden">
            <!-- Header -->
            <div class="p-6 bg-white text-black border-b">
                <div class="flex justify-between items-center">
                    <h2 class="text-2xl font-bold">Recent Orders (Today)</h2>
                    <div class="relative">
                        <input type="text" id="search" placeholder="Search orders..." class="pl-10 pr-4 py-2 rounded-lg bg-gray-100 text-gray-800 focus:outline-none focus:ring-2 focus:ring-teal-300">
                        <i class="ti ti-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500"></i>
                    </div>
                </div>
            </div>

            <!-- Orders Table with sticky header -->
            <div class="table-container">
                <table class="w-full sticky-header">
                    <thead class="text-white">
                        <tr>
                            <th class="py-4 px-6 text-left text-sm font-semibold uppercase tracking-wider">No</th>
                            <th class="py-4 px-6 text-left text-sm font-semibold uppercase tracking-wider">Phone</th>
                            <th class="py-4 px-6 text-left text-sm font-semibold uppercase tracking-wider">Customer</th>
                            <th class="py-4 px-6 text-left text-sm font-semibold uppercase tracking-wider">Payment</th>
                            <th class="py-4 px-6 text-left text-sm font-semibold uppercase tracking-wider">Date & Time</th>
                            <th class="py-4 px-6 text-left text-sm font-semibold uppercase tracking-wider">Status</th>
                            <th class="py-4 px-6 text-left text-sm font-semibold uppercase tracking-wider">Total</th>
                            <th class="py-4 px-6 text-left text-sm font-semibold uppercase tracking-wider">Action</th>
                        </tr>
                    </thead>
                    <tbody id="order-list" class="divide-y divide-gray-200 bg-white">
                        <?php if (empty($orders)): ?>
                            <tr>
                                <td colspan="8" class="py-6 px-6 text-center text-gray-600 text-lg">No orders from today.</td>
                            </tr>
                        <?php else: ?>
                            <?php $rowNumber = ($currentPage - 1) * $itemsPerPage + 1; ?>
                            <?php foreach ($orders as $index => $order): ?>
                                <tr id="order-row-<?php echo $order['id']; ?>" class="<?php echo $index % 2 === 0 ? 'bg-gray-50' : 'bg-white'; ?> hover:bg-teal-50 transition">
                                    <td class="py-4 px-6 text-blue-600 font-medium"><?php echo $rowNumber; ?></td>
                                    <td class="py-4 px-6 text-gray-800 font-medium"><?php echo htmlspecialchars($order['phone'] ?? 'N/A'); ?></td>
                                    <td class="py-4 px-6">
                                        <div class="flex items-center">
                                            <img src="<?php echo htmlspecialchars($order['user_profile'] ?? 'https://i.pravatar.cc/40'); ?>"
                                                class="w-10 h-10 rounded-full mr-3 border-2 border-purple-300" alt="Profile">
                                            <span class="text-gray-800 font-medium"><?php echo htmlspecialchars($order['username'] ?? 'Unknown Customer'); ?></span>
                                        </div>
                                    </td>
                                    <td class="py-4 px-6 text-green-600"><?php echo htmlspecialchars($order['payments'] ?? 'N/A'); ?></td>
                                    <td class="py-4 px-6 text-gray-700"><?php echo htmlspecialchars(date('M d, Y H:i', strtotime($order['orderdate']))); ?></td>
                                    <td class="py-4 px-6">
                                        <span id="status-<?php echo $order['id']; ?>" class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium <?php
                                            echo $order['orderstatus'] === 'Delivered' ? 'bg-green-200 text-green-800' : 
                                            ($order['orderstatus'] === 'Pending' ? 'bg-yellow-200 text-yellow-800' : 'bg-red-200 text-red-800');
                                        ?>">
                                            <?php echo htmlspecialchars($order['orderstatus']); ?>
                                        </span>
                                    </td>
                                    <td class="py-4 px-6 text-purple-600 font-semibold">$<?php echo number_format($order['totalprice'], 2); ?></td>
                                    <td class="py-4 px-6 relative">
                                        <button class="text-gray-600 hover:text-teal-600 transition text-xl p-2 rounded-full hover:bg-gray-100"
                                            onclick="toggleDropdown('dropdown-<?php echo $order['id']; ?>')">
                                            <i class="ti ti-dots-vertical"></i>
                                        </button>
                                        <div id="dropdown-<?php echo $order['id']; ?>"
                                            class="hidden absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-xl z-10 border border-gray-200">
                                            <button onclick="showDetails('<?php echo $order['id']; ?>', '<?php echo htmlspecialchars($order['username'] ?? 'Unknown Customer'); ?>', 
                                                '<?php echo htmlspecialchars($order['phone'] ?? 'N/A'); ?>', '<?php echo htmlspecialchars($order['user_profile'] ?? 'https://i.pravatar.cc/40'); ?>', 
                                                '<?php echo htmlspecialchars($order['payments'] ?? 'N/A'); ?>', '<?php echo htmlspecialchars(date('M d, Y H:i', strtotime($order['orderdate']))); ?>', 
                                                '<?php echo htmlspecialchars($order['orderstatus']); ?>', '<?php echo number_format($order['totalprice'], 2); ?>')"
                                                class="flex items-center px-4 py-3 text-sm text-blue-600 hover:bg-blue-50 w-full text-left">
                                                <i class="ti ti-eye mr-3 text-lg"></i> View Details
                                            </button>
                                            <button onclick="showEdit('<?php echo $order['id']; ?>', '<?php echo htmlspecialchars($order['orderstatus']); ?>')"
                                                class="flex items-center px-4 py-3 text-sm text-green-600 hover:bg-green-50 w-full text-left">
                                                <i class="ti ti-edit mr-3 text-lg"></i> Edit
                                            </button>
                                            <button onclick="showDelete('<?php echo $order['id']; ?>')"
                                                class="flex items-center px-4 py-3 text-sm text-red-600 hover:bg-red-50 w-full text-left">
                                                <i class="ti ti-trash mr-3 text-lg"></i> Delete
                                            </button>
                                            <!-- <button onclick="showMessage('<?php echo $order['id']; ?>')"
                                                class="flex items-center px-4 py-3 text-sm text-purple-600 hover:bg-purple-50 w-full text-left">
                                                <i class="ti ti-mail mr-3 text-lg"></i> Message
                                            </button> -->
                                        </div>
                                    </td>
                                </tr>
                                <?php $rowNumber++; ?>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="p-6 bg-gray-50 flex justify-between items-center">
                <a href="?page=<?php echo $currentPage > 1 ? $currentPage - 1 : 1; ?>"
                    class="px-4 py-2 bg-teal-500 text-white rounded-lg hover:bg-teal-600 transition <?php echo $currentPage <= 1 ? 'opacity-50 cursor-not-allowed' : ''; ?>">
                    Previous
                </a>
                <span class="text-gray-700 font-medium">Page <?php echo $currentPage; ?> of <?php echo $totalPages; ?></span>
                <a href="?page=<?php echo $currentPage < $totalPages ? $currentPage + 1 : $totalPages; ?>"
                    class="px-4 py-2 bg-teal-500 text-white rounded-lg hover:bg-teal-600 transition <?php echo $currentPage >= $totalPages ? 'opacity-50 cursor-not-allowed' : ''; ?>">
                    Next
                </a>
            </div>
        </div>
    </div>

    <!-- Modal for Order Details -->
    <div id="orderModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">×</span>
            <h2 class="text-2xl font-bold mb-4" id="modalTitle">Order Details</h2>
            <div id="modalBody" class="space-y-4">
                <!-- Content will be populated by JavaScript -->
            </div>
        </div>
    </div>

    <!-- JavaScript -->
    <script>
        document.getElementById("search").addEventListener("input", function() {
            let filter = this.value.toLowerCase();
            let rows = document.querySelectorAll("#order-list tr");
            rows.forEach(row => {
                let text = row.innerText.toLowerCase();
                row.style.display = text.includes(filter) ? "" : "none";
            });
        });

        function toggleDropdown(id) {
            const dropdowns = document.querySelectorAll("[id^='dropdown-']");
            dropdowns.forEach(dropdown => {
                if (dropdown.id !== id) dropdown.classList.add("hidden");
            });
            const dropdown = document.getElementById(id);
            dropdown.classList.toggle("hidden");
        }

        document.addEventListener("click", function(event) {
            const dropdowns = document.querySelectorAll("[id^='dropdown-']");
            dropdowns.forEach(dropdown => {
                if (!dropdown.contains(event.target) && !event.target.closest("button")) {
                    dropdown.classList.add("hidden");
                }
            });
        });

        function showDetails(id, username, phone, profile, payment, date, status, total) {
            const modal = document.getElementById("orderModal");
            const modalTitle = document.getElementById("modalTitle");
            const modalBody = document.getElementById("modalBody");
            
            modalTitle.textContent = "Order Details";
            modalBody.innerHTML = `
                <div class="flex items-center mb-4">
                    <img src="${profile}" class="w-12 h-12 rounded-full mr-4 border-2 border-purple-300" alt="Profile">
                    <div>
                        <p class="text-gray-800 font-semibold">${username}</p>
                        <p class="text-gray-600">${phone}</p>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-gray-600">Order ID:</p>
                        <p class="font-medium">${id}</p>
                    </div>
                    <div>
                        <p class="text-gray-600">Date:</p>
                        <p class="font-medium">${date}</p>
                    </div>
                    <div>
                        <p class="text-gray-600">Payment:</p>
                        <p class="font-medium text-green-600">${payment}</p>
                    </div>
                    <div>
                        <p class="text-gray-600">Status:</p>
                        <p class="font-medium ${status === 'Delivered' ? 'text-green-600' : status === 'Pending' ? 'text-yellow-600' : 'text-red-600'}">${status}</p>
                    </div>
                    <div>
                        <p class="text-gray-600">Total:</p>
                        <p class="font-medium text-purple-600">$${total}</p>
                    </div>
                </div>
            `;
            modal.style.display = "block";
        }

        function showEdit(id, currentStatus) {
            const modal = document.getElementById("orderModal");
            const modalTitle = document.getElementById("modalTitle");
            const modalBody = document.getElementById("modalBody");
            
            modalTitle.textContent = "Edit Order";
            modalBody.innerHTML = `
                <div>
                    <div class="mb-4">
                        <label class="block text-gray-700 font-medium mb-2">Order Status</label>
                        <select id="order_status_${id}" class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-teal-300">
                            <option value="Pending" ${currentStatus === 'Pending' ? 'selected' : ''}>Pending</option>
                            <option value="Delivered" ${currentStatus === 'Delivered' ? 'selected' : ''}>Delivered</option>
                            <option value="Canceled" ${currentStatus === 'Canceled' ? 'selected' : ''}>Cancelled</option>
                        </select>
                    </div>
                    <button onclick="updateOrder(${id})" class="px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition">
                        Save Changes
                    </button>
                </div>
            `;
            modal.style.display = "block";
        }

        function updateOrder(orderId) {
            const status = document.getElementById(`order_status_${orderId}`).value;
            const formData = new FormData();
            formData.append('action', 'edit_order');
            formData.append('order_id', orderId);
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
                    // Update the status in the table
                    const statusCell = document.getElementById(`status-${orderId}`);
                    statusCell.textContent = data.new_status;
                    statusCell.className = `inline-flex items-center px-3 py-1 rounded-full text-sm font-medium ${
                        data.new_status === 'Delivered' ? 'bg-green-200 text-green-800' :
                        data.new_status === 'Pending' ? 'bg-yellow-200 text-yellow-800' : 'bg-red-200 text-red-800'
                    }`;
                    alert(data.message);
                    closeModal();
                } else {
                    alert(data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while updating the order.');
            });
        }

        function showDelete(id) {
            const modal = document.getElementById("orderModal");
            const modalTitle = document.getElementById("modalTitle");
            const modalBody = document.getElementById("modalBody");
            
            modalTitle.textContent = "Delete Order";
            modalBody.innerHTML = `
                <p class="text-gray-700 mb-4">Are you sure you want to delete Order #${id}?</p>
                <div>
                    <button onclick="deleteOrder(${id})" class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition">
                        Yes, Delete
                    </button>
                    <button onclick="closeModal()" class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition ml-2">
                        Cancel
                    </button>
                </div>
            `;
            modal.style.display = "block";
        }

        function deleteOrder(orderId) {
            const formData = new FormData();
            formData.append('action', 'delete_order');
            formData.append('order_id', orderId);

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
                    // Remove the row from the table
                    const row = document.getElementById(`order-row-${orderId}`);
                    row.remove();
                    alert(data.message);
                    closeModal();

                    // Check if the table is empty
                    const tbody = document.getElementById('order-list');
                    if (tbody.children.length === 0) {
                        tbody.innerHTML = `
                            <tr>
                                <td colspan="8" class="py-6 px-6 text-center text-gray-600 text-lg">No orders from today.</td>
                            </tr>
                        `;
                    }
                } else {
                    alert(data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while deleting the order.');
            });
        }

        function showMessage(id) {
            const modal = document.getElementById("orderModal");
            const modalTitle = document.getElementById("modalTitle");
            const modalBody = document.getElementById("modalBody");
            
            modalTitle.textContent = "Send Message";
            modalBody.innerHTML = `
                <form method="POST">
                    <input type="hidden" name="order_id" value="${id}">
                    <div class="mb-4">
                        <label class="block text-gray-700 font-medium mb-2">Message</label>
                        <textarea name="message" class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-teal-300" rows="4" placeholder="Type your message here..."></textarea>
                    </div>
                    <button type="submit" name="send_message" class="px-4 py-2 bg-purple-500 text-white rounded-lg hover:bg-purple-600 transition">
                        Send Message
                    </button>
                </form>
            `;
            modal.style.display = "block";
        }

        function closeModal() {
            document.getElementById("orderModal").style.display = "none";
        }

        window.onclick = function(event) {
            const modal = document.getElementById("orderModal");
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }
    </script>

<?php $conn = null; ?>
</body>
</html>