<?php

namespace src\Service\ErrorService;

class ErrorService{

    public static function manualError(string $title, bool $message, string $details, int $time = 0) : void
    {
        if (yaml_parse_file($GLOBALS['routingService']->getRoute('config-parameters'))['siteMode'] === 'DEV') {
            self::createCookie([
                'title' => $title,
                'message' => $message,
                'details' => $details
            ], $time);
        } else {
            self::prodError();
        }
    }

    public static function PDOError(string $title, string $message, array $trace, int $time = 0) : void
    {
        if (yaml_parse_file($GLOBALS['routingService']->getRoute('config-parameters'))['siteMode'] === 'DEV') {
            $cookieContent['title'] = $title;
            $cookieContent['message'] = $message;

            $i = 0;
            foreach ($trace as $array) {
                $cookieContent['details'] .= '(' . ++$i . ') In file: ' . $array['file'] . '(' . $array['line'] . '): '
                    . $array['class'] . $array['type'] . $array['function'] . '<br>';
            }
            self::createCookie($cookieContent, $time);
        } else {
            self::prodError();
        }
    }

    private static function createCookie(array $cookieContent, int $time) : void
    {
        setcookie('error', serialize($cookieContent), $time === 0 ? time()+60 : $time, '/');
    }

    private static function prodError()
    {
        setcookie('error', serialize([
            'title' => 'Unexpected error occurred!',
            'message' => 'Something went wrong, please try again.',
            'details' => 'If error reappears please contact us through <i>'.$_SERVER['SERVER_ADMIN'].
                '</i> address.'
        ]), time()+60, '/');
    }
}
