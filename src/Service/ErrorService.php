<?php

namespace src\Service\ErrorService;

class ErrorService
{
    /*
     *
     */
    public static function generate(
        string $title,
        string $message = null,
        array  $details = null,
        bool   $setCookie = false,
        int    $time = 0
    ): array
    {
        if (yaml_parse_file($GLOBALS['routingService']->getRoute('config-parameters'))['siteMode'] === 'DEV') {
            $errorArray['title'] = $title;
            if (isset($message)) {
                $errorArray['message'] = $message;
            }

            if ($details !== null) {
                $i = 0;
                foreach ($details as $array) {
                    $errorArray['details'] .= '(' . ++$i . ') In file: ' . $array['file'] . '(' . $array['line'] . '): '
                        . $array['class'] . $array['type'] . $array['function'] . "\n";
                }
            }

        } else {
            $errorArray = self::prodDefaultError();
        }

        if ($setCookie) {
            self::createCookie($errorArray, $time);
        }

        return $errorArray;
    }

    /*
     *
     */
    private static function prodDefaultError(): array
    {
        return [
            'title' => 'Unexpected error occurred',
            'message' => 'Something went wrong, please try again.',
            'details' => 'If error reappears please contact us through <i>' . $_SERVER['SERVER_ADMIN'] .
                '</i> address.'
        ];
    }

    /*
     *
     */
    private static function createCookie(array $cookieContent, int $time): void
    {
        setcookie('error', serialize($cookieContent), $time === 0 ? time() + 60 : $time, '/', '', true);
    }
}
