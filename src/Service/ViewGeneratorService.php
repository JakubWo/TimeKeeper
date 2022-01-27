<?php

namespace src\Service\ViewGeneratorService;

use src\Service\ApiService\ApiService;

class ViewGeneratorService
{
    public static function generateWorkday(): string
    {
        $amountOfWorkdays = filter_input(
            INPUT_GET, 'amountOfWorkdays', FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);

        $timesAppended = filter_input(
            INPUT_GET, 'timesAppended', FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);

        $action = filter_input(
            INPUT_GET, 'action', FILTER_SANITIZE_STRING, FILTER_NULL_ON_FAILURE);

        $rowStyle = filter_input(INPUT_GET, 'rowStyle', FILTER_VALIDATE_INT);

        if (empty($amountOfWorkdays) || $amountOfWorkdays < 1 || $timesAppended === null || empty($action) ||
            $rowStyle === false || $rowStyle < 0 || $rowStyle > 1) {
            return self::errorResponse('Generator error');
        }

        $from = $timesAppended * $amountOfWorkdays;

        if ($action === 'update' || $action === 'start') {
            $from = 0;
        }

        $workdays = ApiService::request('getWorkdays', [
            'last' => $amountOfWorkdays,
            'from' => $from
        ]);


        if (empty($workdays)) {
            return self::errorResponse("Empty workday history, or some unexpected error occurred.");
        }

        $rows = null;
        foreach ($workdays as $workday) {
            $notes = null;
            foreach ($workday['notes'] as $key => $val) {
                $notes .= "$key: $val<br>";
            }
            $workday['notes'] = $notes;

            $workday['workday_type'] = strtoupper($workday['workday_type']);

            if (!$workday['is_accepted']) {
                if ($_SESSION['user_id'] === 1) { // TODO: management logic
                    $workday['is_accepted'] = '<td><img src="' . $GLOBALS['routingService']->getRoute('image-favicon') .
                        '" alt="x"></td><td><button id="acceptation_button">Accept</button></td>';
                } else {
                    $workday['is_accepted'] = '<td class="is_accepted"><img src="' .
                        $GLOBALS['routingService']->getRoute('image-favicon') . '" alt="x"></td>';
                }

            } else {
                $workday['is_accepted'] = '<td class="is_accepted"><img src="'
                    . $GLOBALS['routingService']->getRoute('image-accepted') . '" alt="v"></td>';
            }

            $rows .= '<details class="' . $workday['workday_id'] . ' workday_row">' .
                '<summary><table class="row' . $rowStyle % 2 . '"><tr>';

            foreach (['workday_id', 'workday_date', 'workday_type', 'notes'] as $header) {
                $rows .= " <td>$workday[$header]</td> ";
            }

            $rows .= $workday['is_accepted'] . '</tr></table></summary>s</details>';
            $rowStyle += 1;
        }

        return $rows;
    }

    public
    static function generateEvent(): string
    {
        return '';
    }

    public
    static function errorResponse(string $reason = 'Unknown error', int $code = 500): string
    {
        http_response_code($code);
        return '<p>' . ($_SESSION['siteMode'] !== 'PROD' ? $reason : 'Couldn\'t load ' . $_GET['view']) . '</p>';
    }

//    protected static function successResponse(string $action, int $code = 200, array $data = []): array
//    {
//        http_response_code($code);
//        return [
//            'result' => 'Success',
//            'action' => $action,
//            'data' => $data
//        ];
//    }

}