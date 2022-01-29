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
    public function getCheckUserCredentials(array $credentials): ?int
    {
        $query = $this->db->prepare('SELECT check_user(?, ?)');
        $query->execute($credentials);

        if ($query->errorCode() !== "00000") {
            return null;
        }

        return $query->fetchColumn();
    }

    public function getBasicUserData(int $userId)
    {
        $query = $this->db->prepare('SELECT balance, is_admin, time_zone FROM tk_user WHERE user_id = ?;');
        $query->execute([$userId]);

        if ($query->errorCode() !== "00000") {
            return null;
        }

        return $query->fetchAll(PDO::FETCH_ASSOC)[0];
    }

    /**
     * @throws PDOException
     */
    public function getTeamsPermissions(int $userId): ?array
    {
        $query = $this->db->prepare(
            'SELECT t.team_name, permission 
                FROM team_permissions 
                LEFT JOIN team t USING (team_id) 
                WHERE user_id = ?;'
        );

        $query->execute([$userId]);

        if ($query->errorCode() !== "00000") {
            return null;
        }

        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTeamByMemberUsername(string $username): ?array
    {
        $query = $this->db->prepare(
            'SELECT t.team_name, permission 
                FROM team_permissions 
                LEFT JOIN team t USING (team_id) 
                LEFT JOIN tk_user USING (user_id) 
                WHERE username = ? AND (permission = 1 OR permission = 2);'
        );

        $query->execute([$username]);

        if ($query->errorCode() !== '00000') {
            return null;
        }

        return $query->fetchAll(PDO::FETCH_ASSOC)[0];
    }

    public function getTeamByMemberId(int $userId): ?array
    {
        $query = $this->db->prepare(
            'SELECT t.team_name, permission 
                FROM team_permissions 
                LEFT JOIN team t USING (team_id) 
                WHERE user_id = ? AND (permission = 1 OR permission = 2);'
        );

        $query->execute([$userId]);

        if ($query->errorCode() !== '00000') {
            return null;
        }

        return $query->fetchAll(PDO::FETCH_ASSOC)[0];
    }


    public function getMembersOfTeam(string $teamName): ?array
    {
        $query = $this->db->prepare(
            'SELECT username 
                    FROM tk_user 
                    RIGHT JOIN team_permissions tp USING (user_id) 
                    LEFT JOIN team t USING (team_id) 
                    WHERE t.team_name = ? AND (tp.permission = 1 OR tp.permission = 2);'
        );

        $query->execute([$teamName]);

        if ($query->errorCode() !== '00000') {
            return null;
        }

        return $query->fetchAll(PDO::FETCH_ASSOC);
    }


    /**
     * @throws PDOException
     */
    public function getOtherId(string $username): ?string
    {
        $query = $this->db->prepare('SELECT user_id FROM tk_user WHERE username=?;');
        $query->execute([$username]);

        if ($query->errorCode() !== "00000") {
            return null;
        }
        return $query->fetchColumn();
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
        $query = $this->db->prepare('CALL stop_event(?, ?, ?, ?, ?, ?);');
        $query->execute($args);

        if ($query->errorCode() !== "00000") {
            return false;
        }
        return true;
    }

    /**
     * @throws PDOException
     */
    public function acceptWorkday(int $workdayId): bool
    {
        $query = $this->db->prepare('CALL accept_workday(?);');
        $query->execute([$workdayId]);

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
        $query = $this->db->prepare(
            'SELECT time_zone 
                FROM tk_user 
                WHERE user_id = ?'
        );
        $query->execute([$userId]);

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
        $query = $this->db->prepare(
            'SELECT event_type, event_timestamp FROM tk_event 
		        WHERE workday_id = ? 
                ORDER BY event_id DESC;'
        );
        $query->execute([$workdayId]);

        if ($query->errorCode() !== "00000") {
            return null;
        }
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * @throws PDOException
     */
    public function getWorkdays(int $userId, int $n = null, int $from = null): ?array
    {
        if ($n === null) {
            $query = $this->db->prepare(
                'SELECT workday_id, workday_date, workday_type, is_accepted, notes 
                    FROM workday 
                    WHERE user_id = ?
                    ORDER BY workday_id DESC;'
            );
            $query->execute([$userId]);
        } else {
            $query = $this->db->prepare('CALL get_workdays(?, ?, ?);');
            $query->execute([$userId, $n, $from]);
        }

        if ($query->errorCode() !== "00000") {
            return null;
        }

        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * @throws PDOException
     */
    public function getWorkdaysById(array $ids): ?array
    {
        $statement = '
                SELECT workday_id, workday_date, workday_type, is_accepted, notes, user_id
                FROM workday 
                WHERE workday_id = ?
        ';
        for ($i = 1; $i < count($ids); $i++) {
            $statement .= ' OR workday_id = ?';
        }

        $query = $this->db->prepare($statement);
        $query->execute($ids);

        if ($query->errorCode() !== "00000") {
            return null;
        }
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * @throws PDOException
     */
    public function getBlockedIp(string $ip, string $username): ?array
    {
        $query = $this->db->prepare(
            'SELECT ip, blocked_until, username 
                    FROM blocked_ip 
                    WHERE ip= ? AND username = ?;'
        );
        $query->execute([$ip, $username]);

        if ($query->errorCode() !== "00000") {
            return null;
        }

        return $query->fetchAll(PDO::FETCH_ASSOC)[0];
    }

    /**
     * @throws PDOException
     */
    public function updateBlockedIpInfo(string $ip, string $username, int $blockedUntil, bool $inDatabase): bool
    {
        if ($inDatabase) {
            $query = $this->db->prepare('UPDATE blocked_ip SET blocked_until = ? WHERE ip = ? AND username = ?;');
        } else {
            $query = $this->db->prepare('INSERT INTO blocked_ip(blocked_until, ip, username) VALUES (?, ?, ?);');
        }
        $query->execute([$blockedUntil, $ip, $username]);

        if ($query->errorCode() !== "00000") {
            return false;
        }

        return true;
    }

}