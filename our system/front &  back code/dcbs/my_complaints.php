<?php
// my_complaints.php
session_start();
require_once __DIR__ . '/config.php';

// Protect page: must be logged in and user_type = complainer
/*if (empty($_SESSION['user']) || ($_SESSION['user']['user_type'] ?? '') !== 'complainer') {
    header('Location: login.php');
    exit;
}*/

$user_id = (int) $_SESSION['user']['id'];

// Fetch complaints submitted by this complainer
$complaints = [];
$sql = "SELECT c.id, c.title, c.description, c.photo_path, c.video_path, c.audio_path, c.status, c.created_at
        FROM complaints c
        WHERE c.user_id = ?
        ORDER BY c.created_at DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$res = $stmt->get_result();
while ($row = $res->fetch_assoc()) {
    $complaints[] = $row;
}
$stmt->close();
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>My Complaints | DCBS</title>
<style>
/* Basic styles copied from your handler page for consistency */
:root{--logo:#ffbd59;--muted:#4b5563;--white:#fff;--black:#000}
*{box-sizing:border-box;font-family:Arial, Helvetica, sans-serif}
body{margin:0;background:#f4f6f8;color:#111;min-height:100vh}
.topbar{background:var(--black);color:var(--white);padding:10px 20px;display:flex;align-items:center;justify-content:space-between}
.topbar img{height:40px}
.topbar nav a{color:var(--white);text-decoration:none;margin:0 8px;font-weight:500}
main{display:flex;min-height:calc(100vh - 60px)}
.sidebar{width:220px;background:var(--black);color:var(--white);padding:20px 12px;flex-shrink:0}
.sidebar h3{color:var(--logo);margin:0 0 12px;font-size:16px}
.sidebar a{display:block;padding:10px 12px;color:var(--white);text-decoration:none;margin:6px 0;border-radius:6px}
.sidebar a.active, .sidebar a:hover{background:var(--logo);color:var(--black);font-weight:600}
.content{flex:1;padding:20px;overflow:auto}
.table-wrap{background:#fff;padding:16px;border-radius:10px;box-shadow:0 6px 20px rgba(0,0,0,0.04)}
table{width:100%;border-collapse:collapse}
th,td{padding:10px;border-bottom:1px solid #eee;text-align:left;font-size:14px;vertical-align:middle}
th{background:#fafafa}
tr:hover td{background:#fbfbfb}
.media-cell{width:160px}
.thumb{max-width:140px;border-radius:6px;display:block}
.badge{display:inline-block;padding:6px 8px;border-radius:6px;font-weight:700;font-size:13px}
.badge.Pending{background:#fff0e6;color:#7a3b00}
.badge.In\ Progress{background:#e6f4ff;color:#0b5ea8}
.badge.Solved{background:#dff7e6;color:#04662a}
.view-link{color:var(--logo);font-weight:700;text-decoration:none}
.empty{padding:20px;text-align:center;color:var(--muted)}
@media(max-width:900px){ .media-cell{display:none} }
</style>
</head>
<body>

<header class="topbar">
  <div style="display:flex;align-items:center;gap:12px">
    <img src="logo.png" alt="Logo">
  </div>
  <nav>
    <a href="index.php">Home</a>
    <a href="about.php">About</a>
    <a href="help.php">Help</a>
  </nav>
</header>

<main>
  <aside class="sidebar">
  <h3>Complainer</h3>
    <a href="complainer_dashboard.php" >Dashboard</a>
    <a href="submit_complaint.php">Add Complaint</a>
    <a href="my_complaints.php"class="active">My Complaints</a>
    <a href="account.php">My Account</a>
    <a href="login.php">Logout</a>
  </aside>

  <section class="content">
    <h1>My Complaints</h1>
    <div class="table-wrap">
      <?php if (empty($complaints)): ?>
        <div class="empty">You haven’t submitted any complaints yet.</div>
      <?php else: ?>
      <table>
        <thead>
          <tr>
            <th>ID</th>
            <th>Title</th>
            <th class="media-cell">Media</th>
            <th>Status</th>
            <th>Created At</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($complaints as $c): $id = (int)$c['id']; ?>
          <tr>
            <td><?= $id ?></td>
            <td><?= htmlspecialchars($c['title']) ?></td>
            <td class="media-cell">
              <?php if (!empty($c['photo_path'])): ?>
                <img src="<?= htmlspecialchars($c['photo_path']) ?>" alt="photo" class="thumb">
              <?php elseif (!empty($c['video_path'])): ?>
                <video class="thumb" controls>
                  <source src="<?= htmlspecialchars($c['video_path']) ?>" type="video/mp4">
                </video>
              <?php elseif (!empty($c['audio_path'])): ?>
                <audio controls>
                  <source src="<?= htmlspecialchars($c['audio_path']) ?>" type="audio/mpeg">
                </audio>
              <?php else: ?>
                <span style="color:var(--muted);font-size:13px">No media</span>
              <?php endif; ?>
            </td>
            <td><span class="badge <?= htmlspecialchars(str_replace(' ', '\\ ', $c['status'])) ?>"><?= htmlspecialchars($c['status']) ?></span></td>
            <td><?= htmlspecialchars(substr($c['created_at'],0,16)) ?></td>
            <td>
              <a class="view-link" href="view_complaint.php?id=<?= $id ?>">View</a> 
              
            </td>
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
