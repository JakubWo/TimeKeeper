<?php

generateUsers();

function generateUsers(){
    $dbConnectionData = yaml_parse_file("../../config/parameters.yaml")["database"];
    $db = new PDO(
        'mysql:host=' . $dbConnectionData['host'] .
        ';dbname=' . $dbConnectionData['database'] .
        ';charset=ascii',
        $dbConnectionData['username'],
        $dbConnectionData['password']
    );

    $users = getDataArray();
    for ($i = 0; $i < sizeof($users['username']); $i++) {
        $query = $db->prepare(
            'INSERT INTO tk_user(username, passw, is_admin, balance, time_zone) VALUES(
                                                                          "'.$users['username'][$i].'", 
                                                                          "'.$users['password'][$i].'",
                                                                          "'.$users['is_admin'][$i].'",
                                                                          "'.$users['balance'][$i].'",
                                                                          "'.$users['time_zone'][$i].'"
                                                                        )'
        );

        $query->execute();
        if($query->errorCode() !== '00000') {
            print_r($query->errorInfo());
            print_r("Data insertion stopped\n");
            return;
        }
    }

    print_r("Data inserted successfully\n");
}

function getDataArray(): array
{
    return [
        "username" => [
            "admin@mail.pl",
            "glowny_kierownik@mail.pl",
            "kierownik@mail.pl",
            "pracownik_IT@mail.pl",
            "pracownik_IT2@mail.pl",
            "pracownik_biuro@mail.pl",
            "pracownik_kiegowosci@mail.pl",
        ],
        "password" => [
            "admin",
            "hq1",
            "kierownik",
            "password",
            "haslo",
            "123",
            "Si1n3P455!@#dw"
        ],
        "is_admin" => [ 1, 0, 0, 0, 0, 0, 0 ],
        "balance" => [0, 0, 0, 0, 0, 100, -100],
        "time_zone" => [-2, 0, 0, 1, 1, 0, 0]
    ];
}