<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['user_id'])) {
    $this->redirect('/login');
}
?>

<style>
    body {
        background-color: #f8f9fa;
        min-height: 100vh;
        font-family: 'Nunito', 'Kantumruy Pro', sans-serif;
    }

    .main-content {
        padding: 2.5rem;
        max-width: 1400px;
        margin: 0 auto;
    }

    .header {
        padding-bottom: 1.5rem;
        margin-bottom: 2rem;
        border-bottom: 1px solid #dee2e6;
    }

    .header h2 {
        font-weight: 700;
        color: #212529;
        font-size: 1.75rem;
    }

    .header h2 span {
        color: #6c757d;
        font-weight: 400;
        margin: 0 0.5rem;
    }

    .search-bar input {
        border: 1px solid #ced4da;
        padding: 0.5rem 1rem;
        transition: all 0.3s ease;
    }

    .search-bar input:focus {
        border-color: #007bff;
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
    }

    .action-buttons .btn {
        padding: 0.5rem 1.25rem;
        border-radius: 0.375rem;
        transition: all 0.2s ease;
    }

    .action-buttons .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .card {
        border-radius: 0.5rem;
        overflow: hidden;
    }

    .table {
        margin-bottom: 0;
        background-color: #fff;
    }

    .table th {
        background-color: #212529;
        color: #fff;
        font-weight: 600;
        padding: 1rem;
        border-bottom: 2px solid #dee2e6;
    }

    .table td {
        padding: 1rem;
        vertical-align: middle;
    }

    .profile-img {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        object-fit: cover;
    }

    .role-badge {
        padding: 0.25rem 0.75rem;
        border-radius: 1rem;
        font-size: 0.875rem;
    }

    .checkbox {
        width: 2.5rem;
    }

    .actions-column {
        width: 5rem;
        text-align: center;
    }

    .action-menu-btn {
        background: none;
        border: none;
        font-size: 1.25rem;
        color: #6c757d;
        transition: color 0.2s ease;
    }

    .action-menu-btn:hover {
        color: #212529;
    }

    .dropdown-menu {
        min-width: 8rem;
        border-radius: 0.375rem;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .dropdown-item {
        padding: 0.5rem 1rem;
    }

    .dropdown-item:hover {
        background-color: #f8f9fa;
    }

    .table-hover tbody tr:hover {
        background-color: #f8f9fa;
    }

    @media (max-width: 768px) {
        .main-content {
            padding: 1.5rem;
        }

        .header h2 {
            font-size: 1.5rem;
        }

        .search-bar {
            width: 100% !important;
            margin-top: 1rem;
        }

        .action-buttons {
            gap: 0.75rem !important;
            justify-content: center;
        }

        .action-buttons .btn {
            font-size: 0.9rem;
            padding: 0.5rem 1rem;
            flex: 1 1 45%;
            max-width: none;
        }

        .table th, .table td {
            padding: 0.75rem 0.5rem;
            font-size: 0.9rem;
        }

        .profile-img {
            width: 30px;
            height: 30px;
        }

        .checkbox {
            width: 2rem;
        }

        .actions-column {
            width: 4rem;
        }

        .action-menu-btn {
            font-size: 1rem;
        }
    }
</style>

<div class="main-content">
    <div class="header d-flex justify-content-between align-items-center flex-wrap gap-2">
        <h2 class="mb-0">Recycle Bin <span>User Management</span></h2>
        <div class="col-md-4 col-lg-3 search-bar">
            <input type="text" class="form-control rounded-pill" placeholder="Search deleted users" id="recycleSearchInput">
        </div>
    </div>

    <div class="action-buttons d-flex flex-wrap gap-3 mb-4">
        <button class="btn btn-outline-primary" onclick="restoreSelected()" id="restoreBtn" disabled>
            <i class="ti ti-arrow-back-up me-2"></i> Restore Selection
        </button>
        <button class="btn btn-outline-danger" onclick="deleteSelected()" id="deleteBtn" disabled>
            <i class="ti ti-trash me-2"></i> Delete Selection
        </button>
        <button class="btn btn-danger" onclick="emptyRecycleBin()" id="emptyBtn">
            <i class="ti ti-trash-alt me-2"></i> Empty Recycle Bin
        </button>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped align-middle" id="recycleBinTable">
                    <thead>
                        <tr>
                            <th class="checkbox"><input type="checkbox" id="selectAll" class="form-check-input"></th>
                            <th>Profile</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Role</th>
                            <th>Deleted</th>
                            <th class="actions-column">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="recycleBinTableBody">
                        <?php if (!empty($trashUsers)): ?>
                            <?php foreach ($trashUsers as $user): ?>
                                <tr data-id="<?= htmlspecialchars($user['id']) ?>">
                                    <td class="checkbox"><input type="checkbox" class="recycleCheckbox form-check-input" data-id="<?= htmlspecialchars($user['id']) ?>"></td>
                                    <td>
                                        <?php
                                            $profilePath = !empty($user['profile']) ? '/' . htmlspecialchars($user['profile']) : '/assets/images/userPlaceHolder.png';
                                        ?>
                                        <img 
                                            src="<?= $profilePath ?>" 
                                            onerror="this.onerror=null; this.src='/assets/images/userPlaceHolder.png';" 
                                            alt="Profile" 
                                            class="profile-img">
                                    </td>

                                    <td><?= htmlspecialchars($user['username']) ?></td>
                                    <td><?= htmlspecialchars($user['email']) ?></td>
                                    <td><?= htmlspecialchars($user['phone']) ?></td>
                                    <td>
                                        <span class="role-badge text-white px-3 py-1" style="border-radius: 20px; font-size: 12px; background-color: 
                                            <?php echo ($user['role'] == 'Admin') ? '#dc3545' : 
                                            (($user['role'] == 'User') ? '#ffc107' : '#28a745'); ?>;">
                                            <?= htmlspecialchars($user['role']) ?>
                                        </span>
                                    </td>
                                    <td><?= htmlspecialchars($user['deleted_at']) ?></td>
                                    <td class="actions-column">
                                        <div class="dropdown">
                                            <button class="action-menu-btn" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="ti ti-dots-vertical"></i>
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li><a class="dropdown-item text-success restore-user" href="/users/restore/<?= $user['id'] ?>" data-id="<?= htmlspecialchars($user['id']) ?>">
                                                    <i class="ti ti-arrow-back-up me-2"></i>Restore</a>
                                                </li>
                                                <li><a class="dropdown-item text-danger permanent-delete" href="/users/permanent-delete/<?= $user['id'] ?>" data-id="<?= htmlspecialchars($user['id']) ?>">
                                                    <i class="ti ti-trash me-2"></i>Delete</a>
                                                </li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="text-center">No deleted users found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    // Search functionality
    document.getElementById("recycleSearchInput").addEventListener("input", function() {
        const searchTerm = this.value.toLowerCase();
        const rows = document.querySelectorAll("#recycleBinTableBody tr");
        rows.forEach(row => {
            const username = row.querySelector("td:nth-child(3)")?.textContent.toLowerCase();
            const email = row.querySelector("td:nth-child(4)")?.textContent.toLowerCase();
            if (username && email) {
                row.style.display = (username.includes(searchTerm) || email.includes(searchTerm)) ? "" : "none";
            }
        });
    });
    document.addEventListener("DOMContentLoaded", function () {
    const selectAllCheckbox = document.getElementById("selectAll");
    const checkboxes = document.querySelectorAll(".recycleCheckbox");

    selectAllCheckbox.addEventListener("change", function () {
        checkboxes.forEach(checkbox => {
            checkbox.checked = selectAllCheckbox.checked;
        });
    });

    checkboxes.forEach(checkbox => {
        checkbox.addEventListener("change", function () {
            // If any checkbox is unchecked, uncheck "selectAll"
            if (!this.checked) {
                selectAllCheckbox.checked = false;
            } else {
                // If all checkboxes are checked, check "selectAll"
                selectAllCheckbox.checked = [...checkboxes].every(chk => chk.checked);
            }
        });
    });
});

document.addEventListener('DOMContentLoaded', function() {
    const restoreBtn = document.getElementById('restoreBtn');
    const deleteBtn = document.getElementById('deleteBtn');
    const emptyBtn = document.getElementById('emptyBtn');
    const selectAllCheckbox = document.getElementById('selectAll');
    const recycleCheckboxes = document.querySelectorAll('.recycleCheckbox');
    const recycleBinTableBody = document.getElementById('recycleBinTableBody');

    function updateButtonStates() {
        const checkedCount = document.querySelectorAll('.recycleCheckbox:checked').length;
        restoreBtn.disabled = checkedCount === 0;
        deleteBtn.disabled = checkedCount === 0;
    }

    function getSelectedUserIds() {
        const selectedIds = [];
        recycleCheckboxes.forEach(checkbox => {
            if (checkbox.checked) {
                selectedIds.push(checkbox.dataset.id);
            }
        });
        return selectedIds;
    }

    function handleCheckboxChange() {
        updateButtonStates();
    }

    recycleCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', handleCheckboxChange);
    });

    selectAllCheckbox.addEventListener('change', function() {
        recycleCheckboxes.forEach(checkbox => {
            checkbox.checked = selectAllCheckbox.checked;
        });
        updateButtonStates();
    });

    recycleBinTableBody.addEventListener('change', function() {
        const checkedCount = document.querySelectorAll('.recycleCheckbox:checked').length;
        const totalCheckboxes = recycleCheckboxes.length;
        selectAllCheckbox.checked = checkedCount === totalCheckboxes;
        updateButtonStates();
    });

    restoreBtn.addEventListener('click', function() {
        const selectedIds = getSelectedUserIds();
        if (selectedIds.length > 0) {
            restoreSelected(selectedIds);
        }
    });

    deleteBtn.addEventListener('click', function() {
        const selectedIds = getSelectedUserIds();
        if (selectedIds.length > 0) {
            deleteSelected(selectedIds);
        }
    });

    emptyBtn.addEventListener('click', function() {
        emptyRecycleBin();
    });

    function restoreSelected(ids) {
        if (confirm('Are you sure you want to restore the selected users?')) {
            ids.forEach(id => {
                fetch(`/users/restore/${id}`, {
                    method: 'GET',
                })
                .then(response => {
                    if (response.ok) {
                        document.querySelector(`tr[data-id="${id}"]`).remove();
                    } else {
                        alert('Failed to restore user.');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred.');
                });
            });
            updateButtonStates();
            selectAllCheckbox.checked = false;
        }
    }

    function deleteSelected(ids) {
        if (confirm('Are you sure you want to permanently delete the selected users?')) {
            ids.forEach(id => {
                fetch(`/users/permanent-delete/${id}`, {
                    method: 'GET',
                })
                .then(response => {
                    if (response.ok) {
                        document.querySelector(`tr[data-id="${id}"]`).remove();
                    } else {
                        alert('Failed to delete user.');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred.');
                });
            });
            updateButtonStates();
            selectAllCheckbox.checked = false;
        }
    }

    function emptyRecycleBin() {
        if (confirm('Are you sure you want to empty the recycle bin?')) {
            fetch('/users/empty-recycle-bin', {
                method: 'GET',
            })
            .then(response => {
                if (response.ok) {
                    recycleBinTableBody.innerHTML = '<tr><td colspan="8" class="text-center">No deleted users found.</td></tr>';
                } else {
                    alert('Failed to empty recycle bin.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred.');
            });
        }
    }

    updateButtonStates();
});
</script>
