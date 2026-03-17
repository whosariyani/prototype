<?php
include "include/db_conn.php";
session_start();
?>


<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Login | Entrance Monitoring System</title>
    <link rel="icon" type="image/x-icon" href="pics/logos/Lagro_High_School_logo.png">
    <style>
      /* Reset and base styles */
      * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
      }

      body {
        font-family: 'Inter', system-ui, -apple-system, 'Segoe UI', Roboto, 'Helvetica Neue', Arial;
        color: #333;
        line-height: 1.6;
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 100vh;
        padding: 15px;
      }

      .logo img {
        width: 60px;
        height: 60px;
        margin: 0 auto 10px auto;
        display: block;
      }

      /* Main container */
      .container {
        background: white;
        border-radius: 12px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        overflow: hidden;
        width: 100%;
        max-width: 450px;
        position: relative;
      }

      /* Header with toggle */
      .header {
        background: #1b5e20;
        color: white;
        padding: 25px 20px;
        text-align: center;
        position: relative;
      }

      .header h1 {
        font-size: clamp(20px, 5vw, 28px);
        margin-bottom: 15px;
        font-weight: 700;
      }

      .toggle-buttons {
        display: flex;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 25px;
        overflow: hidden;
        margin: 0 auto;
        gap: 0;
        max-width: 100%;
      }

      .toggle-btn {
        flex: 1;
        padding: 12px 15px;
        background: none;
        border: none;
        color: white;
        cursor: pointer;
        font-size: clamp(14px, 3vw, 16px);
        transition: background 0.3s ease;
        white-space: nowrap;
      }

      .toggle-btn.active {
        background: rgba(255, 255, 255, 0.25);
      }

      .toggle-btn:hover {
        background: rgba(255, 255, 255, 0.15);
      }

      /* Forms */
      .form-container {
        padding: 25px 20px;
        max-height: calc(100vh - 200px);
        overflow-y: auto;
      }

      .form-container.active {
        display: block;
      }

      .form-group {
        margin-bottom: 18px;
      }

      label {
        display: block;
        margin-bottom: 6px;
        font-weight: 600;
        color: #1b5e20;
        font-size: clamp(14px, 2.5vw, 16px);
      }

      input[type="text"],
      input[type="email"],
      input[type="password"],
      input[type="number"] {
        width: 100%;
        padding: 11px 12px;
        border: 2px solid #e0e0e0;
        border-radius: 8px;
        font-size: clamp(14px, 2.5vw, 16px);
        transition: border-color 0.3s ease;
      }

      input:focus {
        outline: none;
        border-color: #1b5e20;
      }

      .submit-btn {
        width: 100%;
        padding: 12px;
        background: #1b5e20;
        color: white;
        border: none;
        border-radius: 8px;
        font-size: clamp(14px, 2.5vw, 16px);
        font-weight: 600;
        cursor: pointer;
        transition: background 0.3s ease, transform 0.2s ease;
        margin-top: 12px;
      }

      .submit-btn:hover {
        background: #145a32;
        transform: translateY(-2px);
      }

      .submit-btn:active {
        transform: translateY(0);
      }

      .submit-btn:disabled {
        background: #ccc;
        cursor: not-allowed;
        transform: none;
      }

      /* Error messages */
      .error {
        color: #d32f2f;
        font-size: clamp(12px, 2vw, 14px);
        margin-top: 5px;
        display: none;
      }

      /* Registration section */
      .reg {
        margin-top: 18px;
        text-align: center;
      }

      #reg-msg {
        text-align: center;
        font-size: clamp(13px, 2.5vw, 15px);
      }

      #reg-msg a {
        color: #1b5e20;
        text-decoration: none;
        font-weight: 600;
        transition: color 0.3s ease;
      }

      #reg-msg a:hover {
        color: #145a32;
        text-decoration: underline;
      }

      #employee-login {
        display: none;
      }

      /* Tablet and Landscape adjustments */
      @media (min-width: 481px) {
        body {
          padding: 20px;
        }

        .container {
          max-width: 500px;
        }

        .logo img {
          width: 70px;
          height: 70px;
        }

        .header {
          padding: 30px 25px;
        }

        .form-container {
          padding: 30px 25px;
        }
      }

      /* Desktop adjustments */
      @media (min-width: 768px) {
        body {
          padding: 30px;
        }

        .container {
          max-width: 450px;
        }

        .logo img {
          width: 75px;
          height: 75px;
        }

        .header {
          padding: 35px 30px;
        }

        .header h1 {
          font-size: 28px;
          margin-bottom: 18px;
        }

        .toggle-btn {
          padding: 13px 20px;
          font-size: 16px;
        }

        label {
          font-size: 16px;
        }

        input[type="text"],
        input[type="email"],
        input[type="password"],
        input[type="number"] {
          padding: 13px 14px;
          font-size: 16px;
        }

        .form-container {
          padding: 35px 30px;
        }

        .form-group {
          margin-bottom: 22px;
        }

        .submit-btn {
          padding: 14px;
          font-size: 16px;
          margin-top: 15px;
        }

        .reg {
          margin-top: 22px;
        }

        #reg-msg {
          font-size: 15px;
        }
      }

      /* Large desktop */
      @media (min-width: 1024px) {
        .container {
          max-width: 480px;
        }

        .header h1 {
          font-size: 32px;
        }
      }

      /* Extra large desktop */
      @media (min-width: 1440px) {
        body {
          padding: 40px;
        }

        .container {
          max-width: 500px;
        }

        .header {
          padding: 40px 35px;
        }

        .form-container {
          padding: 40px 35px;
        }
      }

      /* Landscape mode (small devices) */
      @media (max-height: 600px) and (orientation: landscape) {
        body {
          min-height: auto;
          padding: 10px;
        }

        .container {
          max-height: 95vh;
        }

        .form-container {
          max-height: 70vh;
          padding: 20px;
        }

        .header {
          padding: 15px 20px;
        }

        .header h1 {
          margin-bottom: 8px;
        }

        .form-group {
          margin-bottom: 12px;
        }

        label {
          margin-bottom: 3px;
        }
      }
    </style>
  </head>
  <body>
    <div class="container">
      <div class="header">
        <div class="logo">
          <img src="Pics/Logos/Lagro_High_School_logo.png" alt="LHS" />
        </div>
        <h1>Lagro High School</h1>
        <div class="toggle-buttons">
          <button class="toggle-btn active" id="btn1" onclick="switch1()">Student</button>
          <button class="toggle-btn" id="btn2" onclick="switch2()">Employee</button>
        </div>
      </div>

      <div id="login-form" class="form-container">
        <!-- Student Login Form -->
        <form id="loginForm" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
          <div id="student-login">
            <div class="form-group">
              <label for="studentLoginLRN">Learner's Reference Number (LRN)</label>
              <input type="number" id="studentLoginLRN" name="studentLoginLRN" oninput="if(this.value.length > 12) this.value = this.value.slice(0, 12);"  required />
              <div class="error" id="studentLoginLRN-Error"></div>
            </div>
            <div class="form-group">
              <label for="studentLoginPword">Password</label>
              <input type="password" id="studentLoginPword" name="studentLoginPword" required>
              <div class="error" id="studentLoginPword-Error"></div>
            </div>
            <button type="submit" class="submit-btn" id="studentLogin-submit" name="studentLogin-submit">Login</button>
            <div class="reg">
              <p id="reg-msg">Don't have an account? <a href="register.php">Register</a></p>
            </div>
          </div>
        </form>

        <!-- Employee Login Form -->
        <form id="loginForm" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
          <div id="employee-login">
            <div class="form-group">
              <label for="employeeLoginID">Employee ID</label>
              <input type="number" id="employeeLoginID" name="employeeLoginID" oninput="if(this.value.length > 12) this.value = this.value.slice(0, 12);" required>
              <div class="error" id="employeeLoginID-Error"></div>
            </div>
            <div class="form-group">
              <label for="employeeLoginPword">Password</label>
              <input type="password" id="employeeLoginPword" name="employeeLoginPword"  required>
              <div class="error" id="employeeLoginPword-Error"></div>
            </div>
            <button type="submit" class="submit-btn" id="employeeLogin-submit" name="employeeLogin-submit">Login</button>
            <div class="reg">
              <p id="reg-msg">Don't have an account? <a href="register.php">Register</a></p>
            </div>
          </div>
        </form>
      </div>
    </div>
  </body>

  <script>
    // Toggle between Student and Employee login forms
    function switch1() {
      const studentLogin = document.getElementById("student-login");
      const employeeLogin = document.getElementById("employee-login");
      const btn1 = document.getElementById("btn1");
      const btn2 = document.getElementById("btn2");

      studentLogin.style.display = "block";
      btn1.classList.add("active");
      employeeLogin.style.display = "none";
      btn2.classList.remove("active");
    }

    function switch2() {
      const studentLogin = document.getElementById("student-login");
      const employeeLogin = document.getElementById("employee-login");
      const btn1 = document.getElementById("btn1");
      const btn2 = document.getElementById("btn2");

      studentLogin.style.display = "none";
      btn1.classList.remove("active");
      employeeLogin.style.display = "block";
      btn2.classList.add("active");
    }

    // Optimize for touch devices
    const toggleBtns = document.querySelectorAll(".toggle-btn");
    toggleBtns.forEach(btn => {
      btn.addEventListener("touchstart", function() {
        this.style.opacity = "0.8";
      });
      btn.addEventListener("touchend", function() {
        this.style.opacity = "1";
      });
    });

    // Set input to only accept numbers for ID/LRN fields
    const numberInputs = document.querySelectorAll("input[type='number']");
    numberInputs.forEach(input => {
      input.addEventListener("keypress", function(e) {
        if (!/[0-9]/.test(e.key)) {
          e.preventDefault();
        }
      });
    });
  </script>

  <?php
    if (isset($_POST["studentLogin-submit"])) {
    $lrn = $_POST["studentLoginLRN"];
    $lrn_password = $_POST["studentLoginPword"];

    $stmt = $conn->prepare("SELECT lrn, account_password, first_name FROM student_info WHERE lrn = ?");
    $stmt->bind_param("i", $lrn);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows === 1) {
        $user = $result->fetch_assoc();

        // Check if password is hashed (starts with $2y$) or plain text
        if (password_verify($lrn_password, $user['account_password']) || $lrn_password === $user['account_password']) {
            $_SESSION['lrn'] = $user['lrn'];
            $_SESSION['first_name'] = $user['first_name'];
            echo "<script>alert('Successfully Login.'); window.location.href='webpages/about-us.php'</script>";
            exit();
        } else {
            echo "<script>alert('Invalid username or password.');</script>";
        }
    } else {
        echo "<script>alert('Account does not exist.');</script>";
    }
    $stmt->close();
}

if (isset($_POST["employeeLogin-submit"])) {
    $employee_id = $_POST["employeeLoginID"];
    $emId_password = $_POST["employeeLoginPword"];

    $stmt = $conn->prepare("SELECT employee_id, account_password, first_name FROM employee_info WHERE employee_id = ?");
    $stmt->bind_param("i", $employee_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows === 1) {
        $user = $result->fetch_assoc();

        // Check if password is hashed (starts with $2y$) or plain text
        if (password_verify($emId_password, $user['account_password']) || $emId_password === $user['account_password']) {
            $_SESSION['employee_id'] = $user['employee_id'];
            $_SESSION['first_name'] = $user['first_name'];
            echo "<script>alert('Successfully Login.'); window.location.href='webpages/about-us.php'</script>";
            exit();
        } else {
            echo "<script>alert('Invalid username or password.');</script>";
        }
    } else {
        echo "<script>alert('Account does not exist.');</script>";
    }
    $stmt->close();
}
  ?>

</html>
