<?php
    $error = null;
    if(isset($_COOKIE['error'])) {
        $error = unserialize($_COOKIE['error']);
    }
?>
<!DOCTYPE html>
<html lang="pl-PL">
    <head>
        <title><?= $error['title'] ?? 'Unknown error' ?></title>
        <link rel="stylesheet" href="<?= $GLOBALS['routingService']->getRoute('style-error') ?>">
        <link rel="shortcut icon" href="<?= $GLOBALS['routingService']->getRoute('image-favicon') ?>">
        <script src="<?= $GLOBALS['routingService']->getRoute('js-error') ?>"></script>
    </head>

    <body>
        <button onclick="redirect_to_main()">Back to main page</button>

        <header class="error_format_box">
            <h1><?= $error['title'] ?? 'Unknown error' ?></h1>
        </header>

        <div class="error_format_box">
            <h2> <?= $error['message'] ?? 'Empty error stack. :('?> </h2>
            <p> <?= $error['details'] ?> </p>
        </div>
    </body>
</html>

