<?php

authorization();

function authorization(){
    $db_data = $GLOBALS['routing']->getRoute('config-config');
    $db = new mysqli($db_data['host'], $db_data['username'], $db_data['password'], $db_data['database']);

    if($db->connect_error){
        $_SESSION['error'] = 'Database connection error';
        include($GLOBALS['routing']->getRoute('error-404'));
    }

    $table = 'tk_user';
    $result = $db->query("SELECT * FROM {table}");
    var_dump('XD');
    var_dump($result);
}
