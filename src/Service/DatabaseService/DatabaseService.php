<?php

namespace src\Service\DatabaseService;

use PDO;
use PDOException;

class DatabaseService{

    private PDO $db;

    public function __construct($dbData){
        try {
            $this->db = new PDO(
                'mysql:host=' . $dbData['host'] .
                ';dbname=' . $dbData['database'] .
                ';charset=ascii',
                $dbData['username'],
                $dbData['password']
            );
        } catch (PDOException $PDOException) {
            $_SESSION['error']['title'] = "Unknown error";
            $_SESSION['error']['details'] = $PDOException->getMessage();
            require($GLOBALS['routingService']->getRoute('default-/error'));
        }
    }

    public function query(string $sql, array $params = [])
    {
        print_r($sql, $params);die;
        $statement = $this->db->prepare($sql);
        return $statement->execute($params);
    }

    /**
     * @return PDO
     */
    public function getDb(): PDO
    {
        return $this->db;
    }


}