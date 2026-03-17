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
    <title>Login Page</title>
    <style>
    </style>
</head>
<body>

    <div class="Login-student <?= isActiveForm('login', $activeForm)?>">
        <h2>Login</h2>
        <?= showError($errors['login']); ?>
        <form action="login_register.php" method="post">
            LRN: <input type="number" name="LRN" min="0" required><br>
            Password: <input type="passoword" name="password" required><br>
            <button type="submit" name="login">Login</button>
            <p>Don't have an account? <a href="register.php">Register</a></p>
        </form>
    </div>

</body>
</html>