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

            // PDO will now raise an exception on every failed query.
            $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $PDOException) {
            ErrorService::generate(
                "Database connection error",
                $PDOException->getMessage(),
                $PDOException->getTrace(),
                true
            );
            header("Location: /error",);
        }
    }

    public function checkUserTimeZone(int $user_id): ?string
    {
        $query = $this->db->prepare('SELECT get_user_time_zone(?);');
        $query->execute([$user_id]);

        return $query->fetchColumn();
    }

    public function startEvent(array $args): bool
    {
        $query = $this->db->prepare('CALL start_event(?, ?, ?, ?);');
        $query->execute($args);

        if ($query->errorCode() !== "00000") {
            return false;
        }

        return true;
    }

    /**
     * @return PDO
     */
    public function getDb(): PDO
    {
        return $this->db;
    }


}