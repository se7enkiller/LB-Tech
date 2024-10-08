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

    public function parse($statusId = 12): array
    {
        $url = 'https://nutrend-crm.voiptime.app/api/v2/admin/order/list?status_id=eq:' . $statusId;
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
                $service = $statusId === 12 ? 'Asol' : 'Wapi';
                $this->create($order, $service);
                $result[] = $order;
            } catch (Exception $e) {
                continue;
            }
        }

        echo 'Orders created: ' . count($result) . PHP_EOL;
        return $result;
    }

    public function browse($service = 'Asol'): false|array
    {
        $query = 'SELECT * FROM orders 
         WHERE service = "' . $service . '" 
         AND (status_id != ' . StatusType::CANCELLED->value . ' 
         OR status_id != ' . StatusType::RETURNED->value . '
         OR status_id != ' . StatusType::PAID_OUT->value . ')';

        return $this->pdo->query($query)->fetchAll();
    }

    public function create($orderData, $service = 'Asol'): void
    {
        $sql = 'INSERT INTO orders (reference, tracker_id, service) VALUES (:reference, :tracker_id, :service)';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':reference' => $orderData['reference'],
            ':tracker_id' => $orderData['tracker_id'],
            ':service' => $service
        ]);
    }

    public function update($orderData): void
    {
        $sql = 'UPDATE orders SET 
                  status_id = :status_id, 
                  tracker_id = :tracker_id, 
                  track = :track,
                  service = :service
              WHERE reference = :reference';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':status_id' => $orderData['status_id'] ?: 12,
            ':reference' => $orderData['reference'],
            ':tracker_id' => $orderData['tracker_id'],
            ':track' => !empty($orderData['track']) ? $orderData['track'] : '',
            ':service' => $orderData['service']
        ]);
    }

    private function process($order): array
    {
        $cart = [];
        if ($order['cart']) {
            $cart = [
                'total_currency' => 'EUR',
                'total_local_currency' => $order['price'] / count($order['cart'])
            ];
            foreach ($order['cart'] as $item) {
                $cart['products'][] = [
                    'id' => $this->getProductId($item['good_id']),
                    'quantity' => $item['quantity'],
                    'cost_local_currency' => $order['price'],
                ];
            }
        }

        return [
            'reference' => $order['id'] ?: '',
            'name' => $order['full_name'] ?: '',
            'phone' => $order['phone'] ?: '',
            'country_code' => $order['country_code'] ?: 'IT',
            'email' => $order['email'] ?: '',
            'region' => $order['string2'] ?: '',
            'city' => $order['string3'] ?: '',
            'district' => $order['district'] ?: '',
            'zip' => $order['string4'] ?: '',
            'street' => $order['string5'] ?: '',
            'house' => $order['string6'] ?: '',
            'apartment' => $order['string7'] ?: '',
            'address_comment' => $order['string9'] ?: '',
            'cart' => $cart,
        ];
    }

    private function getProductId($cartId): string
    {
        return match ((string) $cartId) {
            '1' => '5f3264e5-b54e-4dd1-85f5-2f41ffb6647e',
            '2' => '6a7d5d1c-f346-40f3-8b97-dd888b8b8fc8',
            '4', '3' => '6fab99b8-1f26-40f8-ab76-59af031cee46',
            '5', '6' => '0aeaadf9-4ff1-418f-98d3-d324c8d2e870',
            '7' => '0f684c6c-0040-4b2a-ba38-ee49ce53273a',
            '8' => '5a2a4388-8b99-4291-ab04-2cd20c715cf8',
            '11' => 'Hondroflex',
            '14', '15' => 'VitaProsta',
            default => (string) $cartId,
        };
    }

    public function updateStatus($orderData, $statusId, $service = 'Asol'): void
    {
        $url = 'https://nutrend-crm.voiptime.app/api/v2/admin/order';
        $token = 'JS4KBYs';
        $curl = new Curl();

        $data = [
            'id' => $orderData['reference'],
            'status_id' => $statusId,
            'string8' => $orderData['track'],
            'string11' => $orderData['tracker_id'],
            'string12' => $service,
        ];

        $curl($url, $token, 'PUT', $data);

        echo 'Update ' . $orderData['reference'] . ' status: ' . $statusId . '<br/>';
    }

    public function delete($reference): array|false
    {
        $query = 'DELETE FROM orders WHERE reference = ' . $reference;

        return $this->pdo->query($query)->fetchAll();
    }
}
