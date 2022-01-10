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
            header("Location: /error");
        }
    }

    /**
     * @throws PDOException
     */
    public function startEvent(array $args): bool
    {
        $query = $this->db->prepare('CALL start_event(?, ?, ?, ?, ?);');
        $query->execute($args);

        if ($query->errorCode() !== "00000") {
            return false;
        }
        return true;
    }

    /**
     * @throws PDOException
     */
    public function stopBreakEvent(int $workdayId): bool
    {
        $query = $this->db->prepare('CALL stop_break(?);');
        $query->execute([$workdayId]);

        if ($query->errorCode() !== "00000") {
            return false;
        }
        return true;
    }

    /**
     * @throws PDOException
     */
    public function breakEvent(int $workdayId): bool
    {
        $query = $this->db->prepare('CALL break_event(?);');
        $query->execute([$workdayId]);

        if ($query->errorCode() !== "00000") {
            return false;
        }
        return true;
    }

    /**
     * @throws PDOException
     */
    public function stopEvent(array $args): bool
    {
        $query = $this->db->prepare('CALL stop_event(?, ?, ?, ?, ?);');
        $query->execute($args);

        if ($query->errorCode() !== "00000") {
            return false;
        }
        return true;
    }

    /**
     * @throws PDOException
     */
    public function getUserTimeZone(int $userId): ?string
    {
        $query = $this->db->prepare('SELECT get_user_time_zone(?);');
        $query->execute([$userId]);

        if ($query->errorCode() !== "00000") {
            return null;
        }

        return $query->fetchColumn();
    }

    /**
     * @throws PDOException
     */
    public function getUserLastWorkdayId(int $userId): ?int
    {
        $query = $this->db->prepare('CALL get_user_last_workday_id(?);');
        $query->execute([$userId]);

        if ($query->errorCode() !== "00000") {
            return null;
        }

        return $query->fetchColumn();
    }

    /**
     * @throws PDOException
     */
    public function getWorkdayLastEventType(int $workdayId): ?string
    {
        $query = $this->db->prepare('CALL get_user_last_event_type(?);');
        $query->execute([$workdayId]);

        if ($query->errorCode() !== "00000") {
            return null;
        }

        return $query->fetchColumn();
    }

    /**
     * @throws PDOException
     */
    public function getWorkdayEvents(int $workdayId): ?array
    {
        $query = $this->db->prepare('CALL get_workday_events(?);');
        $query->execute([$workdayId]);

        if ($query->errorCode() !== "00000") {
            return null;
        }
        return $query->fetchAll();
    }

    /**
     * @throws PDOException
     */
    public function getWorkdays(int $userId, int $n): ?array
    {
        $query = $this->db->prepare('CALL get_workdays(?, ?);');
        $query->execute([$userId, $n]);

        if ($query->errorCode() !== "00000") {
            return null;
        }
        return $query->fetchAll();
    }


    /**
     * @return PDO
     */
    public function getDb(): PDO
    {
        return $this->db;
    }


}