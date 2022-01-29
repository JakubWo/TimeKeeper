<?php

require_once 'FixtureService.php';

use src\Fixture\FixtureService\FixturesService;

generateUsers();

function generateUsers()
{
    $fs = new FixturesService();
    $db = $fs->getDb();

    $users = getDataArray();
    for ($i = 0; $i < sizeof($users['username']); $i++) {
        $query = $db->prepare(
            'INSERT INTO tk_user(username, passw, balance, time_zone, is_admin, salt) VALUES(
                                                                          "' . $users['username'][$i] . '", 
                                                                          "' . $users['password'][$i] . '",
                                                                          "' . $users['balance'][$i] . '",
                                                                          "' . $users['time_zone'][$i] . '",
                                                                          "' . $users['is_admin'][$i] . '",
                                                                          "' . $users['salt'][$i] . '"
                                                                        )'
        );

        $query->execute();
        if ($query->errorCode() !== '00000') {
            $fs->printErrorMessage($query);
            return;
        }
    }

    print_r("Data inserted successfully\n");
}

function getDataArray(): array
{
    return [
        'username' => [
            'admin@mail.pl',
            'glowny_kierownik@mail.pl',
            'kierownik@mail.pl',
            'pracownik_IT@mail.pl',
            'pracownik_IT2@mail.pl',
            'pracownik_biuro@mail.pl',
            'pracownik_kiegowosci@mail.pl',
        ],
        'password' => [
            'admin',
            'hq1',
            'kierownik',
            'password',
            'haslo',
            '123',
            'Si1n3P455!@#dw'
        ],
        'balance' => [0, 0, 0, 0, 0, 100, -100],
        'time_zone' => [
            'Europe/Warsaw',
            'Europe/Warsaw',
            'Africa/Abidjan',
            'Africa/Abidjan',
            'Europe/Warsaw',
            'Europe/London',
            'Europe/Warsaw'
        ],
        'is_admin' => [1, 0, 0, 0, 0, 0, 0],
        'salt' => ['asd', 'xdd', '2d1', 'dsh', '123', 'dhA', 'xh0']
    ];
}