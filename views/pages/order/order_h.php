<?php
$user_id = $_SESSION['user_id'] ?? null;
$orders = $data['orders'] ?? [];
$products = $data['products'] ?? []; // Available products for exchange
?>

    <title>Order History</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN">
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Nunito', 'Kantumruy Pro', sans-serif;
        }
        body {
            background: linear-gradient(to right, #e6f0fa, #f9e6ff);
            min-height: 100vh;
        }
        .order-card {
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .order-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
        }
        .return-form {
            display: none;
        }
        .return-btn:disabled {
            cursor: not-allowed;
        }
        .info-item, .p-name {
            font-size: 14px;
            color: black;
            font-family: 'Nunito', 'Kantumruy Pro', sans-serif;
        }
        .iii {
            display: flex !important;
            background-color: #f8f9fa !important;
            align-items: center !important;
        }
        .info-product {
            padding-top: 15px;
        }
        .hover-red:hover {
            color: red !important;
            background-color: #ffe6e6 !important;
        }
        .hover-effect:hover {
            background-color: gray !important;
            color: white !important;
        }
        /* Chat message styling */
        .chat-message {
            margin-bottom: 10px;
            padding: 8px;
            border-radius: 8px;
            font-size: 14px;
        }
        .chat-message.customer {
            background-color: #e6f3ff;
            text-align: right;
            margin-left: 20%;
        }
        .chat-message.admin {
            background-color: #f0f0f0;
            text-align: left;
            margin-right: 20%;
        }
        .chat-message small {
            color: #6c757d;
            font-size: 12px;
        }
    </style>
    <div class="container py-5">
        <h1 class="text-center mb-5 fw-bold text-primary">YOUR ORDER HISTORY</h1>
        <?php if (empty($orders)): ?>
            <div class="alert alert-info text-center" role="alert">
                You have no orders yet.
            </div>
        <?php else: ?>
            <?php
            // Group orders by order ID
            $groupedOrders = [];
            foreach ($orders as $order) {
                $groupedOrders[$order['id']][] = $order;
            }
            ?>
            <div class="row">
                <?php foreach ($groupedOrders as $orderId => $items): ?>
                    <?php $order = $items[0]; ?>
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="card order-card border-0 shadow-sm">
                            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                                <h5 class="card-title mb-0">ORDER <span></span></h5>
                                <?php if ($order['orderstatus'] === 'Canceled'): ?>
                                    <form action="/delete_order_from_history" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this order?');">
                                        <input type="hidden" name="orderId" value="<?php echo $order['id']; ?>">
                                        <button type="submit" class="btn p-0 border-0 bg-transparent" title="Canceled Order">
                                            <i class="ti ti-trash text-black bg-white rounded-circle d-inline-flex align-items-center justify-content-center hover-red"
                                            style="width: 30px; height: 30px; cursor: pointer;"></i>
                                        </button>
                                    </form>
                                <?php else: ?>
                                    <i class="ti ti-message-circle text-primary bg-white rounded-circle d-inline-flex align-items-center justify-content-center hover-effect" 
                                        style="width: 30px; height: 30px; cursor: pointer;" 
                                        title="View Messages" id="chatIcon" data-order-id="<?php echo $order['id']; ?>">
                                    </i>
                                <?php endif; ?>
                            </div>
                            <div class="card-body">
                                <p class="card-text"><strong>Date:</strong> <?php echo htmlspecialchars(date('M d, Y', strtotime($order['orderdate']))); ?></p>
                                <p class="card-text"><strong>Status:</strong> 
                                    <span class="badge <?php echo $order['orderstatus'] === 'Pending' ? 'bg-warning text-dark' : ($order['orderstatus'] === 'Delivered' ? 'bg-success' : ($order['orderstatus'] === 'Returned' ? 'bg-info' : ($order['orderstatus'] === 'Canceled' ? 'bg-danger' : 'bg-purple'))); ?>">
                                        <?php echo htmlspecialchars($order['orderstatus']); ?>
                                    </span>
                                </p>
                                <h6 class="mt-3">Products:</h6>
                                <ul class="list-group list-group-flush mb-3">
                                    <?php foreach ($items as $item): ?>
                                        <li class="list-group-item d-flex align-items-center iii">
                                            <img src="<?php echo htmlspecialchars($item['imageURL'] ?? 'https://via.placeholder.com/50'); ?>" alt="Product" class="img-thumbnail me-2" style="width: 70px; height: 70px; object-fit: contain;">
                                            <div class="info-product">
                                                <p class="mb-0 p-name"><?php echo htmlspecialchars($item['productname'] ?? 'Unknown Product'); ?></p>
                                                <small class="info-item">Qty: <?php echo htmlspecialchars($item['quantity']); ?> | 
                                                    <?php
                                                        $conversion_rate = 4100;
                                                        if ($order['payments'] == 'KH Riel'){
                                                            $khr_amount = round($item['price'] * $conversion_rate);
                                                            echo number_format($khr_amount, 0) . '៛';
                                                        } else{
                                                            echo '$' . number_format($item['price'], 2);
                                                        }
                                                    ?>
                                                </small>
                                            </div>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                                <p class="card-text"><strong>Location:</strong> <?php echo htmlspecialchars($order['location_name'] ?? 'N/A'); ?></p>
                                <p class="card-text total-price">
                                    <strong>Total:</strong>
                                    <?php echo htmlspecialchars($order['payments']); ?>
                                    <span class="amount">
                                        <?php
                                            if ($order['payments'] === 'KH Riel') {
                                                echo number_format(round($order['totalprice'] * 4100), 0) . '៛';
                                            } else {
                                                echo '$' . number_format($order['totalprice'], 2);
                                            }
                                        ?>
                                    </span>
                                </p>

                                <?php if ($order['return_status']): ?>
                                    <p class="card-text"><strong>Change Request Status:</strong> 
                                        <span class="badge <?php echo $order['return_status'] === 'Pending' ? 'bg-info' : ($order['return_status'] === 'Approved' ? 'bg-success' : ($order['return_status'] === 'Rejected' ? 'bg-danger' : 'bg-primary')); ?>">
                                            <?php echo htmlspecialchars($order['return_status']); ?>
                                        </span>
                                    </p>
                                    <p class="card-text"><strong>Requested Product:</strong> 
                                        <?php
                                        $requestedProduct = array_filter($products, function($p) use ($order) {
                                            return $p['id'] == $order['requested_product_id'];
                                        });
                                        $requestedProduct = reset($requestedProduct);
                                        echo htmlspecialchars($requestedProduct['productname'] ?? 'N/A');
                                        ?>
                                    </p>
                                    <p class="card-text"><strong>Reason:</strong> <?php echo htmlspecialchars($order['return_reason']); ?></p>
                                <?php endif; ?>
                                <?php
                                $canReturn = $order['orderstatus'] === 'Delivered' && !$order['return_status'];
                                if ($canReturn) {
                                    $deliveryDate = new DateTime($order['orderdate']);
                                    $currentDate = new DateTime();
                                    $interval = $deliveryDate->diff($currentDate);
                                    $canReturn = $interval->days <= 3;
                                }
                                ?>
                                <?php if ($canReturn): ?>
                                    <button class="btn btn-outline-warning w-100 mb-2 toggle-return-form" data-order-id="<?php echo $order['id']; ?>">Request Product Change</button>
                                <?php elseif ($order['return_status']): ?>
                                    <button class="btn btn-outline-secondary w-100 mb-2" disabled>Change Request <?php echo $order['return_status']; ?></button>
                                <?php else: ?>
                                    <button class="btn btn-outline-secondary w-100 mb-2" disabled>
                                        <?php echo $order['orderstatus'] === 'Canceled' ? 'Canceled' : ($order['orderstatus'] === 'Pending' ? 'Awaiting Delivery' : 'Change Not Available'); ?>
                                    </button>
                                <?php endif; ?>
                                <div class="return-form" id="return-form-<?php echo $order['id']; ?>">
                                    <form class="return-request-form" data-order-id="<?php echo $order['id']; ?>">
                                        <input type="hidden" name="orderId" value="<?php echo $order['id']; ?>">
                                        <div class="mb-3">
                                            <label for="requestedProduct-<?php echo $order['id']; ?>" class="form-label">Select Replacement Product</label>
                                            <select class="form-select" id="requestedProduct-<?php echo $order['id']; ?>" name="requestedProductId" required>
                                                <option value="">Choose a product</option>
                                                <?php foreach ($products as $product): ?>
                                                    <?php if ($product['stockquantity'] > 0): ?>
                                                        <option value="<?php echo $product['id']; ?>">
                                                            <?php echo htmlspecialchars($product['productname']); ?> ($<?php echo number_format($product['price'], 2); ?>)
                                                        </option>
                                                    <?php endif; ?>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label for="returnReason-<?php echo $order['id']; ?>" class="form-label">Reason for Change</label>
                                            <textarea class="form-control" id="returnReason-<?php echo $order['id']; ?>" name="returnReason" rows="3" placeholder="Why do you want to change this product?" required></textarea>
                                        </div>
                                        <button type="submit" class="btn btn-primary w-100">Submit Change Request</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
          <!-- Modal for Chat -->
    <div id="chatModal" class="modal" tabindex="-1" aria-labelledby="chatModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="chatModalLabel">Chat with Support</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="chatBox" style="max-height: 300px; overflow-y: auto;">
                <!-- Chat messages will go here -->
                </div>
                <textarea id="chatInput" class="form-control" placeholder="Type your message..." rows="3"></textarea>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="sendMessage">Send Message</button>
            </div>
            </div>
        </div>
    </div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL"></script>
<script>
function toggleReturnForm(event) {
    const button = event.currentTarget;
    const orderId = button.getAttribute('data-order-id');
    const form = document.getElementById(`return-form-${orderId}`);
    form.style.display = form.style.display === 'block' ? 'none' : 'block';
    button.textContent = form.style.display === 'block' ? 'Cancel Change Request' : 'Request Product Change';
}

function submitReturnForm(event) {
    event.preventDefault();
    const form = event.currentTarget;
    const orderId = form.getAttribute('data-order-id');
    const formData = new FormData(form);
    const data = Object.fromEntries(formData);

    fetch('/submit_change_request', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: new URLSearchParams(data).toString()
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok: ' + response.statusText);
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            alert('Change request submitted successfully! You will be notified once it’s processed.');
            location.reload();
        } else {
            alert('Failed to submit change request: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while submitting the change request.');
    });
}

const toggleButtons = document.querySelectorAll('.toggle-return-form');
toggleButtons.forEach(button => {
    button.addEventListener('click', toggleReturnForm);
});

const returnForms = document.querySelectorAll('.return-request-form');
returnForms.forEach(form => {
    form.addEventListener('submit', submitReturnForm);
});

document.addEventListener("DOMContentLoaded", function() {
    const orders = document.querySelectorAll(".card-title span");
    orders.forEach((span, index) => {
        span.textContent = index + 1;
    });
});

document.addEventListener("DOMContentLoaded", function() {
    const orderCards = document.querySelectorAll(".order-card");
    orderCards.forEach(card => {
        const paymentMethod = card.querySelector(".total-price")?.textContent.includes("KH Riel");
        if (paymentMethod) {
            const itemPrices = card.querySelectorAll(".info-item");
            let totalKhr = 0;
            itemPrices.forEach(item => {
                const text = item.textContent || "";
                const qtyMatch = text.match(/Qty:\s*(\d+)/);
                const qty = qtyMatch ? parseInt(qtyMatch[1]) : 1;
                let price = 0;
                if (text.includes('$')) {
                    const matchUsd = text.match(/\$([0-9.,]+)/);
                    if (matchUsd) {
                        price = parseFloat(matchUsd[1].replace(/,/g, "")) * 4100;
                    }
                } else if (text.includes('៛')) {
                    const matchKhr = text.match(/([\d,]+)៛/);
                    if (matchKhr) {
                        price = parseInt(matchKhr[1].replace(/,/g, ""));
                    }
                }
                totalKhr += price * qty;
            });
            const amountSpan = card.querySelector(".amount");
            if (amountSpan) {
                amountSpan.textContent = totalKhr.toLocaleString("en-US") + "៛";
            }
        }
    });
});

// Chat functionality
document.addEventListener('DOMContentLoaded', function() {
    const chatIcons = document.querySelectorAll('.bi-chat-left-text');
    const chatModal = new bootstrap.Modal(document.getElementById('chatModal'));
    let currentOrderId = null;

    // Validate session
    const userId = <?php echo json_encode($_SESSION['user_id'] ?? null); ?>;
    if (!userId) {
        console.error('User ID is not set in session. Please log in.');
        chatIcons.forEach(icon => {
            icon.style.pointerEvents = 'none';
            icon.style.opacity = '0.5';
            icon.title = 'Please log in to chat';
        });
        document.getElementById('chatBox').innerHTML = '<p>Please log in to view messages.</p>';
        document.getElementById('sendMessage').disabled = true;
        return;
    }

    chatIcons.forEach(chatIcon => {
        chatIcon.addEventListener('click', function() {
            currentOrderId = chatIcon.getAttribute('data-order-id');
            console.log('Opening chat for order ID:', currentOrderId, 'User ID:', userId);
            loadChatHistory(currentOrderId);
            chatModal.show();
        });
    });

    function loadChatHistory(orderId) {
        console.log('Fetching messages for order ID:', orderId, 'User ID:', userId);
        fetch(`/get_messages?orderId=${orderId}`, {
            headers: { 'Accept': 'application/json' }
        })
        .then(response => {
            console.log('Response status:', response.status);
            if (!response.ok) {
                throw new Error(`HTTP error ${response.status}: ${response.statusText}`);
            }
            return response.json();
        })
        .then(data => {
            console.log('Received messages:', JSON.stringify(data, null, 2));
            const chatBox = document.getElementById('chatBox');
            chatBox.innerHTML = '';

            if (data.success && Array.isArray(data.messages) && data.messages.length > 0) {
                // Group customer messages by content to avoid duplicates
                const messageMap = new Map();
                data.messages.forEach(msg => {
                    const isCustomer = parseInt(msg.sender_id) === parseInt(userId);
                    const isReceiver = parseInt(msg.receiver_id) === parseInt(userId);

                    if (isCustomer && !isReceiver) {
                        // Customer message (sent to admins): Group by message content
                        const key = `${msg.message}-${msg.sender_id}`;
                        if (!messageMap.has(key)) {
                            messageMap.set(key, msg);
                        }
                    } else if (isReceiver) {
                        // Admin message to customer: Always display
                        messageMap.set(`${msg.id}-${msg.sent_at}`, msg);
                    }
                });

                // Convert map values to array and sort by sent_at
                const uniqueMessages = Array.from(messageMap.values()).sort((a, b) => new Date(a.sent_at) - new Date(b.sent_at));

                uniqueMessages.forEach(msg => {
                    const div = document.createElement('div');
                    const isCustomer = parseInt(msg.sender_id) === parseInt(userId);
                    div.className = `chat-message ${isCustomer ? 'customer' : 'admin'}`;
                    const senderName = isCustomer ? 'You' : (msg.sender_name || 'Support');
                    const messageText = msg.message.replace(/</g, '<').replace(/>/g, '>');
                    div.innerHTML = `
                        <strong>${senderName}:</strong> 
                        ${messageText}
                        <br>
                        <small>${new Date(msg.sent_at).toLocaleString()}</small>
                    `;
                    chatBox.appendChild(div);
                });
                chatBox.scrollTop = chatBox.scrollHeight;
            } else {
                chatBox.innerHTML = '<p>No messages yet. Start the conversation!</p>';
                if (!data.success) {
                    console.error('Fetch failed:', data.message);
                    chatBox.innerHTML = `<p>Error: ${data.message}</p>`;
                }
            }
        })
        .catch(error => {
            console.error('Error fetching messages:', error);
            chatBox.innerHTML = `<p>Error loading chat history: ${error.message}</p>`;
        });
    }

    // Debounce function to prevent multiple rapid clicks
    function debounce(func, wait) {
        let timeout;
        return function (...args) {
            clearTimeout(timeout);
            timeout = setTimeout(() => func.apply(this, args), wait);
        };
    }

    // Handle send message with debouncing and button disabling
    const sendMessageButton = document.getElementById('sendMessage');
    const sendMessageHandler = debounce(function() {
        const chatInput = document.getElementById('chatInput');
        const message = chatInput.value.trim();

        if (!message) {
            alert('Please enter a message.');
            return;
        }

        if (!currentOrderId) {
            alert('No order selected for this chat.');
            return;
        }

        // Disable the button to prevent multiple clicks
        sendMessageButton.disabled = true;
        sendMessageButton.textContent = 'Sending...';

        const formData = new FormData();
        formData.append('orderId', currentOrderId);
        formData.append('messageContent', message);

        console.log('Sending message for order ID:', currentOrderId, 'Message:', message);
        fetch('/send_message', {
            method: 'POST',
            body: formData
        })
        .then(response => {
            console.log('Send response status:', response.status);
            if (!response.ok) {
                throw new Error(`HTTP error ${response.status}: ${response.statusText}`);
            }
            return response.json();
        })
        .then(data => {
            console.log('Send message response:', data);
            if (data.success) {
                chatInput.value = '';
                loadChatHistory(currentOrderId);
            } else {
                alert('Failed to send message: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error sending message:', error);
            alert('Error sending message: ' + error.message);
        })
        .finally(() => {
            // Re-enable the button
            sendMessageButton.disabled = false;
            sendMessageButton.textContent = 'Send Message';
        });
    }, 300); // 300ms debounce delay

    // Remove any existing listeners to prevent duplicates
    sendMessageButton.removeEventListener('click', sendMessageHandler);
    sendMessageButton.addEventListener('click', sendMessageHandler);

    document.getElementById('chatModal').addEventListener('hidden.bs.modal', function() {
        document.getElementById('chatBox').innerHTML = '';
        document.getElementById('chatInput').value = '';
        currentOrderId = null;
    });
});
</script>