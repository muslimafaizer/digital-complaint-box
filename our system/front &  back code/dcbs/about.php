<?php
session_start();
$active = 'about';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>About | Digital Complaint Box System</title>

    <!-- Optional: link a global CSS file if you have one -->
    <!-- <link rel="stylesheet" href="assets/css/style.css"> -->

    <style>
        /* page-specific styles (keeps topbar external) */
        body {
            margin: 0;
            font-family: 'Inter', system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial;
            background-color: #ffffff;
            color: #222;
        }

        .container {
            max-width: 1000px;
            margin: 40px auto;
            padding: 0 20px;
        }

        .page-title {
            color: #ffbd59;
            font-size: 32px;
            margin-bottom: 12px;
            font-weight: 700;
        }

        .lead {
            font-size: 16px;
            line-height: 1.6;
            margin-bottom: 18px;
        }

        .features {
            margin: 18px 0;
            padding-left: 18px;
        }

        .features li {
            margin-bottom: 8px;
        }

        .card {
            background: #fff;
            border: 1px solid #eee;
            border-radius: 10px;
            padding: 18px;
            box-shadow: 0 6px 18px rgba(0,0,0,0.04);
        }

        /* small responsive tweak */
        @media (max-width: 600px) {
            .page-title { font-size: 24px; }
        }
    </style>
</head>
<body>

<?php include __DIR__ . '/topbar.php'; ?>
    

    <main class="container">
        <section class="card">
            <h1 class="page-title">About Digital Complaint Box System</h1>

            <p class="lead">
                Welcome to the <strong>Digital Complaint Box System (DCBS)</strong>. DCBS is built to streamline complaint submission,
                tracking, and handling — making the process transparent and efficient for both complainers and handlers.
            </p>

            <p>
                Our goal is to modernize how complaints are received and resolved by providing:
            </p>

            <ul class="features">
                <li>Secure complaint submission with attachments and timestamps.</li>
                <li>Role-based access (Complainer, Handler) for proper workflows.</li>
                <li>Real-time status updates and notifications.</li>
                <li>Simple, responsive UI that works on desktop and mobile.</li>
            </ul>

            <p>
                DCBS is actively maintained and will receive improvements based on user feedback and operational needs.
                If you have suggestions or need support, please contact the system administrator.
            </p>

            <hr />

            <p style="font-size:14px; color:#666; margin:0;">
                Thank you for using <span style="color:#ffbd59; font-weight:600;">Digital Complaint Box System</span>.
            </p>
        </section>
        
    </main>

    <!-- external footer -->
    <?php include __DIR__ . '/footer.php'; ?>

</body>
</html>
