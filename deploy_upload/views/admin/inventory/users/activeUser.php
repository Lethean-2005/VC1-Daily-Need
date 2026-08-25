<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['user_id'])) {
    $this->redirect('/login');
}
?>
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
<div class="header d-flex justify-content-between align-items-center flex-wrap gap-2">
    <h1 class="text-center mb-4">USERS STATUS</h1>
    <div class="col-md-4 col-lg-3 search-bar">
        <input type="text" class="form-control rounded-pill" placeholder="Search users" id="recycleSearchInput">
    </div>
</div>
<div class="container mt-5">
        <table class="table table-bordered table-striped table-hover" style="border-radius: 15px; overflow: y;">
            <thead class="table-dark">
            <tr>
                <th scope="col" style="border-top-left-radius: 2px;">Profile</th>
                <th scope="col">Username</th>
                <th scope="col">Email</th>
                <th scope="col">Phone</th>
                <th scope="col">Role</th>
                <th scope="col">Status</th>
                <th class="text-center" style="border-top-right-radius: 2px;" scope="col">Actions</th>
            </tr>
        </thead>
        <tbody id="userTableBody">
            <?php foreach ($users as $user): ?>
                <?php
                $lastLogin = isset($user['last_login']) ? strtotime($user['last_login']) : null;
                $currentTime = time();
                $isActive = ($lastLogin && ($currentTime - $lastLogin) <= 300);
                $statusLabel = $isActive ? 'Active' : 'Inactive';
                $statusColor = $isActive ? '#28a745' : '#dc3545';
                ?>
                <tr>
                    <td class="text-center align-middle">
                        <img src="/<?= htmlspecialchars($user['profile']) ?>" alt="Profile Image" width="50" height="50" class="rounded-circle">
                    </td>
                    <td><?= htmlspecialchars($user['username']) ?></td>
                    <td><?= htmlspecialchars($user['email']) ?></td>
                    <td><?= htmlspecialchars($user['phone']) ?></td>
                    <td>
                        <span class="badge text-white px-3 py-1" style="border-radius: 20px; font-size: 12px; background-color: 
                            <?= ($user['role'] == 'Admin') ? '#dc3545' : 
                            (($user['role'] == 'User') ? '#ffc107' : '#28a745'); ?>;">
                            <?= htmlspecialchars($user['role']) ?>
                        </span>
                    </td>
                    <td>
                        <span class="badge text-white px-3 py-1" style="border-radius: 20px; font-size: 12px; background-color: <?= $statusColor; ?>;">
                            <?= $statusLabel; ?>
                        </span>
                    </td>
                    <td style="text-align: center; vertical-align: middle;">
                            <div class="dropdown">
                                <button class="btn btn-light border-0" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="background: none;">
                                    <i class="material-icons">more_vert</i>
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="/users/edit/<?= $user['id'] ?>">
                                        <i class="material-icons">edit</i> Edit</a></li>
                                    <?php if ($user['role'] !== 'admin') : ?>
                                        <li><a class="dropdown-item text-danger delete-user" href="#" data-id="<?= $user['id'] ?>">
                                            <i class="material-icons">delete</i> Delete</a></li>
                                    <?php endif; ?>
                                </ul>
                            </div>
                    </td>

                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.getElementById("recycleSearchInput").addEventListener("input", function() {
    const searchTerm = this.value.toLowerCase();
    const rows = document.querySelectorAll("#userTableBody tr");
    rows.forEach(row => {
        const username = row.querySelector("td:nth-child(2)")?.textContent?.toLowerCase() || "";
        const email = row.querySelector("td:nth-child(3)")?.textContent?.toLowerCase() || "";
        const phone = row.querySelector("td:nth-child(4)")?.textContent?.toLowerCase() || "";
        const role = row.querySelector("td:nth-child(5)")?.textContent?.toLowerCase() || "";
        const status = row.querySelector("td:nth-child(6)")?.textContent?.toLowerCase() || "";
        row.style.display = (username.includes(searchTerm) || email.includes(searchTerm) || phone.includes(searchTerm) || role.includes(searchTerm) || status.includes(searchTerm)) ? "" : "none";
    });
});

document.querySelectorAll('.delete-user').forEach(button => {
    button.addEventListener('click', function(event) {
        event.preventDefault();
        const userId = this.getAttribute('data-id');
        Swal.fire({
            title: 'Are you sure?',
            text: "This user will be moved to the trash!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, move to trash!'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = `/users/delete/${userId}`;
            }
        });
    });
});
</script>
