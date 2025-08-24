<?php
session_start();
require_once __DIR__ . '/config.php';

// Only allow logged-in complainers
if (empty($_SESSION['user']) || ($_SESSION['user']['user_type'] ?? '') !== 'complainer') {
    header('Location: login.php');
    exit;
}

$user_id = (int) $_SESSION['user']['id'];

// Fetch current user info
$sql = "SELECT name, email FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';

    // Basic validation
    if ($name === '' || $email === '') {
        $message = '<p style="color:red;">Name and Email are required.</p>';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = '<p style="color:red;">Invalid email format.</p>';
    } elseif ($password !== $password_confirm) {
        $message = '<p style="color:red;">Passwords do not match.</p>';
    } else {
        // Update user info
        if ($password !== '') {
            // Update with password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $sql = "UPDATE users SET name = ?, email = ?, password = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssi", $name, $email, $hashed_password, $user_id);
        } else {
            // Update without password
            $sql = "UPDATE users SET name = ?, email = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssi", $name, $email, $user_id);
        }

        if ($stmt->execute()) {
            $message = '<p style="color:green;">Account updated successfully.</p>';
            // Update session user info
            $_SESSION['user']['name'] = $name;
            $_SESSION['user']['email'] = $email;
            // Refresh user data variable for form
            $user['name'] = $name;
            $user['email'] = $email;
        } else {
            $message = '<p style="color:red;">Error updating account.</p>';
        }
        $stmt->close();
    }
}
?>

<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width,initial-scale=1" />
<title>My Account | DCBS</title>
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
main{display:flex;min-height: calc(100vh - 60px);}
.sidebar{width:220px;background:var(--black);color:var(--white);padding:20px 12px;flex-shrink:0;}
.sidebar h3{color:var(--logo);margin:0 0 12px;font-size:16px}
.sidebar a{display:block;padding:10px 12px;color:var(--white);text-decoration:none;margin:6px 0;border-radius:6px}
.sidebar a.active, .sidebar a:hover{background:var(--logo);color:var(--black);font-weight:600}
.content{flex:1;padding:20px;}
form{background:#fff;padding:20px;border-radius:8px;max-width:500px;box-shadow:0 6px 20px rgba(0,0,0,0.04);}
form label{display:block;margin-top:12px;font-weight:600}
form input{width:100%;padding:10px;margin-top:6px;border:1px solid #ccc;border-radius:6px;font-size:14px}
form button{margin-top:16px;background:var(--logo);color:var(--black);padding:10px 14px;border:none;border-radius:8px;font-weight:700;cursor:pointer}
form button:hover{opacity:0.9}
.message {margin-bottom: 16px;}
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
    <a href="help.php" class="active">Help</a>
  </nav>
</header>

<main>
  <aside class="sidebar">
    <h3>Complainer</h3>
    <a href="complainer_dashboard.php">Dashboard</a>
    <a href="submit_complaint.php">Add Complaint</a>
    <a href="my_complaints.php">My Complaints</a>
    <a href="account.php" class="active">My Account</a>
    <a href="login.php">Logout</a>
  </aside>

  <section class="content">
    <h1>My Account</h1>
    <?= $message ?>

    <form method="post" novalidate>
      <label for="name">Name *</label>
      <input type="text" id="name" name="name" required value="<?= htmlspecialchars($user['name']) ?>">

      <label for="email">Email *</label>
      <input type="email" id="email" name="email" required value="<?= htmlspecialchars($user['email']) ?>">

      <label for="password">New Password (leave blank to keep current)</label>
      <input type="password" id="password" name="password" autocomplete="new-password">

      <label for="password_confirm">Confirm New Password</label>
      <input type="password" id="password_confirm" name="password_confirm" autocomplete="new-password">

      <button type="submit">Update Account</button>
    </form>
  </section>
</main>

</body>
</html>
