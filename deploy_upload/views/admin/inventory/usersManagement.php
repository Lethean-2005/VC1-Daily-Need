<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if (isset($_SESSION['user_id'])) :

function dn_avatar_color($seed) {
    $colors = ['#6366f1', '#10b981', '#f59e0b', '#f43f5e', '#0ea5e9', '#8b5cf6', '#14b8a6', '#d946ef'];
    $hash = 0;
    foreach (str_split((string) $seed) as $ch) {
        $hash = ($hash * 31 + ord($ch)) & 0xFFFFFFFF;
    }
    return $colors[$hash % count($colors)];
}

function dn_initials($name) {
    $parts = preg_split('/\s+/', trim($name));
    $initials = '';
    foreach (array_slice($parts, 0, 2) as $p) {
        if ($p !== '') $initials .= mb_strtoupper(mb_substr($p, 0, 1));
    }
    return $initials ?: '?';
}

function dn_user_row($user, $showDelete) {
    $avatarColor = dn_avatar_color($user['id'] . $user['username']);
    ?>
    <tr>
        <td class="dn-cell w-9">
            <input type="checkbox" class="dn-row-checkbox">
        </td>
        <td class="dn-cell">
            <div class="d-flex align-items-center gap-2">
                <?php if (!empty($user['profile'])): ?>
                    <img src="<?= htmlspecialchars($user['profile']) ?>" alt="" class="dn-avatar-img" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                    <span class="dn-avatar-fallback" style="display:none; background:<?= $avatarColor ?>;"><?= dn_initials($user['username']) ?></span>
                <?php else: ?>
                    <span class="dn-avatar-fallback" style="background:<?= $avatarColor ?>;"><?= dn_initials($user['username']) ?></span>
                <?php endif; ?>
                <span class="fw-semibold"><?= htmlspecialchars($user['username']) ?></span>
            </div>
        </td>
        <td class="dn-cell">
            <a href="mailto:<?= htmlspecialchars($user['email']) ?>" class="dn-email-link"><?= htmlspecialchars($user['email']) ?></a>
        </td>
        <td class="dn-cell"><?= htmlspecialchars($user['phone']) ?></td>
        <td class="dn-cell text-capitalize"><?= htmlspecialchars($user['role']) ?></td>
        <td class="dn-cell">
            <?php if (!empty($user['google_id'])): ?>
                <span class="dn-pill dn-pill-good"><i class="ti ti-brand-google"></i> Google</span>
            <?php else: ?>
                <span class="dn-pill dn-pill-neutral"><i class="ti ti-lock"></i> Password</span>
            <?php endif; ?>
        </td>
        <td class="dn-cell w-36">
            <div class="d-flex align-items-center gap-1">
                <a href="/users/edit/<?= (int) $user['id'] ?>" class="dn-row-action">
                    <i class="ti ti-edit"></i> Edit
                </a>
                <?php if ($showDelete): ?>
                    <a href="#" class="dn-row-action dn-row-action-danger delete-user" data-id="<?= (int) $user['id'] ?>">
                        <i class="ti ti-trash"></i> Delete
                    </a>
                <?php endif; ?>
            </div>
        </td>
    </tr>
    <?php
}
?>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

    <style>
        .dn-users-header { margin-bottom: 24px; }
        .dn-users-header h1 { font-size: 1.35rem; font-weight: 700; color: #1f2a1f; margin: 0; }
        .dn-users-header p { font-size: .8rem; color: #888; margin: 2px 0 0; }
        .dn-search-input {
            border-radius: 8px;
            border: 1px solid #e2e2e2;
            padding: 7px 14px;
            font-size: .85rem;
        }

        .dn-table-panel { border: 1px solid #e2e2e2; background: #fff; margin-bottom: 40px; }
        .dn-table-panel h2 {
            font-size: .82rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .04em;
            color: #555;
            padding: 12px 14px;
            margin: 0;
            border-bottom: 1px solid #e2e2e2;
            background: #fafaf8;
        }
        .dn-table-scroll { max-height: 65vh; overflow: auto; }
        table.dn-grid { width: 100%; border-collapse: separate; border-spacing: 0; }
        .dn-header-cell {
            position: sticky;
            top: 0;
            z-index: 1;
            border-right: 1px solid #ccc;
            border-bottom: 1px solid #ccc;
            padding: 8px 12px;
            background: #cbcbcb;
            text-align: left;
            font-size: .68rem;
            font-weight: 600;
            color: #333;
            white-space: nowrap;
        }
        .dn-header-cell:last-child { border-right: none; }
        .dn-cell {
            border-right: 1px solid #eee;
            border-bottom: 1px solid #eee;
            padding: 8px 12px;
            font-size: .78rem;
            color: #333;
            vertical-align: middle;
        }
        .dn-cell:last-child { border-right: none; }
        table.dn-grid tbody tr:hover { background: #fafaf8; }

        .dn-avatar-img { width: 28px; height: 28px; border-radius: 50%; object-fit: cover; flex-shrink: 0; }
        .dn-avatar-fallback {
            width: 28px; height: 28px; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            color: #fff; font-size: .68rem; font-weight: 700; flex-shrink: 0;
        }
        .dn-email-link { color: #4f46e5; text-decoration: underline; text-decoration-color: #c7d2fe; }
        .dn-email-link:hover { color: #6366f1; }

        .dn-pill {
            display: inline-flex; align-items: center; gap: 5px;
            padding: 3px 9px; border-radius: 20px;
            font-size: .7rem; font-weight: 600;
        }
        .dn-pill-good { background: #ecfdf5; color: #047857; }
        .dn-pill-neutral { background: #f4f3ef; color: #555; }

        .dn-row-action {
            display: inline-flex; align-items: center; gap: 4px;
            border: 1px solid #e2e2e2; background: #fff; border-radius: 6px;
            padding: 4px 8px; font-size: .7rem; font-weight: 600; color: #555;
            text-decoration: none;
        }
        .dn-row-action:hover { background: #f7f6f3; color: #333; }
        .dn-row-action-danger { border-color: #fecaca; color: #dc2626; }
        .dn-row-action-danger:hover { background: #fef2f2; color: #dc2626; }
    </style>

    <div class="dn-users-header d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h1>User Management</h1>
            <p>Manage admin and customer accounts.</p>
        </div>
        <input type="text" class="dn-search-input" placeholder="Search users..." id="recycleSearchInput">
    </div>

    <div class="dn-table-panel">
        <h2>Admin</h2>
        <div class="dn-table-scroll">
            <table class="dn-grid">
                <thead>
                    <tr>
                        <th class="dn-header-cell w-9"><input type="checkbox"></th>
                        <th class="dn-header-cell"><i class="ti ti-user"></i> User</th>
                        <th class="dn-header-cell"><i class="ti ti-at"></i> Email</th>
                        <th class="dn-header-cell"><i class="ti ti-phone"></i> Phone</th>
                        <th class="dn-header-cell"><i class="ti ti-shield-lock"></i> Role</th>
                        <th class="dn-header-cell"><i class="ti ti-key"></i> Auth</th>
                        <th class="dn-header-cell w-36"><i class="ti ti-settings"></i> Actions</th>
                    </tr>
                </thead>
                <tbody id="userTableBody">
                    <?php foreach ($users as $user): ?>
                        <?php if ($user['role'] === 'admin'): dn_user_row($user, false); endif; ?>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="dn-table-panel">
        <h2>Customers</h2>
        <div class="dn-table-scroll">
            <table class="dn-grid">
                <thead>
                    <tr>
                        <th class="dn-header-cell w-9"><input type="checkbox"></th>
                        <th class="dn-header-cell"><i class="ti ti-user"></i> User</th>
                        <th class="dn-header-cell"><i class="ti ti-at"></i> Email</th>
                        <th class="dn-header-cell"><i class="ti ti-phone"></i> Phone</th>
                        <th class="dn-header-cell"><i class="ti ti-shield-lock"></i> Role</th>
                        <th class="dn-header-cell"><i class="ti ti-key"></i> Auth</th>
                        <th class="dn-header-cell w-36"><i class="ti ti-settings"></i> Actions</th>
                    </tr>
                </thead>
                <tbody id="userTableBodyCustomers">
                    <?php foreach ($users as $user): ?>
                        <?php if ($user['role'] === 'users'): dn_user_row($user, true); endif; ?>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
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

    document.getElementById("recycleSearchInput").addEventListener("input", function() {
        const searchTerm = this.value.toLowerCase();
        document.querySelectorAll('#userTableBody tr, #userTableBodyCustomers tr').forEach(row => {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(searchTerm) ? "" : "none";
        });
    });
</script>
<?php
else:
    $this->redirect("/admin-login");
endif;
?>
