<?php
// topbar.php - REUSABLE topbar (include this after session_start())
// Set $active = 'home'|'about'|'help'|'login' etc before including to highlight menu.
$active = $active ?? '';
?>
<style>
:root{
  --black:#000000;
  --logo:#ffbd59;
  --white:#ffffff;
  --muted:#4b5563;
}
.dcbs-topbar{background:var(--black);color:var(--white);border-bottom:3px solid rgba(255,189,89,0.06);width:100%}
.dcbs-topbar-inner{max-width:1100px;margin:0 auto;padding:12px 18px;display:flex;align-items:center;justify-content:space-between}
.dcbs-logo-link{display:flex;align-items:center;text-decoration:none;color:inherit}
.dcbs-logo-img{height: 50px;width: auto;}
.dcbs-title{font-weight:700;font-size:16px;letter-spacing:0.2px}
.dcbs-menu{display:flex;gap:12px;align-items:center}
.dcbs-menu a{color:var(--white);text-decoration:none;padding:8px 10px;border-radius:8px;font-weight:600}
.dcbs-menu a.active{background:rgba(255,189,89,0.12); color:var(--logo)}
.dcbs-menu a:hover{background:rgba(255,255,255,0.06)}
.dcbs-menu a.logout-link{border:1px solid rgba(255,255,255,0.06);padding:8px 12px;background:transparent}
@media(max-width:820px){
  .dcbs-title{display:none}
  .dcbs-topbar-inner{padding:10px}
}
</style>

<header class="dcbs-topbar" role="banner">
  <div class="dcbs-topbar-inner">
    <a class="dcbs-logo-link" href="index.php" aria-label="DCBS Home">
      <img src="logo.png" alt="DCBS Logo" class="dcbs-logo-img">
      
    </a>

    <nav class="dcbs-menu" aria-label="Main menu">
      <a class="<?= $active === 'home' ? 'active' : '' ?>" href="index.php">Home</a>
      <a class="<?= $active === 'about' ? 'active' : '' ?>" href="about.php">About</a>
      <a class="<?= $active === 'help' ? 'active' : '' ?>" href="help.php">Help</a>

      <!--<?php if (!empty($_SESSION['user'])): ?>
        <a href="<?= htmlspecialchars($_SESSION['user']['user_type'] === 'handler' ? 'handler_dashboard.php' : 'complainer_dashboard.php') ?>">Dashboard</a>
        <a href="logout.php" class="logout-link">Logout</a>
      <?php else: ?>
        <a class="<?= $active === 'login' ? 'active' : '' ?>" href="login.php">Login</a>
      <?php endif; ?>--!>
    </nav>
  </div>
</header>
