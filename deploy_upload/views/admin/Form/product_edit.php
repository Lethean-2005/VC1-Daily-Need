
    <style>
        .product-container {
            display: flex;
            justify-content: space-around;
            align-items: center;
            max-width: 4000px;
            margin: 50px auto;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 10px;
            box-shadow: rgba(3, 102, 214, 0.3) 0px 0px 0px 3px;
            text-align: center;
        }
        .product-image{
            width: 100%;
            height: 100%;
        }
        .product-image img {
            width: 400px;
            height: 50vh;
            object-fit: contain;
            padding: 10px;
            border-radius: 8px;
        }
        .star-rating {
            color: #ffc107;
        }
        .file-input-container {
            display: flex;
            justify-content: center;
            gap: 10px;
        }
        .pc {
            display: flex;
            flex-direction: column;
            align-items: center; /* This will center the content horizontally */
            justify-content: center; /* This will center the content vertically */
        }
    </style>
    <div class="product-container">
        <div class="pc">
            <div class="product-image">
                <img src="/<?= htmlspecialchars($product['imageURL']) ?>" alt="Product Image">
            </div>
            <h2 class="mt-3"><?= htmlspecialchars($product['categories']) ?></h2>
        </div>
        <form action="/products/update/<?= $product['id'] ?>" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
            
            <div class="mb-3">
                <label class="form-label">Name Product</label>
                <input type="text" name="productname" class="form-control" value="<?= $product['productname'] ?>">
            </div>
            
            <div class="mb-3">
                <label class="form-label">Description</label>
                <input type="text" name="descriptions" class="form-control" value="<?= $product['descriptions'] ?>">
            </div>
            
            <div class="mb-3">
                <label class="form-label">Category</label>
                <input type="text" name="categories" class="form-control" value="<?= $product['categories'] ?>">
            </div>
            
            <div class="mb-3">
                <label class="form-label">Price</label>
                <input type="text" name="price" class="form-control" value="<?= $product['price'] ?>">
            </div>
            
            <div class="mb-3">
                <label class="form-label">Stock Quantity</label>
                <input type="text" name="stockquantity" class="form-control" value="<?= $product['stockquantity'] ?>">
            </div>
            
            <div class="file-input-container">
                <button type="submit" class="btn btn-success">Update</button>
                <a href="/products" class="btn btn-secondary">Cancel</a>
                <input type="file" name="imageURL" class="form-control">
            </div>
        </form>
    </div>

<!-- script code -->
<script>
    // Get the file input and image elements
    const fileInput = document.querySelector('input[name="imageURL"]');
    const productImage = document.querySelector('.product-image img');

    // Add an event listener to the file input
    fileInput.addEventListener('change', function (event) {
        // Check if a file is selected
        if (event.target.files && event.target.files[0]) {
            const reader = new FileReader(); // Create a FileReader object

            // Set up the FileReader onload event
            reader.onload = function (e) {
                // Update the image source with the new file data
                productImage.src = e.target.result;
            };

            // Read the selected file as a data URL
            reader.readAsDataURL(event.target.files[0]);
        }
    });
</script>