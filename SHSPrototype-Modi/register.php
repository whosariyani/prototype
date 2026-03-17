<?php
session_start();
include_once "include/db_conn.php";
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Register | Entrance Monitoring System</title>
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
      background-color: #f5f5f5;
      color: #333;
      line-height: 1.6;
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 100vh;
      padding: 20px;
    }

    .logo img {
      width: 70px;
      height: 70px;
      justify-content: center;
      align-items: center;
      margin-right: 15px;
      color: white;
    }

    /* Main container */
    .container {
      background: white;
      border-radius: 12px;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
      overflow: scroll;
      overflow-x: hidden;
      scrollbar-width: thin;
      height: 78vh;
      width: 40vw;
      position: relative;
    }

    /* Header with toggle */
    .header {
      background: #1b5e20;
      /* Dark green */
      color: white;
      padding: 20px;
      text-align: center;
      position: relative;
    }

    .header h1 {
      font-size: 24px;
      margin-bottom: 10px;
    }

    .toggle-buttons {
      display: flex;
      background: rgba(255, 255, 255, 0.1);
      border-radius: 25px;
      overflow: hidden;
      margin: 0 20px;
    }

    .toggle-btn {
      flex: 1;
      padding: 10px 20px;
      background: none;
      border: none;
      color: white;
      cursor: pointer;
      font-size: 16px;
      transition: background 0.3s ease;
    }

    .toggle-btn a {
      text-decoration: none;
      color: white;
      padding: 50px;

    }

    .toggle-btn.active {
      background: rgba(255, 255, 255, 0.2);
    }

    .toggle-btn:hover {
      background: rgba(255, 255, 255, 0.15);
    }

    /* Forms */
    .form-container {
      padding: 30px 20px 5px 20px;
    }

    .form-container.active {
      display: block;
    }

    .form-group {
      margin-bottom: 20px;
    }

    .form-group-group {
      display: flex;
      margin-bottom: 20px;
    }

    label {
      display: block;
      margin-bottom: 5px;
      font-weight: 600;
      color: #1b5e20;
      /* Dark green */
    }

    input[type="text"],
    input[type="email"],
    input[type="password"],
    input[type="number"],
    select {
      width: 100%;
      padding: 12px;
      border: 2px solid #e0e0e0;
      border-radius: 8px;
      font-size: 16px;
      transition: border-color 0.3s ease;
    }

    input:focus {
      outline: none;
      border-color: #1b5e20;
      /* Dark green */
    }

    .submit-btn {
      width: 100%;
      padding: 12px;
      background: #1b5e20;
      /* Dark green */
      color: white;
      border: none;
      border-radius: 8px;
      font-size: 16px;
      cursor: pointer;
      transition: background 0.3s ease;
      margin-top: 10px;
    }

    .submit-btn:hover {
      background: #145a32;
      /* Slightly lighter dark green */
    }

    .submit-btn:disabled {
      background: #ccc;
      cursor: not-allowed;
    }

    /* Error messages */
    .error {
      color: #d32f2f;
      font-size: 14px;
      margin-top: 5px;
      display: none;
    }

    /* Link to toggle form */
    .toggle-link {
      text-align: center;
      margin-top: 20px;
      color: #1b5e20;
    }

    .toggle-link a {
      color: #1b5e20;
      text-decoration: none;
      font-weight: 600;
    }

    .toggle-link a:hover {
      text-decoration: underline;
    }

    .toggle {
      text-decoration: none;
      color: white;
      padding: 10px 110px;
    }

    #employee-register {
      display: none;
    }

    .reg {
      margin-top: 20px;
    }

    #reg-msg {
      text-align: center;

    }
  </style>
</head>

<body>
  <div class="container">
    <div class="header">
      <div class="logo">
        <img src="Pics/Logos/Lagro_High_School_logo.png" alt="LHS">
      </div>
      <h1>Lagro High School</h1>
      <div class="toggle-buttons">
        <button class="toggle-btn active" id="btn1" onclick="switch1()">Student</button>
        <button class="toggle-btn" id="btn2" onclick="switch2()">Employee</button>
      </div>
    </div>


    <div id="login-form" class="form-container">
      <!-- Student Register Form -->
      <form id="registerForm" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <div id="student-register">
          <div class="form-group-group">
            <div class="name-field">
              <label for="studentRegFname">First Name</label>
              <input type="text" name="studentRegFname" required>
            </div>

            <div class="name-field" style="margin-left: 8px; margin-right: 8px;">
              <label for="studentRegMname">Middle Name</label>
              <input type="text" name="studentRegMname">
            </div>

            <div class="name-field">
              <label for="studentRegLname">Last Name</label>
              <input type="text" name="studentRegLname" required>
            </div>
          </div>
        
          <div class="form-group">
            <label for="studentRegLRN">Learner's Reference Number (LRN)</label>
            <input type="number" name="studentRegLRN" oninput="if(this.value.length > 12) this.value = this.value.slice(0, 12);" required min="0">
            <div class="error" id="studentRegLRN-Error"></div>
          </div>

          <div class="form-group">
            <label for="studentRegPword">Password</label>
            <input type="password" name="studentRegPword" required>
            <div class="error" id="studentRegPword-Error"></div>
          </div>

          <button type="submit" class="submit-btn" name="studentReg-submit">Register</button>
          <div class="reg">
            <p id="reg-msg">Already have an account? <a href="login.php">Login</a></p>
          </div>
        </div>
      </form>

      <!-- Employee Register Form -->
      <form id="registerForm" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <div id="employee-register">

          <div class="form-group-group">
            <div class="name-field">
              <label for="employeeRegFname">First Name</label>
              <input type="text" name="employeeRegFname" required>
            </div>

            <div class="name-field" style="margin-left: 8px; margin-right: 8px;">
              <label for="employeeRegMname">Middle Name</label>
              <input type="text" name="employeeRegMname">
            </div>

            <div class="name-field">
              <label for="employeeRegLname">Last Name</label>
              <input type="text" name="employeeRegLname" required>
            </div>
          </div>

          <div class="form-group">
            <label for="employeeRegID">Employee ID</label>
            <input type="number" name="employeeRegID" oninput="if(this.value.length > 12) this.value = this.value.slice(0, 12);" required min="0">
            <div class="error" id="employeeRegID-Error"></div>
          </div>

          <div class="form-group">
            <label for="employeeType">Type of Employee</label>
            <select name="employeeType" id="employeeType" value="Select">
              <option  value="" disabled selected>Select a role</option>
              <option value="administrative">Administrative</option>
              <option value="cafeteria">Cafeteria</option>
              <option value="it_Support">IT Support</option>
              <option value="librarian">Librarian</option>
              <option value="maintenance">Maintenance</option>
              <option value="security_personnel">Security Personnel</option>
              <option value="nurse">Nurse</option>
              <option value="teacher">Teacher</option>
              <option value="other">Other</option>
            </select>
            <div class="error" id="employeeRegID-Error"></div>
          </div>

          <div class="form-group">
            <label for="employeeRegPword">Password</label>
            <input type="password" name="employeeRegPword" required>
            <div class="error" id="employeeRegPword-Error"></div>
          </div>

          <button type="submit" class="submit-btn" name="employeeReg-submit">Register</button>
          <div class="reg">
            <p id="reg-msg">Already have an account? <a href="login.php">Login</a></p>
          </div>
        </div>
      </form>
      <div class="toggle-link"></div>
    </div>
</body>

    <script>
      function switch1() {
        document.getElementById("student-register").style.display = "block";
        document.getElementById("btn1").classList.add("active");
        document.getElementById("employee-register").style.display = "none";
        document.getElementById("btn2").classList.remove("active");
      }

      function switch2() {
        document.getElementById("student-register").style.display = "none";
        document.getElementById("btn1").classList.remove("active");
        document.getElementById("employee-register").style.display = "block";
        document.getElementById("btn2").classList.add("active");
      }
    </script>

    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
      if (isset($_POST['studentReg-submit'])) {
        $studentRegLRN = $_POST['studentRegLRN'];
        $studentRegFname = $_POST['studentRegFname'];
        $studentRegMname = $_POST['studentRegMname'];
        $studentRegLname = $_POST['studentRegLname'];
        $studentRegPword = $_POST['studentRegPword'];

        $checkLRN_ifExist = "SELECT * FROM student_lrn_list WHERE LRN='$studentRegLRN'"; // Check LRN if it exist in the database
        $LRNcheckAccount_ifExist = "SELECT * FROM student_info WHERE lrn='$studentRegLRN'"; // Check if LRN was already registered with an account
        $LRNresult_ifExist = $conn->query($checkLRN_ifExist);
        $LRNAccountResult_ifExist = $conn->query($LRNcheckAccount_ifExist);

        if ($LRNresult_ifExist->num_rows == 0) {
          echo "<script> alert('LRN not found. Please enter a valid LRN.'); </script>";
          die();
        } else if ($LRNAccountResult_ifExist->num_rows > 0) {
          echo "<script> alert('An account was already registered with the same LRN.'); </script>";
          die();
        }
        
        else {
          // $h_studentRegPword = password_hash($studentRegPword, PASSWORD_BCRYPT);

          $sql = "INSERT INTO student_info (first_name, middle_name, last_name, lrn,account_password)
                  VALUES ('$studentRegFname', '$studentRegMname', '$studentRegLname', '$studentRegLRN', '$studentRegPword')";
          if ($conn->query($sql) === TRUE) {
            echo "<script> alert('Student LRN registered successfully!');</script>";
            exit();
          } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
          }
        }
      }
      
      else if (isset($_POST['employeeReg-submit'])) {
        $employeeRegID = $_POST['employeeRegID'];
        $employeeRegType = $_POST['employeeType'];
        $employeeRegFname = $_POST['employeeRegFname'];
        $employeeRegMname = $_POST['employeeRegMname'];
        $employeeRegLname = $_POST['employeeRegLname'];
        $employeeRegPword = $_POST['employeeRegPword'];

        $checkEmID_ifExist = "SELECT * FROM employee_id_list WHERE employee_id='$employeeRegID'";
        $emIDcheckAccount_ifExist = "SELECT * FROM employee_info WHERE employee_id='$employeeRegID'";
        $emIDresult_ifExist = $conn->query($checkEmID_ifExist);
        $emIDAccountResult_ifExist = $conn->query($emIDcheckAccount_ifExist);

        if ($emIDresult_ifExist->num_rows == 0) {
          echo "<script> alert('Employee ID not found. Please enter a valid employee ID.'); </script>";
          die();
        } else if ($emIDAccountResult_ifExist->num_rows > 0) {
          echo "<script> alert('An account was already registered with the same employee ID.'); </script>";
          die();
        }
        
        else {
          $h_employeeRegPword = password_hash($employeeRegPword, PASSWORD_BCRYPT);

          $sql = "INSERT INTO employee_info (first_name, middle_name, last_name, employee_type, employee_id, account_password)
                  VALUES ( '$employeeRegFname', '$employeeRegMname', '$employeeRegLname', '$employeeRegType','$employeeRegID', '$employeeRegPword')";
          if ($conn->query($sql) === TRUE) {
            echo "<script> alert('Employee ID registered successfully!'); </script>";
            exit();
          } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
          }
        }
      }
    }
    ?>

</html>