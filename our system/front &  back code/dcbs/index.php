<?php
session_start();
$active = 'home';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Home | DCBS</title>

  <!-- Page styles (no external CSS) -->
  <style>
    :root{--white:#ffffff;--logo:#ffbd59;--muted:#4b5563;--black:#000}
    *{box-sizing:border-box;font-family:Inter,system-ui,-apple-system,Segoe UI,Roboto,'Helvetica Neue',Arial}
    body{margin:0;background:var(--white);color:#111}
    main{max-width:1100px;margin:36px auto;padding:18px}
    .hero{text-align:center;padding:88px 20px;border-radius:12px}
    .hero h1{font-size:32px;margin-bottom:10px}
    .hero p{color:var(--muted);font-size:16px;margin-bottom:20px}
    .btn{display:inline-block;padding:12px 20px;border-radius:10px;text-decoration:none;font-weight:700;background:var(--logo);color:#000;margin:0 8px}
    .btn:hover{opacity:.95}
    /* small responsive */
    @media(max-width:520px){ .hero{padding:40px 12px} .hero h1{font-size:22px} }
  </style>
</head>
<body>

<?php include __DIR__ . '/topbar.php'; ?>

<main>
  <section class="hero" aria-labelledby="hero-title">
    <h1 id="hero-title">Welcome to the Complaint Management System</h1>
    <p>Your Voice Matters — We’re Here to Listen.</p>

    <div>
      <a class="btn" href="login.php">Login</a>
      <a class="btn" href="register.php">Complainer Register</a>
    </div>
  </section>
</main>
<?php include 'footer.php'; ?>

</body>
</html>
