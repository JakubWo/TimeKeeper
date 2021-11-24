<?php
    if(empty($_SESSION['error']['title'])) {
        $_SESSION['error']['title'] = "Error 404";
    }
?>
<!DOCTYPE html>
<html lang="pl-PL">

    <head>
        <title><?= $_SESSION['error']['title'] ?></title>
        <link rel="stylesheet" href="<?= $GLOBALS['routingService']->getRoute('style-error') ?>">
        <link rel="shortcut icon" href="<?= $GLOBALS['routingService']->getRoute('image-favicon') ?>">
    </head>

    <body>
    <nav id="return">
        Back to main page
        
    </nav>
    
    <header>
        <h1><?= $_SESSION['error']['title'] ?></h1>
    </header>

    <div>
        <?php
            if ($GLOBALS['siteMode'] !== 'PROD') {
                if (isset($_SESSION['error']['details'])) {
                    echo $_SESSION['error']['details'];
                } else {
                    echo "Unknown error!";
                }
            } else {
                echo "Looks like something went wrong, please try again";
            }

        ?>
    </div>

    </body>

</html>

<?php
//unset($_SESSION['details']);
unset($_SESSION['error'])
?>

<!-- redirect on refresh? -->

