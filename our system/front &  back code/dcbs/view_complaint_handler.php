<?php
// view_complaint_handler.php
session_start();
require_once __DIR__ . '/config.php';

// protect page: must be logged in and user_type = handler
if (empty($_SESSION['user']) || ($_SESSION['user']['user_type'] ?? '') !== 'handler') {
    header('Location: login.php');
    exit;
}

$handler_id = (int)$_SESSION['user']['id'];
$complaint_id = (int)($_GET['id'] ?? 0);

if ($complaint_id <= 0) {
    die('Invalid complaint ID.');
}

// Fetch complaint with user info
$stmt = $conn->prepare("
    SELECT c.*, u.name AS complainer_name, u.email AS complainer_email
    FROM complaints c
    LEFT JOIN users u ON c.user_id = u.id
    WHERE c.id = ? AND c.handler_id = ?
");
$stmt->bind_param('ii', $complaint_id, $handler_id);
$stmt->execute();
$result = $stmt->get_result();
$complaint = $result->fetch_assoc();
$stmt->close();

if (!$complaint) {
    die('Complaint not found or not assigned to you.');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>View Complaint #<?= htmlspecialchars($complaint['id']) ?> | Handler</title>
<style>
  body {
    font-family: Arial, sans-serif;
    margin: 30px;
    background: #fff;
    color: #111;
    max-width: 900px;
  }
  h1, h2 {
    color: #ffbd59;
    border-bottom: 2px solid #ffbd59;
    padding-bottom: 6px;
  }
  .section {
    margin-bottom: 24px;
  }
  .label {
    font-weight: bold;
    color: #4b5563;
    margin-bottom: 4px;
  }
  .content {
    background: #f9fafb;
    padding: 14px 16px;
    border-radius: 8px;
    box-shadow: inset 0 0 5px #ddd;
  }
  img.thumb {
    max-width: 300px;
    border-radius: 6px;
    margin-top: 8px;
  }
  video, audio {
    max-width: 100%;
    margin-top: 8px;
    border-radius: 6px;
  }
  .status {
    display: inline-block;
    padding: 6px 12px;
    border-radius: 20px;
    font-weight: 700;
    color: #fff;
    margin-top: 6px;
  }
  .status.Pending { background: #f97316; }
  .status['In Progress'] { background: #3b82f6; }
  .status.Solved { background: #22c55e; }
  @media print {
    body {
      margin: 10mm;
    }
    a[href]:after {
      content: " (" attr(href) ")";
      font-size: smaller;
    }
  }
</style>
</head>
<body>

<h1>Complaint Details (ID #<?= htmlspecialchars($complaint['id']) ?>)</h1>

<div class="section">
  <div class="label">Title:</div>
  <div class="content"><?= nl2br(htmlspecialchars($complaint['title'])) ?></div>
</div>

<div class="section">
  <div class="label">Description:</div>
  <div class="content"><?= nl2br(htmlspecialchars($complaint['description'])) ?></div>
</div>

<div class="section">
  <div class="label">Complainer Name:</div>
  <div class="content"><?= htmlspecialchars($complaint['complainer_name']) ?></div>
</div>

<div class="section">
  <div class="label">Complainer Email:</div>
  <div class="content"><?= htmlspecialchars($complaint['complainer_email']) ?></div>
</div>

<div class="section">
  <div class="label">Status:</div>
  <div class="status <?= htmlspecialchars(str_replace(' ', '_', $complaint['status'])) ?>">
    <?= htmlspecialchars($complaint['status']) ?>
  </div>
</div>

<div class="section">
  <div class="label">Submitted On:</div>
  <div class="content"><?= htmlspecialchars($complaint['created_at']) ?></div>
</div>

<?php if ($complaint['photo_path']): ?>
  <div class="section">
    <div class="label">Photo Evidence:</div>
    <img src="<?= htmlspecialchars($complaint['photo_path']) ?>" alt="Photo evidence" class="thumb" />
  </div>
<?php endif; ?>

<?php if ($complaint['video_path']): ?>
  <div class="section">
    <div class="label">Video Evidence:</div>
    <video controls>
      <source src="<?= htmlspecialchars($complaint['video_path']) ?>" type="video/mp4" />
      Your browser does not support the video tag.
    </video>
  </div>
<?php endif; ?>

<?php if ($complaint['audio_path']): ?>
  <div class="section">
    <div class="label">Audio Evidence:</div>
    <audio controls>
      <source src="<?= htmlspecialchars($complaint['audio_path']) ?>" type="audio/mpeg" />
      Your browser does not support the audio element.
    </audio>
  </div>
<?php endif; ?>

<div class="section">
  <a href="handler_my_complaints.php" style="color:#ffbd59;text-decoration:none;font-weight:bold;">&larr; Back to My Complaints</a>
</div>

</body>
</html>
