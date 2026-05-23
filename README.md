:root{
  --bg:#0b1020; --panel:#121933; --panel-2:#1a2347; --border:#243056;
  --text:#e6e9f5; --muted:#8a93b8; --primary:#6c8bff; --primary-2:#4f6dff;
  --success:#39d39f; --warn:#ffb547; --danger:#ff5b6b; --accent:#b388ff;
  --shadow:0 10px 30px -10px rgba(0,0,0,.55);
}
html,body{background:var(--bg);color:var(--text);font-family:'Inter',system-ui,sans-serif;min-height:100vh}
a{color:var(--primary)}
.app{display:flex;min-height:100vh}
.sidebar{width:260px;background:linear-gradient(180deg,#101635,#0b1020);border-right:1px solid var(--border);position:fixed;inset:0 auto 0 0;padding:20px;overflow-y:auto;transition:.25s}
.sidebar .brand{font-weight:800;font-size:1.25rem;letter-spacing:.3px;color:#fff;margin-bottom:24px;display:flex;gap:10px;align-items:center}
.sidebar .brand .dot{width:14px;height:14px;border-radius:4px;background:linear-gradient(135deg,var(--primary),var(--accent));box-shadow:0 0 18px var(--primary)}
.sidebar a.nav-link{color:var(--muted);display:flex;align-items:center;gap:12px;padding:11px 14px;border-radius:10px;margin-bottom:4px;font-weight:500;transition:.2s}
.sidebar a.nav-link:hover{background:var(--panel-2);color:#fff;transform:translateX(2px)}
.sidebar a.nav-link.active{background:linear-gradient(135deg,rgba(108,139,255,.18),rgba(179,136,255,.12));color:#fff;border:1px solid var(--border)}
.sidebar .section-title{font-size:.72rem;text-transform:uppercase;letter-spacing:.12em;color:#566089;margin:18px 8px 8px}
.main{flex:1;margin-left:260px;display:flex;flex-direction:column;min-width:0}
.topbar{position:sticky;top:0;z-index:20;background:rgba(11,16,32,.85);backdrop-filter:blur(10px);border-bottom:1px solid var(--border);padding:14px 24px;display:flex;align-items:center;justify-content:space-between}
.topbar .title{font-weight:700;font-size:1.05rem}
.profile-chip{display:flex;align-items:center;gap:10px;padding:6px 12px;border:1px solid var(--border);border-radius:999px;background:var(--panel)}
.profile-chip .avatar{width:30px;height:30px;border-radius:50%;background:linear-gradient(135deg,var(--primary),var(--accent));display:grid;place-items:center;color:#fff;font-weight:700;font-size:.8rem}
.content{padding:28px;animation:fadeUp .4s ease}
@keyframes fadeUp{from{opacity:0;transform:translateY(8px)}to{opacity:1;transform:none}}
.card-soft{background:var(--panel);border:1px solid var(--border);border-radius:16px;box-shadow:var(--shadow);color:var(--text)}
.stat{padding:20px;border-radius:16px;background:linear-gradient(135deg,var(--panel),var(--panel-2));border:1px solid var(--border);position:relative;overflow:hidden;transition:.25s}
.stat:hover{transform:translateY(-3px);border-color:#3a4a85}
.stat .label{color:var(--muted);font-size:.8rem;text-transform:uppercase;letter-spacing:.1em}
.stat .value{font-size:1.9rem;font-weight:800;margin-top:6px}
.stat .icon{position:absolute;right:16px;top:16px;width:42px;height:42px;border-radius:12px;display:grid;place-items:center;background:rgba(108,139,255,.15);color:var(--primary);font-size:1.2rem}
.stat.success .icon{background:rgba(57,211,159,.15);color:var(--success)}
.stat.warn .icon{background:rgba(255,181,71,.15);color:var(--warn)}
.stat.danger .icon{background:rgba(255,91,107,.15);color:var(--danger)}
.table-dark-soft{--bs-table-bg:transparent;color:var(--text)}
.table-dark-soft thead th{color:var(--muted);font-weight:600;font-size:.78rem;text-transform:uppercase;letter-spacing:.08em;border-bottom:1px solid var(--border)}
.table-dark-soft tbody tr{border-color:var(--border)}
.table-dark-soft tbody tr:hover{background:rgba(255,255,255,.02)}
.form-control,.form-select{background:var(--panel-2);border:1px solid var(--border);color:var(--text);border-radius:10px;padding:.65rem .85rem}
.form-control:focus,.form-select:focus{background:var(--panel-2);color:var(--text);border-color:var(--primary);box-shadow:0 0 0 .2rem rgba(108,139,255,.18)}
.form-label{color:var(--muted);font-weight:500;font-size:.85rem}
.btn-primary{background:linear-gradient(135deg,var(--primary),var(--primary-2));border:0;font-weight:600;padding:.6rem 1.1rem;border-radius:10px;box-shadow:0 8px 20px -10px var(--primary)}
.btn-primary:hover{transform:translateY(-1px);filter:brightness(1.05)}
.btn-outline-light{border-color:var(--border);color:var(--text)}
.btn-outline-light:hover{background:var(--panel-2);color:#fff;border-color:#3a4a85}
.badge-status{padding:.4em .7em;border-radius:999px;font-weight:600;font-size:.7rem;text-transform:uppercase;letter-spacing:.06em}
.badge-pending{background:rgba(255,181,71,.15);color:var(--warn)}
.badge-ongoing{background:rgba(108,139,255,.18);color:var(--primary)}
.badge-completed{background:rgba(57,211,159,.18);color:var(--success)}
.auth-wrap{min-height:100vh;display:grid;place-items:center;background:radial-gradient(1200px 600px at 10% 10%,rgba(108,139,255,.15),transparent 60%),radial-gradient(900px 500px at 90% 80%,rgba(179,136,255,.12),transparent 60%),var(--bg);padding:24px}
.auth-card{width:100%;max-width:440px;background:var(--panel);border:1px solid var(--border);border-radius:20px;padding:36px;box-shadow:var(--shadow);animation:fadeUp .4s ease}
.auth-card h1{font-weight:800;font-size:1.6rem;margin-bottom:6px}
.auth-card .sub{color:var(--muted);margin-bottom:24px}
.sidebar-toggle{display:none;background:transparent;border:0;color:var(--text);font-size:1.4rem}
@media (max-width: 991.98px){
  .sidebar{transform:translateX(-100%);z-index:30}
  .sidebar.open{transform:translateX(0)}
  .main{margin-left:0}
  .sidebar-toggle{display:inline-flex}
}
.chart-wrap{position:relative;height:300px}
