<?php
require_once 'includes/auth.php';
$active = 'about';
$page_title = 'About Us';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>About Us - kharchaPatra</title>
<link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<div class="app-layout">
    <?php include 'includes/sidebar.php'; ?>
    <div class="main-content">
        <?php include 'includes/topbar.php'; ?>
        <div class="page-body">
        <div class="about-section">

            <!-- Hero -->
            <div class="about-hero">
                <div class="hero-text">
                    <div class="estd">🇳🇵 Established 13 July 2026</div>
                    <h1>About kharchaPatra</h1>
                    <p class="lead">kharchaPatra was founded as a student project with the
                       vision of making personal finance management simple, organized, and
                       accessible for everyone.</p>
                </div>
                <div class="hero-image">
                    <img src="assets/images/dashboard.png" alt="kharchaPatra dashboard preview">
                </div>
            </div>

            <!-- Mission & Vision -->
            <div class="info-card-grid">
                <div class="info-card mission">
                    <div class="icon-badge">🎯</div>
                    <h3>Mission</h3>
                    <p>To simplify personal money management by providing an easy-to-use
                       platform where users can track income, record expenses, monitor
                       savings, and make informed financial decisions.</p>
                </div>
                <div class="info-card vision">
                    <div class="icon-badge">👁️</div>
                    <h3>Vision</h3>
                    <p>To become a trusted personal finance companion that encourages
                       responsible spending, consistent saving, and financial awareness for
                       students, professionals, and families.</p>
                </div>
            </div>

            <!-- Our Story -->
            <div class="story-block">
                <div class="story-image">
                    <img src="assets/images/piggy.png" alt="Piggy bank representing savings">
                </div>
                <div class="story-text">
                    <h3>Our Story</h3>
                    <p>kharchaPatra was created with a simple goal: to make personal finance
                       easy for everyone. From a small idea to a fully functional web
                       application, the journey focused on building a clean, user-friendly
                       platform where users can record income, track expenses, monitor
                       savings, and visualize financial progress through meaningful reports.
                       Every feature is designed to encourage smarter spending and better
                       budgeting habits.</p>
                </div>
            </div>

            <!-- Technologies -->
            <div class="section-heading">
                <h2>🛠️ Technologies We Use</h2>
            </div>
            <div class="tech-grid">
                <div class="tech-card">
                    <h4>Frontend</h4>
                    <p>HTML<br>CSS<br>JavaScript</p>
                </div>
                <div class="tech-card">
                    <h4>Backend</h4>
                    <p>PHP<br>Database<br>MySQL</p>
                </div>
                <div class="tech-card">
                    <h4>Tools</h4>
                    <p>XAMPP<br>Figma</p>
                </div>
            </div>

            <!-- Target Users -->
            <div class="users-block">
                <div class="users-text">
                    <h3>🌍 Target Users</h3>
                    <ul>
                        <li>Students</li>
                        <li>Working Professionals</li>
                        <li>Freelancers</li>
                        <li>Small Business Owners</li>
                        <li>Anyone who wants to manage personal finances effectively</li>
                    </ul>
                </div>
                <div class="users-image">
                    <img src="assets/images/software-users.png" alt="Users of kharchaPatra">
                </div>
            </div>

            <!-- Journey -->
            <div class="section-heading">
                <h2>🚀 OUR JOURNEY</h2>
            </div>
            <p style="text-align:center; color:var(--text-muted); font-size:12.5px; margin-top:-14px; margin-bottom:26px;">
                Every great idea starts with a simple problem.
            </p>
            <div class="timeline">
                <div class="timeline-item">
                    <h4>💡 Idea</h4>
                    <p>Many students and individuals found it difficult to keep track of
                       their daily income and expenses using notebooks or scattered mobile
                       notes.</p>
                </div>
                <div class="timeline-item">
                    <h4>🔍 Research</h4>
                    <p>We explored how people manage their personal finances and identified
                       the need for a simple, clean, and beginner-friendly expense tracking
                       application.</p>
                </div>
                <div class="timeline-item">
                    <h4>🎨 Design</h4>
                    <p>A minimal interface with a calm sage green theme was created to make
                       money management feel simple, organized, and stress-free.</p>
                </div>
                <div class="timeline-item">
                    <h4>💻 Development</h4>
                    <p>kharchaPatra was developed as a web application using HTML, CSS,
                       JavaScript, PHP, and MySQL to provide an easy way to record income,
                       expenses, savings, and budgets.</p>
                </div>
                <div class="timeline-item">
                    <h4>📊 Smart Insights</h4>
                    <p>Interactive charts and reports were added to help users understand
                       spending patterns and make better financial decisions.</p>
                </div>
                <div class="timeline-item">
                    <h4>🚩 Today</h4>
                    <p>kharchaPatra continues to help users build better financial habits by
                       making personal expense tracking simple, organized, and accessible
                       for everyone.</p>
                </div>
            </div>

            <div class="footer-quote">
                <div class="label">Footer Quote</div>
                <div class="quote">"Every Rupee Counts. Every Decision Matters."</div>
                <div class="meta">© 2026 kharchaPatra | Version 1.0</div>
            </div>

        </div>
        </div>
    </div>
</div>
</body>
</html>