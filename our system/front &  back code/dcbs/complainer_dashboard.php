<?php
// complainer_dashboard.php
session_start();

// protect page: must be logged in and user_type = complainer
if (empty($_SESSION['user']) || ($_SESSION['user']['user_type'] ?? '') !== 'complainer') {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . '/config.php'; // provides $conn (mysqli)

// get logged in user id
$user_id = (int) $_SESSION['user']['id'];

// --- fetch counts ---
$counts = ['Solved' => 0, 'In Progress' => 0, 'Pending' => 0];
$stmt = $conn->prepare("SELECT status, COUNT(*) AS total FROM complaints WHERE user_id = ? GROUP BY status");
$stmt->bind_param('i', $user_id);
$stmt->execute();
$res = $stmt->get_result();
while ($row = $res->fetch_assoc()) {
    $counts[$row['status']] = (int) $row['total'];
}
$stmt->close();

// --- fetch recent complaints ---
$recent = [];
$stmt2 = $conn->prepare("SELECT id, title, handler_id, created_at, status FROM complaints WHERE user_id = ? ORDER BY created_at DESC LIMIT 8");
$stmt2->bind_param('i', $user_id);
$stmt2->execute();
$res2 = $stmt2->get_result();
while ($r = $res2->fetch_assoc()) {
    $recent[] = $r;
}
$stmt2->close();
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Complainer Dashboard | DCBS</title>
<style>
:root{--logo:#ffbd59;--muted:#4b5563;--white:#fff;--black:#000}
*{box-sizing:border-box;font-family:Arial, Helvetica, sans-serif}
body{margin:0;background:#f4f6f8;color:#111;min-height:100vh}

/* Topbar */
.topbar{
  background:var(--black);
  color:var(--white);
  padding:10px 20px;
  display:flex;
  align-items:center;
  justify-content:space-between;
}
.topbar .logo-section{display:flex;align-items:center;gap:10px}
.topbar img{height:40px}
.topbar h1{margin:0;font-size:18px;color:var(--logo)}
.topbar nav a{
  color:var(--white);
  text-decoration:none;
  margin:0 8px;
  font-weight:500;
}
.topbar nav a:hover{
  color:var(--logo);
}

/* Layout */
main{display:flex;min-height:calc(100vh - 60px)}

.sidebar{
  width:220px;background:var(--black);color:var(--white);padding:20px 12px;flex-shrink:0;
}
.sidebar h3{color:var(--logo);margin:0 0 12px;font-size:16px}
.sidebar a{display:block;padding:10px 12px;color:var(--white);text-decoration:none;margin:6px 0;border-radius:6px}
.sidebar a.active, .sidebar a:hover{background:var(--logo);color:var(--black);font-weight:600}

.content{flex:1;padding:20px;overflow-y:auto}
.header-row{display:flex;align-items:center;justify-content:space-between;margin-bottom:16px}
.header-row h1{font-size:20px;margin:0}
.header-row p{color:var(--muted);margin:0}

.cards{display:grid;grid-template-columns:repeat(auto-fit,minmax(170px,1fr));gap:16px;margin-bottom:20px}
.card{
  background:#fff;padding:18px;border-radius:10px;box-shadow:0 6px 20px rgba(0,0,0,0.04);text-align:center;
}
.card h4{margin:0;color:var(--muted);font-size:14px}
.card .num{margin-top:10px;font-size:22px;font-weight:700}

.table-wrap{background:#fff;padding:16px;border-radius:10px;box-shadow:0 6px 20px rgba(0,0,0,0.04)}
table{width:100%;border-collapse:collapse}
th,td{padding:10px;border-bottom:1px solid #eee;text-align:left;font-size:14px}
th{background:#fafafa;font-weight:700}
tr:hover td{background:#fbfbfb}
.badge{display:inline-block;padding:6px 8px;border-radius:6px;font-weight:700;font-size:13px}
.badge.Pending{background:#ffccd5;color:#8b0000}
.badge.In\ Progress{background:#ffe9c7;color:#7a4f00}
.badge.Solved{background:#d6f5e0;color:#04662a}

.empty{padding:20px;text-align:center;color:var(--muted)}
@media(max-width:820px){ .sidebar{display:none} main{min-height:auto} .cards{grid-template-columns:repeat(1,1fr)} }
</style>
</head>
<body>

<!-- Internal topbar -->
<header class="topbar">
  <div class="logo-section">
    <img src="logo.png" alt="Logo">
  </div>
  <nav>
    <a href="index.php">Home</a>
    <a href="about.php">About</a>
    <a href="help.php">Help</a>
  </nav>
</header>

<main>
  <!-- Sidebar -->
  <aside class="sidebar">
    <h3>Complainer</h3>
    <a href="complainer_dashboard.php" class="active">Dashboard</a>
    <a href="submit_complaint.php">Add Complaint</a>
    <a href="my_complaints.php">My Complaints</a>
    <a href="account.php">My Account</a>
    <a href="login.php">Logout</a>
  </aside>

  <!-- Main content -->
  <section class="content">
    <div class="header-row">
      <div>
        <h1>Welcome, <?= htmlspecialchars($_SESSION['user']['name'] ?? 'Complainer') ?></h1>
        <p>Here is a quick view of your complaints.</p>
      </div>
      <div>
        <a href="submit_complaint.php" style="background:var(--logo);color:#000;padding:10px 14px;border-radius:8px;text-decoration:none;font-weight:700">Submit New Complaint</a>
      </div>
    </div>

    <!-- Status cards -->
    <div class="cards">
      <div class="card" style="border-top:4px solid #16a34a">
        <h4>✅ Solved</h4>
        <div class="num"><?= (int) $counts['Solved'] ?></div>
      </div>
      <div class="card" style="border-top:4px solid #f59e0b">
        <h4>🔄 In Progress</h4>
        <div class="num"><?= (int) $counts['In Progress'] ?></div>
      </div>
      <div class="card" style="border-top:4px solid #ef4444">
        <h4>⏳ Pending</h4>
        <div class="num"><?= (int) $counts['Pending'] ?></div>
      </div>
    </div>

    <!-- Recent complaints table -->
    <div class="table-wrap">
      <h3 style="margin-top:0">Recent Complaints</h3>
      <?php if (count($recent) === 0): ?>
        <div class="empty">You have not submitted any complaints yet. <a href="submit_complaint.php">Submit your first complaint</a>.</div>
      <?php else: ?>
        <table>
          <thead>
            <tr>
              <th>ID</th>
              <th>Title</th>
              <th>Date</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($recent as $row): 
              $id = (int) $row['id'];
              $title = htmlspecialchars($row['title']);
              $date = htmlspecialchars(substr($row['created_at'],0,19));
              $status = htmlspecialchars($row['status']);
              $status_class = str_replace(' ', '\ ', $status);
            ?>
            <tr>
              <td><?= $id ?></td>
              <td><a href="view_complaint.php?id=<?= $id ?>"><?= $title ?></a></td>
              <td><?= $date ?></td>
              <td><span class="badge <?= $status_class ?>"><?= $status ?></span></td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      <?php endif; ?>
    </div>
  </section>
</main>

</body>
</html>
