<?php
// handler_my_complaints.php
session_start();
require_once __DIR__ . '/config.php'; // provides $conn (mysqli)

// protect page: must be logged in and user_type = handler
/*if (empty($_SESSION['user']) || ($_SESSION['user']['user_type'] ?? '') !== 'handler') {
    header('Location: login.php');
    exit;
}*/

$handler_id = (int) $_SESSION['user']['id'];
$message = '';

// Handle inline status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'update_status') {
    $complaint_id = (int) ($_POST['complaint_id'] ?? 0);
    $new_status = trim($_POST['status'] ?? '');
    $allowed = ['Pending', 'In Progress', 'Solved'];

    if ($complaint_id > 0 && in_array($new_status, $allowed, true)) {
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

// Fetch all complaints assigned to this handler
$complaints = [];
$sql = "SELECT c.id, c.title, c.description, c.photo_path, c.video_path, c.audio_path, c.status, c.created_at, u.name AS complainer_name
        FROM complaints c
        LEFT JOIN users u ON c.user_id = u.id
        WHERE c.handler_id = ?
        ORDER BY c.created_at DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $handler_id);
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
<title>My Complaints (Handler) | DCBS</title>
<style>
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

/* table */
.table-wrap{background:#fff;padding:16px;border-radius:10px;box-shadow:0 6px 20px rgba(0,0,0,0.04)}
table{width:100%;border-collapse:collapse}
th,td{padding:10px;border-bottom:1px solid #eee;text-align:left;font-size:14px;vertical-align:middle}
th{background:#fafafa;cursor:pointer;position:relative}
tr:hover td{background:#fbfbfb}
.media-cell{width:160px}
.thumb{max-width:140px;border-radius:6px;display:block}
.badge{display:inline-block;padding:6px 8px;border-radius:6px;font-weight:700;font-size:13px}
.badge.Pending{background:#fff0e6;color:#7a3b00}
.badge.In\ Progress{background:#e6f4ff;color:#0b5ea8}
.badge.Solved{background:#dff7e6;color:#04662a}

.form-inline{display:flex;gap:6px;align-items:center}
.form-inline select{padding:6px;border-radius:6px;border:1px solid #ccc}
.form-inline button{padding:6px 8px;border-radius:6px;border:none;background:var(--logo);font-weight:700;cursor:pointer}

.view-link{color:var(--logo);font-weight:700;text-decoration:none}

/* Sort arrows */
th.asc::after{content:" ▲";position:absolute;right:8px}
th.desc::after{content:" ▼";position:absolute;right:8px}

.empty{padding:20px;text-align:center;color:var(--muted)}
@media(max-width:900px){ .media-cell{display:none} }
.message{margin:12px 0}
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
    <h3>Handler</h3>
    <a href="handler_dashboard.php">Dashboard</a>
    
    <a href="handler_my_complaints.php" class="active">My Complaints</a>
    <a href="handler_profile.php">My Account</a>
    <a href="login.php">Logout</a>
  </aside>

  <section class="content">
    <h1>Complaints Assigned to You</h1>

    <?php if ($message): ?>
      <div class="message"><?= $message ?></div>
    <?php endif; ?>

    <div class="table-wrap">
      <?php if (empty($complaints)): ?>
        <div class="empty">No complaints assigned to you yet.</div>
      <?php else: ?>
      <table id="complaintsTable">
        <thead>
          <tr>
            <th data-type="number">ID</th>
            <th data-type="string">Title</th>
            <th data-type="string">Complainer</th>
            <th class="media-cell" data-type="string">Media</th>
            <th data-type="string">Status</th>
            <th data-type="date">Created At</th>
            <th data-type="string">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($complaints as $c): $id = (int)$c['id']; ?>
          <tr>
            <td><?= $id ?></td>
            <td style="font-weight:700"><a class="view-link" href="view_complaint_handler.php?id=<?= $id ?>"><?= htmlspecialchars($c['title']) ?></a></td>
            <td><?= htmlspecialchars($c['complainer_name'] ?? '—') ?></td>

            <td class="media-cell">
              <?php if (!empty($c['photo_path'])): ?>
                <img src="<?= htmlspecialchars($c['photo_path']) ?>" alt="photo" class="thumb">
              <?php elseif (!empty($c['video_path'])): ?>
                <video class="thumb" controls>
                  <source src="<?= htmlspecialchars($c['video_path']) ?>" type="video/mp4">
                  Your browser does not support the video tag.
                </video>
              <?php elseif (!empty($c['audio_path'])): ?>
                <audio controls>
                  <source src="<?= htmlspecialchars($c['audio_path']) ?>" type="audio/mpeg">
                  Your browser does not support the audio element.
                </audio>
              <?php else: ?>
                <span style="color:var(--muted);font-size:13px">No media</span>
              <?php endif; ?>
            </td>

            <td><span class="badge <?= htmlspecialchars(str_replace(' ', '\\ ', $c['status'])) ?>"><?= htmlspecialchars($c['status']) ?></span></td>
            <td><?= htmlspecialchars(substr($c['created_at'],0,16)) ?></td>

            <td>
              <div style="display:flex;gap:8px;align-items:center">
                <a class="view-link" href="view_complaint_handler.php?id=<?= $id ?>">View</a>

                <form class="form-inline" method="post" style="margin:0;">
                  <input type="hidden" name="action" value="update_status">
                  <input type="hidden" name="complaint_id" value="<?= $id ?>">
                  <select name="status" aria-label="Status">
                    <option value="Pending" <?= $c['status'] === 'Pending' ? 'selected' : '' ?>>Pending</option>
                    <option value="In Progress" <?= $c['status'] === 'In Progress' ? 'selected' : '' ?>>In Progress</option>
                    <option value="Solved" <?= $c['status'] === 'Solved' ? 'selected' : '' ?>>Solved</option>
                  </select>
                  <button type="submit" title="Update">Update</button>
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

<script>
// Simple client-side sortable table (click headers)
document.addEventListener('DOMContentLoaded', function () {
  const table = document.getElementById('complaintsTable');
  if (!table) return;
  const headers = table.querySelectorAll('th');
  let sortDir = Array(headers.length).fill(null);

  headers.forEach((header, index) => {
    header.addEventListener('click', () => {
      const type = header.getAttribute('data-type');
      const tbody = table.tBodies[0];
      const rows = Array.from(tbody.querySelectorAll('tr'));
      const current = sortDir[index];
      const dir = current === 'asc' ? 'desc' : 'asc';

      headers.forEach(h => h.classList.remove('asc','desc'));

      rows.sort((a,b) => {
        let A = a.cells[index].textContent.trim();
        let B = b.cells[index].textContent.trim();

        if (type === 'number') {
          A = parseInt(A) || 0; B = parseInt(B) || 0;
          return dir === 'asc' ? A - B : B - A;
        } else if (type === 'date') {
          A = new Date(A); B = new Date(B);
          return dir === 'asc' ? A - B : B - A;
        } else {
          A = A.toLowerCase(); B = B.toLowerCase();
          if (A < B) return dir === 'asc' ? -1 : 1;
          if (A > B) return dir === 'asc' ? 1 : -1;
          return 0;
        }
      });

      rows.forEach(r => tbody.appendChild(r));
      header.classList.add(dir);
      sortDir.fill(null);
      sortDir[index] = dir;
    });
  });
});
</script>

</body>
</html>
