<?php require_once '../config/database.php';require_once '../config/helpers.php';
requireAdmin();$users=$pdo->query("SELECT id,name,email,phone,created_at FROM users ORDER BY id DESC")->fetchAll();$pageTitle='Customers';
include '../includes/admin_header.php';?><h1>Customers</h1><div class="table-wrap">
    <table class="table"><tr><th>ID</th><th>Name</th><th>Email</th><th>Phone</th><th>Registered</th><th>Actions</th></tr>
    <?php foreach($users as $u):?><tr id="user-row-<?=$u['id']?>"><td><?=$u['id']?></td><td><?=e($u['name'])?></td><td><?=e($u['email'])?></td><td><?=e($u['phone'])?></td><td><?=e($u['created_at'])?></td>
        <td><a class="btn small" href="edit-user.php?id=<?=$u['id']?>">Edit</a> <a class="btn danger small remove-btn" href="delete-user.php?id=<?=$u['id']?>" data-confirm="Are you sure you want to delete <?=e($u['name'])?>? This can't be undone.">Delete</a></td></tr>
        <?php endforeach;?></table>
    <?php if(!$users): ?><div class="empty">No customers yet.</div><?php endif; ?></div><?php include '../includes/admin_footer.php';?>