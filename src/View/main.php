<?php require($GLOBALS['routingService']->getRoute('listener-session_checker')); ?>
<!DOCTYPE HTML>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>TimeKeeper</title>
    <link rel="shortcut icon" href="<?= $GLOBALS['routingService']->getRoute('image-favicon') ?>">
    <script src="<?= $GLOBALS['routingService']->getRoute('vendor-jquery') ?>"></script>

    <link rel="stylesheet" href="<?= $GLOBALS['routingService']->getRoute('style-main') ?>">
    <script src="<?= $GLOBALS['routingService']->getRoute('js-main') ?>"></script>

    <link rel="stylesheet" href="<?= $GLOBALS['routingService']->getRoute('style-console') ?>">
    <script src="<?= $GLOBALS['routingService']->getRoute('js-console') ?>"></script>

</head>

<body>
<button type="button" id="logout">log out</button>
<?php //var_dump($_SESSION) ?>

<?php readfile($GLOBALS['routingService']->getRoute('view-console')) ?>

</body>
</html>