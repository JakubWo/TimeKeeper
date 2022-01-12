<!DOCTYPE html>
<html lang="en">

<head>
    <?php require($GLOBALS['routingService']->getRoute('view-head')) ?>
    <title>Login</title>
    <link rel="stylesheet" href="<?= $GLOBALS['routingService']->getRoute('style-login') ?>">
    <script src="<?= $GLOBALS['routingService']->getRoute('js-login') ?>"></script>
</head>

<body>
<div>
    <form id="login_form" method="post" autocomplete="off">
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

        <p id="error_login"></p>
        <label>
            <button type="button" id="submit_button">Log in</button>
        </label>

        <p><a href="">Terms of use</a></p>

    </form>
</div>

<footer>

</footer>
</body>
</html>