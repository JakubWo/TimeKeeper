<?php

namespace src\Service\ApiService;

class ApiService
{
    protected const REGULAR = 1;
    protected const IRREGULAR = 2;

    protected const REGULARITY_MAX_DIFF_STARTING_TIME = 900;
    protected const REGULARITY_MAX_WORK_TIME_DIFF = 3600;
    protected const REGULARITY_MAX_BREAK_TIME = 3600;

    protected const STANDARD_WORK_TIME = 28800;
    protected const MAX_BREAK_TIME = 900;

    protected static function secondsToTime(int $totalTime): string
    {
        $hours = floor($totalTime / 3600);
        $minutes = floor($totalTime / 60 % 60);
        $seconds = floor($totalTime % 60);

        return sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
    }

    protected static function notesToString(array $notes): string
    {
        $note = '';
        foreach ($notes as $key => $val) {
            $note .= "$key:$val;";
        }
        return $note;
    }

    public static function errorResponse($title): array
    {
        return [
            'error' => [
                'title' => $title
            ]
        ];
    }

    protected static function successResponse(string $action, array $data = []): array
    {
        return [
            'result' => 'Success',
            'action' => $action,
            'data' => $data
        ];
    }
}