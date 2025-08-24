<?php
// handler_dashboard.php
session_start();
require_once __DIR__ . '/config.php'; // provides $conn (mysqli)

// protect page: must be logged in and user_type = handler
if (empty($_SESSION['user']) || ($_SESSION['user']['user_type'] ?? '') !== 'handler') {
    header('Location: login.php');
    exit;
}

$handler_id = (int) $_SESSION['user']['id'];
$message = '';

// Handle inline status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_status') {
    $complaint_id = (int) ($_POST['complaint_id'] ?? 0);
    $new_status = trim($_POST['status'] ?? '');

    // basic validation
    $allowed = ['Pending', 'In Progress', 'Solved'];
    if ($complaint_id > 0 && in_array($new_status, $allowed, true)) {
        // update only if this complaint is assigned to this handler
        $upd = $conn->prepare("UPDATE complaints SET status = ?, updated_at = NOW() WHERE id = ? AND handler_id = ?");
        $upd->bind_param('sii', $new_status, $complaint_id, $handler_id);
        if ($upd->execute() && $upd->affected_rows > 0) {
            $message = '<p style="color:green">Status updated successfully.</p>';
        } else {
            $message = '<p style="color:red">Unable to update status (maybe not assigned or no change).</p>';
        }
        $upd->close();
    } else {
        $message = '<p style="color:red">Invalid input for status update.</p>';
    }
}

// --- fetch counts ---
$counts = ['Solved' => 0, 'In Progress' => 0, 'Pending' => 0];
$stmt = $conn->prepare("SELECT status, COUNT(*) AS total FROM complaints WHERE handler_id = ? GROUP BY status");
$stmt->bind_param('i', $handler_id);
$stmt->execute();
$res = $stmt->get_result();
while ($row = $res->fetch_assoc()) {
    $counts[$row['status']] = (int) $row['total'];
}
$stmt->close();

// --- fetch recent complaints assigned to this handler ---
$recent = [];
$stmt2 = $conn->prepare("SELECT c.id, c.title, c.description, c.photo_path, c.video_path, c.audio_path, c.status, c.created_at, u.name AS complainer_name
    FROM complaints c
    LEFT JOIN users u ON c.user_id = u.id
    WHERE c.handler_id = ?
    ORDER BY c.created_at DESC
    LIMIT 20");
$stmt2->bind_param('i', $handler_id);
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
<title>Handler Dashboard | DCBS</title>
<style>
:root{--logo:#ffbd59;--muted:#4b5563;--white:#fff;--black:#000}
*{box-sizing:border-box;font-family:Arial, Helvetica, sans-serif}
body{margin:0;background:#f4f6f8;color:#111;min-height:100vh}
.topbar{background:var(--black);color:var(--white);padding:10px 20px;display:flex;align-items:center;justify-content:space-between;}
.topbar .logo-section{display:flex;align-items:center;gap:10px}
.topbar img{height:40px}
.topbar h1{margin:0;font-size:18px;color:var(--logo)}
.topbar nav a{color:var(--white);text-decoration:none;margin:0 8px;font-weight:500;}
.topbar nav a:hover{color:var(--logo);}
main{display:flex;min-height:calc(100vh - 60px)}
.sidebar{width:220px;background:var(--black);color:var(--white);padding:20px 12px;flex-shrink:0;}
.sidebar h3{color:var(--logo);margin:0 0 12px;font-size:16px}
.sidebar a{display:block;padding:10px 12px;color:var(--white);text-decoration:none;margin:6px 0;border-radius:6px}
.sidebar a.active, .sidebar a:hover{background:var(--logo);color:var(--black);font-weight:600}
.content{flex:1;padding:20px;overflow-y:auto}
.header-row{display:flex;align-items:center;justify-content:space-between;margin-bottom:16px}
.header-row h1{font-size:20px;margin:0}
.header-row p{color:var(--muted);margin:0}

.cards{display:grid;grid-template-columns:repeat(auto-fit,minmax(170px,1fr));gap:16px;margin-bottom:20px}
.card{background:#fff;padding:18px;border-radius:10px;box-shadow:0 6px 20px rgba(0,0,0,0.04);text-align:center;}
.card h4{margin:0;color:var(--muted);font-size:14px}
.card .num{margin-top:10px;font-size:22px;font-weight:700}

.table-wrap{background:#fff;padding:16px;border-radius:10px;box-shadow:0 6px 20px rgba(0,0,0,0.04)}
table{width:100%;border-collapse:collapse}
th,td{padding:10px;border-bottom:1px solid #eee;text-align:left;font-size:14px;vertical-align:middle}
th{background:#fafafa;font-weight:700}
tr:hover td{background:#fbfbfb}
.badge{display:inline-block;padding:6px 8px;border-radius:6px;font-weight:700;font-size:13px}
.badge.Pending{background:#ffecd6;color:#7a3b00}
.badge.In\ Progress{background:#e6f4ff;color:#0b5ea8}
.badge.Solved{background:#dff7e6;color:#04662a}

.thumb{max-width:100px;border-radius:6px;display:block}
.media-cell{width:120px}
.form-inline{display:flex;gap:8px;align-items:center}
.form-inline select{padding:6px;border-radius:6px;border:1px solid #ccc}
.form-inline button{padding:6px 8px;border-radius:6px;border:none;background:var(--logo);font-weight:700;cursor:pointer}

.view-link{color:var(--logo);font-weight:700;text-decoration:none}

.empty{padding:20px;text-align:center;color:var(--muted)}
@media(max-width:900px){
  .media-cell{display:none}
  .cards{grid-template-columns:repeat(1,1fr)}
}
</style>
</head>
<body>

<header class="topbar">
  <div class="logo-section">
    <img src="logo.png" alt="Logo">
  </div>
  <nav>
    <a href="index.php">Home</a>
    <a href="about.php">About</a>
    <a href=".php">Help</a>
  </nav>
</header>

<main>
  <aside class="sidebar">
    <h3>Handler</h3>
    <a href="handler_dashboard.php" class="active">Dashboard</a>
    <a href="handler_my_complaints.php">My Complaints</a>
    <a href="handler_profile.php">My Account</a>
    <a href="login.php">Logout</a>
  </aside>

  <section class="content">
    <div class="header-row">
      <div>
        <h1>Welcome, <?= htmlspecialchars($_SESSION['user']['name'] ?? 'Handler') ?></h1>
        <p>Assigned complaints quick view.</p>
      </div>
    </div>

    <?= $message ?>

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

    <!-- Recent assigned complaints -->
    <div class="table-wrap">
      <h3 style="margin-top:0">Assigned Complaints</h3>

      <?php if (count($recent) === 0): ?>
        <div class="empty">No complaints assigned to you yet.</div>
      <?php else: ?>
        <table>
          <thead>
            <tr>
              <th>ID</th>
              <th>Title / Complainer</th>
              <th class="media-cell">Media</th>
              <th>Status</th>
              <th>Date</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($recent as $row): 
              $id = (int)$row['id'];
            ?>
            <tr>
              <td><?= $id ?></td>
              <td>
                <div style="font-weight:700"><a class="view-link" href="view_complaint_handler.php?id=<?= $id ?>"><?= htmlspecialchars($row['title']) ?></a></div>
                <div style="color:var(--muted);font-size:13px">By: <?= htmlspecialchars($row['complainer_name'] ?? '—') ?></div>
              </td>

              <td class="media-cell">
                <?php if ($row['photo_path']): ?>
                  <img src="<?= htmlspecialchars($row['photo_path']) ?>" alt="photo" class="thumb">
                <?php elseif ($row['video_path']): ?>
                  <video class="thumb" controls>
                    <source src="<?= htmlspecialchars($row['video_path']) ?>" type="video/mp4">
                    Your browser does not support the video tag.
                  </video>
                <?php elseif ($row['audio_path']): ?>
                  <audio controls>
                    <source src="<?= htmlspecialchars($row['audio_path']) ?>" type="audio/mpeg">
                    Your browser does not support the audio element.
                  </audio>
                <?php else: ?>
                  <span style="color:var(--muted);font-size:13px">No media</span>
                <?php endif; ?>
              </td>

              <td>
                <span class="badge <?= htmlspecialchars(str_replace(' ', '\\ ', $row['status'])) ?>"><?= htmlspecialchars($row['status']) ?></span>
              </td>

              <td><?= htmlspecialchars(substr($row['created_at'],0,16)) ?></td>

              <td>
                <div style="display:flex;gap:8px;align-items:center">
                  <a class="view-link" href="view_complaint_handler.php?id=<?= $id ?>">View</a>

                  <!-- inline status update form -->
                  <form class="form-inline" method="post" style="display:inline-block;margin:0;">
                    <input type="hidden" name="action" value="update_status">
                    <input type="hidden" name="complaint_id" value="<?= $id ?>">
                    <select name="status" aria-label="status">
                      <option value="Pending" <?= $row['status'] === 'Pending' ? 'selected' : '' ?>>Pending</option>
                      <option value="In Progress" <?= $row['status'] === 'In Progress' ? 'selected' : '' ?>>In Progress</option>
                      <option value="Solved" <?= $row['status'] === 'Solved' ? 'selected' : '' ?>>Solved</option>
                    </select>
                    <button type="submit" title="Update status">Update</button>
                  </form>
                </div>
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
