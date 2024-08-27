<?php
require_once __DIR__ . '/../autoloader.php';

use Classes\Asol;
use Classes\Order;
use Enum\StatusType;

$order = new Order();
$asol = new Asol();

foreach ($order->browse() as $orderData) {
    $status = $asol->getStatus($orderData['tracker_id']);
    $statusId = StatusType::getId($status);

    $order->update([
        'status_id' => $statusId,
        'tracker_id' => $orderData['tracker_id'],
        'reference' => $orderData['reference']
    ]);

    $order->updateStatus($orderData, $statusId);
}
