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

verifyPageAccess("scanner.php");

if (!isset($_SESSION['lrn']) && !isset($_SESSION['employee_id'])) {
    header("Location: ../login.php");
    exit();
}

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['text'])) {
  $scannedText = $_POST['text'];

  // Prepare and execute the SQL statement to prevent SQL injection
  $stmt = $conn->prepare("SELECT * FROM student_info WHERE lrn = ?");
  $stmt->bind_param("s", $scannedText);
  $stmt->execute();
  $result = $stmt->get_result();

  // Insert attendance record
  if ($result->num_rows > 0) {
    $student = $result->fetch_assoc();
    $studentId = $student['lrn'];

    // Insert attendance with current timestamp
    $attendanceStmt = $conn->prepare("INSERT INTO attendance (student_lrn, timestamp) VALUES (?, NOW())");
    $attendanceStmt->bind_param("s", $studentId);
    $attendanceStmt->execute();

    // Fetch the result again for display
    $stmt->execute();
    $result = $stmt->get_result();
  }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Student Attendance System</title>
  <link rel="icon" type="image/x-icon" href="../pics/logos/Lagro_High_School_logo.png">
  <script type="text/javascript" src="https://unpkg.com/html5-qrcode"></script>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    /* Reset & Base Styles */
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
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

    /* Page Title */
    .page-title {
        text-align: center;
        padding: 2rem 1rem;
        border-bottom: 1px solid #e0e0e0;
        background-color: var(--bg);
    }
  
    .page-title h2 {
        color: var(--dark-color);
        font-size: 1.8rem;
        margin-bottom: 0.5rem;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
    }
    
    .page-title p {
        color: var(--text);
        max-width: 600px;
        margin: 0 auto;
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
    @media (max-width: 768px) {
        header {
            flex-direction: column;
            align-items: flex-start;
        }

        .logo-container {
            margin-bottom: 1rem;
        }

        .nav {
            width: 100%;
            overflow-x: auto;
            padding-bottom: 0.5rem;
        }

        .nav-menu {
            white-space: nowrap;
        }

        .page-title h2 {
            font-size: 1.5rem;
        }
    }

    /* Scanner Specific Styles */
    /* Modern CSS Reset */
    *,
    *::before,
    *::after {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }

    /* Custom Properties */
    .app-container {
      max-width: 1200px;
      width: 100%;
      margin: 0 auto;
      padding: 2rem 1rem;
    }

    /* Main Layout */
    main {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 2rem;
    }

    @media (max-width: 1024px) {
      main {
        grid-template-columns: 1fr;
      }
    }

    /* Card Styles */
    .card {
      background-color: var(--white);
      border-radius: var(--border-radius-md);
      box-shadow: var(--shadow-md);
      overflow: hidden;
      display: flex;
      flex-direction: column;
      height: 100%;
      transition: transform var(--transition-normal), box-shadow var(--transition-normal);
    }

    .card:hover {
      transform: translateY(-5px);
      box-shadow: var(--shadow-lg);
    }

    .card-header {
      padding: 1.25rem 1.5rem;
      background-color: var(--primary-dark);
      color: var(--white);
    }

    .card-header h2 {
      font-size: 1.25rem;
      font-weight: 600;
      margin: 0;
      display: flex;
      align-items: center;
      gap: 0.5rem;
    }

    .card-body {
      padding: 1.5rem;
      flex: 1;
      background-color: var(--bg);
    }

    .card-footer {
      padding: 1.25rem 1.5rem;
      border-top: 1px solid var(--grey-200);
      background-color: var(--grey-100);
      display: none;
    }

    /* Scanner Styles */
    .scanner-card {
      min-height: 600px;
    }

    .scanner-container {
      position: relative;
      aspect-ratio: 4/3;
      max-width: 100%;
      border-radius: var(--border-radius-sm);
      overflow: hidden;
      margin-bottom: 1rem;
      background-color: var(--grey-800);
      box-shadow: var(--shadow-md);
    }

    .scanner-overlay {
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      z-index: 2;
      pointer-events: none;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .scanner-guide {
      width: 70%;
      height: 70%;
      border: 2px solid var(--secondary-color);
      border-radius: var(--border-radius-sm);
      position: relative;
    }

    .scanner-guide::before,
    .scanner-guide::after {
      content: '';
      position: absolute;
      width: 15px;
      height: 15px;
      border-color: var(--secondary-color);
      border-style: solid;
    }

    .scanner-guide::before {
      top: -2px;
      left: -2px;
      border-width: 3px 0 0 3px;
    }

    .scanner-guide::after {
      bottom: -2px;
      right: -2px;
      border-width: 0 3px 3px 0;
    }

    #preview {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }

    #scanning-indicator {
      position: absolute;
      bottom: 10px;
      left: 50%;
      transform: translateX(-50%);
      background-color: rgba(0, 0, 0, 0.5);
      color: var(--white);
      padding: 0.5rem 1rem;
      border-radius: 50px;
      display: flex;
      align-items: center;
      gap: 0.5rem;
      font-size: 0.875rem;
      z-index: 3;
    }

    .pulse {
      display: inline-block;
      width: 10px;
      height: 10px;
      border-radius: 50%;
      background-color: var(--success-color);
      animation: pulse 1.5s infinite;
    }

    @keyframes pulse {
      0% {
        transform: scale(0.8);
        opacity: 1;
      }

      50% {
        transform: scale(1.2);
        opacity: 0.7;
      }

      100% {
        transform: scale(0.8);
        opacity: 1;
      }
    }

    .camera-controls {
      display: flex;
      justify-content: center;
      margin-top: 1rem;
    }

    /* Button Styles */
    .btn {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      padding: 0.5rem 1rem;
      border-radius: var(--border-radius-sm);
      font-weight: 500;
      cursor: pointer;
      transition: background-color var(--transition-fast), transform var(--transition-fast);
      border: none;
      outline: none;
      font-size: 0.9rem;
      gap: 0.5rem;
    }

    .btn:hover {
      transform: translateY(-2px);
    }

    .btn:active {
      transform: translateY(0);
    }

    .btn-primary {
      background-color: var(--primary-color);
      color: var(--white);
    }

    .btn-primary:hover {
      background-color: var(--primary-dark);
    }

    /* Form Styles */
    .form-group {
      margin-bottom: 1rem;
    }

    label {
      display: block;
      margin-bottom: 0.5rem;
      font-weight: 500;
      color: var(--grey-700);
      font-size: 0.9rem;
    }

    .input-with-icon {
      position: relative;
    }

    .input-with-icon i {
      position: absolute;
      left: 1rem;
      top: 50%;
      transform: translateY(-50%);
      color: var(--grey-500);
    }

    input[type="text"] {
      width: 100%;
      padding: 0.75rem 1rem 0.75rem 2.5rem;
      border: 1px solid var(--grey-300);
      border-radius: var(--border-radius-sm);
      font-size: 1rem;
      transition: border-color var(--transition-fast), box-shadow var(--transition-fast);
      outline: none;
      background-color: var(--white);
    }

    input[type="text"]:focus {
      border-color: var(--primary-color);
      box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.15);
    }

    /* Student Card Styles */
    .student-card {
      border-radius: var(--border-radius-md);
      overflow: hidden;
      color: var(--white);
      box-shadow: var(--shadow-md);
      margin-bottom: 1.5rem;
    }

    .student-card-header {
      padding: 1.5rem;
      text-align: center;
      position: relative;
      border-bottom: 1px solid rgba(255, 255, 255, 0.2);
    }

    .school-badge {
      position: absolute;
      top: 1rem;
      right: 1rem;
      width: 36px;
      height: 36px;
      background-color: rgba(255, 255, 255, 0.2);
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .student-card-header h3 {
      margin: 0;
      font-size: 1.2rem;
      font-weight: 600;
      letter-spacing: 1px;
    }

    .student-card-header p {
      margin-top: 0.25rem;
      opacity: 0.8;
      font-size: 0.9rem;
    }

    .student-card-body {
      padding: 1.5rem;
      display: flex;
      flex-direction: column;
      gap: 1.5rem;
    }

    @media (min-width: 768px) {
      .student-card-body {
        flex-direction: row;
        align-items: flex-start;
      }
    }

    .student-photo {
      width: 150px;
      height: 180px;
      background-color: var(--white);
      border-radius: var(--border-radius-sm);
      border: 4px solid var(--white);
      overflow: hidden;
      box-shadow: var(--shadow-md);
      margin: 0 auto;
    }

    @media (min-width: 768px) {
      .student-photo {
        margin: 0;
      }
    }

    .student-photo img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      object-position: center top;
    }

    .student-details {
      flex: 1;
      display: flex;
      flex-direction: column;
      gap: 1rem;
    }

    .detail-row {
      display: flex;
      flex-direction: column;
      gap: 0.25rem;
      position: relative;
      padding-left: 0.75rem;
    }

    .detail-row::before {
      content: "";
      position: absolute;
      left: 0;
      top: 0.25rem;
      height: calc(100% - 0.5rem);
      width: 3px;
      background-color: rgba(255, 255, 255, 0.5);
      border-radius: 3px;
    }

    .detail-label {
      font-size: 0.8rem;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      opacity: 0.7;
    }

    .detail-value {
      font-size: 1.1rem;
      font-weight: 500;
      letter-spacing: 0.5px;
    }

    .lrn-value {
      font-family: monospace;
      font-size: 1.2rem;
      letter-spacing: 1px;
      background-color: rgba(255, 255, 255, 0.2);
      display: inline-block;
      padding: 0.25rem 0.5rem;
      border-radius: var(--border-radius-sm);
      margin-top: 0.25rem;
    }

    .student-card-footer {
      padding: 1rem;
      text-align: center;
      border-top: 1px solid rgba(255, 255, 255, 0.2);
      font-size: 0.9rem;
    }

    .authorized-badge {
      display: inline-flex;
      align-items: center;
      gap: 0.5rem;
      background-color: rgba(255, 255, 255, 0.2);
      padding: 0.5rem 1rem;
      border-radius: 50px;
    }

    /* Attendance Confirmation */
    .attendance-confirmation {
      background-color: var(--success-color);
      color: var(--white);
      border-radius: var(--border-radius-md);
      padding: 1.25rem;
      display: flex;
      align-items: center;
      gap: 1rem;
      box-shadow: var(--shadow-md);
      animation: slideIn var(--transition-normal);
    }

    .attendance-icon {
      width: 48px;
      height: 48px;
      background-color: rgba(255, 255, 255, 0.2);
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.5rem;
    }

    .attendance-details h4 {
      margin: 0;
      font-size: 1.1rem;
      font-weight: 600;
    }

    .attendance-details p {
      margin: 0.25rem 0 0;
      font-size: 0.9rem;
      opacity: 0.9;
    }

    /* Error Notification */
    .error-notification {
      background-color: var(--danger-color);
      color: var(--white);
      border-radius: var(--border-radius-md);
      padding: 1.25rem;
      display: flex;
      align-items: center;
      gap: 1rem;
      box-shadow: var(--shadow-md);
      animation: slideIn var(--transition-normal);
    }

    .error-icon {
      width: 48px;
      height: 48px;
      background-color: rgba(255, 255, 255, 0.2);
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.5rem;
    }

    .error-message h4 {
      margin: 0;
      font-size: 1.1rem;
      font-weight: 600;
    }

    .error-message p {
      margin: 0.25rem 0 0;
      font-size: 0.9rem;
      opacity: 0.9;
    }

    /* Empty State */
    .empty-state {
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      padding: 3rem 1.5rem;
      color: var(--grey-500);
      text-align: center;
      min-height: 300px;
    }

    .empty-state i {
      font-size: 3rem;
      margin-bottom: 1rem;
      opacity: 0.3;
    }

    .empty-state p {
      font-size: 1rem;
    }

    /* Notification */
    .notification {
      position: fixed;
      top: 2rem;
      right: 2rem;
      padding: 1rem 1.5rem;
      background-color: var(--success-color);
      color: var(--white);
      border-radius: var(--border-radius-md);
      box-shadow: var(--shadow-lg);
      transform: translateX(calc(100% + 2rem));
      transition: transform var(--transition-normal);
      z-index: 1000;
    }

    .notification.show {
      transform: translateX(0);
    }

    .notification-content {
      display: flex;
      align-items: center;
      gap: 0.75rem;
    }

    .notification-content i {
      font-size: 1.25rem;
    }

    /* Utility Classes */
    .hidden {
      display: none;
    }

    /* Animations */
    @keyframes slideIn {
      from {
        opacity: 0;
        transform: translateY(20px);
      }

      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    /* Media Queries */
    @media (max-width: 768px) {
      .app-container {
        padding: 1rem;
      }

      main {
        grid-template-columns: 1fr;
      }

      h1 {
        font-size: 1.5rem;
      }

      .card-header h2 {
        font-size: 1.1rem;
      }

      .student-photo {
        width: 120px;
        height: 150px;
      }

      .notification {
        right: 1rem;
        left: 1rem;
        transform: translateY(calc(-100% - 2rem));
      }

      .notification.show {
        transform: translateY(0);
      }
    }
  </style>
  <script>
    // QR Scanner Configuration
    let html5QrcodeScanner;
    let scannerActive = false;
    let isProcessing = false;
    let resizeTimeout;
    const SCAN_COOLDOWN = 500; // 0.5 seconds cooldown between scans
    const STORAGE_KEY = 'qrScannerLastScanTime';

    // Get last scan time from localStorage
    function getLastScanTime() {
      const stored = localStorage.getItem(STORAGE_KEY);
      return stored ? parseInt(stored, 10) : 0;
    }

    // Save scan time to localStorage
    function setLastScanTime(time) {
      localStorage.setItem(STORAGE_KEY, time.toString());
    }

    // Check if cooldown is still active
    function isCooldownActive() {
      const lastScanTime = getLastScanTime();
      const currentTime = Date.now();
      return (currentTime - lastScanTime) < SCAN_COOLDOWN;
    }

    // Calculate responsive qrbox size based on container
    function calculateQRBoxSize() {
      const previewElement = document.getElementById('preview');
      if (!previewElement) return { width: 250, height: 250 };
      
      const container = previewElement.parentElement;
      const containerWidth = container.offsetWidth;
      const containerHeight = container.offsetHeight;
      
      // Use 70% of container width/height for qrbox, but maintain aspect ratio
      const size = Math.min(containerWidth, containerHeight) * 0.7;
      return { width: Math.round(size), height: Math.round(size) };
    }

    // Initialize the QR Scanner
    function initializeQRScanner() {
      const textInput = document.getElementById('text');
      const switchCameraBtn = document.getElementById('switchCamera');
      
      if (!textInput) {
        console.error('Required input element not found for QR scanner');
        return;
      }

      // Check if cooldown is still active from previous page load
      if (isCooldownActive()) {
        const lastScanTime = getLastScanTime();
        const remainingTime = Math.ceil((SCAN_COOLDOWN - (Date.now() - lastScanTime)) / 1000);
        console.log('[v0] Cooldown active, scanner will be enabled in ' + remainingTime + ' seconds');
        
        // Wait for cooldown to expire before starting scanner
        setTimeout(startQRScanner, SCAN_COOLDOWN - (Date.now() - lastScanTime));
      } else {
        // Start scanner immediately if no cooldown
        startQRScanner();
      }

      // Switch camera button functionality
      if (switchCameraBtn) {
        switchCameraBtn.addEventListener('click', async () => {
          if (scannerActive && html5QrcodeScanner) {
            try {
              await html5QrcodeScanner.stop();
              scannerActive = false;
              isProcessing = false;
              setTimeout(startQRScanner, 500);
            } catch (error) {
              console.error('Unable to switch camera:', error);
            }
          }
        });
      }

      // Add resize listener for responsiveness
      window.addEventListener('resize', handleWindowResize);
    }

    // Handle window resize with debouncing
    function handleWindowResize() {
      if (resizeTimeout) clearTimeout(resizeTimeout);
      
      resizeTimeout = setTimeout(async () => {
        if (scannerActive && html5QrcodeScanner) {
          console.log('[v0] Window resized, reinitializing scanner');
          try {
            await html5QrcodeScanner.stop();
            scannerActive = false;
            isProcessing = false;
            setTimeout(startQRScanner, 300);
          } catch (error) {
            console.log('[v0] Error during resize reinit:', error);
          }
        }
      }, 500); // Wait 500ms after resize stops before reinitializing
    }

    // Start the QR Scanner
    async function startQRScanner() {
      try {
        console.log('[v0] Initializing Html5Qrcode scanner');
        html5QrcodeScanner = new Html5Qrcode('preview');
        
        // Calculate responsive qrbox size
        const qrboxSize = calculateQRBoxSize();
        console.log('[v0] QR box size: ' + qrboxSize.width + 'x' + qrboxSize.height);
        
        const config = { 
          fps: 10, 
          qrbox: qrboxSize
        };
        
        await html5QrcodeScanner.start(
          { facingMode: 'environment' },
          config,
          async (qrCodeMessage) => {
            console.log('[v0] QR code scanned:', qrCodeMessage);
            
            // Prevent multiple simultaneous scans
            if (isProcessing) {
              console.log('[v0] Already processing a scan, ignoring this one');
              return;
            }

            // Check cooldown using localStorage
            if (isCooldownActive()) {
              const lastScanTime = getLastScanTime();
              const remainingTime = Math.ceil((SCAN_COOLDOWN - (Date.now() - lastScanTime)) / 1000);
              console.log('[v0] Scan cooldown active, ' + remainingTime + 's remaining');
              return; // Ignore scan if within cooldown period
            }

            // Mark as processing to prevent duplicate submissions
            isProcessing = true;
            const currentTime = Date.now();
            setLastScanTime(currentTime); // Save to localStorage for persistence

            // Populate the input field with scanned LRN
            const textInput = document.getElementById('text');
            if (textInput) {
              textInput.value = qrCodeMessage;
              console.log('[v0] Form submitting with QR code:', qrCodeMessage);
              
              // Stop scanner immediately to prevent multiple scans
              try {
                await html5QrcodeScanner.stop();
                scannerActive = false;
              } catch (error) {
                console.log('[v0] Error stopping scanner:', error);
              }
              
              // Auto-submit the form
              const form = document.getElementById('qr-form');
              if (form) {
                form.submit();
              }
            }
          },
          (errorMessage) => {
            // Silent error handling for continuous scanning
          }
        );

        scannerActive = true;
        isProcessing = false;
        console.log('[v0] Scanner started successfully');
      } catch (error) {
        console.error('[v0] Unable to start camera:', error.message);
        scannerActive = false;
      }
    }

    // Save scan time to localStorage
    function setLastScanTime(time) {
      localStorage.setItem(STORAGE_KEY, time.toString());
    }

    // Check if cooldown is still active
    function isCooldownActive() {
      const lastScanTime = getLastScanTime();
      const currentTime = Date.now();
      return (currentTime - lastScanTime) < SCAN_COOLDOWN;
    }

    // Initialize the QR Scanner
    function initializeQRScanner() {
      const textInput = document.getElementById('text');
      const switchCameraBtn = document.getElementById('switchCamera');
      
      if (!textInput) {
        console.error('Required input element not found for QR scanner');
        return;
      }

      // Check if cooldown is still active from previous page load
      if (isCooldownActive()) {
        const lastScanTime = getLastScanTime();
        const remainingTime = Math.ceil((SCAN_COOLDOWN - (Date.now() - lastScanTime)) / 1000);
        console.log('Cooldown active, scanner will be enabled in ' + remainingTime + ' seconds');
        
        // Wait for cooldown to expire before starting scanner
        setTimeout(startQRScanner, SCAN_COOLDOWN - (Date.now() - lastScanTime));
      } else {
        // Start scanner immediately if no cooldown
        startQRScanner();
      }

      // Switch camera button functionality
      if (switchCameraBtn) {
        switchCameraBtn.addEventListener('click', async () => {
          if (scannerActive && html5QrcodeScanner) {
            try {
              await html5QrcodeScanner.stop();
              scannerActive = false;
              isProcessing = false;
              setTimeout(startQRScanner, 500);
            } catch (error) {
              console.error('Unable to switch camera:', error);
            }
          }
        });
      }
    }

    // Start the QR Scanner
    async function startQRScanner() {
      try {
        console.log('Initializing Html5Qrcode scanner');
        html5QrcodeScanner = new Html5Qrcode('preview');
        
        const config = { 
          fps: 10, 
          qrbox: { width: 250, height: 250 }
        };
        
        await html5QrcodeScanner.start(
          { facingMode: 'environment' },
          config,
          async (qrCodeMessage) => {
            console.log('QR code scanned:', qrCodeMessage);
            
            // Prevent multiple simultaneous scans
            if (isProcessing) {
              console.log('Already processing a scan, ignoring this one');
              return;
            }

            // Check cooldown using localStorage
            if (isCooldownActive()) {
              const lastScanTime = getLastScanTime();
              const remainingTime = Math.ceil((SCAN_COOLDOWN - (Date.now() - lastScanTime)) / 1000);
              console.log('Scan cooldown active, ' + remainingTime + 's remaining');
              return; // Ignore scan if within cooldown period
            }

            // Mark as processing to prevent duplicate submissions
            isProcessing = true;
            const currentTime = Date.now();
            setLastScanTime(currentTime); // Save to localStorage for persistence

            // Populate the input field with scanned LRN
            const textInput = document.getElementById('text');
            if (textInput) {
              textInput.value = qrCodeMessage;
              console.log('Form submitting with QR code:', qrCodeMessage);
              
              // Stop scanner immediately to prevent multiple scans
              try {
                await html5QrcodeScanner.stop();
                scannerActive = false;
              } catch (error) {
                console.log('Error stopping scanner:', error);
              }
              
              // Auto-submit the form
              const form = document.getElementById('qr-form');
              if (form) {
                form.submit();
              }
            }
          },
          (errorMessage) => {
            // Silent error handling for continuous scanning
          }
        );

        scannerActive = true;
        isProcessing = false;
        console.log('Scanner started successfully');
      } catch (error) {
        console.error('Unable to start camera:', error.message);
        scannerActive = false;
      }
    }

    // Stop the QR Scanner
    async function stopQRScanner() {
      if (scannerActive && html5QrcodeScanner) {
        try {
          await html5QrcodeScanner.stop();
          scannerActive = false;
          isProcessing = false;
          console.log('Scanner stopped');
        } catch (error) {
          console.error('Error stopping scanner:', error.message);
        }
      }
    }

    // Initialize scanner when DOM is ready
    document.addEventListener('DOMContentLoaded', initializeQRScanner);

    // Stop scanner when page unloads
    window.addEventListener('beforeunload', stopQRScanner);
  </script>
</head>

<body>
    <!-- NAVBAR from student_form.php -->
    <?php
    include "../include/header.php";
    ?>

    <div class="page-title">
        <h2><i class="fas fa-qrcode"></i> QR Code Scanner</h2>
        <p>Scan student QR codes to record attendance.</p>
    </div>

    <div class="app-container">
        <main>
            <div class="card scanner-card">
                <div class="card-header">
                    <h2><i class="fas fa-camera"></i> QR Code Scanner</h2>
                </div>
                <div class="card-body">
                    <div class="scanner-container">
                        <div id="preview" style="width: 100%; height: 100%;"></div>
                        <div class="scanner-overlay">
                            <div class="scanner-guide"></div>
                        </div>
                        <div id="scanning-indicator">
                            <span class="pulse"></span>
                            <p>Scanning...</p>
                        </div>
                    </div>
                    <div class="camera-controls">
                        <button id="switchCamera" class="btn btn-primary">
                            <i class="fas fa-sync-alt"></i> Switch Camera
                        </button>
                    </div>
                </div>
                <div class="card-footer">
                    <form action="" method="post" id="qr-form">
                        <div class="form-group">
                            <label for="text">Scanned QR Code</label>
                            <div class="input-with-icon">
                                <i class="fas fa-qrcode"></i>
                                <input type="text" name="text" id="text" placeholder="Scan QR Code" readonly>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card result-card">
                <div class="card-header">
                    <h2><i class="fas fa-user-graduate"></i> Student Information</h2>
                </div>
                <div class="card-body">
                    <div id="result-placeholder" class="<?php echo isset($result) ? 'hidden' : ''; ?>">
                        <div class="empty-state">
                            <i class="fas fa-qrcode"></i>
                            <p>Scan a student's QR code to display information</p>
                        </div>
                    </div>

                    <?php
                    if (isset($result) && $result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                    ?>
                          <div class="student-card" id="card">
                            <div class="student-card-header">
                              <div class="school-badge">
                                <i class="fas fa-university"></i>
                              </div>
                              <h3>Student Identification</h3>
                              <p>School Year 2024-2025</p>
                            </div>

                            <div class="student-card-body">
                              <div class="student-photo">
                                <?php 
                                // Fix for profile picture display
                                $profilePicture = '';
                                
                                // Check if profile_picture field exists and is not empty
                                if (!empty($row['profile_picture'])) {
                                  // Extract just the filename from the full path if needed
                                  $profilePicture = basename($row['profile_picture']);
                                  
                                  // Check if the file exists in the uploads directory
                                  if (file_exists('uploads/' . $profilePicture)) {
                                    echo '<img src="uploads/' . htmlspecialchars($profilePicture) . '" alt="Student Photo">';
                                  } else {
                                    // If the file doesn't exist in uploads but the path might be complete
                                    if (file_exists($row['profile_picture'])) {
                                      echo '<img src="' . htmlspecialchars($row['profile_picture']) . '" alt="Student Photo">';
                                    } else {
                                      echo '<img src="assets/default-avatar.svg" alt="Default Photo">';
                                    }
                                  }
                                } else {
                                  echo '<img src="assets/default-avatar.svg" alt="Default Photo">';
                                }
                                ?>
                              </div>

                              <div class="student-details">
                                <div class="detail-row">
                                  <div class="detail-label">Full Name</div>
                                  <div class="detail-value">
                                    <?php echo htmlspecialchars($row['last_name'] . ', ' . $row['first_name'] . ' ' . $row['middle_name']); ?>
                                  </div>
                                </div>

                                <div class="detail-row">
                                  <div class="detail-label">Grade Level</div>
                                  <div class="detail-value" id="grade-level">
                                    Grade <?php echo htmlspecialchars($row['grade_level']); ?>
                                  </div>
                                </div>

                                <div class="detail-row">
                                  <div class="detail-label">Section</div>
                                  <div class="detail-value">
                                    <?php echo htmlspecialchars($row['section']); ?>
                                  </div>
                                </div>

                                <div class="detail-row">
                                  <div class="detail-label">LRN</div>
                                  <div class="detail-value lrn-value">
                                    <?php echo htmlspecialchars($row['lrn']); ?>
                                  </div>
                                </div>
                              </div>
                            </div>

                            <div class="student-card-footer">
                              <div class="authorized-badge">
                                <i class="fas fa-check-circle"></i> Authorized Student
                              </div>
                            </div>
                          </div>

                          <div class="attendance-confirmation">
                            <div class="attendance-icon">
                              <i class="fas fa-clipboard-check"></i>
                            </div>
                            <div class="attendance-details">
                              <h4>Attendance Recorded</h4>
                              <p><?php echo date('F j, Y, g:i a'); ?></p>
                            </div>
                          </div>
                        <?php
                        }
                      } else if (isset($result)) {
                        ?>
                        <div class="error-notification">
                          <div class="error-icon">
                            <i class="fas fa-exclamation-triangle"></i>
                          </div>
                          <div class="error-message">
                            <h4>No Record Found</h4>
                            <p>The scanned QR code does not match any student in our database.</p>
                          </div>
                        </div>
                      <?php
                      }
                      ?>
                </div>
            </div>
        </main>

        <div id="notification" class="notification">
            <div class="notification-content">
                <i class="fas fa-check-circle"></i>
                <span>Attendance recorded successfully!</span>
            </div>
        </div>
    </div>

    <?php include '../include/footer.php'; ?>
</body>
<script>
  let card = document.getElementById('card');
  let gradelevel = document.getElementById('grade-level');
    switch (gradelevel.textContent.trim()) {
      case 'Grade 7':
        card.style.backgroundColor = '#103d20'; // Green
        break;
      case 'Grade 8':
        card.style.backgroundColor = '#d8a217'; // Yellow
        break;
      case 'Grade 9':
        card.style.backgroundColor = '#9a0000'; // Red
        break;
      case 'Grade 10':
        card.style.backgroundColor = '#001454'; // Blue
        break;
      case 'Grade 11':
        card.style.backgroundColor = '#3c0058'; // Purple
        break;
      case 'Grade 12':
        card.style.backgroundColor = '#520a0a'; // Pink
        break;
      default:
        card.style.backgroundColor = '#103d20'; // for unknown grade levels  
    }
</script>
</html>
