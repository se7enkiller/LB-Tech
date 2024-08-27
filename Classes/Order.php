<?php

namespace Classes;

use Enum\StatusType;
use Exception;
use PDO;

class Order
{
    public PDO $pdo;

    public function __construct()
    {
        $db = new DB();

        $this->pdo = $db->pdo;
    }

    public function parse(): array
    {
        $url = 'https://nutrend-crm.voiptime.app/api/v2/admin/order/list';
        $token = 'JS4KBYs';
        $curl = new Curl();
        $orders = $curl($url, $token);
        if (empty($orders['success']) || $orders['count'] === 0) {
            echo 'Can\'t get order or count is zero';
            return [];
        }

        $filteredOrders = array_map([$this, 'process'], $orders['data']);
        $result = [];

        foreach ($filteredOrders as $order) {
            try {
                  $this->create($order);
                  $result[] = $order;
            } catch (Exception $e) {
                continue;
            }
        }

        echo 'Orders created: ' . count($result) . PHP_EOL;
        return $result;
    }

    public function browse(): false|array
    {
        $query = 'SELECT * FROM orders 
         WHERE status_id != ' . StatusType::CANCELLED . ' 
         OR status_id != ' . StatusType::RETURNED . '
         OR status_id != ' . StatusType::PAID_OUT;
        return $this->pdo->query($query)->fetchAll();
    }

    public function create($orderData): void
    {
        $sql = 'INSERT INTO orders (reference, tracker_id) VALUES (:reference, :tracker_id)';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':reference' => $orderData['reference'], ':tracker_id' => $orderData['tracker_id']]);
    }

    public function update($orderData): void
    {
        $sql = 'UPDATE orders SET status_id = :status_id, tracker_id = :tracker_id WHERE reference = :reference';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':status_id' => $orderData['status_id'] ?: 12,
            ':reference' => $orderData['reference'],
            ':tracker_id' => $orderData['tracker_id']
        ]);
    }

    private function process($order): array
    {
        $cart = [];
        if ($order['cart']) {
            $cart = [
                'total_currency' => 'EUR'
            ];
            foreach ($order['cart'] as $item) {
                $price = $item['selling_price'] * 100 * $item['quantity'];
                $cart['total_local_currency'] += $price;
                $cart['products'][] = [
                    'id' => $this->getProductId($item['good_id']),
                    'quantity' => $item['quantity'],
                    'cost_local_currency' => $price,
                ];
            }
        }

        return [
            'reference' => $order['id'] ?: '',
            'name' => $order['full_name'] ?: '',
            'phone' => $order['phone'] ?: '',
            'country_code' => $order['country_code'] ?: '',
            'email' => $order['email'] ?: '',
            'region' => $order['string2'] ?: '',
            'city' => $order['string3'] ?: '',
            'district' => $order['district'] ?: '',
            'zip' => $order['string4'] ?: '',
            'street' => $order['string5'] ?: '',
            'house' => $order['string6'] ?: '',
            'apartment' => $order['string7'] ?: '',
            'cart' => $cart,
        ];
    }

    private function getProductId($cartId): string
    {
        return match ((string) $cartId) {
            '1' => '5f3264e5-b54e-4dd1-85f5-2f41ffb6647e',
            '2' => '6a7d5d1c-f346-40f3-8b97-dd888b8b8fc8',
            '4', '3' => '6fab99b8-1f26-40f8-ab76-59af031cee46',
            default => (string) $cartId,
        };
    }

    public function updateStatus($orderData, $statusId): void
    {
        $url = 'https://nutrend-crm.voiptime.app/api/v2/admin/order';
        $token = 'JS4KBYs';
        $curl = new Curl();
        $data = [
            'id' => $orderData['reference'],
            'string8' => $orderData['tracker_id'],
            'status_id' => $statusId
        ];
        $curl($url, $token, 'PUT', $data);

        echo 'Update ' . $orderData['reference'] . ' status: ' . $statusId . '<br/>';
    }
}
