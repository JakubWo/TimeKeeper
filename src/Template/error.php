<?php
    if ($_SESSION['siteMode'] !== 'PROD') {
        $_SESSION['error']['title'] = $_SESSION['error']['title'] ?? "Unknown error";
    } else {
        $_SESSION['error']['title'] = $_SESSION['error']['title'] ? "Unknown error" : "Error 404";
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

    <a href="/"><button>Back to main page</button></a>


    <header class="error_format_box">
        <h1><?= $_SESSION['error']['title'] ?></h1>
    </header>

    <div class="error_format_box">
        <?php
            if ($_SESSION['siteMode'] !== 'PROD') {
                echo "<h2>".($_SESSION['error']['message'] ?? "Unknown error!")."</h2>";
                echo "<p>".($_SESSION['error']['details'] ?? 'Unknown')."</p>";
            } else {
                echo "<h2>Looks like something went wrong, please try again</h2>";
            }

        ?>
    </div>

    </body>

</html>

<?php
    unset($_SESSION['error'])
?>

<!-- redirect on refresh? -->

