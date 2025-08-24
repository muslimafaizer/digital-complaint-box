<?php
session_start();
$active = 'register';

// DB connection
require_once 'config.php'; // must define $conn (mysqli)

// initialization
$errors = [];
$name = '';
$email = '';

// handle POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';
    $user_type = 'complainer'; // registration for complainers only

    // basic server-side validation
    if ($name === '') $errors[] = "Name is required.";
    if ($email === '') $errors[] = "Email is required.";
    if ($password === '') $errors[] = "Password is required.";
    if ($password !== $confirm) $errors[] = "Passwords do not match.";
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Invalid email address.";
    if (strlen($password) < 6) $errors[] = "Password must be at least 6 characters.";

    if (empty($errors)) {
        // check if email already exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? LIMIT 1");
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $res = $stmt->get_result();
        if ($res && $res->num_rows > 0) {
            $errors[] = "An account with this email already exists. Try logging in.";
        } else {
            // insert user
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $ins = $conn->prepare("INSERT INTO users (name, email, password, user_type) VALUES (?, ?, ?, ?)");
            $ins->bind_param('ssss', $name, $email, $hash, $user_type);
            if ($ins->execute()) {
                // registration success -> redirect to login with a flag
                header('Location: login.php?registered=1');
                exit;
            } else {
                $errors[] = "Registration failed. Please try again. (" . htmlspecialchars($ins->error) . ")";
            }
            $ins->close();
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
  <title>Register | DCBS</title>

  <!-- Inline page styles (no external CSS) -->
  <style>
    :root{--logo:#ffbd59;--muted:#4b5563;--white:#fff}
    *{box-sizing:border-box;font-family:Inter,system-ui,-apple-system,Segoe UI,Roboto,'Helvetica Neue',Arial}
    body{margin:0;background:var(--white);color:#111;min-height:100vh}
    main{max-width:900px;margin:36px auto;padding:18px}
    .card{background:#fff;border-radius:10px;box-shadow:0 8px 30px rgba(10,10,10,0.06);padding:24px}
    h1{margin:0 0 8px 0}
    p.lead{color:var(--muted);margin:0 0 14px 0}
    label{display:block;margin-top:12px;font-weight:600}
    input[type=text],input[type=email],input[type=password]{
      width:100%;padding:10px;border:1px solid #e5e7eb;border-radius:8px;margin-top:6px;font-size:14px;
    }
    .btn-submit{margin-top:16px;padding:10px 18px;border-radius:8px;border:none;background:var(--logo);color:#000;font-weight:700;cursor:pointer}
    .errors{background:#fee2e2;color:#991b1b;padding:12px;border-radius:8px;margin-bottom:12px}
    .success{background:#ecfdf5;color:#065f46;padding:12px;border-radius:8px;margin-bottom:12px}
    .small{color:var(--muted);font-size:13px;margin-top:10px}
    .center{text-align:center}
    @media(max-width:700px){ main{padding:12px} }
  </style>

  <script>
    // client-side password match check
    function validateForm(e) {
      const pw = document.getElementById('password').value;
      const cpw = document.getElementById('confirm_password').value;
      if (pw.length < 6) {
        alert('Password must be at least 6 characters.');
        e.preventDefault();
        return false;
      }
      if (pw !== cpw) {
        alert('Passwords do not match.');
        e.preventDefault();
        return false;
      }
      return true;
    }
    document.addEventListener('DOMContentLoaded', function(){
      const form = document.getElementById('regForm');
      if (form) form.addEventListener('submit', validateForm);
    });
  </script>
</head>
<body>

<?php include __DIR__ . '/topbar.php'; ?>

<main>
  <div class="card" role="region" aria-label="Registration card">
    <h1>Create your account</h1>
    <p class="lead">Register as a complainer to submit complaints and view responses.</p>

    <?php if (!empty($errors)): ?>
      <div class="errors">
        <strong>Please fix the following:</strong>
        <ul>
          <?php foreach($errors as $e): ?>
            <li><?= htmlspecialchars($e) ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
    <?php endif; ?>

    <form id="regForm" method="post" action="register.php" novalidate>
      <label for="name">Full name</label>
      <input id="name" name="name" type="text" value="<?= htmlspecialchars($name) ?>" required>

      <label for="email">Email</label>
      <input id="email" name="email" type="email" value="<?= htmlspecialchars($email) ?>" required>

      <label for="password">Password</label>
      <input id="password" name="password" type="password" required placeholder="Min 6 characters">

      <label for="confirm_password">Confirm password</label>
      <input id="confirm_password" name="confirm_password" type="password" required>

      <div style="margin-top:12px">
        <button class="btn-submit" type="submit">Register</button>
      </div>

      <div class="small center">
        Already have an account? <a href="login.php">Login</a>
      </div>
    </form>
  </div>
</main>
<?php include 'footer.php'; ?>
</body>
</html>
