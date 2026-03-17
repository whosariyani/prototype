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

verifyPageAccess("student_form.php");

if (!isset($_SESSION['lrn']) && !isset($_SESSION['employee_id'])) {
    header("Location: ../login.php");
    exit();
}

$lrn = $_SESSION['lrn'] ?? null;
$student = [];
$already_registered = false;

if ($lrn) {
  $stmt = $conn->prepare("SELECT first_name, middle_name, last_name, lrn, is_registered FROM student_info WHERE lrn = ?");
  $stmt->bind_param("s", $lrn);
  $stmt->execute();
  $student = $stmt->get_result()->fetch_assoc() ?? [];
  $stmt->close();
  
  // Check if student has already registered and generated QR code
  if (!empty($student['is_registered']) && $student['is_registered'] == 1) {
    $already_registered = true;
  }
}

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Initialize variables with safe defaults
$fname = '';
$mname = '';
$lname = '';
$lrn = '';
$glevel = 0;
$section = '';
$bday = '';
$age = 0;
$gender = '';
$stuAddress = '';
$contact_number = '';
$email = '';
$guardian = '';
$guardian_contact = '';
$relationship = '';

$errorMsg = "";
$successMsg = "";

// Only populate POST variables when form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    $fname = isset($_POST["firstName"]) ? trim($_POST["firstName"] ?? '') : '';
    $mname = isset($_POST["middleName"]) ? trim($_POST["middleName"] ?? '') : '';
    $lname = isset($_POST["lastName"]) ? trim($_POST["lastName"] ?? '') : '';
    $lrn = isset($_POST["lrn"]) ? trim($_POST["lrn"] ?? '') : '';
    $glevel = isset($_POST["gradeLevel"]) ? (int)$_POST["gradeLevel"] : 0;
    $section = isset($_POST["section"]) ? trim($_POST["section"] ?? '') : '';
    $bday = isset($_POST["birthdate"]) ? trim($_POST["birthdate"] ?? '') : '';
    $age = isset($_POST["age"]) ? (int)$_POST["age"] : 0;
    $gender = isset($_POST["gender"]) ? trim($_POST["gender"] ?? '') : '';
    $stuAddress = isset($_POST["address"]) ? trim($_POST["address"] ?? '') : '';
    $contact_number = isset($_POST["contactNumber"]) ? trim($_POST["contactNumber"] ?? '') : '';
    $email = isset($_POST["email"]) ? trim($_POST["email"] ?? '') : '';
    $guardian = isset($_POST["guardianName"]) ? trim($_POST["guardianName"] ?? '') : '';
    $guardian_contact = isset($_POST["guardianContact"]) ? trim($_POST["guardianContact"] ?? '') : '';
    $relationship = isset($_POST["relationship"]) ? trim($_POST["relationship"] ?? '') : '';
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="theme-color" content="#229221">
    <title>Student Registration</title>
    <link rel="icon" type="image/x-icon" href="../pics/logos/Lagro_High_School_logo.png">

    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Reset & Base Styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --bg: #f4f6f8;
            --card: #ffffff;
            --muted: #6b7280;
            --green-1: #095d2fff;
            /* gradient start */
            --green-2: #1fa25a;
            /* gradient end */
            --radius: 12px;
            --shadow-sm: 0 6px 18px rgba(19, 42, 34, 0.06);
            --shadow-md: 0 12px 30px rgba(19, 42, 34, 0.08);
            --border: 1px solid rgba(15, 23, 42, 0.06);
            --text: #0f172a;
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


        /* Simple flat scan button */
        .btn-scan {
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

        /* Page Title */
        .page-title {
            text-align: center;
            padding: clamp(1rem, 4vw, 2rem) 1rem;
            border-bottom: 1px solid #e0e0e0;
            background-color: var(--bg);
            width: 100%;
            box-sizing: border-box;
        }

        .page-title h2 {
            color: var(--dark-color);
            font-size: clamp(1.4rem, 5vw, 1.8rem);
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        .page-title p {
            color: var(--text);
            max-width: 600px;
            margin: 0 auto;
            font-size: clamp(0.9rem, 3vw, 1rem);
            padding: 0 1rem;
        }

        /* Alert Messages */
        .alert {
            padding: 15px;
            margin: 20px auto;
            max-width: 1200px;
            border-radius: var(--border-radius);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .alert-danger {
            background-color: #fde8e8;
            color: var(--danger-color);
            border-left: 4px solid var(--danger-color);
        }

        .alert-success {
            background-color: #e6f7ef;
            color: var(--secondary-color);
            border-left: 4px solid var(--secondary-color);
        }

        /* Main Content and Form */
        main {
            padding: 1rem;
            max-width: 1200px;
            margin: 0 auto;
            width: 100%;
            box-sizing: border-box;
        }

        .form-container {
            background-color: var(--bg);
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            overflow: hidden;
            animation: fadeIn 0.5s ease-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .form-section {
            border-bottom: 1px solid #eee;
            transition: var(--transition);
            background-color: var(--bg);
        }

        .form-section:last-child {
            border-bottom: none;
        }

        .section-header {
            padding: 1rem 1.5rem;
            background-color: var(--bg);
            cursor: pointer;
            transition: var(--transition);
        }

        .section-header:hover {
            background-color: var(--bg);
        }

        .section-header h3 {
            font-size: 1.2rem;
            color: var(--dark-color);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .section-header h3 i {
            color: var(--primary-color);
        }

        .section-content {
            padding: 1.5rem;
            display: grid;
            gap: 1.5rem;
        }

        /* Form Elements */
        .form-group {
            margin-bottom: 1rem;
            position: relative;
        }

        .input-group {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1rem;
        }

        label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: var(--dark-color);
        }

        .required {
            color: var(--danger-color);
        }

        input[type="text"],
        input[type="email"],
        input[type="tel"],
        input[type="date"],
        input[type="number"],
        select,
        textarea {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #ddd;
            border-radius: var(--border-radius);
            font-family: 'Montserrat', sans-serif;
            font-size: 1rem;
            transition: var(--transition);
            background-color: var(--bg);
            color: var(--text);
        }

        input:focus,
        select:focus,
        textarea:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.2);
        }

        /* Radio Buttons */
        .radio-group {
            display: flex;
            gap: 1.5rem;
            margin-top: 0.5rem;
        }

        .radio-label {
            display: flex;
            align-items: center;
            cursor: pointer;
            user-select: none;
        }

        .radio-label input[type="radio"] {
            display: none;
        }

        .radio-custom {
            display: inline-block;
            width: 18px;
            height: 18px;
            border: 2px solid #ddd;
            border-radius: 50%;
            margin-right: 8px;
            position: relative;
            transition: var(--transition);
        }

        .radio-custom::after {
            content: '';
            display: block;
            width: 10px;
            height: 10px;
            background-color: var(--primary-color);
            border-radius: 50%;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) scale(0);
            transition: var(--transition);
        }

        .radio-label input[type="radio"]:checked+.radio-custom {
            border-color: var(--primary-color);
        }

        .radio-label input[type="radio"]:checked+.radio-custom::after {
            transform: translate(-50%, -50%) scale(1);
        }

        /* File Upload */
        .image-upload-container {
            display: flex;
            align-items: center;
            gap: 1.5rem;
            margin-bottom: 1.5rem;
            flex-wrap: wrap;
        }

        .upload-controls {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .image-preview-container {
            width: clamp(120px, 25vw, 150px);
            height: clamp(120px, 25vw, 150px);
            border-radius: 50%;
            overflow: hidden;
            background-color: #f5f5f5;
            display: flex;
            justify-content: center;
            align-items: center;
            border: 3px solid #eee;
            box-shadow: var(--shadow-sm);
            flex-shrink: 0;
        }

        #image-preview {
            width: 100%;
            height: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            color: #ccc;
            font-size: 4rem;
            overflow: hidden;
        }

        #image-preview img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            animation: fadeIn 0.3s ease-out;
        }

        input[type="file"] {
            display: none;
        }

        .custom-file-upload {
            display: inline-block;
            padding: 10px 16px;
            background-color: var(--green-1);
            color: white;
            border-radius: var(--border-radius);
            cursor: pointer;
            transition: var(--transition);
            font-weight: 500;
            text-align: center;
        }

        .custom-file-upload:hover {
            background-color: var(--secondary-dark);
        }

        .hint {
            font-size: 0.8rem;
            color: var(--text);
            margin-top: 0.5rem;
        }

        /* QR Code Styling */
        .qr-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 1rem;
            text-align: center;
        }

        #qr-box {
            width: 200px;
            height: 200px;
            border-radius: var(--border-radius);
            border: 2px dashed #ddd;
            display: flex;
            justify-content: center;
            align-items: center;
            position: relative;
            overflow: hidden;
            margin-bottom: 1rem;
            transition: var(--transition);
        }

        #qr-image {
            width: 100%;
            height: 100%;
            object-fit: contain;
            padding: 10px;
            opacity: 0;
            transition: var(--transition);
            position: absolute;
            top: 0;
            left: 0;
        }

        .qr-placeholder {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: #ccc;
            transition: var(--transition);
        }

        .qr-placeholder i {
            font-size: 3rem;
            margin-bottom: 0.5rem;
        }

        .qr-placeholder p {
            font-size: 0.8rem;
            max-width: 150px;
        }

        .show-qr #qr-image {
            opacity: 1;
        }

        .show-qr .qr-placeholder {
            opacity: 0;
        }

        .show-qr {
            border-color: var(--primary-color) !important;
        }

        .qr-info {
            color: #888;
            font-size: 0.9rem;
            max-width: 300px;
        }

        /* Form Actions */
        .form-actions {
            display: flex;
            justify-content: flex-end;
            gap: 1rem;
            padding: clamp(1rem, 3vw, 1.5rem);
            background-color: var(--bg);
            color: var(--text);
            border-top: 1px solid var(--muted);
            flex-wrap: wrap;
        }

        .btn {
            padding: clamp(8px, 2vw, 10px) clamp(16px, 3vw, 20px);
            border: none;
            border-radius: var(--border-radius);
            font-family: 'Montserrat', sans-serif;
            font-weight: 500;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: var(--transition);
            font-size: clamp(0.85rem, 2vw, 0.95rem);
            white-space: nowrap;
            min-width: 120px;
        }

        .btn-primary {
            background-color: var(--green-1);
            color: white;
        }

        .btn-primary:hover {
            background-color: var(--primary-dark);
        }

        .btn-secondary {
            background-color: var(--card);
            color: var(--text);
        }

        .btn-secondary:hover {
            background-color: var(--card);
            color: var(--muted);
        }

        /* Error Messages */
        .error-message {
            color: var(--danger-color);
            font-size: 0.85rem;
            margin-top: 0.3rem;
            display: none;
            background-color: var(--bg);
        }

        .form-group.error input,
        .form-group.error select,
        .form-group.error textarea {
            border-color: var(--danger-color);
        }

        .form-group.error .error-message {
            display: block;
            animation: fadeIn 0.3s ease-out;
        }

        /* Success Modal */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            justify-content: center;
            align-items: center;
        }

        .modal-content {
            background-color: white;
            border-radius: var(--border-radius);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
            padding: 2rem;
            text-align: center;
            width: 90%;
            max-width: 500px;
            transform: translateY(-50px);
            opacity: 0;
            transition: all 0.3s ease-out;
            position: relative;
        }

        .modal.show {
            display: flex;
        }

        .modal.show .modal-content {
            transform: translateY(0);
            opacity: 1;
        }

        .close-modal {
            position: absolute;
            top: 15px;
            right: 15px;
            font-size: 1.5rem;
            cursor: pointer;
            color: #888;
            transition: var(--transition);
        }

        .close-modal:hover {
            color: var(--danger-color);
        }

        .success-icon {
            font-size: 4rem;
            color: var(--secondary-color);
            margin-bottom: 1rem;
            animation: scaleIn 0.5s ease-out;
        }

        @keyframes scaleIn {
            from {
                transform: scale(0);
            }

            to {
                transform: scale(1);
            }
        }

        .modal h3 {
            color: var(--dark-color);
            margin-bottom: 0.5rem;
        }

        .modal p {
            color: #666;
            margin-bottom: 1.5rem;
        }

        .qr-download {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-top: 1rem;
        }

        #modal-qr-image {
            width: 150px;
            height: 150px;
            margin-bottom: 1rem;
        }

        /* Footer */
        footer {
            background: linear-gradient(90deg, var(--green-1), var(--green-2));
            color: #fff;
            padding: 18px 28px;
            gap: 18px;
            box-shadow: var(--shadow-sm);
            position: sticky;
            top: 0;
            z-index: 40;
            text-align: center;
        }

        /* Responsive Design */
        /* Tablet: 768px and below */
        @media (max-width: 768px) {
            main {
                padding: 0.75rem;
            }

            .page-title {
                padding: 1rem 0.75rem;
            }

            .input-group {
                grid-template-columns: 1fr;
                gap: 1rem;
            }

            .image-upload-container {
                flex-direction: column;
                align-items: center;
                gap: 1rem;
            }

            .radio-group {
                flex-direction: column;
                gap: 0.75rem;
            }

            .form-actions {
                flex-direction: column;
            }

            .btn {
                width: 100%;
                min-width: unset;
            }

            .section-content {
                padding: 1rem;
            }

            .section-header {
                padding: 0.75rem 1rem;
            }
        }

        /* Mobile: 480px and below */
        @media (max-width: 480px) {
            main {
                padding: 0.5rem;
            }

            .page-title {
                padding: 0.75rem 0.5rem;
            }

            .page-title h2 {
                font-size: clamp(1.2rem, 4vw, 1.5rem);
                gap: 6px;
            }

            .page-title p {
                font-size: 0.85rem;
            }

            .form-container {
                border-radius: 8px;
            }

            .section-header h3 {
                font-size: clamp(1rem, 4vw, 1.2rem);
            }

            .section-content {
                padding: 0.75rem;
                gap: 1rem;
            }

            .section-header {
                padding: 0.5rem 0.75rem;
            }

            label {
                font-size: 0.9rem;
            }

            input[type="text"],
            input[type="email"],
            input[type="tel"],
            input[type="date"],
            input[type="number"],
            select,
            textarea {
                padding: 8px 10px;
                font-size: 1rem;
            }

            .image-preview-container {
                width: 100px;
                height: 100px;
            }

            #image-preview {
                font-size: 3rem;
            }

            .custom-file-upload {
                padding: 8px 12px;
                font-size: 0.9rem;
            }

            .hint {
                font-size: 0.75rem;
            }

            .form-actions {
                padding: 0.75rem;
                gap: 0.5rem;
            }

            .btn {
                padding: 8px 12px;
                font-size: 0.85rem;
            }

            .alert {
                padding: 12px;
                margin: 1rem 0.5rem;
                font-size: 0.9rem;
            }

            .radio-group {
                gap: 1rem;
            }
        }

        /* Extra small: 360px and below */
        @media (max-width: 360px) {
            .page-title h2 {
                font-size: 1.1rem;
            }

            .section-header h3 {
                font-size: 0.95rem;
            }

            .image-preview-container {
                width: 90px;
                height: 90px;
            }

            #image-preview {
                font-size: 2.5rem;
            }

            input[type="text"],
            input[type="email"],
            input[type="tel"],
            input[type="date"],
            input[type="number"],
            select,
            textarea {
                padding: 6px 8px;
                font-size: 16px;
            }
        }

        /* Animations */
        @keyframes pulse {
            0% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.05);
            }

            100% {
                transform: scale(1);
            }
        }

        .pulse {
            animation: pulse 1.5s infinite;
        }

        @keyframes shake {

            0%,
            100% {
                transform: translateX(0);
            }

            10%,
            30%,
            50%,
            70%,
            90% {
                transform: translateX(-5px);
            }

            20%,
            40%,
            60%,
            80% {
                transform: translateX(5px);
            }
        }

        .shake {
            animation: shake 0.5s;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                max-height: 0;
            }

            to {
                opacity: 1;
                max-height: 1000px;
            }
        }

        .slide-down {
            animation: slideDown 0.5s ease-out forwards;
            overflow: hidden;
        }

        @keyframes fadeInOut {

            0%,
            100% {
                opacity: 0;
            }

            50% {
                opacity: 1;
            }
        }

        .fade-in-out {
            animation: fadeInOut 2s infinite;
        }
    </style>
</head>

<!-- Registration Already Complete Modal -->
<?php if ($already_registered): ?>
<div id="alreadyRegisteredModal" style="display: flex; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5); align-items: center; justify-content: center;">
    <div style="background-color: white; padding: 40px; border-radius: 12px; text-align: center; max-width: 400px; box-shadow: 0 4px 20px rgba(0,0,0,0.3);">
        <i class="fas fa-check-circle" style="font-size: 3rem; color: #27ae60; margin-bottom: 20px;"></i>
        <h2 style="color: #2d572c; margin-bottom: 15px;">Registration Complete</h2>
        <p style="color: #555; margin-bottom: 25px; font-size: 1.05rem;">You have already registered and generated your QR code. You can view it in your profile or download it from Saved QR Codes.</p>
        <button onclick="redirectToAboutUs()" style="background-color: #27ae60; color: white; border: none; padding: 12px 30px; border-radius: 8px; font-size: 1rem; cursor: pointer; font-weight: 600;">Go to About Us</button>
    </div>
</div>

<script>
    function redirectToAboutUs() {
        window.location.href = 'about-us.php';
    }
</script>
<?php endif; ?>

<!-- Registration Already Complete Modal -->
<?php if ($already_registered): ?>
<div id="alreadyRegisteredModal" style="display: flex; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5); align-items: center; justify-content: center;">
    <div style="background-color: white; padding: 40px; border-radius: 12px; text-align: center; max-width: 400px; box-shadow: 0 4px 20px rgba(0,0,0,0.3);">
        <i class="fas fa-check-circle" style="font-size: 3rem; color: #27ae60; margin-bottom: 20px;"></i>
        <h2 style="color: #2d572c; margin-bottom: 15px;">Registration Complete</h2>
        <p style="color: #555; margin-bottom: 25px; font-size: 1.05rem;">You have already registered and generated your QR code. You can view it in your profile or download it from Saved QR Codes.</p>
        <button onclick="redirectToAboutUs()" style="background-color: #27ae60; color: white; border: none; padding: 12px 30px; border-radius: 8px; font-size: 1rem; cursor: pointer; font-weight: 600;">Go to About Us</button>
    </div>
</div>

<script>
    function redirectToAboutUs() {
        window.location.href = 'about-us.php';
    }
</script>
<?php endif; ?>

<body>
    <?php
    if (isset($_POST['submit'])) {
        try {
            // Validate required fields
            $required_fields = [
                'First Name' => $fname,
                'Last Name' => $lname,
                'LRN' => $lrn,
                'Grade Level' => $glevel,
                'Section' => $section,
                'Birthdate' => $bday,
                'Gender' => $gender,
                'Address' => $stuAddress,
                'Contact Number' => $contact_number,
                'Email' => $email,
                'Guardian Name' => $guardian,
                'Guardian Contact' => $guardian_contact
            ];

            $missing_fields = [];
            foreach ($required_fields as $field_name => $field_value) {
                if (empty($field_value)) {
                    $missing_fields[] = $field_name;
                }
            }

            if (!empty($missing_fields)) {
                $errorMsg = "<div class='alert alert-danger'><i class='fas fa-exclamation-triangle'></i> The following fields are required: " . implode(", ", $missing_fields) . "</div>";
            }
            // Validate LRN (12 digits)
            elseif (!preg_match("/^\d{12}$/", $lrn)) {
                $errorMsg = "<div class='alert alert-danger'><i class='fas fa-exclamation-triangle'></i> LRN must be exactly 12 digits.</div>";
            }
            // Validate email
            elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errorMsg = "<div class='alert alert-danger'><i class='fas fa-exclamation-triangle'></i> Please enter a valid email address.</div>";
            }
            // Validate phone numbers (10-12 digits)
            elseif (!preg_match("/^\d{10,12}$/", preg_replace("/\D/", "", $contact_number))) {
                $errorMsg = "<div class='alert alert-danger'><i class='fas fa-exclamation-triangle'></i> Student contact number must be 10-12 digits.</div>";
            } elseif (!preg_match("/^\d{10,12}$/", preg_replace("/\D/", "", $guardian_contact))) {
                $errorMsg = "<div class='alert alert-danger'><i class='fas fa-exclamation-triangle'></i> Guardian contact number must be 10-12 digits.</div>";
            } else {
                // Connect to database
                include_once "../include/db_conn.php";

                // Check if LRN already exists
                $check_sql = "SELECT * FROM student_info WHERE lrn = ?";
                $check_stmt = mysqli_prepare($conn, $check_sql);

                if (!$check_stmt) {
                    throw new Exception("Prepare statement failed: " . mysqli_error($conn));
                }

                mysqli_stmt_bind_param($check_stmt, "s", $lrn);
                mysqli_stmt_execute($check_stmt);
                $result = mysqli_stmt_get_result($check_stmt);
                $student_exists = mysqli_num_rows($result) > 0;

                // Process profile picture if uploaded
                $profile_picture_path = null;
                if (isset($_FILES['profile-picture']) && $_FILES['profile-picture']['error'] == 0) {
                    $upload_dir = "uploads/";

                    // Create directory if it doesn't exist
                    if (!file_exists($upload_dir)) {
                        mkdir($upload_dir, 0777, true);
                    }

                    $file_name = $lrn . "_" . basename($_FILES['profile-picture']['name']);
                    $target_file = $upload_dir . $file_name;
                    $file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

                    // Check file size (max 10MB)
                    if ($_FILES['profile-picture']['size'] > 10 * 1024 * 1024) {
                        $errorMsg = "<div class='alert alert-danger'><i class='fas fa-exclamation-triangle'></i> Image size should be less than 10MB.</div>";
                    }
                    // Check file type
                    elseif (!in_array($file_type, ['jpg', 'jpeg', 'png', 'gif'])) {
                        $errorMsg = "<div class='alert alert-danger'><i class='fas fa-exclamation-triangle'></i> Only JPG, JPEG, PNG & GIF files are allowed.</div>";
                    }
                    // Upload file
                    elseif (move_uploaded_file($_FILES['profile-picture']['tmp_name'], $target_file)) {
                        $profile_picture_path = $target_file;
                    } else {
                        $errorMsg = "<div class='alert alert-danger'><i class='fas fa-exclamation-triangle'></i> Failed to upload image.</div>";
                    }
                }

                // If no errors with file upload, proceed with database operation
                if (empty($errorMsg)) {
                    if ($student_exists) {
                        // UPDATE existing record
                        $sql = "UPDATE student_info
                                SET first_name=?, middle_name=?, last_name=?, grade_level=?, section=?, birthdate=?,
                                    age=?, sex=?, student_address=?, contact_number=?, email_address=?, parent_guardian=?,
                                    parent_guardian_contact=?, relationship=?, profile_picture=?
                                WHERE lrn=?";
                        $stmt = mysqli_prepare($conn, $sql);

                        if (!$stmt) {
                            throw new Exception("Prepare statement failed: " . mysqli_error($conn));
                        }

                        mysqli_stmt_bind_param(
                            $stmt,
                            "sssisssissssssss",
                            $fname,
                            $mname,
                            $lname,
                            $glevel,
                            $section,
                            $bday,
                            $age,
                            $gender,
                            $stuAddress,
                            $contact_number,
                            $email,
                            $guardian,
                            $guardian_contact,
                            $relationship,
                            $profile_picture_path,
                            $lrn
                        );

                        if (mysqli_stmt_execute($stmt)) {
                            $successMsg = "<div class='alert alert-success'><i class='fas fa-check-circle'></i> Student information updated successfully!</div>";

                            // Generate QR code image and save as Base64
                            $qrData = $lrn; // Only LRN for scanner
                            $qrApiUrl = "https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=" . urlencode($qrData);
                            
                            // Fetch QR code image and convert to Base64
                            $qrImageContent = @file_get_contents($qrApiUrl);
                            if ($qrImageContent !== false) {
                                $qrBase64 = "data:image/png;base64," . base64_encode($qrImageContent);
                            } else {
                                $qrBase64 = null;
                            }

                            // Save QR code as Base64 and mark as registered in database
                            if ($qrBase64) {
                                $updateQrSql = "UPDATE student_info SET qr_code_url = ?, qr_code_generated_at = NOW(), is_registered = 1 WHERE lrn = ?";
                                $updateQrStmt = mysqli_prepare($conn, $updateQrSql);
                                if ($updateQrStmt) {
                                    mysqli_stmt_bind_param($updateQrStmt, "ss", $qrBase64, $lrn);
                                    if (!mysqli_stmt_execute($updateQrStmt)) {
                                        error_log("QR Update failed: " . mysqli_stmt_error($updateQrStmt));
                                    }
                                    mysqli_stmt_close($updateQrStmt);
                                }
                                $_SESSION['qr_url'] = $qrBase64;
                            }

                            $_SESSION['student_name'] = "$fname $lname";
                            $_SESSION['student_lrn'] = $lrn;
                        } else {
                            throw new Exception("Execute statement failed: " . mysqli_stmt_error($stmt));
                        }
                    } else {
                        // INSERT new record
                        $sql = "INSERT INTO student_info 
                                (first_name, middle_name, last_name, lrn, grade_level, section, birthdate,
                                 age, sex, student_address, contact_number, email_address, parent_guardian,
                                 parent_guardian_contact, relationship, profile_picture, account_password)
                                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                        $stmt = mysqli_prepare($conn, $sql);

                        if (!$stmt) {
                            throw new Exception("Prepare statement failed: " . mysqli_error($conn));
                        }

                        // Default password (can be changed later)
                        $default_password = password_hash($lrn, PASSWORD_DEFAULT);

                        mysqli_stmt_bind_param(
                            $stmt,
                            "sssisssisssssssss",
                            $fname,
                            $mname,
                            $lname,
                            $lrn,
                            $glevel,
                            $section,
                            $bday,
                            $age,
                            $gender,
                            $stuAddress,
                            $contact_number,
                            $email,
                            $guardian,
                            $guardian_contact,
                            $relationship,
                            $profile_picture_path,
                            $default_password
                        );

                        if (mysqli_stmt_execute($stmt)) {
                            $successMsg = "<div class='alert alert-success'><i class='fas fa-check-circle'></i> Student registered successfully!</div>";

                            // Generate QR code image and save as Base64
                            $qrData = $lrn; // Only LRN for scanner
                            $qrApiUrl = "https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=" . urlencode($qrData);
                            
                            // Fetch QR code image and convert to Base64
                            $qrImageContent = @file_get_contents($qrApiUrl);
                            if ($qrImageContent !== false) {
                                $qrBase64 = "data:image/png;base64," . base64_encode($qrImageContent);
                            } else {
                                $qrBase64 = null;
                            }

                            // Save QR code as Base64 and mark as registered in database
                            if ($qrBase64) {
                                $updateQrSql = "UPDATE student_info SET qr_code_url = ?, qr_code_generated_at = NOW(), is_registered = 1 WHERE lrn = ?";
                                $updateQrStmt = mysqli_prepare($conn, $updateQrSql);
                                if ($updateQrStmt) {
                                    mysqli_stmt_bind_param($updateQrStmt, "ss", $qrBase64, $lrn);
                                    if (!mysqli_stmt_execute($updateQrStmt)) {
                                        error_log("QR Update failed: " . mysqli_stmt_error($updateQrStmt));
                                    }
                                    mysqli_stmt_close($updateQrStmt);
                                }
                                $_SESSION['qr_url'] = $qrBase64;
                            }

                            $_SESSION['student_name'] = "$fname $lname";
                            $_SESSION['student_lrn'] = $lrn;
                        } else {
                            throw new Exception("Execute statement failed: " . mysqli_stmt_error($stmt));
                        }
                    }
                }

                // Close database connection
                mysqli_close($conn);
            }
        } catch (Exception $e) {
            $errorMsg = "<div class='alert alert-danger'><i class='fas fa-exclamation-triangle'></i> Error: " . $e->getMessage() . "</div>";
        }
    }
    ?>

    <?php include '../include/header.php'; ?>

    <div class="page-title">
        <h2><i class="fas fa-user-plus"></i> Student Registration Form</h2>
        <p>Please fill in all the required information to register as a student.</p>
    </div>

    <!-- Display error and success messages -->
    <?php if (!empty($errorMsg)): ?>
        <?php echo $errorMsg; ?>
    <?php endif; ?>

    <?php if (!empty($successMsg)): ?>
        <?php echo $successMsg; ?>
    <?php endif; ?>

    <main>
        <form id="registration-form" method="POST" action="<?php echo ($_SERVER["PHP_SELF"]); ?>" enctype="multipart/form-data">
            <div class="form-container">
                <!-- Personal Information Section -->
                <div class="form-section" id="personal-info">
                    <div class="section-header">
                        <h3><i class="fas fa-user"></i> Personal Information</h3>
                    </div>
                    <div class="section-content">
                        <div class="image-upload-container">
                            <div class="image-preview-container">
                                <div id="image-preview">
                                    <i class="fas fa-user-circle"></i>
                                </div>
                            </div>
                            <div class="upload-controls">
                                <label for="file-upload" class="custom-file-upload">
                                    <i class="fas fa-cloud-upload-alt"></i> Upload Photo
                                </label>
                                <input type="file" id="file-upload" name="profile-picture" accept="image/*">
                                <p class="hint">Image size should be less than 10MB</p>
                            </div>
                        </div>

                        <div class="input-group">
                            <div class="form-group">
                                <label for="firstName">First Name <span class="required">*</span></label>
                                <input type="text" id="firstName" name="firstName" value="<?php echo htmlspecialchars($student['first_name'] ?? ''); ?>" required>
                                <span class="error-message"></span>
                            </div>
                            <div class="form-group">
                                <label for="middleName">Middle Name</label>
                                <input type="text" id="middleName" name="middleName" value="<?php echo htmlspecialchars($student['middle_name'] ?? ''); ?>">
                            </div>
                            <div class="form-group">
                                <label for="lastName">Last Name <span class="required">*</span></label>
                                <input type="text" id="lastName" name="lastName" value="<?php echo htmlspecialchars($student['last_name'] ?? ''); ?>" required>
                                <span class="error-message"></span>
                            </div>
                        </div>

                        <div class="input-group">
                            <div class="form-group">
                                <label for="lrn">LRN <span class="required">*</span></label>
                                <input type="text" id="lrn" name="lrn" value="<?php echo htmlspecialchars($student['lrn'] ?? ''); ?>" oninput="if(this.value.length > 12) this.value = this.value.slice(0, 12);" readonly>
                                <span class="error-message"></span>
                            </div>
                            <div class="form-group">
                                <label for="birthdate">Birthdate <span class="required">*</span></label>
                                <input type="date" id="birthdate" name="birthdate" value="<?php echo $bday; ?>" required>
                                <span class="error-message"></span>
                            </div>
                            <div class="form-group">
                                <label for="age">Age</label>
                                <input type="number" id="age" name="age" readonly>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Sex <span class="required">*</span></label>
                            <div class="radio-group">
                                <label class="radio-label">
                                    <input type="radio" name="gender" value="male" <?php if ($gender == 'male') echo 'checked'; ?> required>
                                    <span class="radio-custom"></span>
                                    Male
                                </label>
                                <label class="radio-label">
                                    <input type="radio" name="gender" value="female" <?php if ($gender == 'female') echo 'checked'; ?>>
                                    <span class="radio-custom"></span>
                                    Female
                                </label>
                            </div>
                            <span class="error-message"></span>
                        </div>
                    </div>
                </div>

                <!-- Academic Information Section -->
                <div class="form-section" id="academic-info">
                    <div class="section-header">
                        <h3><i class="fas fa-school"></i> Academic Information</h3>
                    </div>
                    <div class="section-content">
                        <div class="input-group">
                            <div class="form-group">
                                <label for="gradeLevel">Grade Level <span class="required">*</span></label>
                                <select id="gradeLevel" name="gradeLevel" required>
                                    <option value="" disabled selected>Select Grade Level</option>
                                    <option value="7" <?php if ($glevel == 7) echo 'selected'; ?>>Grade 7</option>
                                    <option value="8" <?php if ($glevel == 8) echo 'selected'; ?>>Grade 8</option>
                                    <option value="9" <?php if ($glevel == 9) echo 'selected'; ?>>Grade 9</option>
                                    <option value="10" <?php if ($glevel == 10) echo 'selected'; ?>>Grade 10</option>
                                    <option value="11" <?php if ($glevel == 11) echo 'selected'; ?>>Grade 11</option>
                                    <option value="12" <?php if ($glevel == 12) echo 'selected'; ?>>Grade 12</option>
                                </select>
                                <span class="error-message"></span>
                            </div>
                            <div class="form-group">
                                <label for="section">Section <span class="required">*</span></label>
                                <input type="text" id="section" name="section" value="<?php echo $section; ?>" required>
                                <span class="error-message"></span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Contact Information Section -->
                <div class="form-section" id="contact-info">
                    <div class="section-header">
                        <h3><i class="fas fa-address-book"></i> Contact Information</h3>
                    </div>
                    <div class="section-content">
                        <div class="form-group">
                            <label for="address">Complete Address <span class="required">*</span></label>
                            <textarea id="address" name="address" rows="2" required><?php echo $stuAddress; ?></textarea>
                            <span class="error-message"></span>
                        </div>

                        <div class="input-group">
                            <div class="form-group">
                                <label for="contactNumber">Contact Number <span class="required">*</span></label>
                                <input type="tel" id="contactNumber" name="contactNumber" value="<?php echo $contact_number; ?>" oninput="if(this.value.length > 11) this.value = this.value.slice(0, 11);" required>
                                <span class="error-message"></span>
                            </div>
                            <div class="form-group">
                                <label for="email">Email Address <span class="required">*</span></label>
                                <input type="email" id="email" name="email" value="<?php echo $email; ?>" required>
                                <span class="error-message"></span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Guardian Information Section -->
                <div class="form-section" id="guardian-info">
                    <div class="section-header">
                        <h3><i class="fas fa-user-shield"></i> Guardian Information</h3>
                    </div>
                    <div class="section-content">
                        <div class="input-group">
                            <div class="form-group">
                                <label for="guardianName">Guardian's Name <span class="required">*</span></label>
                                <input type="text" id="guardianName" name="guardianName" value="<?php echo $guardian; ?>" required>
                                <span class="error-message"></span>
                            </div>
                            <div class="form-group">
                                <label for="guardianContact">Guardian's Contact <span class="required">*</span></label>
                                <input type="tel" id="guardianContact" name="guardianContact" value="<?php echo $guardian_contact; ?>" oninput="if(this.value.length > 11) this.value = this.value.slice(0, 11);" required>
                                <span class="error-message"></span>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="relationship">Relationship to Student</label>
                            <select id="relationship" name="relationship">
                                <option value="" disabled selected>Select Relationship</option>
                                <option value="parent" <?php if ($relationship == 'parent') echo 'selected'; ?>>Parent</option>
                                <option value="grandparent" <?php if ($relationship == 'grandparent') echo 'selected'; ?>>Grandparent</option>
                                <option value="sibling" <?php if ($relationship == 'sibling') echo 'selected'; ?>>Sibling</option>
                                <option value="other" <?php if ($relationship == 'other') echo 'selected'; ?>>Other</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- QR Code Section -->
                <div class="form-section" id="qr-section">
                    <div class="section-header">
                        <h3><i class="fas fa-qrcode"></i> Student QR Code</h3>
                    </div>
                    <div class="section-content">
                        <div class="qr-container">
                            <div id="qr-box">
                                <img src="/placeholder.svg" id="qr-image" alt="QR Code">
                                <div class="qr-placeholder">
                                    <i class="fas fa-qrcode"></i>
                                    <p>QR code will be generated after submission</p>
                                </div>
                            </div>
                            <p class="qr-info">This QR code contains the student's identification information for easy scanning.</p>
                        </div>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="button" id="reset-btn" class="btn btn-secondary">
                        <i class="fas fa-undo"></i> Reset Form
                    </button>
                    <button type="submit" id="submit-btn" class="btn btn-primary" name="submit">
                        <i class="fas fa-save"></i> Submit Registration
                    </button>
                </div>
            </div>
        </form>
    </main>

    <div id="success-modal" class="modal">
        <div class="modal-content">
            <span class="close-modal">&times;</span>
            <div class="success-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <h3>Registration Successful!</h3>
            <p>The student has been successfully registered in the system.</p>
            <div class="qr-download">
                <img id="modal-qr-image" src="/placeholder.svg" alt="Student QR Code">
                <a id="download-qr" href="#" class="btn btn-primary">
                    <i class="fas fa-download"></i> Download QR Code
                </a>
            </div>
        </div>
    </div>

    <?php include '../include/footer.php'; ?>

    <script>
        // DOM Elements
        const form = document.getElementById('registration-form');
        const fileUpload = document.getElementById('file-upload');
        const imagePreview = document.getElementById('image-preview');
        const birthdateInput = document.getElementById('birthdate');
        const ageInput = document.getElementById('age');
        const qrBox = document.getElementById('qr-box');
        const qrImage = document.getElementById('qr-image');
        const resetBtn = document.getElementById('reset-btn');
        const submitBtn = document.getElementById('submit-btn');
        const successModal = document.getElementById('success-modal');
        const closeModal = document.querySelector('.close-modal');
        const modalQrImage = document.getElementById('modal-qr-image');
        const downloadQrBtn = document.getElementById('download-qr');
        const firstNameInput = document.getElementById('firstName');
        const lrnInput = document.getElementById('lrn');

        // Initialize the form animations
        document.addEventListener('DOMContentLoaded', function() {
            // Add animation classes to form sections with a delay
            const sections = document.querySelectorAll('.form-section');
            sections.forEach((section, index) => {
                setTimeout(() => {
                    section.classList.add('slide-down');
                }, index * 100);
            });

            // Initialize section toggle functionality
            initializeSectionToggle();

            // Set up event listeners
            setupEventListeners();

            // Calculate age if birthdate is already set
            if (birthdateInput.value) {
                calculateAge();
            }

            // Show success modal if there's a success message
            if (document.querySelector('.alert-success')) {
                // Generate QR code if registration was successful
                generateQR();
                successModal.classList.add('show');
            }

            // Check if we have a profile picture path in session
            <?php if (isset($_SESSION['profile_picture']) && !empty($_SESSION['profile_picture'])): ?>
                // Display the profile picture if available
                showProfilePicture('<?php echo $_SESSION['profile_picture']; ?>');
            <?php endif; ?>
        });

        // Set up all event listeners
        function setupEventListeners() {
            // Image upload preview
            fileUpload.addEventListener('change', handleImageUpload);

            // Calculate age automatically when birthdate changes
            birthdateInput.addEventListener('change', calculateAge);

            // Form validation and submission
            form.addEventListener('submit', handleFormSubmit);

            // Reset button
            resetBtn.addEventListener('click', resetForm);

            // Close modal
            closeModal.addEventListener('click', () => {
                successModal.classList.remove('show');
            });

            // Click outside modal to close
            window.addEventListener('click', (e) => {
                if (e.target === successModal) {
                    successModal.classList.remove('show');
                }
            });

            // Input validation on blur
            const requiredInputs = document.querySelectorAll('input[required], select[required], textarea[required]');
            requiredInputs.forEach(input => {
                input.addEventListener('blur', () => validateField(input));
                input.addEventListener('input', () => {
                    // Remove error state when user starts typing
                    const formGroup = input.closest('.form-group');
                    if (formGroup.classList.contains('error')) {
                        formGroup.classList.remove('error');
                    }
                });
            });

            // Add validation to prevent numbers in name fields
            const nameInputs = document.querySelectorAll('#firstName, #middleName, #lastName, #guardianName');
            nameInputs.forEach(input => {
                input.addEventListener('input', function(e) {
                    // Remove any numeric characters
                    this.value = this.value.replace(/[0-9]/g, '');
                });
            });
        }

        // Toggle form sections open/closed
        function initializeSectionToggle() {
            const sectionHeaders = document.querySelectorAll('.section-header');
            sectionHeaders.forEach(header => {
                header.addEventListener('click', () => {
                    const section = header.parentElement;
                    const content = header.nextElementSibling;

                    if (content.style.display === 'none') {
                        content.style.display = 'grid';
                        header.classList.add('active');
                    } else {
                        content.style.display = 'none';
                        header.classList.remove('active');
                    }
                });
            });
        }

        // Handle image upload and preview
        function handleImageUpload(e) {
            const file = e.target.files[0];

            if (file) {
                // Check if file is an image
                if (!file.type.startsWith('image/')) {
                    alert('Please select an image file');
                    return;
                }

                // Check file size (max 10MB)
                if (file.size > 10 * 1024 * 1024) {
                    alert('Image size should be less than 10MB');
                    return;
                }

                const reader = new FileReader();

                reader.onload = function(event) {
                    // Clear existing content
                    imagePreview.innerHTML = '';

                    // Create new image element
                    const img = document.createElement('img');
                    img.src = event.target.result;
                    img.alt = 'Profile Preview';

                    // Add to preview
                    imagePreview.appendChild(img);

                    // Add animation
                    img.classList.add('pulse');
                    setTimeout(() => {
                        img.classList.remove('pulse');
                    }, 1000);
                };

                reader.readAsDataURL(file);
            }
        }

        // Display profile picture from path
        function showProfilePicture(path) {
            if (!path) return;

            // Clear existing content
            imagePreview.innerHTML = '';

            // Create new image element
            const img = document.createElement('img');
            img.src = path;
            img.alt = 'Profile Picture';
            img.onerror = function() {
                // If image fails to load, show default icon
                imagePreview.innerHTML = '<i class="fas fa-user-circle"></i>';
            };

            // Add to preview
            imagePreview.appendChild(img);
        }

        // Calculate age from birthdate
        function calculateAge() {
            const birthdate = new Date(birthdateInput.value);
            const today = new Date();

            let age = today.getFullYear() - birthdate.getFullYear();
            const monthDiff = today.getMonth() - birthdate.getMonth();

            if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthdate.getDate())) {
                age--;
            }

            if (age < 0) {
                age = 0;
            }

            ageInput.value = age;
        }



        // Validate the entire form
        function validateForm() {
            let isValid = true;

            // Validate all required fields
            const requiredInputs = document.querySelectorAll('input[required], select[required], textarea[required]');
            requiredInputs.forEach(input => {
                if (!validateField(input)) {
                    isValid = false;
                }
            });

            // Validate radio buttons
            const genderInputs = document.querySelectorAll('input[name="gender"]');
            const genderSelected = Array.from(genderInputs).some(input => input.checked);

            if (!genderSelected) {
                const formGroup = genderInputs[0].closest('.form-group');
                formGroup.classList.add('error');
                formGroup.querySelector('.error-message').textContent = 'Please select a gender';
                isValid = false;
            }

            return isValid;
        }

        // Handle form submission
        function handleFormSubmit(e) {
            // For regular form submission, validate first
            if (!validateForm()) {
                e.preventDefault(); // Prevent form submission if validation fails

                // Scroll to the first error
                const firstError = document.querySelector('.form-group.error');
                if (firstError) {
                    firstError.scrollIntoView({
                        behavior: 'smooth',
                        block: 'center'
                    });
                }
                return false;
            }

            // If validation passes, show loading state
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';

            // Form will submit normally
            return true;
        }

        // Generate QR Code
        function generateQR() {
            // Get student info for QR code
            const lrn = lrnInput.value || '<?php echo isset($_SESSION["student_lrn"]) ? $_SESSION["student_lrn"] : ""; ?>';
            const studentName = '<?php echo isset($_SESSION["student_name"]) ? $_SESSION["student_name"] : ""; ?>';

            if (!lrn) return;

            // Create data string for QR
            const qrData = `${lrn}`;

            // Get QR code from API
            const qrUrl = `https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=${encodeURIComponent(qrData)}`;

            // Set QR image source
            qrImage.src = qrUrl;
            modalQrImage.src = qrUrl;

            // Show QR code with animation
            qrBox.classList.add('show-qr');

            // Set download link
            downloadQrBtn.href = qrUrl;
            downloadQrBtn.download = `${lrn}_qr_code.png`;
        }

        // Reset form
        function resetForm() {
            // Clear form fields
            form.reset();

            // Reset image preview
            imagePreview.innerHTML = '<i class="fas fa-user-circle"></i>';

            // Hide QR code
            qrBox.classList.remove('show-qr');

            // Remove all error states
            const errorGroups = document.querySelectorAll('.form-group.error');
            errorGroups.forEach(group => {
                group.classList.remove('error');
            });

            // Focus on first field
            firstNameInput.focus();
        }
    </script>

</body>

</html>
