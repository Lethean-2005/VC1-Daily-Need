<?php
    require_once "Router.php";
    require_once "Controllers/admin/BaseadminController.php";
    require_once "Database/database.php";
    require_once "Controllers/admin/home/HomeController.php";
    require_once "Controllers/admin/inventory/ProductController.php";
    require_once "Controllers/admin/inventory/StockController.php";
    require_once "Controllers/admin/inventory/SalesreportController.php";
    require_once "Controllers/admin/inventory/UserController.php";
    require_once "Controllers/admin/inventory/OrderController.php";
    require_once "Controllers/admin/inventory/LoginController.php";
    require_once "Controllers/admin/inventory/RegisterController.php";
    require_once "Controllers/admin/inventory/SomepageController.php";
    require_once "Controllers/admin/inventory/DiscountController.php";

    //customer
    require_once "Controllers/admin/basecustomerController.php";
    require_once "Controllers/admin/page/HomeController.php";
    require_once "Controllers/admin/page/ProductController.php";
    require_once "Controllers/admin/page/DetailproductController.php";
    require_once "Controllers/admin/page/AboutController.php";
    require_once "Controllers/admin/page/ContactControler.php";
    require_once "Controllers/admin/page/LoginController.php";
    require_once "Controllers/admin/page/RegisterController.php";
    require_once "Controllers/admin/inventory/CartController.php";
    require_once "Controllers/admin/inventory/PaymentFrontController.php";
    require_once "Controllers/admin/page/OrderHistory.php";
    require_once "Controllers/admin/page/SocialAuthController.php";

    $route = new Router();
    //home admin
    $route->get('/admin', [HomeController::class, 'index']);
    $route->get('/somepage', [HomeController::class, 'somepage']);

    //products management
    $route->get('/products', [ProductController::class, 'products']);
    $route->get('/add-product', [ProductController::class, 'addproduct']);
    $route->post('/products/store', [ProductController::class, 'store']);
    $route->get('/products/edit/{id}', [ProductController::class, 'edit']);
    $route->post('/products/update/{id}', [ProductController::class, 'update']);
    $route->delete('/products/delete/{id}', [ProductController::class, 'delete']);
    $route->get("/add-stock", [ProductController::class, 'addstock']);
    $route->get('/products/view/{id}', [ProductController::class, 'show']);
    //Discount 
    $route->get('/products/add-discount', [ProductController::class, 'addDiscount']);
    $route->post('/products/store-discount', [ProductController::class, 'storeDiscount']);

    //stock management
    $route->get('/stock', [StockController::class, 'stock']);
    $route->get('/stock/in', [StockController::class, 'stockIn']);
    $route->get('/stock/out', [StockController::class, 'stockOut']);

    //sales report
    $route->get('/salesreport', [SalesreportController::class, 'salesreport']);

    //user management
    $route->get('/users', [UserController::class, 'users']);
    $route->get('/users/edit/{id}', [UserController::class, 'editUser']);
    $route->put('/users/update/{id}', [UserController::class, 'updateUser']); 
    $route->delete('/users/delete/{id}', [UserController::class, 'deleteUser']);
    $route->get('/users/trash', [UserController::class, 'trashUser']);   
    $route->get('/users/active', [UserController::class, 'active']);
    $route->get('/users/restore/{id}', [UserController::class, 'restoreUser']);  
    $route->delete('/users/permanent-delete/{id}', [UserController::class, 'permanentlyDeleteUser']); 

    //order management
    $route->get('/orders', [OrderController::class, 'orders']);
    $route->get('/All_order', [OrderController::class, 'order_all']);
    $route->get('/recent_order', [OrderController::class, 'recent_order']);
    $route->get('/order_history', [OrderController::class, 'order_history']);
    $route->get('/order_pending', [OrderController::class, 'order_pending']);
    $route->get('/old_order', [OrderController::class, 'old_order']);
    $route->post('/delete_order', [OrderController::class, 'deleteOrder']);
    $route->post('/update_order_status', [OrderController::class, 'updateOrderStatus']);
    $route->post('/cancel_order', [OrderController::class, 'cancelOrder']);
    $route->get('/cancel_expired_orders', [OrderController::class, 'cancelExpiredOrders']);

    //login management
    $route->get('/admin-login', [LoginController::class, 'login_admin']);
    $route->post('/authenticate', [LoginController::class, 'authenticate']);
    $route->get('/logout', [LoginController::class, 'logout']);

    //social login
    $route->get('/auth/google', [SocialAuthController::class, 'googleRedirect']);
    $route->get('/auth/google/callback', [SocialAuthController::class, 'googleCallback']);

    //register management
    $route->get('/register', [RegisterController::class, 'register']);
    $route->post('/register/store', [RegisterController::class, 'store']);

    //somepage management
    $route->get('/somepage', [SomepageController::class, 'somepage']);

    //customer
    //home
    $route->get('/', [CustomerController::class, 'index']);

    //product
    $route->get('/product', [products::class, 'index']);
    $route->get('/oral', [products::class, 'oral']);
    $route->get('/beverage', [products::class, 'beverage']);
    $route->get('/cooking', [products::class, 'cooking']);
    $route->get('/drinking', [products::class, 'drinking']);
    $route->get('/feminine', [products::class, 'feminine']);
    $route->get('/houeshold', [products::class, 'houeshold']);
    $route->get('/saop', [products::class, 'saop']);
    $route->get('/snacks', [products::class, 'snacks']);
    $route->get('/tissue', [products::class, 'tissue']);
    
    //product detail
    $route->get('/product_detail', [DetailController::class, 'index']);

    //about
    $route->get('/about', [AboutController::class, 'index']);

    //contact
    $route->get('/contact', [ContactController::class, 'index']);

    //Login
    $route->get('/F_login', [LoginController::class, 'login']);

    //Register
    $route->get('/F_register', [FrontRegisterController::class, 'index']);
    
    // Cart
    $route->get('/cart/items', [CartController::class, 'getItems']);
    $route->post('/cart/add', [CartController::class, 'add']);
    $route->post('/cart/remove', [CartController::class, 'remove']);
    $route->get('/viewcart', [CartController::class, 'viewcart']);
    $route->get('/checkouts', [CartController::class, 'checkout']);
    $route->post('/cart/update', [CartController::class, 'update']);

    // Order History for customer
    $route->get('/order_h', [orderHistoryController::class, 'index']);
    $route->post('/submit_return', [orderHistoryController::class, 'submitReturn']);
    $route->post('/process_return', [orderHistoryController::class, 'processReturn']);
    $route->post('/bulk_cancel_orders', [OrderController::class, 'bulkCancelOrders']);
    $route->post('/bulk_delete_orders', [OrderController::class, 'bulkDeleteOrders']);
    $route->post('/process_return', [OrderHistoryController::class, 'processChangeRequest']);
    $route->post('/send_message', [OrderController::class, 'sendMessage']); // Assuming this exists
    $route->get('/create_order', [OrderController::class, 'createOrder']); // Assuming this exists
    $route->post('/create_order', [OrderController::class, 'createOrder']);
    $route->get('/order_all', [OrderController::class, 'order_all']);
    $route->post('/delete_order_from_history', [OrderController::class, 'deleteOrderFromHistory']);

    // For chat control
    $route->post('/send_message', [OrderController::class, 'sendMessage']);
    $route->get('/get_messages', [OrderController::class, 'getMessages']);



    //payment
    $route->get('/confirmpayment', [payController::class, 'index']);
    $route->post('/confirm-payment', [payController::class, 'confirmPayment']);
    $route->get('/orderSuccess', [payController::class, 'OrderSuccess']);
    $route->post('/checkout/process', [CartController::class, 'process']);
    $route->post('/cart/apply-coupon', [CartController::class, 'applyCoupon']);

    // Order Success
    $route->get('/order-success', [PaymentController::class, 'orderSuccess']);
   
    $route->route();
?>