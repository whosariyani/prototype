<?php

session_start();
require_once "db_conn.php";

if (isset($_POST['register'])) {
    $LRN = $_POST['LRN'];
    $Fname = $_POST['Fname'];
    $Mname = $_POST['Mname'];
    $Lname = $_POST['Lname'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $checkLRN = $conn->query("SELECT LRN FROM users_student WHERE LRN = '$LRN'");
    if ($checkLRN->num_rows > 0) {
        $_SESSION['register_error'] = 'LRN is already registered';
        $_SESSION['active_form'] = 'registered';
    } else {
        $conn->query("INSERT INTO users_student(LRN, Fname, Mname, Lname, password) VALUES ('$LRN', '$Fname', '$Mname', '$Lname', '$password')");
    }

    header('Location: login.php');
    exit();
}

if (isset($_POST['login'])) {
    $LRN = $_POST['LRN'];
    $password = $_POST['password'];

    $result = $conn->query("SELECT * FROM users_student WHERE LRN = '$LRN'");
    if ($result->num_rows > 0) {
        $username = $result->fetch_assoc();
        if (password_verify($password, $username['password'])) {
            $_SESSION['name'] = $username['name'];
            $_SESSION['LRN'] = $username['LRN'];
        }
    }

    $_SESSION['login_error'] = 'Incorrect LRN or password';
    $_SESSION['active_form'] = 'login';
    header('Location: attendance-log.php');
    exit();
}