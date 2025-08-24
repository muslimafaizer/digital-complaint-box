<?php
session_start();

// Only allow logged-in complainers
if (empty($_SESSION['user']) || ($_SESSION['user']['user_type'] ?? '') !== 'complainer') {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . '/config.php'; // DB connection

$user_id = (int) $_SESSION['user']['id'];

// Get list of handlers
$handlers = [];
$res = $conn->query("SELECT id, name FROM users WHERE user_type = 'handler' ORDER BY name");
while ($row = $res->fetch_assoc()) {
    $handlers[] = $row;
}

// Handle form submission
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $handler_id = isset($_POST['handler_id']) && $_POST['handler_id'] !== '' ? (int) $_POST['handler_id'] : null;

    // Basic validation
    if ($title === '' || $description === '' || $handler_id === null) {
        $message = '<p style="color:red">Please fill all required fields.</p>';
    } else {
        // Prepare upload directory
        $upload_dir = __DIR__ . '/uploads/';
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);

        // Initialize paths
        $photo_path = null;
        $video_path = null;
        $audio_path = null;

        // Function to handle uploads
        function handle_upload($file_input_name, $upload_dir) {
            if (!empty($_FILES[$file_input_name]['name'])) {
                $filename = time() . '_' . basename($_FILES[$file_input_name]['name']);
                $target_path = $upload_dir . $filename;

                // Validate file type by mime for security (basic)
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $mime = finfo_file($finfo, $_FILES[$file_input_name]['tmp_name']);
                finfo_close($finfo);

                // Accept certain mime types depending on input
                $allowed_photo = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
                $allowed_video = ['video/mp4', 'video/quicktime', 'video/x-msvideo', 'video/x-ms-wmv'];
                $allowed_audio = ['audio/mpeg', 'audio/wav', 'audio/ogg'];

                if (
                    ($file_input_name === 'photo' && in_array($mime, $allowed_photo)) ||
                    ($file_input_name === 'video' && in_array($mime, $allowed_video)) ||
                    ($file_input_name === 'audio' && in_array($mime, $allowed_audio))
                ) {
                    if (move_uploaded_file($_FILES[$file_input_name]['tmp_name'], $target_path)) {
                        return 'uploads/' . $filename;
                    }
                }
            }
            return null;
        }

        $photo_path = handle_upload('photo', $upload_dir);
        $video_path = handle_upload('video', $upload_dir);
        $audio_path = handle_upload('audio', $upload_dir);

        // Insert into DB
        $stmt = $conn->prepare("INSERT INTO complaints (user_id, handler_id, title, description, photo_path, video_path, audio_path, status, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, 'Pending', NOW(), NOW())");
        $stmt->bind_param('iisssss', $user_id, $handler_id, $title, $description, $photo_path, $video_path, $audio_path);

        if ($stmt->execute()) {
            $message = '<p style="color:green">Complaint submitted successfully!</p>';
        } else {
            $message = '<p style="color:red">Error submitting complaint: ' . htmlspecialchars($stmt->error) . '</p>';
        }
        $stmt->close();
    }
}
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Submit Complaint | DCBS</title>
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
main{display:flex;}
.sidebar{width:220px;background:var(--black);color:var(--white);padding:20px 12px;flex-shrink:0;}
.sidebar h3{color:var(--logo);margin:0 0 12px;font-size:16px}
.sidebar a{display:block;padding:10px 12px;color:var(--white);text-decoration:none;margin:6px 0;border-radius:6px}
.sidebar a.active, .sidebar a:hover{background:var(--logo);color:var(--black);font-weight:600}
.content{flex:1;padding:20px;}
form{background:#fff;padding:20px;border-radius:8px;max-width:600px;box-shadow:0 6px 20px rgba(0,0,0,0.04);}
form label{display:block;margin-top:12px;font-weight:600}
form input, form textarea, form select{width:100%;padding:10px;margin-top:6px;border:1px solid #ccc;border-radius:6px;font-size:14px}
form button{margin-top:16px;background:var(--logo);color:var(--black);padding:10px 14px;border:none;border-radius:8px;font-weight:700;cursor:pointer}
form button:hover{opacity:0.9}
</style>
</head>
<body>

<header class="topbar">
  <div class="logo-section">
    <img src="logo.png" alt="Logo">
  </div>
  <nav>
    <a href="complainer_dashboard.php">Home</a>
    <a href="my_complaints.php" class="active">About</a>
    <a href="account.php">Help</a>
  </nav>
</header>

<main>
  <aside class="sidebar">
    <h3>Complainer</h3>
    <a href="complainer_dashboard.php">Dashboard</a>
    <a href="submit_complaint.php" class="active">Add Complaint</a>
    <a href="my_complaints.php">My Complaints</a>
    <a href="account.php">My Account</a>
    <a href="login.php">Logout</a>
  </aside>

  <section class="content">
    <h1>Submit New Complaint</h1>
    <?= $message ?>
    <form method="post" enctype="multipart/form-data">
      <label for="title">Title *</label>
      <input type="text" name="title" id="title" required>

      <label for="description">Description *</label>
      <textarea name="description" id="description" rows="5" required></textarea>

      <label for="handler_id">Assign To *</label>
      <select name="handler_id" id="handler_id" required>
        <option value="">-- Select Handler --</option>
        <?php foreach ($handlers as $h): ?>
          <option value="<?= $h['id'] ?>"><?= htmlspecialchars($h['name']) ?></option>
        <?php endforeach; ?>
      </select>

      <label for="photo">Photo (optional)</label>
      <input type="file" name="photo" id="photo" accept="image/*">

      <label for="video">Video (optional)</label>
      <input type="file" name="video" id="video" accept="video/*">

      <label for="audio">Audio (optional)</label>
      <input type="file" name="audio" id="audio" accept="audio/*">

      <button type="submit">Submit Complaint</button>
    </form>
  </section>
</main>

</body>
</html>
