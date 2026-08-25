<div class="add-product-wrapper">
    <h2 class="title">Add New Product</h2>
    
    <form action="/products/store" method="POST" enctype="multipart/form-data" class="add-product-form">
        <!-- Left: Image Preview -->
        <div class="form-image">
            <label for="imageURL" class="form-label">Product Image</label>
            <input type="file" id="imageURL" name="imageURL" accept="image/*" required>
            <div id="imagePreview" class="image-preview mt-3"></div>
        </div>

        <!-- Right: Product Info -->
        <div class="form-fields">
            <!-- Product Name -->
            <div class="mb-3">
                <label for="productName" class="form-label">Product Name</label>
                <input type="text" id="productName" name="productname" required>
            </div>

            <!-- Description -->
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea id="description" name="descriptions" rows="3" required></textarea>
            </div>

            <!-- Category -->
            <div class="mb-3">
                <label for="category" class="form-label">Category</label>
                <select id="category" name="categories" required>
                    <option value="" disabled selected>Select Category</option>
                    <option value="Drinking Water">Drinking Water</option>
                    <option value="Tissue">Tissue</option>
                    <option value="Feminine Hygiene">Feminine Hygiene</option>
                    <option value="House Hold Hygiene">House Hold Hygiene</option>
                    <option value="Oral Health">Oral Health</option>
                    <option value="Beverages">Beverages</option>
                    <option value="Soap">Soap</option>
                    <option value="Cooking Ingredients">Cooking Ingredients</option>
                    <option value="Snacks">Snacks</option>
                </select>
            </div>

            <!-- Price -->
            <div class="mb-3">
                <label for="price" class="form-label">Price ($)</label>
                <input type="number" id="price" name="price" step="0.01" required>
            </div>

            <!-- Stock Quantity -->
            <div class="mb-3">
                <label for="stockQuantity" class="form-label">Stock Quantity</label>
                <input type="number" id="stockQuantity" name="stockquantity" required>
            </div>

            <!-- Buttons -->
            <div class="action-buttons">
                <button type="submit" class="btn-primary">Add Product</button>
                <a href="/products" class="btn-secondary">Cancel</a>
            </div>
        </div>
    </form>
</div>

<style>
    .add-product-wrapper {
        max-width: 1000px;
        background: #fff;
        padding: 40px;
        margin: 50px auto;
        border-radius: 16px;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
    }

    .title {
        text-align: center;
        font-size: 2.2rem;
        color: #1d36f3;
        margin-bottom: 2rem;
        font-weight: 700;
        text-transform: uppercase;
    }

    .add-product-form {
        display: flex;
        gap: 40px;
        flex-wrap: wrap;
    }

    .form-image {
        flex: 1;
        min-width: 280px;
    }

    .form-fields {
        flex: 2;
        min-width: 300px;
    }

    .form-label {
        font-weight: 600;
        color: #333;
        margin-bottom: 5px;
        display: block;
    }

    input[type="text"],
    input[type="number"],
    select,
    textarea,
    input[type="file"] {
        width: 100%;
        padding: 12px;
        border: 1px solid #58a2e0;
        border-radius: 10px;
        font-size: 1rem;
        margin-top: 4px;
        transition: 0.3s;
    }

    input:focus,
    select:focus,
    textarea:focus {
        border-color: #0d6efd;
        box-shadow: 0 0 6px rgba(13, 110, 253, 0.3);
        outline: none;
    }

    .image-preview {
        margin-top: 10px;
        border: 2px dashed #58a2e0;
        border-radius: 10px;
        background-color: #f1f3f5;
        min-height: 240px;
        display: flex;
        justify-content: center;
        align-items: center;
        overflow: hidden;
    }

    .image-preview img {
        width: 100%;
        height: auto;
        max-height: 240px;
        object-fit: contain;
        border-radius: 10px;
    }

    .mb-3 {
        margin-bottom: 20px;
    }

    .action-buttons {
        display: flex;
        justify-content: flex-start;
        gap: 15px;
        margin-top: 30px;
    }

    .btn-primary,
    .btn-secondary {
        padding: 12px 24px;
        font-size: 1.1rem;
        border-radius: 10px;
        font-weight: 500;
        border: none;
        transition: 0.3s;
        cursor: pointer;
        text-decoration: none;
        display: inline-block;
        text-align: center;
    }

    .btn-primary {
        background-color: #0d6efd;
        color: white;
    }

    .btn-primary:hover {
        background-color: #0b5ed7;
    }

    .btn-secondary {
        background-color: #6c757d;
        color: white;
    }

    .btn-secondary:hover {
        background-color: #5a6268;
    }

    @media (max-width: 768px) {
        .add-product-form {
            flex-direction: column;
        }

        .form-image,
        .form-fields {
            width: 100%;
        }

        .image-preview {
            min-height: 180px;
        }

        .image-preview img {
            max-height: 180px;
        }
    }
</style>

<script>
    document.getElementById('imageURL').addEventListener('change', function(event) {
        const file = event.target.files[0];
        const preview = document.getElementById('imagePreview');

        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.innerHTML = `<img src="${e.target.result}" alt="Product Preview">`;
            };
            reader.readAsDataURL(file);
        } else {
            preview.innerHTML = '';
        }
    });
</script>
