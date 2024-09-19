<?php

namespace Classes;

class Wapi
{
    private string $token;

    public function __construct()
    {
        $this->token = '79293bc6-abb8-4834-a995-320c5727baa5';
    }

    public function getTrackerData($orderData)
    {
        $url = 'https://api.wapi.com/API/hs/v1/ExternalAPI/APIIntegration/CreateOrder';
        $curl = new Curl();

        $address = $orderData['street'] . ' ' . $orderData['house'] . ', ' . $orderData['apartment'];
        $products = [];
        foreach ($orderData['cart']['products'] as $product) {
            $products[] = [
                'product' => [
                    'id' => $product['id'],
                ],
                'quantity' => $product['quantity'],
                'price' => $product['cost_local_currency'],
                'total' => $orderData['cart']['total_local_currency']
            ];
        }

        $data = [
            'order' => [
              'id' => 'a2559667-ee10-4afb-b4df-08cb32efb147',
              'number' => (string)$orderData['reference']
            ],
            'date' => date('Y-m-d\TH:i:s'),
            'currency' => 'EUR',
            'codCurrency' => 'EUR',
            'products' => $products,
            'receiver' => [
                'fullName' => $orderData['name'],
                'phone' => $orderData['phone'],
                'email' => $orderData['email'],
                'address' => $address,
                'city' => $orderData['city'],
                'county' => $orderData['region'],
                'country' => $orderData['country_code'],
                'zipCode' => $orderData['zip']
            ]
        ];

        $result = $curl($url, $this->token, 'POST', $data);

        if (empty($result['success'])) {
            echo $result['errorMessage'];
            return false;
        }

        return $result['data'];
    }

    public function getOrderInfo($tracker)
    {
        $url = 'https://api.wapi.com/API/hs/v1/ExternalAPI/APIIntegration/GetStatusHistory';
        $curl = new Curl();

        $data = [
            'orders' => [
                [
                    'uuid' => $tracker['uuid'],
                    'wapiTrackingNumber' => $tracker['tracker_id'],
                    'number' => $tracker['reference'],
                ]
            ]
        ];

        $result = $curl($url, $this->token, 'POST', $data);

        if (empty($result['success'])) {
            return false;
        }

        return $result['orders'][0];
    }
}
