<?php

namespace Classes;

class Asol
{
    public function getTrackerId($orderData)
    {
        $url = 'https://service.asol.pro/api/v1/order';
        $token = 'Basic Zml0ZW5zaWFwcC53ZWJzaXRlOlRoR3NTZjhoRUU4eDgkMTUxbTVL';
        $curl = new Curl();
        $data = [
            'number' => (string)$orderData['reference'],
            'counterparty_id' => 'a2559667-ee10-4afb-b4df-08cb32efb147',
            'country_code' => $orderData['country_code'],
            'name' => $orderData['name'],
            'phone' => $orderData['phone'],
            'email' => $orderData['email'],
            'zip' => $orderData['zip'],
            'region' => $orderData['region'],
            'city' => $orderData['city'],
            'municipality' => '-',
            'district' => '-',
            'colony' => '-',
            'street' => $orderData['street'],
            'house' => $orderData['house'],
            'apartment' => $orderData['apartment'],
            'cart' => $orderData['cart']
        ];

        $result = $curl($url, $token, 'POST', $data);

        if (empty($result['id'])) {
            return false;
        }

        return $result['id'];
    }

    public function getOrderInfo($trackerId)
    {
        $url = 'https://service.asol.pro/api/v1/order/' . $trackerId . '/status';
        $token = 'Basic Zml0ZW5zaWFwcC53ZWJzaXRlOlRoR3NTZjhoRUU4eDgkMTUxbTVL';
        $curl = new Curl();

        $result = $curl($url, $token);

        if (empty($result['id'])) {
            return false;
        }

        return $result;
    }
}
