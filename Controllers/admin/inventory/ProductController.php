<?php
require_once "Models/ProductModel.php";
class ProductController extends BaseAdminController {
    private $products;

    public function __construct() {
        $this->products = new ProductModel();
    }

    public function products() {
        $products = $this->products->getProducts();
        $this->view('admin/inventory/products', ['products' => $products]);
    }

    public function addproduct() {
        $this->view('admin/Form/addProduct');
    }

    public function store() {
        $productName = htmlspecialchars($_POST['productname']);
        $description = htmlspecialchars($_POST['descriptions']);
        $category = htmlspecialchars($_POST['categories']);
        $price = htmlspecialchars($_POST['price']);
        $stockQuantity = htmlspecialchars($_POST['stockquantity']);
    
        $uploadDir = 'uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
    
        if (isset($_FILES['imageURL']) && $_FILES['imageURL']['error'] === UPLOAD_ERR_OK) {
            $fileName = time() . "_" . basename($_FILES['imageURL']['name']);
            $targetFilePath = $uploadDir . $fileName;
    
            $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
            $fileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));
    
            if (in_array($fileType, $allowedTypes)) {
                if (move_uploaded_file($_FILES['imageURL']['tmp_name'], $targetFilePath)) {
                    $imagePath = $targetFilePath;
                } else {
                    die("Error uploading the image.");
                }
            } else {
                die("Invalid file type. Only JPG, JPEG, PNG, and GIF are allowed.");
            }
        } else {
            die("No image uploaded.");
        }
    
        $this->products->addProduct($productName, $description, $category, $price, $stockQuantity, $imagePath);
        header('Location:/products');
    }

    public function edit($id) {
        $product = $this->products->getProductById($id);
        $this->view('admin/Form/product_edit', ['product' => $product]);
    }

    private function handleImageUpload() {
        $uploadDir = 'uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
    
        if (isset($_FILES['imageURL']) && $_FILES['imageURL']['error'] === UPLOAD_ERR_OK) {
            $fileName = time() . "_" . basename($_FILES['imageURL']['name']);
            $targetFilePath = $uploadDir . $fileName;
    
            $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
            $fileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));
    
            if (in_array($fileType, $allowedTypes)) {
                if (move_uploaded_file($_FILES['imageURL']['tmp_name'], $targetFilePath)) {
                    return $targetFilePath;
                } else {
                    die("Error uploading the image.");
                }
            } else {
                die("Invalid file type. Only JPG, JPEG, PNG, and GIF are allowed.");
            }
        } else {
            return '';
        }
    }

    public function update($id) {
        $productName = htmlspecialchars($_POST['productname']);
        $description = htmlspecialchars($_POST['descriptions']);
        $category = htmlspecialchars($_POST['categories']);
        $price = htmlspecialchars($_POST['price']);
        $stockQuantity = htmlspecialchars($_POST['stockquantity']);

        $imagePath = $this->handleImageUpload();
    
        if (empty($imagePath)) {
            $existingProduct = $this->products->getProductById($id);
            $imagePath = $existingProduct['imageURL'];
        }
    
        $this->products->updateProduct($productName, $description, $category, $price, $stockQuantity, $imagePath, $id);
        header('Location: /products');
    }

    public function delete($id) {
        $this->products->deleteProduct($id);
        header('Location: /products');
    } 

    public function discount() {
        $this->view('admin/inventory/products/Discount');
    }

    public function pro_discount() {
        $this->view('admin/inventory/products/ProductDiscount');
    }

    public function show($id) {
        $product = $this->products->getProductById($id);
        if (!$product) {
            die("Product not found.");
        }
        $this->view('admin/inventory/products/view', ['product' => $product]);
    }

    public function addDiscount() {
        $this->view('admin/inventory/products/add_discount');
    }

    public function storeDiscount() {
        $code = htmlspecialchars($_POST['code']);
        $discount_type = htmlspecialchars($_POST['discount_type']);
        $discount_value = floatval($_POST['discount_value']);
        $max_usage = !empty($_POST['max_usage']) ? intval($_POST['max_usage']) : null;
        $expiry_date = !empty($_POST['expiry_date']) ? $_POST['expiry_date'] : null;

        $success = $this->products->addDiscountCode($code, $discount_type, $discount_value, $max_usage, $expiry_date);
        if ($success) {
            $this->view('admin/inventory/products/add_discount', ['success' => "Discount code '$code' created successfully!"]);
        } else {
            $this->view('admin/inventory/products/add_discount', ['error' => "Discount code '$code' already exists. Please use a different code."]);
        }
    }
}
?>