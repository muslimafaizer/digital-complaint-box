<?php
session_start();
require_once __DIR__ . '/config.php';

// Check login and complainer type
if (empty($_SESSION['user']) || ($_SESSION['user']['user_type'] ?? '') !== 'complainer') {
    header('Location: login.php');
    exit;
}

$user_id = (int) $_SESSION['user']['id'];
$complaint_id = (int) ($_GET['id'] ?? 0);
if ($complaint_id <= 0) {
    die("Invalid complaint ID.");
}

// Fetch complaint details, only if belongs to logged-in user
$sql = "SELECT c.*, u.name AS handler_name FROM complaints c LEFT JOIN users u ON c.handler_id = u.id WHERE c.id = ? AND c.user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $complaint_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();
$complaint = $result->fetch_assoc();
$stmt->close();

if (!$complaint) {
    die("Complaint not found or you do not have permission.");
}
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width,initial-scale=1" />
<title>Complaint Details | DCBS</title>
<style>
  body {
    font-family: Arial, sans-serif;
    max-width: 800px;
    margin: 20px auto;
    padding: 20px;
    background: #fff;
    border: 1px solid #ddd;
    border-radius: 8px;
    color: #111;
  }
  h1 {
    color: #ffbd59;
    margin-bottom: 10px;
  }
  section {
    margin-bottom: 20px;
  }
  label {
    font-weight: 700;
    display: block;
    margin-top: 12px;
  }
  .media {
    margin-top: 8px;
  }
  img, video, audio {
    max-width: 100%;
    border-radius: 6px;
  }
  .status {
    display: inline-block;
    padding: 6px 12px;
    border-radius: 6px;
    font-weight: bold;
    text-transform: capitalize;
  }
  .status.Pending { background: #ffe7c2; color: #d17b00; }
  .status.In\ Progress { background: #c6f7d0; color: #0b6e28; }
  .status.Solved, .status.Resolved { background: #c6f7d0; color: #0b6e28; }
  .status.Rejected { background: #f8d7da; color: #842029; }
  button.print-btn {
    background: #ffbd59;
    border: none;
    padding: 12px 20px;
    font-size: 16px;
    font-weight: bold;
    border-radius: 8px;
    cursor: pointer;
    color: #111;
    margin-bottom: 20px;
  }
  button.print-btn:hover {
    opacity: 0.9;
  }
</style>
</head>
<body>

<h1>Complaint Details</h1>

<button class="print-btn" onclick="window.print()">🖨️ Print</button>

<section>
  <label>Complaint ID:</label>
  <div><?= htmlspecialchars($complaint['id']) ?></div>
</section>

<section>
  <label>Title:</label>
  <div><?= htmlspecialchars($complaint['title']) ?></div>
</section>

<section>
  <label>Description:</label>
  <div style="white-space: pre-wrap;"><?= htmlspecialchars($complaint['description']) ?></div>
</section>

<section>
  <label>Handler:</label>
  <div><?= htmlspecialchars($complaint['handler_name'] ?? 'Not Assigned') ?></div>
</section>

<section>
  <label>Status:</label>
  <div><span class="status <?= htmlspecialchars(str_replace(' ', '\\ ', $complaint['status'])) ?>"><?= htmlspecialchars($complaint['status']) ?></span></div>
</section>

<section>
  <label>Created At:</label>
  <div><?= date("Y-m-d H:i", strtotime($complaint['created_at'])) ?></div>
</section>

<section>
  <label>Updated At:</label>
  <div><?= date("Y-m-d H:i", strtotime($complaint['updated_at'])) ?></div>
</section>

<section>
  <label>Photo:</label>
  <div class="media">
    <?php if ($complaint['photo_path']): ?>
      <img src="<?= htmlspecialchars($complaint['photo_path']) ?>" alt="Photo">
    <?php else: ?>
      No Photo Uploaded.
    <?php endif; ?>
  </div>
</section>

<section>
  <label>Video:</label>
  <div class="media">
    <?php if ($complaint['video_path']): ?>
      <video controls>
        <source src="<?= htmlspecialchars($complaint['video_path']) ?>" type="video/mp4">
        Your browser does not support the video tag.
      </video>
    <?php else: ?>
      No Video Uploaded.
    <?php endif; ?>
  </div>
</section>

<section>
  <label>Audio:</label>
  <div class="media">
    <?php if ($complaint['audio_path']): ?>
      <audio controls>
        <source src="<?= htmlspecialchars($complaint['audio_path']) ?>" type="audio/mpeg">
        Your browser does not support the audio element.
      </audio>
    <?php else: ?>
      No Audio Uploaded.
    <?php endif; ?>
  </div>
</section>

</body>
</html>
