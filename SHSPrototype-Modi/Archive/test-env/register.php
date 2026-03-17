<?php

session_start();

$errors = [
    'login' => $_SESSION['login_error'] ?? '',
    'register' => $_SESSION['register_error'] ?? ''
];

$activeForm = $_SESSION['active_form'] ?? 'login';

session_unset();

function showError($error) {
    return !empty($error) ? "<p class='error-message'>$error</p>" : '';
}

function isActiveForm($forName, $activeForm) {
    return $forName === $activeForm ? 'active' : '';
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Page</title>
    <style>
    </style>
</head>
<body>

    <div class="Register-student <?= isActiveForm('register', $activeForm)?>">
        <h2>Register</h2>
        <?= showError($errors['register']); ?>
        <form action="login_register.php" method="post">
            LRN: <input type="number" name="LRN" min="0" required><br>
            First name: <input type="text" name="Fname" id="Fname" required>
            Middle name: <input type="text" name="Mname">
            Last name:<input type="text" name="Lname" required>
            Extension name: <input type="text" name="suffix_name"><br>
            Password: <input type="passoword" name="password" required><br>
            <button type="submit" name="register">Register</button><br>
            <p>Already have an account? <a href="login.php">Login</a></p>
        </form>
    </div>

</body>
</html>