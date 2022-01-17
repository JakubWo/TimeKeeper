<?php

namespace src\API\ApiController;

class ApiController
{
    protected const REGULAR = 1;
    protected const IRREGULAR = 2;

    protected const REGULARITY_MAX_DIFF_STARTING_TIME = 900;
    protected const REGULARITY_MAX_WORK_TIME_DIFF = 3600;
    protected const REGULARITY_MAX_BREAK_TIME = 3600;

    protected const STANDARD_WORK_TIME = 28800;
    protected const MAX_BREAK_TIME = 900;

    protected static function secondsToTime(int $totalTime): array
    {
        return [
            'hours' => floor($totalTime / 3600),
            'minutes' => floor($totalTime / 60 % 60),
            'seconds' => floor($totalTime % 60)
        ];
    }

    protected static function notesToString(array $notes): string
    {
        $note = '';
        foreach ($notes as $key => $val) {
            $note .= "$key:$val;";
        }
        return $note;
    }

    protected static function notesToArray(string $notes): array
    {
        $notes_array = [];
        foreach (explode(';', rtrim($notes, '; ')) as $note) {
            $note_parts = explode(':', $note);
            $notes_array[$note_parts[0]] = $note_parts[1];
        }
        return $notes_array;
    }

    public static function errorResponse($title = 'Unexpected error', $code = 400): array
    {
        http_response_code($code);
        return [
            'error' => [
                'title' => $title
            ]
        ];
    }

    protected static function successPostResponse(string $action, $code = 200, array $data = []): array
    {
        http_response_code($code);
        return [
            'result' => 'Success',
            'action' => $action,
            'data' => $data
        ];
    }
}