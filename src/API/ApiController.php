<?php

namespace src\API\ApiController;

class ApiController
{
    protected const TIMESTAMP_NOTES = [
        'CustomStart',
        'CustomStartTimeOff',
        'LongBreak',
        'OverWork',
        'UnderHours'
    ];

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
        if (!empty($notes)) {
            foreach (explode(';', rtrim($notes, '; ')) as $note) {
                $note_parts = explode(':', $note);

                if (in_array($note_parts[0], self::TIMESTAMP_NOTES)) {
                    $time = self::secondsToTime($note_parts[1]);

                    $note_parts[1] = sprintf(
                        '%02d:%02d:%02d',
                        $time['hours'],
                        $time['minutes'],
                        $time['seconds']
                    );
                }
                $notes_array[$note_parts[0]] = $note_parts[1];
            }
        }
        return $notes_array;
    }

    protected static function reformatEvents(array $events): array
    {
        $reformattedEvents = null;
        foreach ($events as $event) {
            $reformattedEvents[$event['event_timestamp']][] = $event['event_type'];
        }
        return $reformattedEvents;
    }

    public static function errorResponse(string $title, int $code = 400): array
    {
        http_response_code($code);
        return [
            'error' => [
                'title' => $title
            ]
        ];
    }

    protected static function successPostResponse(string $action, int $code = 200, array $data = []): array
    {
        http_response_code($code);
        return [
            'result' => 'Success',
            'action' => $action,
            'data' => $data
        ];
    }
}