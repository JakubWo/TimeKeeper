<?php

namespace src\Service\ApiService;

class ApiService
{
    public static function request(string $endpoint, array $params = []): ?array
    {
        $parameters = null;
        if (!empty($params)) {
            $parameters = '?';
            foreach ($params as $param => $val) {
                $parameters .= "$param=$val&";
            }
            $parameters = rtrim($parameters, '& ');
        }


        $url = 'http' . ($_SERVER['HTTPS'] === 'on' ? 's' : '') . '://' . $_SERVER['SERVER_NAME'] . '/api/' . $endpoint . $parameters;
        $curlOptions = [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_COOKIE => session_name() . '=' . session_id()
        ];

        $ch = curl_init();
        curl_setopt_array($ch, $curlOptions);
        // In order to share session:
        session_write_close();
        $data = curl_exec($ch);

        curl_close($ch);

        if ($data === false) {
            return null;
        }

        return json_decode($data, true)['response'];
    }
}