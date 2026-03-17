<?php
    session_start();
    if (isset($_POST['logout'])) {
        session_unset();
        session_destroy();
        header("Location: ../login.php");
        exit();
    }

    include_once "../include/db_conn.php";

    if (!isset($_SESSION['lrn']) && !isset($_SESSION['employee_id'])) {
        header("Location: ../login.php");
        exit();
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ID Registration | Entrance Monitoring System</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            text-align: center;
        }

        main {
            display: flex;
            justify-content: space-evenly;
        }
    </style>
</head>
<body>
    <header>
        <h1>ID Registration Page | Entrance Monitoring System</h1>
    </header>

    <main>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
            <section class="studentLRN">
                    <label for="studentLRN">Student LRN: </label>
                    <input type="number" name="studentLRN" id="studentLRN">
                    <input type="submit" value="Submit" name="submit-studentLRN">
            </section>
        </form>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
            <section class="employeeID">
                    <label for="employeeID">Employee ID: </label>
                    <input type="number" name="employeeID" id="employeeID">
                    <input type="submit" value="Submit" name="submit-employeeID">
            </section>
        </form>
    </main>

    <?php
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $studentLRN = $_POST['studentLRN'];
            $employeeID = $_POST['employeeID'];

            if (isset($_POST['submit-studentLRN'])) {
                $sql ="INSERT INTO student_lrn_list (LRN)
                VALUES ('$studentLRN')";
        
                if ($conn->query($sql) === TRUE) {
                    echo "<script> alert('Student LRN registered successfully!'); window.location.href='ID-registration.admin.php'; </script>";
                    exit();
                } else {
                    echo "Error: " . $sql . "<br>" . $conn->error;
                }
            }

            else if (isset($_POST['submit-employeeID'])) {
                $sql ="INSERT INTO employee_id_list (employee_id)
                VALUES ('$employeeID')";
        
                if ($conn->query($sql) === TRUE) {
                    echo "<script> alert('Employee ID registered successfully!'); window.location.href='ID-registration.admin.php'; </script>";
                    exit();
                } else {
                    echo "Error: " . $sql . "<br>" . $conn->error;
                }
            }
        }
    ?>
</body>

</html>