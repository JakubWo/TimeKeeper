<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <!--    <meta name="viewport"-->
    <!--          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">-->
    <!--    <meta http-equiv="X-UA-Compatible" content="ie=edge">-->
    <link rel="stylesheet" href="<?= $GLOBALS['routingService']->getRoute('style-login') ?>">
    <link rel="shortcut icon" href="<?= $GLOBALS['routingService']->getRoute('image-favicon') ?>">
    <script src="<?= $GLOBALS['routingService']->getRoute('js-login') ?>"></script>
    <title>Login</title>
</head>

<body>
<div>
    <form id="login_form" method="post" action="\login_authorization" autocomplete="off">
        <p>Email:</p>
        <label>
            <input type="email" id="email_input" value="admin@mail.pl" name="email">
        </label>
        <p id="error_email"></p>

        <p>Password:</p>
        <label>
            <input type="password" id="password_input" value="admin" name="password">
        </label>
        <p id="error_password"></p>

        <p id="error_login">
            <?php
            echo $_SESSION['loginResult'];
            unset($_SESSION['loginResult']);
            ?>
        </p>
        <label>
            <button type="button" onclick="checkLoginParams()" id="submit_button">Log in</button>
        </label>

        <p><a href="">Terms of use</a></p>

    </form>
</div>

<footer>

</footer>
</body>
</html>