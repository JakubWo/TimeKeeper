<?php

namespace src\Service\DatabaseService;

use PDO;
use PDOException;

class DatabaseService{

    private PDO $db;

    public function __construct($dbData){
        try {
            $this->db = new PDO(
                'myxsql:host=' . $dbData['host'] .
                ';dbname=' . $dbData['database'] .
                ';charset=ascii',
                $dbData['username'],
                $dbData['password']
            );
        } catch (PDOException $PDOException) {
            $_SESSION['error']['title'] = "Database error";
            $_SESSION['error']['message'] = $PDOException->getMessage();
            $i = 0;
            foreach($PDOException->getTrace() as $array){
                $_SESSION['error']['details'] .= '('.++$i.') In file: '.$array['file'].'('.$array['line'].'): '
                    .$array['class'].$array['type'].$array['function'].'<br>';
            }
            header("Location: /error");
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