<?php require_once '../config/database.php';require_once '../config/helpers.php';
requireAdmin();$pageTitle='Admins';
$admins=$pdo->query("SELECT id,name,email FROM admins ORDER BY id ASC")->fetchAll();
include '../includes/admin_header.php';?>
<div class="admin-actions"><h1>Admins</h1><a class="btn" href="add-admin.php">+ Add Admin</a></div>
<div class="table-wrap">
    <table class="table"><tr><th>ID</th><th>Name</th><th>Email</th><th>Actions</th></tr>
    <?php foreach($admins as $a):?><tr id="admin-row-<?=$a['id']?>"><td><?=$a['id']?></td><td><?=e($a['name'])?><?php if($a['id']==$_SESSION['admin_id']):?> <span class="badge">You</span><?php endif;?></td><td><?=e($a['email'])?></td>
        <td><?php if($a['id']!=$_SESSION['admin_id']):?><a class="btn danger small remove-btn" href="delete-admin.php?id=<?=$a['id']?>" data-confirm="Are you sure you want to remove admin <?=e($a['name'])?>?">Delete</a><?php else:?><span class="muted small">Current account</span><?php endif;?></td></tr>
        <?php endforeach;?></table>
    <?php if(!$admins): ?><div class="empty">No admins found.</div><?php endif; ?>
</div>
<?php include '../includes/admin_footer.php';?>
