<?php
    require_once 'Models/UserModel.php';
    require_once 'Models/OrderModel.php';
    class UserController extends BaseAdminController {
        private $users;
        private $orders;

        // Constructor to start the session and initialize the user model
        public function __construct(){
            $this->users = new UserModel();
            $this->orders = new OrderModel();
        }

        // Get all users from the database
        public function users() {
            $users = $this->users->getUsers();

            // Pass data to the view
            $this->view('admin/inventory/usersManagement', ['users' => $users]);
        }

        // Edit user
        public function editUser($id) {
            $user = $this->users->getUserById($id);
            $this->view('admin/Form/editUser', ['user' => $user]);
        }

        // Update user
        public function updateUser($id) {
            $username = htmlspecialchars($_POST['username']);
            $email = htmlspecialchars($_POST['email']);
            $phone = htmlspecialchars($_POST['phone']);
            $role = htmlspecialchars($_POST['role']);
            
            // Initialize profile variable
            $profile = '';
        
            // Check if a file was uploaded
            if (isset($_FILES['profile']) && $_FILES['profile']['error'] === UPLOAD_ERR_OK) {
                $fileName = time() . "_" . basename($_FILES['profile']['name']);
                $uploadDir = 'profiles/'; // Ensure this directory exists
        
                // Create the directory if it doesnâ€™t exist
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }
        
                $targetFilePath = $uploadDir . $fileName;
        
                // Validate file type (JPG, JPEG, PNG, GIF)
                $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
                $fileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));
        
                if (in_array($fileType, $allowedTypes)) {
                    if (move_uploaded_file($_FILES['profile']['tmp_name'], $targetFilePath)) {
                        $profile = $targetFilePath; // Set the new image path
                    } else {
                        die("Error uploading the image.");
                    }
                } else {
                    die("Invalid file type. Only JPG, JPEG, PNG, and GIF are allowed.");
                }
            } else {
                // If no new file was uploaded, retain the existing profile image
                $existingUser = $this->users->getUserById($id);
                $profile = $existingUser['profile']; // Keep the old profile image
            }
        
            //update to the database
            $this->users->updateUser($id, $username, $email, $phone,$profile, $role);
            header('Location: /users');
        }

        // Delete user
        public function deleteUser($id) {
            $this->users->deleteUser($id);
            header('Location: /users');
        }

        //User trash
        public function trashUser(){
            $trashUsers  = $this->users->getTrashUsers();
            $this->view('admin/inventory/users/trashUser', ['trashUsers' => $trashUsers]);
        }

       //Delete user permanently
       public function permanentlyDeleteUser($id) {
            $this->users->permanentlyDeleteUser($id); 
            header('Location: /users/trash');
        }

        //Restore user from trash
        public function restoreUser($id) {
            $this->users->restoreUser($id); // Restores user from trash_user to users
            header('Location: /users/trash');
        }

         //Active user
         public function active(){
            $users = $this->users->getUsersWithStatus();
            $this->view('admin/inventory/users/activeUser', ['users' => $users]);
        }
        public function order_all() {
            $orders = $this->orders->getAllOrders();
            $this->view('admin/inventory/Order/Allorder', ['orders' => $orders]);
        }
        
    }
?>