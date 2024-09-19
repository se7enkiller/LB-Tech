<?php

namespace Cron;

use Classes\Order;
use Classes\Wapi;
use Enum\StatusType;

class WapiOrder
{
    private Order $order;
    private Wapi $wapi;

    public function __construct()
    {
        $this->order = new Order();
        $this->wapi = new Wapi();

        $this->addOrder();
        $this->updateOrder();
    }

    private function addOrder(): void
    {
        $filteredOrders = $this->order->parse(21);

        foreach ($filteredOrders as $orderData) {
            $tracker = $this->wapi->getTrackerData($orderData);

            if ($tracker === false) {
                continue;
            }
            $orderData['tracker_id'] = $tracker['wapiTrackingNumber'];
            $orderData['track'] = $tracker['uuid'];

            $this->order->update($orderData);
        }
    }

    private function updateOrder(): void
    {
        foreach ($this->order->browse('Wapi') as $orderData) {
            $orderInfo = $this->wapi->getOrderInfo($orderData);

            $statusId = StatusType::getId($orderInfo['status']);

            $this->order->update([
                'status_id' => $statusId,
                'tracker_id' => $orderData['tracker_id'],
                'reference' => $orderData['reference'],
                'track' => $orderData['track']
            ]);

            $this->order->updateStatus($orderData, $statusId, 'Wapi');
        }
    }
}