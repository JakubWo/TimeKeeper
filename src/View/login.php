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

<!-- CSS breaks it -->
<script type="text/javascript" id="cookieinfo"
        src="//cookieinfoscript.com/js/cookieinfo.min.js"
        data-bg="#645862"
        data-fg="#FFFFFF"
        data-link="#F1D600"
        data-cookie="CookieInfoScript"
        data-text-align="left"
        data-close-text="Got it!">
</script>
</body>
</html>