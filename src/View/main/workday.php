<?php

require_once($GLOBALS['routingService']->getRoute('service-api'));

use src\Service\ApiService\ApiService;

echo generate();

function generate(): string
{
    $headers = [
        'workday_id' => 'id',
        'workday_date' => 'date',
        'workday_type' => 'type',
        'notes' => 'notes',
        'is_accepted' => 'is accepted'
    ];
    $rows = [];

    $workdays = ApiService::request('getWorkdays');
    if ($workdays === null) {
        return "Empty workday history, or some unexpected error occurred.";
    }

    $table = '<table id="headers"><tr>';
    foreach ($headers as $header) {
        $table .= "<th>$header</th>";
    }
    $table .= '<tr></table>';
    foreach ($workdays as $workday) {
        $notes = null;
        foreach ($workday['notes'] as $key => $val) {
            $notes .= "$key: $val<br>";
        }
        $workday['notes'] = $notes;
        $workday['workday_type'] = strtoupper($workday['workday_type']);

        if (!$workday['is_accepted']) {
            $workday['is_accepted'] = '<img src="' . $GLOBALS['routingService']->getRoute('image-favicon') . '" alt="x">';
        }

        $row = null;
        foreach ($headers as $column => $header) {
            $row .= " <td>$workday[$column]</td> ";
        }
        $rows[] = $row;
    }

    foreach ($rows as $row) {
        $table .= "<details><summary><table><tr>$row</tr></table></summary>xDD</details > ";
    }
    return $table;
}


