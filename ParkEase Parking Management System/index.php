<?php
// =============================================
// ParkEase — Modern Smart Campus Mobility Landing Page
// Real-World UI & Interactive Entrance
// =============================================
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Redirect already logged-in users directly to their dashboard
if (isset($_SESSION['user_id'])) {
    header("Location: user/dashboard.php");
    exit();
}
if (isset($_SESSION['admin_id'])) {
    header("Location: admin/dashboard.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="ParkEase - Intelligent Smart Campus Parking & Mobility System">
    <title>ParkEase — Smart Campus Parking Platform</title>
    <link rel="icon" type="image/svg+xml" href="assets/images/logo.svg">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .hero-section {
            padding: 80px 0 50px;
            text-align: center;
            position: relative;
        }
        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: var(--primary-subtle);
            color: var(--primary-hover);
            padding: 6px 18px;
            border-radius: var(--radius-pill);
            font-size: 0.84rem;
            font-weight: 700;
            border: 1px solid var(--primary-border);
            margin-bottom: 24px;
        }
        .hero-title {
            font-size: clamp(2.4rem, 5vw, 3.8rem);
            font-weight: 800;
            color: var(--text-primary);
            line-height: 1.15;
            letter-spacing: -0.04em;
            max-width: 840px;
            margin: 0 auto 20px;
        }
        .hero-title span {
            background: linear-gradient(135deg, #60A5FA 0%, #3B82F6 50%, #93C5FD 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .hero-subtitle {
            font-size: clamp(1rem, 2vw, 1.15rem);
            color: var(--text-secondary);
            max-width: 640px;
            margin: 0 auto 36px;
            line-height: 1.65;
        }
        .hero-cta-group {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 16px;
            flex-wrap: wrap;
            margin-bottom: 50px;
        }
        .feature-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
            margin-top: 50px;
        }
        .feature-card {
            background: var(--bg-surface);
            border: 1px solid var(--border-subtle);
            border-radius: var(--radius-lg);
            padding: 32px 24px;
            text-align: left;
            transition: var(--transition);
        }
        .feature-card:hover {
            transform: translateY(-4px);
            border-color: var(--border-medium);
            box-shadow: var(--shadow-card);
        }
        .feature-icon-wrap {
            width: 48px;
            height: 48px;
            border-radius: var(--radius-sm);
            background: var(--bg-surface-elevated);
            border: 1px solid var(--border-subtle);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary-hover);
            margin-bottom: 20px;
        }
    </style>
</head>
<body>

    <!-- Landing Navigation -->
    <nav class="navbar">
        <a href="index.php" class="logo">
            <div class="logo-icon">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="3" width="18" height="18" rx="4"/><path d="M9 17V7h4a3 3 0 0 1 0 6H9"/></svg>
            </div>
            <span>Park<span style="color:var(--primary-hover);">Ease</span></span>
            <span class="logo-badge">CAMPUS NETWORK</span>
        </a>

        <div style="display:flex; align-items:center; gap:12px;">
            <a href="admin/login.php" class="btn btn-outline btn-sm" style="border-color:transparent; color:var(--text-muted);">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                Security & Staff
            </a>
            <a href="user/login.php" class="btn btn-primary btn-sm">
                Driver Sign In →
            </a>
        </div>
    </nav>

    <!-- Main Hero -->
    <div class="container">
        <section class="hero-section">
            <div class="hero-badge">
                <span style="width:7px; height:7px; border-radius:50%; background:var(--success); box-shadow:0 0 6px var(--success);"></span>
                Smart Campus Mobility • Active Deck Live Sensor Grid
            </div>

            <h1 class="hero-title">
                Seamless Campus Parking <span>Driven by Real-Time Telemetry</span>
            </h1>

            <p class="hero-subtitle">
                Instant ultrasonic bay reservations, zero double-booking, offline intelligent driver assistance, and complete administrative control for modern campuses.
            </p>

            <div class="hero-cta-group">
                <a href="user/login.php" class="btn btn-primary" style="padding:14px 28px; font-size:0.95rem;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"></rect><path d="M9 17V7h4a3 3 0 0 1 0 6H9"></path></svg>
                    Reserve Parking Bay →
                </a>
                <a href="user/signup.php" class="btn btn-outline" style="padding:14px 24px; font-size:0.95rem;">
                    Driver Registration
                </a>
            </div>

            <!-- Features Showcase -->
            <div class="feature-grid">
                <div class="feature-card">
                    <div class="feature-icon-wrap">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"></rect><path d="M9 17V7h4a3 3 0 0 1 0 6H9"></path></svg>
                    </div>
                    <h3 style="font-size:1.15rem; color:var(--text-primary); margin-bottom:8px;">Instant Bay Allocation</h3>
                    <p style="color:var(--text-muted); font-size:0.9rem;">
                        Real-time status synchronization ensures that when a vehicle checks into a slot, it immediately updates across all drivers and barrier gates.
                    </p>
                </div>

                <div class="feature-card">
                    <div class="feature-icon-wrap">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="16" rx="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                    </div>
                    <h3 style="font-size:1.15rem; color:var(--text-primary); margin-bottom:8px;">Digital Gate QR Passes</h3>
                    <p style="color:var(--text-muted); font-size:0.9rem;">
                        Automated digital parking permits with barcode/QR verification, student verification tokens, and instant self-checkout flow.
                    </p>
                </div>

                <div class="feature-card">
                    <div class="feature-icon-wrap">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                    </div>
                    <h3 style="font-size:1.15rem; color:var(--text-primary); margin-bottom:8px;">Administrative Control</h3>
                    <p style="color:var(--text-muted); font-size:0.9rem;">
                        High-resolution operations console for security staff to track parking lot utilization rates, register new bays, and oversee authorized drivers.
                    </p>
                </div>
            </div>
        </section>
    </div>

    <!-- Footer -->
    <footer class="site-footer">
        <p>&copy; <?php echo date('Y'); ?> <strong>ParkEase</strong> • Modern Smart Campus Mobility System</p>
    </footer>

    <!-- Chatbot Widget -->
    <?php include_once __DIR__ . '/chatbot/chatbot.php'; ?>

    <script src="assets/js/main.js"></script>
    <script src="assets/js/chatbot.js"></script>
</body>
</html>
