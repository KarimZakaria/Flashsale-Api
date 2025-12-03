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

        $order = $this->orders->findByPaymentId($data['payment_id']);

        if ($order) {
            return response()->json([
                'message' => 'Payment already processed',
                'order_id' => $order->id,
                'status' => $order->status
            ]);
        }


        $pendingOrder = \App\Models\Order::where('status', 'pending')->latest()->first();
        if (!$pendingOrder) {
            return response()->json(['message' => 'No pending order found'], 404);
        }

        $updatedOrder = $this->orders->updateStatus($pendingOrder, $data['status'], $data['payment_id']);

        return response()->json([
            'message' => 'Order updated',
            'order_id' => $updatedOrder->id,
            'status' => $updatedOrder->status
        ]);
    }
}
