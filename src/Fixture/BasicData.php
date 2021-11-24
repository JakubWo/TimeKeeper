<?php
$dbData = require(getenv('PWD').'/config/config.php');
$dbData = $dbData['database'];

$db = new mysqli($dbData['host'], $dbData['username'], $dbData['password'], $dbData['database']);
if($db->connect_error){
    echo "Couldn't access database\n"; die;
}

$table = 'basic_data';

$cities = ['Gdansk', 'Gdynia', 'Rumia', 'Wejherowo'];
$streets = ['Gdanska 1', 'Gdynska 2', 'Kartuska 3', 'Wejherowska 4'];

$day = 86400;
$halfHour = 1800;
$dateTime = new DateTime();

foreach($cities as $city) {
    foreach ($streets as $street) {
        $dateTime->setDate(2021, 10, 01);
        for ($i = 0; $i < 2; $i++) {
            $dateTime->setTime(9,0);
            for($j = 0; $j < 10; $j++) {
                $date = $dateTime->format('Y-m-d');
                $time = $dateTime->format('H:i');

                $db->query("INSERT INTO {$table}(city, street, visit_date, visit_hour)".
                    " VALUES('".$city."', '".$street."', '".$date."', '".$time."')");

                $dateTime->setTimestamp($dateTime->getTimestamp()+$halfHour);
            }
            $dateTime->setTimestamp($dateTime->getTimestamp()+$day);
        }
    }
}

echo "Works\n";die;





