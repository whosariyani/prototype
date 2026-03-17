<?php
include 'db_conn.php';
include 'role_access.php';

// Get current user's role for navigation filtering
$user_role = getCurrentUserRole();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Inter font -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <title>Document</title>
    <style>
        :root {
            --bg: #f4f6f8;
            --card: #ffffff;
            --muted: #6b7280;
            --green-1: #095d2fff;
            /* gradient start */
            --primary-color: #229221;
            --green-2: #1fa25a;
            /* gradient end */

            --radius: 12px;
            --shadow-sm: 0 6px 18px rgba(19, 42, 34, 0.06);
            --shadow-md: 0 12px 30px rgba(19, 42, 34, 0.08);
            --border: 1px solid rgba(15, 23, 42, 0.06);
            --text: #0f172a;

            --primary-dark: #105a0f;
            --secondary-color: #24a85b;
            --secondary-dark: #27ae60;
            --accent-color: #f39c12;
            --dark-color: #306e3a;
            --light-color: #ecf0f1;
            --danger-color: #e74c3c;
            --grey-100: #f8f9fa;
            --grey-200: #e9ecef;
            --grey-300: #dee2e6;
            --grey-400: #ced4da;
            --grey-500: #adb5bd;
            --grey-600: #6c757d;
            --grey-700: #495057;
            --grey-800: #343a40;
            --grey-900: #212529;
            --white: #ffffff;
            --shadow-sm: 0 2px 4px rgba(0, 0, 0, 0.05);
            --shadow-md: 0 4px 8px rgba(0, 0, 0, 0.1);
            --shadow-lg: 0 8px 16px rgba(0, 0, 0, 0.1);
            --border-radius-sm: 4px;
            --border-radius-md: 8px;
            --border-radius-lg: 16px;
            --transition-fast: 150ms ease;
            --transition-normal: 300ms ease;
            --transition-slow: 500ms ease;
            --font-family: 'Montserrat', sans-serif;
            --success-color: #52b788;
            /* Added success color */
            --danger-color: #e63946;
            /* Added danger color */
        }

        /* Dark mode */
        body.dark {
            --bg: #0b1220;
            --card: #0f1724;
            --muted: #9aa4b2;
            --shadow-sm: 0 6px 18px rgba(2, 6, 23, 0.7);
            --shadow-md: 0 12px 30px rgba(2, 6, 23, 0.75);
            --border: 1px solid rgba(255, 255, 255, 0.03);
            --text: #e6eef8;
        }

        /* Basic reset */
        * {
            box-sizing: border-box;
        }

        html,
        body {
            height: 100%;
        }

        body {
            margin: 0;
            font-family: 'Inter', system-ui, -apple-system, 'Segoe UI', Roboto, 'Helvetica Neue', Arial;
            background: var(--bg);
            color: var(--text);
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }


        header {
            background: linear-gradient(90deg, var(--green-1), var(--green-2));
            color: #fff;
            padding: 18px 28px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: var(--shadow-sm);
            position: sticky;
            top: 0;
            z-index: 40;
        }

        .header-left {
            display: flex;
            align-items: center;
            gap: 18px;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .brand img {
            width: 64px;
            height: 64px;
            border-radius: 999px;
            object-fit: cover;
            border: 3px solid rgba(255, 255, 255, 0.12);
        }

        .brand h1 {
            font-size: 20px;
            font-weight: 700;
            letter-spacing: 0.6px;
            margin: 0;
        }

        .header-actions {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        /* Simple flat scan button */
        .btn-scan {
            background: var(--green-1);
            color: #fff;
            padding: 10px 16px;
            border-radius: 10px;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            border: 1px solid rgba(255, 255, 255, 0.06);
        }

        /* Dark mode */
        .toggle {
            background: rgba(255, 255, 255, 0.08);
            padding: 8px;
            border-radius: 8px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            border: 1px solid rgba(255, 255, 255, 0.05);
        }

        .toggle i {
            font-size: 14px;
        }

        .nav {
            display: flex;
            gap: 1rem;
        }

        .nav-menu {
            position: relative;
            display: inline-block;
        }

        .nav-close {
            display: none;
            background: transparent;
            border: none;
            color: #333;
            font-size: 1.4rem;
            align-self: flex-end;
            cursor: pointer;
            margin-bottom: 0.5rem;
        }

        /* Mobile menu button */
        .menu-toggle {
            display: none;
            width: 40px;
            height: 40px;
            min-width: 40px;
            min-height: 40px;
            border-radius: 8px;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 1rem;
            cursor: pointer;
            border: 1px solid rgba(255, 255, 255, 0.28);
        }

        .toggle {
            display: inline-flex;
            width: 40px;
            height: 40px;
            min-width: 40px;
            min-height: 40px;
            border-radius: 8px;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 1rem;
            cursor: pointer;
            border: 1px solid rgba(36, 168, 91, 0.45);
            background: rgba(36, 168, 91, 0.25);
        }

        .menu-toggle {
            background: rgba(255, 255, 255, 0.2);
        }

        .toggle {
            background: rgba(36, 168, 91, 0.25);
            border: 1px solid rgba(36, 168, 91, 0.45);
        }

        .toggle-btn {
            display: none;
        }

        .menu-toggle i,
        .toggle i {
            font-size: 1rem;
        }

        @media (max-width: 900px) {
            header {
                width: 100%;
                flex-wrap: wrap;
                justify-content: space-between;
                align-items: center;
                margin: 0;
                padding-top: 30px;
            }

            .header-left {
                width: 100%;
                display: flex;
                justify-content: space-between;
            }

            .toggle-btn {
                display: inline-flex;
                justify-content: center;
                align-items: center;
                gap: 8px;
            }

            .header-actions {
                width: 100%;
                margin-top: 10px;
                display: flex;
                flex-direction: column;
                align-items: stretch;
                gap: 8px;
            }

            .nav {
                position: fixed;
                top: 0;
                right: 0;
                height: 100%;
                width: 280px;
                transform: translateX(100%);
                transition: transform 250ms ease-in-out;
                flex-direction: column;
                align-items: stretch;
                padding: 1rem;
                background: var(--dark-color);
                border-left: 1px solid rgba(0, 0, 0, 0.08);
                box-shadow: -6px 0 24px rgba(0, 0, 0, 0.18);
                z-index: 90;
                overflow-y: auto;
            }

            .mobile-nav-overlay {
                display: none;
                position: fixed;
                inset: 0;
            }

            .mobile-nav-overlay.show {
                display: block;
            }

            .nav.show {
                transform: translateX(0);
            }

            .nav-close {
                display: block;
                background: transparent;
                border: none;
                color: #ffffff;
                font-size: 1.4rem;
                align-self: flex-end;
                cursor: pointer;
                margin-bottom: 0.5rem;
            }

            .nav-menu,
            .dropbtn {
                width: 100%;
            }

            .dropbtn {
                justify-content: space-between;
            }

            .menu-toggle,
            .toggle {
                display: inline-flex;
                width: 40px;
                height: 40px;
                min-width: 40px;
                min-height: 40px;
                padding: 0;
                margin-left: 4px;
            }


            .brand h1 {
                font-size: 20px;
            }

            .brand img {
                width: 60px;
                height: 60px;
            }
        }

        @media (max-width: 576px) {
            header {
                padding: 10px 12px;
            }

            .brand h1 {
                font-size: 16px;
            }

            .brand div {
                font-size: 11px;
            }

            .btn-scan,
            .dropbtn,
            .toggle {
                font-size: 13px;
                padding: 8px 10px;
            }
        }

        .dropbtn {
            background: rgba(255, 255, 255, 0.12);
            color: #fff;
            padding: 10px 16px;
            border-radius: 10px;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            border: 1px solid rgba(255, 255, 255, 0.06);
            cursor: pointer;
        }

        .dropbtn i {
            font-size: 0.8rem;
            transition: var(--transition);
        }

        .dropdown-content {
            display: none;
            position: absolute;
            background-color: white;
            color: black;
            min-width: 190px;
            box-shadow: var(--shadow);
            z-index: 1;
            border-radius: var(--border-radius);
            overflow: hidden;
            transform: translateY(10px);
            opacity: 0;
            transition: var(--transition);
        }

        .dropdown-content a {
            color: var(--text-dark);
            padding: 12px 16px;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 10px;
            transition: var(--transition);
        }

        .dropdown-content a i {
            color: var(--primary-color);
        }

        .dropdown-content a:hover {
            background-color: #f1f1f1;
            color: var(--primary-color);
        }

        .nav-menu:hover .dropdown-content {
            display: block;
            transform: translateY(0);
            opacity: 1;
        }

        .nav-menu:hover .dropbtn {
            background-color: rgba(255, 255, 255, 0.1);
        }

        .nav-menu:hover .dropdown-arrow {
            transform: rotate(180deg);
        }

        /* Logout modal */
        .logout-modal {
            display: none;
            position: fixed;
            z-index: 999;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.4);
            animation: fadeIn 0.3s ease;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        .logout-modal-content {
            background-color: #f9f9f9;
            margin: 15% auto;
            padding: 30px;
            border-radius: 12px;
            width: 90%;
            max-width: 400px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
            animation: slideDown 0.3s ease;
        }

        @keyframes slideDown {
            from {
                transform: translateY(-50px);
                opacity: 0;
            }

            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .logout-modal-header {
            font-size: 20px;
            font-weight: 700;
            color: #d32f2f;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .logout-modal-body {
            font-size: 16px;
            color: #333;
            margin-bottom: 25px;
            line-height: 1.5;
        }

        .logout-modal-footer {
            display: flex;
            gap: 12px;
            justify-content: flex-end;
        }

        .btn-logout-confirm,
        .btn-logout-cancel {
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-logout-confirm {
            background-color: #d32f2f;
            color: white;
        }

        .btn-logout-confirm:hover {
            background-color: #b71c1c;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(211, 47, 47, 0.3);
        }

        .btn-logout-cancel {
            background-color: #e0e0e0;
            color: #333;
        }

        .btn-logout-cancel:hover {
            background-color: #d0d0d0;
        }
    </style>
</head>

<body>
    <header>
        <div class="header-left">
            <div class="brand">
                <img src="../Pics/Logos/Lagro_High_School_logo.png" alt="logo">
                <div>
                    <h1>LAGRO HIGH SCHOOL</h1>
                    <div style="font-size:12px; color:rgba(255,255,255,0.9);">Security Management System</div>
                </div>
            </div>
            <button id="menuToggle" class="menu-toggle" aria-label="Toggle navigation"><i class="fas fa-bars"></i></button>
        </div>
        <div class="header-actions">
            <nav class="nav">
                <button class="nav-close" aria-label="Close menu">&times;</button>
                <div class="nav-menu">
                    <button class="dropbtn">Home <i class="fas fa-chevron-down dropdown-arrow"></i></button>
                    <div class="dropdown-content">
                        <a href="about-us.php"><i class="fa-solid fa-users-line"></i> About Us</a>
                        <?php if (in_array($user_role, ['TEACHER', 'SECURITY'])): ?>
                            <a href="attendance-log.php"><i class="fas fa-clipboard-check"></i> Attendance Log</a>
                        <?php endif; ?>
                    </div>
                </div>

                <?php if ($user_role !== 'SECURITY'): ?>
                    <div class="nav-menu">
                        <button class="dropbtn">Registration <i class="fas fa-chevron-down dropdown-arrow"></i></button>
                        <div class="dropdown-content">
                            <?php if ($user_role === 'STUDENT'): ?>
                                <a href="student_form.php"><i class="fas fa-user-graduate"></i> Student</a>
                            <?php elseif (in_array($user_role, ['TEACHER', 'OTHER_PERSONNEL'])): ?>
                                <a href="personnel_form.php"><i class="fas fa-user-tie"></i> School Employee</a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if ($user_role === 'SECURITY'): ?>
                    <div class="nav-menu">
                        <button class="dropbtn">QR Tools <i class="fas fa-chevron-down dropdown-arrow"></i></button>
                        <div class="dropdown-content">
                            <a href="scanner.php"><i class="fas fa-qrcode"></i> QR Scanner</a>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="nav-menu">
                    <button class="dropbtn"><i class="fa-solid fa-user"></i><?php echo $_SESSION['first_name'] ?? 'User'; ?> <i class="fas fa-chevron-down" id="dropbtn"></i></button>
                    <div class="dropdown-content">
                        <?php if (in_array($user_role, ['STUDENT', 'TEACHER', 'OTHER_PERSONNEL'])): ?>
                            <a href="profile.php"><i class="fa-solid fa-user"></i> User Information</a>
                        <?php endif; ?>
                        <a href="#" onclick="showLogoutModal(); return false;"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
                    </div>
                </div>
                <div class="nav-menu">
                    <button id="darkToggle" class="toggle" title="Toggle dark mode" aria-label="Toggle dark mode"><i id="darkIcon" class="fa-solid fa-moon"></i></button>
                </div>

            </nav>

        </div>
    </header>
    <div id="mobileNavOverlay" class="mobile-nav-overlay" aria-hidden="true"></div>
    <!-- Logout modal -->
    <div id="logoutModal" class="logout-modal">
        <div class="logout-modal-content">
            <div class="logout-modal-header">
                <i class="fas fa-exclamation-circle"></i>
                Confirm Logout
            </div>
            <div class="logout-modal-body">
                Are you sure you want to logout? You will be redirected to the login page.
            </div>
            <div class="logout-modal-footer">
                <button class="btn-logout-cancel" onclick="closeLogoutModal()">Cancel</button>
                <button class="btn-logout-confirm" onclick="confirmLogout()">Yes, Logout</button>
            </div>
        </div>
    </div>
    <form id="logoutForm" method="POST" style="display: none;">
        <input type="hidden" name="logout" value="1">
    </form>

    <script>
        (function() {
            const body = document.body;
            const toggle = document.getElementById('darkToggle');
            const icon = document.getElementById('darkIcon');
            // Load saved preference
            const saved = localStorage.getItem('lagro_dark_mode');
            if (saved === '1') {
                body.classList.add('dark');
                icon.className = 'fa-solid fa-sun';
            }

            toggle.addEventListener('click', function() {
                const isDark = body.classList.toggle('dark');
                icon.className = isDark ? 'fa-solid fa-sun' : 'fa-solid fa-moon';
                // Save preference
                localStorage.setItem('lagro_dark_mode', isDark ? '1' : '0');
            });
        })();

        // Simple animation for stats on load
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.stat').forEach((el, i) => {
                el.style.opacity = 0;
                el.style.transform = 'translateY(8px)';
                setTimeout(() => {
                    el.style.transition = 'all 300ms ease';
                    el.style.opacity = 1;
                    el.style.transform = 'translateY(0)';
                }, i * 90);
            });
        });

        // Logout modal
        function showLogoutModal() {
            document.getElementById('logoutModal').style.display = 'block';
        }

        function closeLogoutModal() {
            document.getElementById('logoutModal').style.display = 'none';
        }

        function confirmLogout() {
            closeLogoutModal();
            document.getElementById('logoutForm').submit();
        }

        window.onclick = function(event) {
            const modal = document.getElementById('logoutModal');
            if (event.target === modal) {
                closeLogoutModal();
            }
        }

        // Mobile menu handlers
        var mobileNav = document.querySelector('.nav');
        var mobileNavOverlay = document.getElementById('mobileNavOverlay');
        var menuToggle = document.getElementById('menuToggle');
        var navClose = document.querySelector('.nav-close');

        function openNav() {
            mobileNav.classList.add('show');
            mobileNavOverlay.classList.add('show');
        }

        function closeNav() {
            mobileNav.classList.remove('show');
            mobileNavOverlay.classList.remove('show');
        }

        menuToggle.addEventListener('click', function() {
            if (mobileNav.classList.contains('show')) {
                closeNav();
            } else {
                openNav();
            }
        });

        if (navClose) {
            navClose.addEventListener('click', function() {
                closeNav();
            });
        }

        mobileNavOverlay.addEventListener('click', function() {
            closeNav();
        });
    </script>
</body>

</html>