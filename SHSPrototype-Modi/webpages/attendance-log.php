<?php
session_start();
if (isset($_POST['logout'])) {
    session_unset();
    session_destroy();
    header("Location: ../login.php");
    exit();
}

include_once "../include/db_conn.php";
include "../include/role_access.php";

verifyPageAccess("attendance-log.php");

if (!isset($_SESSION['lrn']) && !isset($_SESSION['employee_id'])) {
    header("Location: ../login.php");
    exit();
}

$records_per_page = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $records_per_page;

// Get filter parameters
$date_filter = isset($_GET['date']) ? $_GET['date'] : '';
$section_filter = isset($_GET['section']) ? $_GET['section'] : '';
$sort_order = isset($_GET['sort']) ? $_GET['sort'] : 'DESC'; // DESC = last to first, ASC = first to last

// Validate sort order
if ($sort_order !== 'ASC' && $sort_order !== 'DESC') {
    $sort_order = 'DESC';
}

$where_clause = '';
$conditions = [];

if (!empty($date_filter)) {
    $conditions[] = "DATE(a.timestamp) = '$date_filter'";
}

if (!empty($section_filter)) {
    $conditions[] = "s.section = '$section_filter'";
}

if (!empty($conditions)) {
    $where_clause = " WHERE " . implode(" AND ", $conditions);
}

// Count total records
$count_sql = "SELECT COUNT(*) as total FROM attendance a LEFT JOIN student_info s ON a.student_lrn = s.lrn" . $where_clause;
$count_result = $conn->query($count_sql);
$total_records = $count_result->fetch_assoc()['total'];
$total_pages = ceil($total_records / $records_per_page);

// Get attendance records
$sql = "SELECT a.id, a.student_lrn, a.timestamp, 
        s.first_name, s.last_name, s.grade_level, s.section, s.profile_picture
        FROM attendance a
        LEFT JOIN student_info s ON a.student_lrn = s.lrn
        $where_clause
        ORDER BY a.timestamp $sort_order
        LIMIT $offset, $records_per_page";

$result = $conn->query($sql);

// Get distinct dates for filter
$dates_sql = "SELECT DISTINCT DATE(timestamp) as date FROM attendance ORDER BY date DESC";
$dates_result = $conn->query($dates_sql);
$dates = [];
while ($date_row = $dates_result->fetch_assoc()) {
    $dates[] = $date_row['date'];
}

// Get distinct sections for filter
$sections_sql = "SELECT DISTINCT section FROM student_info WHERE section IS NOT NULL AND section != '' ORDER BY section ASC";
$sections_result = $conn->query($sections_sql);
$sections = [];
while ($section_row = $sections_result->fetch_assoc()) {
    $sections[] = $section_row['section'];
}


$today = date('Y-m-d');
$stats_sql = "SELECT 
    (SELECT COUNT(*) FROM attendance WHERE DATE(timestamp) = '$today') as today_count,
    (SELECT COUNT(*) FROM attendance WHERE DATE(timestamp) = DATE_SUB('$today', INTERVAL 1 DAY)) as yesterday_count,
    (SELECT COUNT(*) FROM attendance WHERE DATE(timestamp) BETWEEN DATE_SUB('$today', INTERVAL 7 DAY) AND '$today') as week_count,
    (SELECT COUNT(*) FROM attendance) as total_count";
$stats_result = $conn->query($stats_sql);
$stats = $stats_result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance Log - Lagro High School</title>
    <link rel="icon" type="image/x-icon" href="../pics/logos/Lagro_High_School_logo.png">

    <style>
        * {
            box-sizing: border-box;
        }

        :root {
            --bg: #f4f6f8;
            --card: #ffffff;
            --muted: #6b7280;
            --green-1: #095d2fff;
            --green-2: #1fa25a;
            --radius: 12px;
            --shadow-sm: 0 2px 4px rgba(0, 0, 0, 0.05);
            --shadow-md: 0 4px 8px rgba(0, 0, 0, 0.1);
            --shadow-lg: 0 8px 16px rgba(0, 0, 0, 0.1);
            --border: 1px solid rgba(15, 23, 42, 0.06);
            --text: #0f172a;
            --primary-color: #229221;
            --primary-dark: #105a0f;
            --secondary-color: #24a85b;
            --accent-color: #f39c12;
            --danger-color: #e74c3c;
            --grey-100: #f8f9fa;
            --transition-fast: 150ms ease;
            --transition-normal: 300ms ease;
            --font-family: 'Montserrat', sans-serif;
        }

        html, body {
            margin: 0;
            padding: 0;
        }

        /* ========================
           MAIN LAYOUT
           ======================== */
        .container {
            max-width: 1200px;
            margin: 16px auto;
            padding: 12px 16px 40px;
        }

        /* Page title block */
        .page-title {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            gap: 8px;
            margin-bottom: 16px;
        }

        .page-title h2 {
            font-size: clamp(18px, 5vw, 24px);
            margin: 0;
        }

        .page-title p {
            margin: 0;
            color: var(--muted);
            font-size: clamp(12px, 3vw, 14px);
        }

        /* ========================
           STATS CARDS (4) — modern look
           ======================== */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 12px;
            margin-bottom: 16px;
        }

        .stat {
            background: var(--card);
            border-radius: 10px;
            padding: 14px;
            display: flex;
            flex-direction: column;
            gap: 8px;
            box-shadow: var(--shadow-sm);
            border: var(--border);
            transition: all var(--transition-fast);
            z-index: -1;
        }

        .stat:hover {
            box-shadow: var(--shadow-md);
            transform: translateY(-2px);
        }

        .stat .icon {
            width: 48px;
            height: 48px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 18px;
        }

        .stat .info h3 {
            margin: 0;
            font-size: clamp(16px, 4vw, 22px);
            font-weight: 700;
        }

        .stat .info p {
            margin: 0;
            color: var(--muted);
            font-size: clamp(11px, 2.5vw, 12px);
            font-weight: 500;
        }

        .icon.today {
            background: linear-gradient(180deg, #2ea14a, #1e8f3f);
        }

        .icon.yesterday {
            background: linear-gradient(180deg, #28b37c, #1fa25a);
        }

        .icon.week {
            background: linear-gradient(180deg, #f6b64c, #f39c12);
        }

        .icon.total {
            background: linear-gradient(180deg, #1b6b3a, #154f2b);
        }

        /* ========================
           FILTER + TABLE
           ======================== */
        .panel {
            background: var(--card);
            border-radius: 10px;
            padding: 14px;
            box-shadow: var(--shadow-md);
            border: var(--border);
        }

        .filter-row {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 14px;
            padding-bottom: 12px;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        }

        .filter-left {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 10px;
            flex: 1;
            min-width: 0;
        }

        .filter-label {
            color: var(--muted);
            font-weight: 600;
            font-size: 13px;
            white-space: nowrap;
        }

        select#date,
        select#section,
        select#sort {
            padding: 7px 10px;
            border-radius: 6px;
            border: 1px solid rgba(0, 0, 0, 0.06);
            background: transparent;
            color: var(--muted);
            font-size: 13px;
            cursor: pointer;
            transition: all var(--transition-fast);
            min-width: 120px;
        }

        select#date:hover,
        select#section:hover,
        select#sort:hover {
            border-color: rgba(0, 0, 0, 0.12);
            background: rgba(0, 0, 0, 0.02);
        }

        select#date:focus,
        select#section:focus,
        select#sort:focus {
            outline: none;
            border-color: var(--primary-color);
            background: rgba(34, 146, 33, 0.03);
        }

        .btn-scan {
            background: linear-gradient(90deg, #1fa25a, #11924a);
            color: white;
            padding: 8px 14px;
            border-radius: 6px;
            text-decoration: none;
            font-size: 13px;
            font-weight: 600;
            transition: all var(--transition-fast);
            white-space: nowrap;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .btn-scan:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }

        /* Table wrapper for responsive scrolling */
        .table-wrapper {
            overflow-x: auto;
            border-radius: 8px;
            -webkit-overflow-scrolling: touch;
        }

        /* Table styling */
        .attendance-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
        }

        .attendance-table thead th {
            text-align: left;
            padding: 12px 10px;
            color: var(--muted);
            font-weight: 600;
            border-bottom: 2px solid rgba(0, 0, 0, 0.08);
            background: rgba(0, 0, 0, 0.01);
            white-space: nowrap;
        }

        .attendance-table tbody tr {
            border-bottom: 1px solid rgba(0, 0, 0, 0.04);
            transition: background-color var(--transition-fast);
        }

        .attendance-table tbody tr:hover {
            background-color: rgba(0, 0, 0, 0.01);
        }

        .attendance-table td {
            padding: 12px 10px;
            vertical-align: middle;
        }

        /* Student info */
        .student-info {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .avatar {
            width: 36px;
            height: 36px;
            border-radius: 999px;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f1f5f9;
            border: 1px solid rgba(15, 23, 42, 0.03);
            flex-shrink: 0;
        }

        .avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .student-name {
            font-weight: 600;
            font-size: 13px;
        }

        .student-meta {
            font-size: 11px;
            color: var(--muted);
        }

        .lrn-badge {
            background: none;
            padding: 4px 0;
            border-radius: 4px;
            font-family: monospace;
            font-size: 12px;
            color: var(--muted);
            border: 1px solid rgba(0, 0, 0, 0.03);
        }

        /* Pagination */
        .pagination {
            display: flex;
            gap: 6px;
            justify-content: center;
            margin-top: 14px;
            flex-wrap: wrap;
        }

        .pagination a,
        .pagination span {
            padding: 7px 9px;
            background: var(--card);
            border-radius: 6px;
            box-shadow: var(--shadow-sm);
            border: var(--border);
            text-decoration: none;
            color: var(--text);
            font-size: 12px;
            transition: all var(--transition-fast);
            cursor: pointer;
        }

        .pagination a:hover {
            box-shadow: var(--shadow-md);
            transform: translateY(-1px);
        }

        .pagination .active {
            background: linear-gradient(90deg, #1fa25a, #11924a);
            color: white;
        }

        /* Empty state */
        .empty-state {
            text-align: center;
            padding: 24px;
            color: var(--muted);
            font-size: 14px;
        }

        /* Clear filters button */
        .btn.muted {
            color: var(--muted);
            padding: 7px 12px;
            border: 1px solid rgba(0, 0, 0, 0.1);
            border-radius: 6px;
            transition: all var(--transition-fast);
            display: inline-block;
            text-decoration: none;
            font-size: 12px;
        }

        .btn.muted:hover {
            background: rgba(0, 0, 0, 0.05);
        }

        /* ========================
           RESPONSIVE DESIGN
           ======================== */

        /* Tablet: 768px */
        @media (max-width: 768px) {
            .container {
                margin: 12px auto;
                padding: 10px 12px 32px;
            }

            .page-title {
                gap: 6px;
            }

            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 10px;
                margin-bottom: 14px;
            }

            .stat {
                padding: 12px;
                gap: 6px;
            }

            .stat .icon {
                width: 44px;
                height: 44px;
                font-size: 16px;
            }

            .panel {
                padding: 12px;
            }

            .filter-row {
                gap: 10px;
                padding-bottom: 10px;
                margin-bottom: 10px;
            }

            .filter-left {
                width: 100%;
                gap: 8px;
            }

            select#date,
            select#section,
            select#sort {
                min-width: 100px;
                padding: 6px 8px;
                font-size: 12px;
            }

            .btn-scan {
                padding: 7px 12px;
                font-size: 12px;
            }

            .attendance-table thead th {
                padding: 10px 8px;
                font-size: 12px;
            }

            .attendance-table td {
                padding: 10px 8px;
                font-size: 12px;
            }

            .avatar {
                width: 32px;
                height: 32px;
            }

            .student-info {
                gap: 8px;
            }
        }

        /* Mobile: 480px */
        @media (max-width: 480px) {
            .container {
                margin: 8px auto;
                padding: 8px 10px 24px;
            }

            .page-title h2 {
                font-size: 18px;
            }

            .page-title p,
            .page-title div:last-child {
                font-size: 11px;
            }

            .stats-grid {
                grid-template-columns: 1fr;
                gap: 8px;
                margin-bottom: 12px;
            }

            .stat {
                padding: 10px;
                flex-direction: row;
                gap: 10px;
            }

            .stat .icon {
                width: 40px;
                height: 40px;
                font-size: 14px;
                flex-shrink: 0;
            }

            .stat .info h3 {
                font-size: 16px;
            }

            .stat .info p {
                font-size: 10px;
            }

            .panel {
                padding: 10px;
                border-radius: 8px;
            }

            .filter-row {
                flex-direction: column;
                gap: 8px;
                padding-bottom: 8px;
                margin-bottom: 8px;
            }

            .filter-left {
                width: 100%;
                flex-direction: column;
            }

            .filter-left > div {
                display: flex !important;
                flex-direction: column;
                width: 100%;
            }

            .filter-label {
                font-size: 12px;
            }

            select#date,
            select#section,
            select#sort {
                width: 100%;
                min-width: unset;
                padding: 10px 8px;
                font-size: 14px;
            }

            .btn-scan {
                width: 100%;
                justify-content: center;
                padding: 10px;
                font-size: 13px;
            }

            .table-wrapper {
                border-radius: 6px;
                margin: -10px -10px 0 -10px;
                width: calc(100% + 20px);
            }

            .attendance-table {
                font-size: 11px;
            }

            .attendance-table thead th {
                padding: 8px 6px;
                font-size: 11px;
            }

            .attendance-table td {
                padding: 8px 6px;
                font-size: 11px;
            }

            .avatar {
                width: 30px;
                height: 30px;
                font-size: 12px;
            }

            .student-info {
                gap: 6px;
            }

            .student-name {
                font-size: 12px;
            }

            .student-meta {
                font-size: 10px;
            }

            .lrn-badge {
                font-size: 11px;
                padding: 3px 0;
            }

            .pagination {
                gap: 4px;
                margin-top: 10px;
            }

            .pagination a,
            .pagination span {
                padding: 6px 8px;
                font-size: 11px;
            }

            .btn.muted {
                font-size: 11px;
                padding: 6px 10px;
            }

            .empty-state {
                padding: 16px;
                font-size: 12px;
            }
        }

        /* Small mobile: 360px */
        @media (max-width: 360px) {
            .container {
                padding: 6px 8px 16px;
            }

            .page-title h2 {
                font-size: 16px;
            }

            .stats-grid {
                gap: 6px;
            }

            .stat {
                padding: 8px;
            }

            .panel {
                padding: 8px;
            }

            select#date,
            select#section,
            select#sort {
                font-size: 13px;
            }
        }

        /* Utility classes */
        .muted {
            color: var(--muted);
        }

        a.btn {
            text-decoration: none;
        }
    </style>

    <script>
        // Mobile optimization - Prevent layout shifts
        document.addEventListener('DOMContentLoaded', function() {
            // Auto-submit filters with slight delay on mobile
            const selectElements = document.querySelectorAll('select#date, select#section, select#sort');
            selectElements.forEach(select => {
                select.addEventListener('change', function() {
                    setTimeout(() => {
                        document.getElementById('filterForm').submit();
                    }, 100);
                });
            });

            // Smooth scroll to table on filter change
            if (window.innerWidth <= 768) {
                selectElements.forEach(select => {
                    select.addEventListener('change', function() {
                        setTimeout(() => {
                            const tableWrapper = document.querySelector('.table-wrapper');
                            if (tableWrapper) {
                                tableWrapper.scrollIntoView({ behavior: 'smooth', block: 'start' });
                            }
                        }, 300);
                    });
                });
            }
        });
    </script>
</head>

<body>
    <?php
    include "../include/header.php";
    ?>
    <!-- ====================
         MAIN DASHBOARD LAYOUT
         ==================== -->
    <main class="container">

        <div class="page-title">
            <div>
                <h2>Attendance Log</h2>
                <p>View and manage attendance records for students, teachers, and personnel.</p>
            </div>
            <div class="muted">Showing latest attendance records</div>
        </div>

        <!-- LEFT COLUMN: stats + table -->
        <section>
            <!-- Stats cards (kept PHP values intact) -->
            <div class="stats-grid">
                <div class="stat">
                    <div class="icon today"><i class="fas fa-calendar-day"></i></div>
                    <div class="info">
                        <h3><?php echo $stats['today_count']; ?></h3>
                        <p>Today's Attendance</p>
                    </div>
                </div>

                <div class="stat">
                    <div class="icon yesterday"><i class="fas fa-calendar-minus"></i></div>
                    <div class="info">
                        <h3><?php echo $stats['yesterday_count']; ?></h3>
                        <p>Yesterday's Attendance</p>
                    </div>
                </div>

                <div class="stat">
                    <div class="icon week"><i class="fas fa-calendar-week"></i></div>
                    <div class="info">
                        <h3><?php echo $stats['week_count']; ?></h3>
                        <p>This Week's Attendance</p>
                    </div>
                </div>

                <div class="stat">
                    <div class="icon total"><i class="fas fa-calendar-alt"></i></div>
                    <div class="info">
                        <h3><?php echo $stats['total_count']; ?></h3>
                        <p>Total Attendance Records</p>
                    </div>
                </div>
            </div>

            <!-- FILTER + TABLE PANEL -->
            <div class="panel">
                <div class="filter-row">
                    <div class="filter-left">
                        <form method="GET" action="" id="filterForm" style="display: flex; align-items: center; gap: 12px;">
                            <div style="display: flex; align-items: center; gap: 8px;">
                                <label class="filter-label">Date:</label>
                                <select id="date" name="date" onchange="document.getElementById('filterForm').submit();">
                                    <option value="">All Dates</option>
                                    <?php foreach ($dates as $date): ?>
                                        <option value="<?php echo $date; ?>" <?php echo ($date_filter == $date) ? 'selected' : ''; ?>>
                                            <?php echo date('F j, Y', strtotime($date)); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div style="display: flex; align-items: center; gap: 8px;">
                                <label class="filter-label">Section:</label>
                                <select id="section" name="section" onchange="document.getElementById('filterForm').submit();">
                                    <option value="">All Sections</option>
                                    <?php foreach ($sections as $section): ?>
                                        <option value="<?php echo $section; ?>" <?php echo ($section_filter == $section) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($section); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div style="display: flex; align-items: center; gap: 8px;">
                                <label class="filter-label">Sort:</label>
                                <select id="sort" name="sort" onchange="document.getElementById('filterForm').submit();">
                                    <option value="DESC" <?php echo ($sort_order == 'DESC') ? 'selected' : ''; ?>>Last to First</option>
                                    <option value="ASC" <?php echo ($sort_order == 'ASC') ? 'selected' : ''; ?>>First to Last</option>
                                </select>
                            </div>
                        </form>
                        <?php if (!empty($date_filter) || !empty($section_filter)): ?>
                            <a href="attendance-log.php" class="btn muted" style="margin-left:8px;">Clear Filters</a>
                        <?php endif; ?>
                    </div>
                    <div>
                        <!-- kept simple flat style for scan button -->
                        <a class="btn-scan" href="scanner.php"><i class="fa-solid fa-qrcode"></i> Scan Attendance</a>
                    </div>
                </div>

                <!-- Attendance table (PHP loop preserved) -->
                <?php if ($result && $result->num_rows > 0): ?>
                    <div class="table-wrapper">
                        <table class="attendance-table">
                            <thead>
                                <tr>
                                    <th style="width:70px;">ID</th>
                                    <th>Student</th>
                                    <th style="width:180px;">LRN</th>
                                    <th style="width:260px;">Date & Time</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = $result->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo $row['id']; ?></td>
                                        <td>
                                            <div class="student-info">
                                                <div class="avatar">
                                                    <?php if (!empty($row['profile_picture']) && file_exists($row['profile_picture'])): ?>
                                                        <img src="<?php echo htmlspecialchars($row['profile_picture']); ?>" alt="Student Photo">
                                                    <?php else: ?>
                                                        <i class="fas fa-user" style="color:var(--muted);"></i>
                                                    <?php endif; ?>
                                                </div>
                                                <div>
                                                    <div class="student-name">
                                                        <?php
                                                        if (!empty($row['first_name']) && !empty($row['last_name'])) {
                                                            echo htmlspecialchars($row['first_name'] . ' ' . $row['last_name']);
                                                        } else {
                                                            echo '<span style="color: var(--muted);">Unknown Student</span>';
                                                        }
                                                        ?>
                                                    </div>
                                                    <?php if (!empty($row['grade_level']) && !empty($row['section'])): ?>
                                                        <div class="student-meta">Grade <?php echo htmlspecialchars($row['grade_level']); ?> - <?php echo htmlspecialchars($row['section']); ?></div>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="lrn-badge"><?php echo htmlspecialchars($row['student_lrn']); ?></div>
                                        </td>
                                        <td class="muted"><?php echo date('F j, Y, g:i a', strtotime($row['timestamp'])); ?></td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination with preserved filters -->
                    <?php 
                    // Build query string for pagination links
                    $query_params = '';
                    if (!empty($date_filter)) {
                        $query_params .= '&date=' . urlencode($date_filter);
                    }
                    if (!empty($section_filter)) {
                        $query_params .= '&section=' . urlencode($section_filter);
                    }
                    if ($sort_order !== 'DESC') {
                        $query_params .= '&sort=' . urlencode($sort_order);
                    }
                    ?>
                    <?php if ($total_pages > 1): ?>
                        <div class="pagination">
                            <?php if ($page > 1): ?>
                                <a href="?page=1<?php echo $query_params; ?>">
                                    <i class="fas fa-angle-double-left"></i>
                                </a>
                                <a href="?page=<?php echo $page - 1; ?><?php echo $query_params; ?>">
                                    <i class="fas fa-angle-left"></i>
                                </a>
                            <?php else: ?>
                                <span class="disabled"><i class="fas fa-angle-double-left"></i></span>
                                <span class="disabled"><i class="fas fa-angle-left"></i></span>
                            <?php endif; ?>

                            <?php
                            $range = 2;
                            $start_page = max(1, $page - $range);
                            $end_page = min($total_pages, $page + $range);

                            if ($start_page > 1) {
                                echo '<a href="?page=1' . $query_params . '">1</a>';
                                if ($start_page > 2) {
                                    echo '<span class="disabled">...</span>';
                                }
                            }

                            for ($i = $start_page; $i <= $end_page; $i++) {
                                if ($i == $page) {
                                    echo '<span class="active">' . $i . '</span>';
                                } else {
                                    echo '<a href="?page=' . $i . $query_params . '">' . $i . '</a>';
                                }
                            }

                            if ($end_page < $total_pages) {
                                if ($end_page < $total_pages - 1) {
                                    echo '<span class="disabled">...</span>';
                                }
                                echo '<a href="?page=' . $total_pages . $query_params . '">' . $total_pages . '</a>';
                            }
                            ?>

                            <?php if ($page < $total_pages): ?>
                                <a href="?page=<?php echo $page + 1; ?><?php echo $query_params; ?>">
                                    <i class="fas fa-angle-right"></i>
                                </a>
                                <a href="?page=<?php echo $total_pages; ?><?php echo $query_params; ?>">
                                    <i class="fas fa-angle-double-right"></i>
                                </a>
                            <?php else: ?>
                                <span class="disabled"><i class="fas fa-angle-right"></i></span>
                                <span class="disabled"><i class="fas fa-angle-double-right"></i></span>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                <?php else: ?>
                    <div class="empty-state">
                        <i class="fas fa-clipboard" style="font-size:36px;"></i>
                        <h3>No Attendance Records Found</h3>
                        <p>
                            <?php if (!empty($date_filter)): ?>
                                No attendance records were found for the selected date. Try selecting a different date or clearing the filter.
                            <?php else: ?>
                                There are no attendance records in the system yet. Use the QR scanner to record attendance.
                            <?php endif; ?>
                        </p>
                        <div style="margin-top: 1.5rem;">
                            <?php if (!empty($date_filter)): ?>
                                <a href="attendance-log.php" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Clear Filter
                                </a>
                            <?php else: ?>
                                <a href="scanner.php" class="btn-scan">
                                    <i class="fas fa-qrcode"></i> Go to Scanner
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </section>
    </main>
    
    <?php include '../include/footer.php'; ?>
</body>
    <script>

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
    </script>
</html>
