<?php
//@TODO: dark motive button
if (!isset($_SESSION['user_id'])) {
    header('Location: /');
}
?>

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

<div class="box">
    <div class="console">
        <?php readfile($GLOBALS['routingService']->getRoute('view-console')) ?>
    </div>
    <div class="management-panel">

    </div>
    <div class="user_panel">
        <button type="button" id="logout">log out</button>
        <br>
    </div>
</div>

<div class="workday">
    <table class="headers">
        <tr>
            <th class="tooltip"><span class="tooltiptext"></span>id</th>
            <th class="tooltip"><span class="tooltiptext"></span>date</th>
            <th class="tooltip">
                <span class="tooltiptext">Type of the workday,<br>Possible values:<br>IRREGULAR<br>REGULAR</span>type
            </th>
            <th class="tooltip"><span class="tooltiptext"></span>notes</th>
            <th class="tooltip"><span class="tooltiptext"></span>is_accepted</th>
        </tr>
    </table>
</div>

<div class="load_more">
    <a><span class="bottom"></span></a>
</div>

</body>
</html>