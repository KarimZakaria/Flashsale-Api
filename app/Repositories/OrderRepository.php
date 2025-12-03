<?php

namespace App\Repositories;

use App\Models\Order;

class OrderRepository
{
    public function create(array $data): Order
    {
        return Order::create($data);
    }

    public function findByPaymentId(string $paymentId): ?Order
    {
        return Order::where('payment_id', $paymentId)->first();
    }

    public function updateStatus(Order $order, string $status, ?string $paymentId = null): Order
    {
        if ($paymentId) {
            $order->payment_id = $paymentId;
        }
        $order->status = $status;
        $order->save();

        return $order;
    }
}
