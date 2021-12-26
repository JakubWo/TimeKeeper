<?php

namespace src\Service\DatabaseService;

use PDO;
use PDOException;
use src\Service\ErrorService\ErrorService;

class DatabaseService
{

    private PDO $db;

    public function __construct()
    {
        $dbData = yaml_parse_file($GLOBALS['routingService']->getRoute('config-parameters'))['database'];
        try {
            $this->db = new PDO(
                'mysql:host=' . $dbData['host'] .
                ';dbname=' . $dbData['database'] .
                ';charset=ascii',
                $dbData['username'],
                $dbData['password']
            );
        } catch (PDOException $PDOException) {
            ErrorService::PDOError(
                "Database connection error",
                $PDOException->getMessage(),
                $PDOException->getTrace()
            );
            header("Location: /error",);
        }
    }

    /**
     * @return PDO
     */
    public function getDb(): PDO
    {
        return $this->db;
    }


}