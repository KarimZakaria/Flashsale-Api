<?php

namespace App\Actions\Hold;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use App\Repositories\HoldRepository;
use App\Actions\Stock\CalculateAvailableStockAction;
use Carbon\Carbon;

class CreateHoldAction
{
    protected CalculateAvailableStockAction $stockAction;

    public function __construct(protected HoldRepository $holds)
    {
        $this->stockAction = new CalculateAvailableStockAction();
    }

    public function execute(int $productId, int $qty, int $userId)
    {
        $lockKey = "product:{$productId}:lock";

        $lock = Redis::set($lockKey, 1, 'NX', 'EX', 5);

        if (!$lock) {
            throw new \Exception('High traffic. Please try again.');
        }

        try {
            return DB::transaction(function () use ($productId, $qty, $userId) {

                $stockData = $this->stockAction->handleByProductId($productId);

                if ($stockData['available'] < $qty) {
                    throw new \Exception('Not enough stock available.');
                }

                // 2) Create hold record
                $hold = $this->holds->create([
                    'user_id'    => $userId,
                    'product_id' => $productId,
                    'qty'   => $qty,
                    'expires_at' => Carbon::now()->addMinutes(5), // TTL = 3 mins
                ]);

                CalculateAvailableStockAction::clearCache($productId);

                return $hold;
            });
        } finally {
            Redis::del($lockKey);
        }
    }
}
