<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport"
              content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <link rel="stylesheet" href="<?= $GLOBALS['routing']->getRoute('style-login') ?>">
        <link rel="shortcut icon" href="<?= $GLOBALS['routing']->getRoute('image-favicon') ?>">
        <title>Login</title>
    </head>

    <body>
    <div>
        <form action="<?= $GLOBALS['routing']->getRoute('service-auth') ?>">
            <label>
                <p>Username:</p>
                <input type="email" id="username_input">
            </label>

            <label>
                <p>Password:</p>
                <input type="password" id="password_input">
            </label>

            <p><a href="">Terms of use</a></p>

            <label>
                <button type="submit" id="submit_button">Log in</button>
            </label>
        </form>
    </div>
    </body>
</html>