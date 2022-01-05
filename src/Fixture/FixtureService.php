<?php

namespace src\Fixture\FixtureService;

use PDO;

class FixturesService
{

    public static function getDb(): PDO
    {
        $host = 'localhost';
        $dbname = 'TimeKeeper';
        $username = 'tk_prod';
        $password = 'ACBDsHe+jjJ$3rtf';

        return new PDO(
            'mysql:host=localhost; dbname=TimeKeeper;charset=ascii',
            'tk_prod',
            'ACBDsHe+jjJ$3rtf'
        );
    }

    public static function printErrorMessage($query): void
    {
        print_r($query->errorInfo());
        print_r("Data insertion stopped\n");
    }


}