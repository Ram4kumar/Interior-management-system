<?php $page='Dashboard'; require_once __DIR__.'/../includes/header.php';
$totClients   = (int)$pdo->query("SELECT COUNT(*) FROM clients")->fetchColumn();
$totProjects  = (int)$pdo->query("SELECT COUNT(*) FROM projects")->fetchColumn();
$ongoing      = (int)$pdo->query("SELECT COUNT(*) FROM projects WHERE status='ongoing'")->fetchColumn();
$completed    = (int)$pdo->query("SELECT COUNT(*) FROM projects WHERE status='completed'")->fetchColumn();
$pendingPay   = (float)$pdo->query("SELECT COALESCE(SUM(amount),0) FROM payments WHERE status='pending'")->fetchColumn();
$revenueRows  = $pdo->query("SELECT DATE_FORMAT(paid_at,'%Y-%m') m, SUM(amount) s FROM payments WHERE status='paid' AND paid_at IS NOT NULL GROUP BY m ORDER BY m DESC LIMIT 6")->fetchAll();
$revenueRows  = array_reverse($revenueRows);
$activities   = $pdo->query("SELECT a.*, u.full_name FROM activity_logs a LEFT JOIN users u ON u.user_id=a.user_id ORDER BY a.created_at DESC LIMIT 8")->fetchAll();
?>
<div class="row g-3 mb-4">
  <div class="col-12 col-md-6 col-xl-3"><div class="stat"><div class="label">Total clients</div><div class="value"><?= $totClients ?></div><div class="icon"><i class="bi bi-people-fill"></i></div></div></div>
  <div class="col-12 col-md-6 col-xl-3"><div class="stat success"><div class="label">Total projects</div><div class="value"><?= $totProjects ?></div><div class="icon"><i class="bi bi-briefcase-fill"></i></div></div></div>
  <div class="col-12 col-md-6 col-xl-3"><div class="stat warn"><div class="label">Ongoing</div><div class="value"><?= $ongoing ?></div><div class="icon"><i class="bi bi-hourglass-split"></i></div></div></div>
  <div class="col-12 col-md-6 col-xl-3"><div class="stat danger"><div class="label">Pending payments</div><div class="value">₹<?= number_format($pendingPay,0) ?></div><div class="icon"><i class="bi bi-cash-stack"></i></div></div></div>
</div>

<div class="row g-3">
  <div class="col-12 col-xl-8"><div class="card-soft p-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h5 class="m-0">Monthly revenue</h5><span class="badge bg-secondary"><?= $completed ?> completed</span>
    </div>
    <div class="chart-wrap"><canvas id="revChart"></canvas></div>
  </div></div>
  <div class="col-12 col-xl-4"><div class="card-soft p-4">
    <h5 class="mb-3">Recent activity</h5>
    <?php if(!$activities):?><p class="text-secondary m-0">No activity yet.</p>
    <?php else: foreach($activities as $a):?>
      <div class="d-flex gap-3 py-2" style="border-bottom:1px solid var(--border)">
        <div style="width:34px;height:34px;border-radius:10px;background:var(--panel-2);display:grid;place-items:center;color:var(--primary)"><i class="bi bi-activity"></i></div>
        <div class="flex-grow-1">
          <div style="font-size:.9rem;font-weight:600"><?=e($a['action'])?></div>
          <div style="font-size:.78rem;color:var(--muted)"><?=e($a['full_name']??'system')?> · <?=e($a['created_at'])?></div>
        </div>
      </div>
    <?php endforeach; endif;?>
  </div></div>
</div>

<script>
const ctx = document.getElementById('revChart');
new Chart(ctx, {
  type:'line',
  data:{
    labels: <?= json_encode(array_column($revenueRows,'m') ?: ['—']) ?>,
    datasets:[{label:'Revenue (₹)', data: <?= json_encode(array_map('floatval',array_column($revenueRows,'s')) ?: [0]) ?>,
      borderColor:'#6c8bff', backgroundColor:'rgba(108,139,255,.18)', fill:true, tension:.35, borderWidth:2, pointRadius:4}]
  },
  options:{responsive:true,maintainAspectRatio:false,
    plugins:{legend:{labels:{color:'#e6e9f5'}}},
    scales:{x:{ticks:{color:'#8a93b8'},grid:{color:'rgba(255,255,255,.05)'}},
            y:{ticks:{color:'#8a93b8'},grid:{color:'rgba(255,255,255,.05)'}}}}
});
</script>
<?php require_once __DIR__.'/../includes/footer.php'; ?>
