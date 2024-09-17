<?php

namespace Cron;

use Classes\Asol;
use Classes\Order;
use Enum\StatusType;

class AsolOrder
{
    private Order $order;
    private Asol $asol;

    public function __construct()
    {
        $this->order = new Order();
        $this->asol = new Asol();

        $this->addOrder();
        $this->updateOrder();
    }

    private function addOrder(): void
    {

        $filteredOrders = $this->order->parse();

        foreach ($filteredOrders as $orderData) {
            $trackerId = $this->asol->getTrackerId($orderData);
            if ($trackerId === false) {
                continue;
            }
            $orderData['tracker_id'] = $trackerId;

            $this->order->update($orderData);
        }
    }

    private function updateOrder(): void
    {
        foreach ($this->order->browse() as $orderData) {
            $orderInfo = $this->asol->getOrderInfo($orderData['tracker_id']);
            $statusId = StatusType::getId($orderInfo['status']);
            $orderData['track'] = !empty($orderInfo['track']) ? $orderInfo['track'] : '';

            $this->order->update([
                'status_id' => $statusId,
                'tracker_id' => $orderData['tracker_id'],
                'reference' => $orderData['reference']
            ]);

            $this->order->updateStatus($orderData, $statusId);
        }
    }
}
