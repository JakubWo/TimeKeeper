<!DOCTYPE HTML>
<html lang="en">
<head>
    <?php require_once($GLOBALS['routingService']->getRoute('view-head')) ?>
    <title>TimeKeeper</title>

    <link rel="stylesheet" href="<?= $GLOBALS['routingService']->getRoute('style-main') ?>">
    <script src="<?= $GLOBALS['routingService']->getRoute('js-main') ?>"></script>

    <link rel="stylesheet" href="<?= $GLOBALS['routingService']->getRoute('style-console') ?>">
    <script src="<?= $GLOBALS['routingService']->getRoute('js-console') ?>"></script>
</head>

<body>
<button type="button" id="logout">log out</button>

<?php readfile($GLOBALS['routingService']->getRoute('view-console')) ?>

<br><br><br>
<?php include_once($GLOBALS['routingService']->getRoute('view-workday')) ?>

</body>
</html>