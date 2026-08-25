<form action="/users/update/<?= $user['id']?>" method="POST" enctype="multipart/form-data" style="background-color: #f8f9fa; padding: 30px; border-radius: 10px; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);">
   <div style="text-align: center; margin-bottom: 25px;">
       <img id="profilePreview" src="/<?= $user['profile'] ?>" width="100" height="100" class="rounded-circle" style="object-fit: cover; border: 3px solid #ddd; box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);">
       <label for="profile" class="btn btn-light btn-sm mt-2" style="cursor: pointer; border: 1px solid #ced4da; border-radius: 5px; font-size: 0.9em;">
           <i class="ti ti-upload me-1"></i> Change Image
           <input type="file" name="profile" id="profile" accept="image/*" style="display: none;">
       </label>
   </div>

   <div class="form-group" style="margin-bottom: 20px;">
       <label for="username" style="display: block; font-weight: 500; color: #495057; margin-bottom: 5px;">Username</label>
       <input type="text" class="form-control" name="username" id="username" value="<?= htmlspecialchars($user['username']) ?>" style="width: 100%; padding: 10px 15px; font-size: 1rem; line-height: 1.5; color: #495057; background-color: #fff; border: 1px solid #ced4da; border-radius: 5px; transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;">
   </div>
   <div class="form-group" style="margin-bottom: 20px;">
       <label for="email" style="display: block; font-weight: 500; color: #495057; margin-bottom: 5px;">Email</label>
       <input type="email" class="form-control" name="email" id="email" value="<?= htmlspecialchars($user['email']) ?>" style="width: 100%; padding: 10px 15px; font-size: 1rem; line-height: 1.5; color: #495057; background-color: #fff; border: 1px solid #ced4da; border-radius: 5px; transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;">
   </div>
   <div class="form-group" style="margin-bottom: 20px;">
       <label for="phone" style="display: block; font-weight: 500; color: #495057; margin-bottom: 5px;">Phone</label>
       <input type="text" class="form-control" name="phone" id="phone" value="<?= htmlspecialchars($user['phone']) ?>" style="width: 100%; padding: 10px 15px; font-size: 1rem; line-height: 1.5; color: #495057; background-color: #fff; border: 1px solid #ced4da; border-radius: 5px; transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;">
   </div>
   <div class="form-group" style="margin-bottom: 20px;">
       <label for="role" style="display: block; font-weight: 500; color: #495057; margin-bottom: 5px;">Role</label>
       <select class="form-control" name="role" id="role" style="width: 100%; padding: 10px 15px; font-size: 1rem; line-height: 1.5; color: #495057; background-color: #fff; border: 1px solid #ced4da; border-radius: 5px;">
           <option value="users" <?= $user['role'] == 'users' ? 'selected' : '' ?>>Users</option>
           <option value="admin" <?= $user['role'] == 'admin' ? 'selected' : '' ?>>Admin</option>
       </select>
   </div>

   <div style="margin-top: 30px;">
       <button type="submit" class="btn btn-primary" style="background-color: #007bff; color: #fff; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; font-size: 1rem; transition: background-color 0.15s ease-in-out;">Save Changes</button>
       <a href="/users" class="btn btn-secondary" style="background-color: #6c757d; color: #fff; padding: 10px 20px; border: none; border-radius: 5px; text-decoration: none; margin-left: 10px; font-size: 1rem;">Cancel</a>
   </div>
</form>

<script>
   // JavaScript to update the profile image preview (same as before)
   document.getElementById('profile').addEventListener('change', function(event) {
       const file = event.target.files[0]; // Get the selected file
       if (file) {
           const reader = new FileReader(); // Create a FileReader to read the file

           // Set up the FileReader onload event
           reader.onload = function(e) {
               // Update the src attribute of the image preview
               document.getElementById('profilePreview').src = e.target.result;
           };

           // Read the file as a Data URL (base64 encoded)
           reader.readAsDataURL(file);
       }
   });
</script>