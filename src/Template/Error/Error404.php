<?php
    if(empty($_SESSION['error'])) $_SESSION['error'] = "Error 404";
?>
<!DOCTYPE html>
<html lang="pl-PL">

    <head>
        <title>
            <?= $_SESSION['error'] ?>
        </title>
        <link rel="stylesheet" href="<?= $GLOBALS['routing']->getRoute('style-error') ?>">
        <link rel="shortcut icon" href="<?= $GLOBALS['routing']->getRoute('favicon-error') ?>">
    </head>

    <body>
    <div id="return">
        Back to main page
        
    </div>
    
    <header>
        <h1><?= $_SESSION['error'] ?></h1>
    </header>
    </body>

</html>