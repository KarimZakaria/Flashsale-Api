<?php

namespace App\Actions\Order;

use App\Repositories\OrderRepository;
use App\Repositories\HoldRepository;
use App\Actions\Stock\CalculateAvailableStockAction;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CreateOrderAction
{
    public function __construct(
        protected OrderRepository $orders,
        protected HoldRepository $holds
    ) {}

    public function execute(int $holdId)
    {
        return DB::transaction(function () use ($holdId) {

            $hold = $this->holds->findById($holdId);
            if (!$hold) {
                throw new \Exception('Hold not found');
            }

            if ($hold->expires_at < Carbon::now() || $hold->used_in_order) {
                throw new \Exception('Hold expired or already used');
            }

            $total = $hold->qty * $hold->product->price;
            $order = $this->orders->create([
                'hold_id' => $hold->id,
                'status' => 'pending',
                'amount' => $total,
            ]);

            $this->holds->markAsUsed($holdId);

            CalculateAvailableStockAction::clearCache($hold->product_id);

            return $order;
        });
    }
}
