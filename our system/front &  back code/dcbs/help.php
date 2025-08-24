<?php
session_start();
$active = 'help';
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Help | DCBS</title>

  <style>
    :root{--logo:#ffbd59;--muted:#4b5563;--white:#fff;--black:#000}
    *{box-sizing:border-box;font-family:Inter,system-ui,-apple-system,Segoe UI,Roboto,'Helvetica Neue',Arial}
    body{margin:0;background:var(--white);color:#111;min-height:100vh}
    main{max-width:1000px;margin:36px auto;padding:18px}
    .hero{padding:28px 18px;border-radius:10px;margin-bottom:18px}
    .hero h1{font-size:26px;margin:0 0 6px}
    .hero p{color:var(--muted);margin:0}
    .card{background:#fff;border-radius:10px;box-shadow:0 8px 30px rgba(10,10,10,0.04);padding:20px;margin-bottom:18px}
    .faq-item{border-top:1px solid #f1f1f1;padding:14px 0;cursor:pointer}
    .faq-item:first-child{border-top:0}
    .question{display:flex;justify-content:space-between;align-items:center;font-weight:700}
    .answer{margin-top:8px;color:var(--muted);display:none}
    .contact-grid{display:grid;grid-template-columns:1fr 1fr;gap:12px}
    .contact-box{padding:14px;border-radius:8px;background:#fafafa}
    a.button{display:inline-block;padding:10px 16px;border-radius:8px;background:var(--logo);color:#000;font-weight:700;text-decoration:none}
    .small{color:var(--muted);font-size:13px}
    @media(max-width:760px){ .contact-grid{grid-template-columns:1fr} .hero h1{font-size:20px} }
    /* simple chevron */
    .chev{font-size:14px;color:var(--muted);transition:transform .18s ease}
    .chev.open{transform:rotate(180deg)}
  </style>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
      document.querySelectorAll('.faq-item').forEach(function(item){
        item.addEventListener('click', function(){
          const ans = item.querySelector('.answer');
          const chev = item.querySelector('.chev');
          if (ans.style.display === 'block') {
            ans.style.display = 'none';
            chev.classList.remove('open');
          } else {
            // close others
            document.querySelectorAll('.answer').forEach(a => a.style.display = 'none');
            document.querySelectorAll('.chev').forEach(c => c.classList.remove('open'));
            ans.style.display = 'block';
            chev.classList.add('open');
            // scroll into view a little
            item.scrollIntoView({behavior:'smooth', block:'center'});
          }
        });
      });
    });
  </script>
</head>
<body>

<?php include __DIR__ . '/topbar.php'; ?>

<main>
  <section class="hero">
    <h1>Help & Support</h1>
    <p>Need help using the Digital Complaint Box System? Below are quick answers and ways to contact us.</p>
  </section>

  <section class="card" aria-labelledby="faq-title">
    <h2 id="faq-title">Frequently Asked Questions</h2>

    <div class="faq-item" role="button" tabindex="0">
      <div class="question">
        <span>How do I create an account?</span>
        <span class="chev">▾</span>
      </div>
      <div class="answer">Go to <a href="register.php">Register</a> and fill in your name, email, and password. After registering you can login and submit complaints.</div>
    </div>

    <div class="faq-item" role="button" tabindex="0">
      <div class="question">
        <span>What should I include in a complaint?</span>
        <span class="chev">▾</span>
      </div>
      <div class="answer">Provide a clear title, a detailed description, and an optional image if available. Choose the most relevant category so handlers can respond faster.</div>
    </div>

    <div class="faq-item" role="button" tabindex="0">
      <div class="question">
        <span>I forgot my password. What now?</span>
        <span class="chev">▾</span>
      </div>
      <div class="answer">Click <a href="forgot.php">Forgot password?</a> on the login page, enter your email, and follow the reset link sent to you.</div>
    </div>

    <div class="faq-item" role="button" tabindex="0">
      <div class="question">
        <span>How long until my complaint is handled?</span>
        <span class="chev">▾</span>
      </div>
      <div class="answer">Response times vary by department. Handlers will update the complaint status — check your <a href="complainer_dashboard.php">My Complaints</a> area for progress.</div>
    </div>

  </section>

  <section class="card" aria-labelledby="contact-title">
    <h2 id="contact-title">Contact Support</h2>

    <div class="contact-grid">
      <div class="contact-box">
        <strong>Support email</strong>
        <p class="small">For account issues or technical problems:</p>
        <p><a href="mailto:support@example.com">support@example.com</a></p>
        
      </div>

      <div class="contact-box">
        <strong>Phone / WhatsApp</strong>
        <p class="small">If urgent, call or message:</p>
        <p><a href="tel:+94123456789">+94 12 345 6789</a></p>
        
      </div>

      <div class="contact-box">
        <strong>Submit a complaint</strong>
        <p class="small">Ready to tell us? Click below to submit a new complaint.</p>
        <p><a class="button" href="submit_complaint.php">Submit Complaint</a></p>
      </div>

      <div class="contact-box">
        <strong>Account help</strong>
        <p class="small">Need to register or login?</p>
        <p><a class="button" href="register.php">Register</a> <a class="button" href="login.php" style="margin-left:8px">Login</a></p>
      </div>
    </div>
  </section>

  <section class="card" aria-labelledby="guidelines-title">
    <h2 id="guidelines-title">Guidelines & Good Practices</h2>
    <ul>
      <li class="small">Be specific — include dates, locations and steps to reproduce the issue when possible.</li>
      <li class="small">Attach evidence — images or documents help handlers resolve issues faster.</li>
      <li class="small">Respect privacy — do not include sensitive personal data in public complaints.</li>
    </ul>
  </section>

  <footer style="text-align:center;color:var(--muted);font-size:13px;margin-top:10px">
    <p>Still need help? Email <a href="mailto:support@example.com">support@example.com</a> or call +94 12 345 6789.</p>
  </footer>
</main>

</body>
</html>
 