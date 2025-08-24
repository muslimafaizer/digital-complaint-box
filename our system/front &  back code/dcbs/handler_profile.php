<?php
// handler_profile.php
session_start();
require_once __DIR__ . '/config.php';

// protect page: must be logged in and user_type = handler
if (empty($_SESSION['user']) || ($_SESSION['user']['user_type'] ?? '') !== 'handler') {
    header('Location: login.php');
    exit;
}

$handler_id = (int)$_SESSION['user']['id'];
$message = '';

// Fetch current user data
$stmt = $conn->prepare("SELECT name, email FROM users WHERE id = ? AND user_type = 'handler' LIMIT 1");
$stmt->bind_param('i', $handler_id);
$stmt->execute();
$res = $stmt->get_result();
$user = $res->fetch_assoc();
$stmt->close();

if (!$user) {
    die('User not found.');
}

// Handle form submission for update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';

    // Basic validation
    if ($name === '' || $email === '') {
        $message = '<p style="color:red;">Name and Email cannot be empty.</p>';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = '<p style="color:red;">Invalid email format.</p>';
    } elseif ($password !== '' && $password !== $password_confirm) {
        $message = '<p style="color:red;">Passwords do not match.</p>';
    } else {
        if ($password !== '') {
            // Update name, email, and password (hash password)
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE users SET name = ?, email = ?, password = ? WHERE id = ? AND user_type = 'handler'");
            $stmt->bind_param('sssi', $name, $email, $hashed_password, $handler_id);
        } else {
            // Update name and email only
            $stmt = $conn->prepare("UPDATE users SET name = ?, email = ? WHERE id = ? AND user_type = 'handler'");
            $stmt->bind_param('ssi', $name, $email, $handler_id);
        }

        if ($stmt->execute()) {
            $message = '<p style="color:green;">Profile updated successfully.</p>';
            $_SESSION['user']['name'] = $name;
            $_SESSION['user']['email'] = $email;
            $user['name'] = $name;
            $user['email'] = $email;
        } else {
            $message = '<p style="color:red;">Failed to update profile.</p>';
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Handler Profile | DCBS</title>
<style>
  :root{--logo:#ffbd59;--muted:#4b5563;--white:#fff;--black:#000}
  *{box-sizing:border-box;font-family:Arial, Helvetica, sans-serif}
  body{margin:0;background:#f4f6f8;color:#111;min-height:100vh}

  /* Topbar */
  .topbar {
    background: var(--black);
    color: var(--white);
    padding: 10px 20px;
    display: flex;
    align-items: center;
    justify-content: space-between;
  }
  .topbar .logo-section {
    display: flex;
    align-items: center;
    gap: 10px;
  }
  .topbar img {
    height: 40px;
  }
  .topbar h1 {
    margin: 0;
    font-size: 18px;
    color: var(--logo);
  }
  .topbar nav a {
    color: var(--white);
    text-decoration: none;
    margin: 0 8px;
    font-weight: 500;
  }
  .topbar nav a:hover {
    color: var(--logo);
  }

  /* Layout */
  main {
    display: flex;
    min-height: calc(100vh - 60px);
  }

  .sidebar {
    width: 220px;
    background: var(--black);
    color: var(--white);
    padding: 20px 12px;
    flex-shrink: 0;
  }
  .sidebar h3 {
    color: var(--logo);
    margin: 0 0 12px;
    font-size: 16px;
  }
  .sidebar a {
    display: block;
    padding: 10px 12px;
    color: var(--white);
    text-decoration: none;
    margin: 6px 0;
    border-radius: 6px;
  }
  .sidebar a.active,
  .sidebar a:hover {
    background: var(--logo);
    color: var(--black);
    font-weight: 600;
  }

  .content {
    flex: 1;
    padding: 20px;
    overflow-y: auto;
    max-width: 600px;
  }

  h1 {
    color: var(--logo);
    margin-bottom: 20px;
  }
  form {
    background: #fff;
    padding: 20px;
    border-radius: 12px;
    box-shadow: 0 6px 20px rgba(0,0,0,0.05);
  }
  label {
    display: block;
    margin-top: 15px;
    font-weight: bold;
    color: var(--muted);
  }
  input[type="text"],
  input[type="email"],
  input[type="password"] {
    width: 100%;
    padding: 10px 12px;
    margin-top: 6px;
    border-radius: 6px;
    border: 1px solid #ccc;
    font-size: 14px;
  }
  button {
    margin-top: 20px;
    background: var(--logo);
    border: none;
    padding: 12px 18px;
    border-radius: 8px;
    font-weight: 700;
    cursor: pointer;
    color: #000;
    font-size: 16px;
  }
  button:hover {
    background: #e6ac4c;
  }
  .message {
    margin-bottom: 15px;
  }

  @media(max-width: 820px) {
    .sidebar {
      display: none;
    }
    main {
      min-height: auto;
    }
  }
</style>
</head>
<body>

<header class="topbar">
  <div class="logo-section">
    <img src="logo.png" alt="Logo" />
    
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
    <a href="handler_my_complaints.php">My Complaints</a>
    <a href="handler_profile.php" class="active">My Account</a>
    <a href="login.php">Logout</a>
  </aside>

  <section class="content">
    <h1>My Profile</h1>

    <div class="message"><?= $message ?></div>

    <form method="post" action="">
      <label for="name">Name</label>
      <input type="text" id="name" name="name" required value="<?= htmlspecialchars($user['name']) ?>" />

      <label for="email">Email</label>
      <input type="email" id="email" name="email" required value="<?= htmlspecialchars($user['email']) ?>" />

      <label for="password">New Password (leave blank to keep current)</label>
      <input type="password" id="password" name="password" placeholder="Enter new password" />

      <label for="password_confirm">Confirm New Password</label>
      <input
        type="password"
        id="password_confirm"
        name="password_confirm"
        placeholder="Confirm new password"
      />

      <button type="submit">Update Profile</button>
    </form>
  </section>
</main>

</body>
</html>
