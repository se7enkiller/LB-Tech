<?php

namespace Classes;

class Curl
{
    public function __invoke($url, $token, $method = 'GET', $data = null)
    {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'Authorization: ' . $token,
                'token: ' . $token,
            ),
        ));

        if (curl_errno($curl)) {
            echo 'cURL error: ' . curl_error($curl);
        }

        $response = curl_exec($curl);
        curl_close($curl);

        return json_decode($response, true);
    }
}
