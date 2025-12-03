<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Repositories\OrderRepository;
use App\Http\Controllers\Controller;


class PaymentWebhookController extends Controller
{
    protected OrderRepository $orders;

    public function __construct(OrderRepository $orders)
    {
        $this->orders = $orders;
    }

    public function handle(Request $request)
    {
        $data = $request->validate([
            'payment_id' => 'required|string',
            'status' => 'required|in:paid,failed',
        ]);

        // 1) Check if this payment_id already processed (idempotency)
        $order = $this->orders->findByPaymentId($data['payment_id']);

        if ($order) {
            return response()->json([
                'message' => 'Payment already processed',
                'order_id' => $order->id,
                'status' => $order->status
            ]);
        }

        // 2) Find order by hold_id? Or payment_id? If payment_id comes first, you can handle "out-of-order" later

        // For simplicity, let's assume you attach payment_id now:
        // You must have sent the hold_id in your webhook if needed
        // Here we just update the latest pending order for demo

        $pendingOrder = \App\Models\Order::where('status', 'pending')->latest()->first();
        if (!$pendingOrder) {
            return response()->json(['message' => 'No pending order found'], 404);
        }

        // 3) Update order status and attach payment_id
        $updatedOrder = $this->orders->updateStatus($pendingOrder, $data['status'], $data['payment_id']);

        return response()->json([
            'message' => 'Order updated',
            'order_id' => $updatedOrder->id,
            'status' => $updatedOrder->status
        ]);
    }
}
