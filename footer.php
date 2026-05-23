<?php $page='Projects'; require_once __DIR__.'/../includes/header.php';
$action=$_GET['action']??'list'; $id=(int)($_GET['id']??0);

if ($_SERVER['REQUEST_METHOD']==='POST') {
  csrf_check($_POST['csrf'] ?? '');
  $data=[
    'title'=>trim($_POST['title']??''),
    'client_id'=>(int)($_POST['client_id']??0),
    'assigned_to'=>($_POST['assigned_to']??'')===''?null:(int)$_POST['assigned_to'],
    'status'=>in_array($_POST['status']??'',['pending','ongoing','completed'])?$_POST['status']:'pending',
    'start_date'=>$_POST['start_date']?:null,
    'end_date'=>$_POST['end_date']?:null,
    'budget'=>(float)($_POST['budget']??0),
    'location'=>trim($_POST['location']??''),
    'notes'=>trim($_POST['notes']??''),
  ];
  // file upload
  $cover=null;
  if (!empty($_FILES['cover']['name'])) {
    $allowed=['jpg','jpeg','png','webp']; $ext=strtolower(pathinfo($_FILES['cover']['name'],PATHINFO_EXTENSION));
    if (in_array($ext,$allowed) && $_FILES['cover']['size']<=4*1024*1024) {
      $fn='p_'.time().'_'.bin2hex(random_bytes(4)).'.'.$ext;
      $dest=__DIR__.'/../uploads/projects/'.$fn;
      if (move_uploaded_file($_FILES['cover']['tmp_name'],$dest)) $cover='uploads/projects/'.$fn;
    }
  }
  if (!empty($_POST['id'])) {
    $sql="UPDATE projects SET title=?,client_id=?,assigned_to=?,status=?,start_date=?,end_date=?,budget=?,location=?,notes=?".($cover?",cover_image=?":"")." WHERE project_id=?";
    $args=[...array_values($data)]; if($cover)$args[]=$cover; $args[]=(int)$_POST['id'];
    $pdo->prepare($sql)->execute($args);
    log_activity($pdo,'project.update','Updated project #'.(int)$_POST['id']);
  } else {
    $pdo->prepare("INSERT INTO projects (title,client_id,assigned_to,status,start_date,end_date,budget,location,notes,cover_image) VALUES (?,?,?,?,?,?,?,?,?,?)")
        ->execute([...array_values($data),$cover]);
    log_activity($pdo,'project.create','Created project '.$data['title']);
  }
  header('Location: projects.php?ok=1'); exit;
}
if ($action==='delete' && $id) {
  $pdo->prepare("DELETE FROM projects WHERE project_id=?")->execute([$id]);
  log_activity($pdo,'project.delete','Deleted project #'.$id);
  header('Location: projects.php?ok=1'); exit;
}

$editing=null;
if($action==='edit' && $id){
  $s=$pdo->prepare("SELECT * FROM projects WHERE project_id=?"); $s->execute([$id]); $editing=$s->fetch();
}

$status = $_GET['status'] ?? '';
$sql = "SELECT p.*, c.name client_name, u.full_name assignee FROM projects p
        LEFT JOIN clients c ON c.client_id=p.client_id
        LEFT JOIN users u ON u.user_id=p.assigned_to";
$args=[];
if (in_array($status,['pending','ongoing','completed'])) { $sql.=" WHERE p.status=?"; $args=[$status]; }
$sql.=" ORDER BY p.created_at DESC";
$st=$pdo->prepare($sql); $st->execute($args); $rows=$st->fetchAll();

$clients=$pdo->query("SELECT client_id,name FROM clients ORDER BY name")->fetchAll();
$staff=$pdo->query("SELECT user_id,full_name FROM users WHERE is_active=1 ORDER BY full_name")->fetchAll();
?>
<div class="d-flex justify-content-between align-items-center mb-4">
  <div><h3 class="m-0">Projects</h3><div class="text-secondary small">Plan, assign and track interior projects end-to-end</div></div>
  <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#projectModal" onclick="resetForm()"><i class="bi bi-plus-lg me-1"></i> New project</button>
</div>
<?php if(!empty($_GET['ok'])):?><div class="alert alert-success py-2">Saved.</div><?php endif;?>
<div class="d-flex gap-2 mb-3 flex-wrap">
  <a class="btn btn-sm <?= $status===''?'btn-primary':'btn-outline-light'?>" href="projects.php">All</a>
  <a class="btn btn-sm <?= $status==='pending'?'btn-primary':'btn-outline-light'?>" href="?status=pending">Pending</a>
  <a class="btn btn-sm <?= $status==='ongoing'?'btn-primary':'btn-outline-light'?>" href="?status=ongoing">Ongoing</a>
  <a class="btn btn-sm <?= $status==='completed'?'btn-primary':'btn-outline-light'?>" href="?status=completed">Completed</a>
</div>

<div class="row g-3">
<?php if(!$rows):?><div class="col-12"><div class="card-soft p-5 text-center text-secondary">No projects yet.</div></div><?php endif;?>
<?php foreach($rows as $r):?>
  <div class="col-12 col-md-6 col-xl-4"><div class="card-soft h-100" style="overflow:hidden">
    <?php if($r['cover_image']):?>
      <img src="<?=APP_URL.'/'.e($r['cover_image'])?>" style="width:100%;height:160px;object-fit:cover">
    <?php else:?>
      <div style="height:160px;background:linear-gradient(135deg,var(--panel-2),#293567);display:grid;place-items:center;color:var(--muted)"><i class="bi bi-image" style="font-size:2rem"></i></div>
    <?php endif;?>
    <div class="p-3">
      <div class="d-flex justify-content-between align-items-start gap-2 mb-1">
        <h5 class="m-0" style="font-size:1.05rem"><?=e($r['title'])?></h5>
        <span class="badge-status badge-<?=e($r['status'])?>"><?=e($r['status'])?></span>
      </div>
      <div class="small text-secondary mb-2"><i class="bi bi-person me-1"></i><?=e($r['client_name']??'—')?> · <i class="bi bi-geo-alt me-1"></i><?=e($r['location']?:'—')?></div>
      <div class="small mb-3"><i class="bi bi-person-badge me-1 text-secondary"></i><?=e($r['assignee']?:'Unassigned')?></div>
      <div class="d-flex justify-content-between align-items-center">
        <div><div class="small text-secondary">Budget</div><div style="font-weight:700">₹<?=number_format((float)$r['budget'],0)?></div></div>
        <div class="d-flex gap-1">
          <a class="btn btn-sm btn-outline-light" href="?action=edit&id=<?=$r['project_id']?>"><i class="bi bi-pencil"></i></a>
          <a class="btn btn-sm btn-outline-light" href="?action=delete&id=<?=$r['project_id']?>" onclick="return confirm('Delete project?')"><i class="bi bi-trash"></i></a>
        </div>
      </div>
    </div>
  </div></div>
<?php endforeach;?>
</div>

<div class="modal fade" id="projectModal" tabindex="-1"><div class="modal-dialog modal-lg modal-dialog-centered"><div class="modal-content" style="background:var(--panel);border:1px solid var(--border)">
  <form method="post" enctype="multipart/form-data">
    <div class="modal-header" style="border-color:var(--border)"><h5 class="modal-title" id="pmTitle">New project</h5><button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button></div>
    <div class="modal-body">
      <input type="hidden" name="csrf" value="<?=csrf_token()?>"><input type="hidden" name="id" id="pm-id">
      <div class="row g-3">
        <div class="col-md-8"><label class="form-label">Title *</label><input name="title" id="pm-title" class="form-control" required></div>
        <div class="col-md-4"><label class="form-label">Status</label>
          <select name="status" id="pm-status" class="form-select"><option value="pending">Pending</option><option value="ongoing">Ongoing</option><option value="completed">Completed</option></select></div>
        <div class="col-md-6"><label class="form-label">Client *</label><select name="client_id" id="pm-client" class="form-select" required>
          <option value="">— Select client —</option><?php foreach($clients as $c):?><option value="<?=$c['client_id']?>"><?=e($c['name'])?></option><?php endforeach;?></select></div>
        <div class="col-md-6"><label class="form-label">Assigned to</label><select name="assigned_to" id="pm-assignee" class="form-select">
          <option value="">Unassigned</option><?php foreach($staff as $u):?><option value="<?=$u['user_id']?>"><?=e($u['full_name'])?></option><?php endforeach;?></select></div>
        <div class="col-md-4"><label class="form-label">Start date</label><input type="date" name="start_date" id="pm-start" class="form-control"></div>
        <div class="col-md-4"><label class="form-label">End date</label><input type="date" name="end_date" id="pm-end" class="form-control"></div>
        <div class="col-md-4"><label class="form-label">Budget (₹)</label><input type="number" step="0.01" name="budget" id="pm-budget" class="form-control"></div>
        <div class="col-md-6"><label class="form-label">Location</label><input name="location" id="pm-location" class="form-control"></div>
        <div class="col-md-6"><label class="form-label">Cover image</label><input type="file" name="cover" class="form-control" accept="image/*"></div>
        <div class="col-12"><label class="form-label">Notes</label><textarea name="notes" id="pm-notes" class="form-control" rows="3"></textarea></div>
      </div>
    </div>
    <div class="modal-footer" style="border-color:var(--border)"><button type="button" class="btn btn-outline-light" data-bs-dismiss="modal">Cancel</button><button class="btn btn-primary">Save project</button></div>
  </form>
</div></div></div>

<script>
function resetForm(){
  document.getElementById('pmTitle').textContent='New project';
  ['id','title','client','assignee','start','end','budget','location','notes'].forEach(k=>{const el=document.getElementById('pm-'+k); if(el) el.value='';});
  document.getElementById('pm-status').value='pending';
}
<?php if($editing): ?>
window.addEventListener('DOMContentLoaded',()=>{
  document.getElementById('pmTitle').textContent='Edit project';
  const v=<?=json_encode($editing)?>;
  document.getElementById('pm-id').value=v.project_id;
  document.getElementById('pm-title').value=v.title||'';
  document.getElementById('pm-client').value=v.client_id||'';
  document.getElementById('pm-assignee').value=v.assigned_to||'';
  document.getElementById('pm-status').value=v.status||'pending';
  document.getElementById('pm-start').value=v.start_date||'';
  document.getElementById('pm-end').value=v.end_date||'';
  document.getElementById('pm-budget').value=v.budget||'';
  document.getElementById('pm-location').value=v.location||'';
  document.getElementById('pm-notes').value=v.notes||'';
  new bootstrap.Modal(document.getElementById('projectModal')).show();
});
<?php endif;?>
</script>
<?php require_once __DIR__.'/../includes/footer.php'; ?>
