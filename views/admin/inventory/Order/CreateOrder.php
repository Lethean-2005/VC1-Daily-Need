<?php
$products = $data['products'] ?? [];
$locations = $data['locations'] ?? [];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Purchase Order</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gradient-to-r from-blue-100 to-purple-100 min-h-screen">
    <div class="container mx-auto px-4 py-8">
        <div class="bg-white rounded-xl shadow-2xl overflow-hidden max-w-2xl mx-auto">
            <!-- Header -->
            <div class="p-6 bg-[#2C4A6B] text-white border-b border-gray-200 shadow-sm">
                <div class="flex items-center space-x-4">
                    <div class="bg-teal-500 p-3 rounded-full">
                        <i class="ti ti-plus text-white text-xl"></i>
                    </div>
                    <div>
                        <h2 class="text-3xl font-extrabold text-white tracking-tight">Create Purchase Order</h2>
                        <p class="text-gray-200 text-sm mt-1 italic">Add a new order to ShopZen inventory.</p>
                    </div>
                </div>
            </div>

            <!-- Form -->
            <div class="p-6">
                <form id="createOrderForm" action="/create_order" method="POST">
                    <div class="mb-4">
                        <label for="productId" class="block text-gray-600 text-sm mb-2">Product</label>
                        <select id="productId" name="productId" class="w-full border border-gray-300 rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-teal-300" required>
                            <option value="">Select a product</option>
                            <?php foreach ($products as $product): ?>
                                <option value="<?php echo htmlspecialchars($product['id']); ?>">
                                    <?php echo htmlspecialchars($product['productname']); ?> 
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="mb-4">
                        <label for="price" class="block text-gray-600 text-sm mb-2">Price per Unit</label>
                        <input type="number" id="price" name="price" step="0.01" min="0" class="w-full border border-gray-300 rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-teal-300" required>
                    </div>

                    <div class="mb-4">
                        <label for="quantity" class="block text-gray-600 text-sm mb-2">Quantity</label>
                        <input type="number" id="quantity" name="quantity" min="1" class="w-full border border-gray-300 rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-teal-300" required>
                    </div>
                    <div class="mb-4">
                        <label for="locationId" class="block text-gray-600 text-sm mb-2">Destination</label>
                        <select id="locationId" name="locationId" class="w-full border border-gray-300 rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-teal-300" required>
                            <option value="">Select a destination</option>
                            <?php foreach ($locations as $location): ?>
                                <option value="<?php echo htmlspecialchars($location['id']); ?>">
                                    <?php echo htmlspecialchars($location['location_name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="flex justify-end space-x-2">
                        <a href="/order_all" class="px-4 py-2 bg-gray-300 text-gray-800 rounded-lg hover:bg-gray-400 transition">Cancel</a>
                        <button type="submit" class="px-4 py-2 bg-teal-600 text-white rounded-lg hover:bg-teal-700 transition">Create Order</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('createOrderForm').addEventListener('submit', function(event) {
            event.preventDefault();
            const formData = new FormData(this);
            fetch('/create_order', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Order created successfully!');
                    window.location.href = '/order_all';
                } else {
                    alert('Failed to create order: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while creating the order.');
            });
        });
    </script>
</body>
</html>