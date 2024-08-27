<?php
require_once __DIR__ . '/autoloader.php';

use Classes\Asol;
use Classes\Order;

$orderClass = new Order();
$asol = new Asol();


$filteredOrders = $orderClass->parse();

foreach ($filteredOrders as $orderData) {
    $trackerId = $asol->getTrackerId($orderData);
    if ($trackerId === false) {
        continue;
    }
    $orderData['tracker_id'] = $trackerId;

    $orderClass->update($orderData);
}
