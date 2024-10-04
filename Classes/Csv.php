<?php

namespace Classes;

use Enum\StatusType;

class Csv
{
    private Order $order;

    public function __construct()
    {
        $this->order = new Order();
        var_dump($this->order->parse());
        die();
    }

    public function read($filePath): array
    {
        $csvData = [];
        if (($handle = fopen($filePath, 'rb')) !== false) {
            fgetcsv($handle, 1000);

            while (($data = fgetcsv($handle, 1000)) !== false) {
                $csvData[] = [
                    'lead_id' => $data[0],
                    'delivery_status' => $data[1],
                    'tracking_number' => $data[2]
                ];
            }
            fclose($handle);
        }

        foreach ($csvData as $num => $data) {
            $orderId = $data['lead_id'];
            $status = $data['delivery_status'];
            $trackCode = $data['tracking_number'];
            var_dump($orderId);

            if ($orderId !== null && $status !== null && $trackCode !== null) {
                $statusId = StatusType::getId($status);

                if ($statusId) {
                    $orderData = [
                        'reference' => $orderId,
                        'track' => $trackCode
                    ];

//                    $this->order->updateStatus($orderData, $statusId, 'Csv');
                }
            }
        }

        return $csvData;
    }
}
