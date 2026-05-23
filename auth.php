<?php $page='Clients'; require_once __DIR__.'/../includes/header.php';
$action = $_GET['action'] ?? 'list';
$id = (int)($_GET['id'] ?? 0);
$flash = '';

if ($_SERVER['REQUEST_METHOD']==='POST') {
  csrf_check($_POST['csrf'] ?? '');
  $data = [
    'name'=>trim($_POST['name'] ?? ''),
    'phone'=>trim($_POST['phone'] ?? ''),
    'email'=>trim($_POST['email'] ?? ''),
    'address'=>trim($_POST['address'] ?? ''),
    'requirements'=>trim($_POST['requirements'] ?? ''),
    'budget'=>(float)($_POST['budget'] ?? 0),
  ];
  if ($data['name']==='') { $flash='Name is required'; }
  elseif (!empty($_POST['id'])) {
    $s=$pdo->prepare("UPDATE clients SET name=?,phone=?,email=?,address=?,requirements=?,budget=? WHERE client_id=?");
    $s->execute([...array_values($data),(int)$_POST['id']]);
    log_activity($pdo,'client.update','Updated client #'.(int)$_POST['id']);
    header('Location: clients.php?ok=1'); exit;
  } else {
    $s=$pdo->prepare("INSERT INTO clients (name,phone,email,address,requirements,budget,created_by) VALUES (?,?,?,?,?,?,?)");
    $s->execute([...array_values($data),$_SESSION['user']['id']]);
    log_activity($pdo,'client.create','Added client '.$data['name']);
    header('Location: clients.php?ok=1'); exit;
  }
}
if ($action==='delete' && $id) {
  $pdo->prepare("DELETE FROM clients WHERE client_id=?")->execute([$id]);
  log_activity($pdo,'client.delete','Deleted client #'.$id);
  header('Location: clients.php?ok=1'); exit;
}

$editing = null;
if ($action==='edit' && $id) {
  $s=$pdo->prepare("SELECT * FROM clients WHERE client_id=?"); $s->execute([$id]); $editing=$s->fetch();
}
$q = trim($_GET['q'] ?? '');
$sql = "SELECT * FROM clients"; $args=[];
if ($q!==''){ $sql.=" WHERE name LIKE ? OR email LIKE ? OR phone LIKE ?"; $like="%$q%"; $args=[$like,$like,$like]; }
$sql.=" ORDER BY created_at DESC";
$st=$pdo->prepare($sql); $st->execute($args); $clients=$st->fetchAll();
?>
<div class="d-flex justify-content-between align-items-center mb-4">
  <div><h3 class="m-0">Clients</h3><div class="text-secondary small">Manage your client directory and project requirements</div></div>
  <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#clientModal" onclick="resetForm()"><i class="bi bi-plus-lg me-1"></i> Add client</button>
</div>
<?php if(!empty($_GET['ok'])):?><div class="alert alert-success py-2">Saved.</div><?php endif;?>
<div class="card-soft p-3 mb-3">
  <form class="d-flex gap-2" method="get">
    <input class="form-control" name="q" value="<?=e($q)?>" placeholder="Search by name, email or phone...">
    <button class="btn btn-outline-light"><i class="bi bi-search"></i></button>
  </form>
</div>
<div class="card-soft p-0">
  <div class="table-responsive"><table class="table table-dark-soft align-middle m-0">
    <thead><tr><th class="ps-4">Client</th><th>Contact</th><th>Budget</th><th>Created</th><th class="text-end pe-4">Actions</th></tr></thead>
    <tbody>
    <?php if(!$clients):?><tr><td colspan="5" class="text-center text-secondary py-5">No clients yet. Add your first one.</td></tr><?php endif;?>
    <?php foreach($clients as $c):?>
      <tr>
        <td class="ps-4"><div style="font-weight:600"><?=e($c['name'])?></div><div class="small text-secondary"><?=e(mb_strimwidth($c['requirements']??'',0,60,'…'))?></div></td>
        <td><div class="small"><?=e($c['email'])?></div><div class="small text-secondary"><?=e($c['phone'])?></div></td>
        <td>₹<?= number_format((float)$c['budget'],0) ?></td>
        <td class="small text-secondary"><?=e($c['created_at'])?></td>
        <td class="text-end pe-4">
          <a class="btn btn-sm btn-outline-light" href="?action=edit&id=<?=$c['client_id']?>"><i class="bi bi-pencil"></i></a>
          <a class="btn btn-sm btn-outline-light" href="?action=delete&id=<?=$c['client_id']?>" onclick="return confirm('Delete this client?')"><i class="bi bi-trash"></i></a>
        </td>
      </tr>
    <?php endforeach;?>
    </tbody>
  </table></div>
</div>

<div class="modal fade" id="clientModal" tabindex="-1"><div class="modal-dialog modal-lg modal-dialog-centered"><div class="modal-content" style="background:var(--panel);border:1px solid var(--border)">
  <form method="post">
    <div class="modal-header" style="border-color:var(--border)"><h5 class="modal-title" id="cmTitle">Add client</h5><button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button></div>
    <div class="modal-body">
      <input type="hidden" name="csrf" value="<?=csrf_token()?>">
      <input type="hidden" name="id" id="cm-id" value="">
      <div class="row g-3">
        <div class="col-md-6"><label class="form-label">Name *</label><input name="name" id="cm-name" class="form-control" required></div>
        <div class="col-md-6"><label class="form-label">Phone</label><input name="phone" id="cm-phone" class="form-control"></div>
        <div class="col-md-6"><label class="form-label">Email</label><input name="email" id="cm-email" type="email" class="form-control"></div>
        <div class="col-md-6"><label class="form-label">Budget (₹)</label><input name="budget" id="cm-budget" type="number" step="0.01" class="form-control"></div>
        <div class="col-12"><label class="form-label">Address</label><textarea name="address" id="cm-address" class="form-control" rows="2"></textarea></div>
        <div class="col-12"><label class="form-label">Project requirements</label><textarea name="requirements" id="cm-req" class="form-control" rows="3"></textarea></div>
      </div>
    </div>
    <div class="modal-footer" style="border-color:var(--border)"><button type="button" class="btn btn-outline-light" data-bs-dismiss="modal">Cancel</button><button class="btn btn-primary">Save client</button></div>
  </form>
</div></div></div>

<script>
function resetForm(){
  document.getElementById('cmTitle').textContent='Add client';
  ['id','name','phone','email','budget','address','req'].forEach(k=>{const el=document.getElementById('cm-'+k); if(el) el.value='';});
}
<?php if($editing): ?>
window.addEventListener('DOMContentLoaded',()=>{
  document.getElementById('cmTitle').textContent='Edit client';
  const v = <?= json_encode($editing) ?>;
  document.getElementById('cm-id').value=v.client_id;
  document.getElementById('cm-name').value=v.name||'';
  document.getElementById('cm-phone').value=v.phone||'';
  document.getElementById('cm-email').value=v.email||'';
  document.getElementById('cm-budget').value=v.budget||'';
  document.getElementById('cm-address').value=v.address||'';
  document.getElementById('cm-req').value=v.requirements||'';
  new bootstrap.Modal(document.getElementById('clientModal')).show();
});
<?php endif; ?>
</script>
<?php require_once __DIR__.'/../includes/footer.php'; ?>
