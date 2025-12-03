<?php

namespace App\Actions\Stock;

use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class CalculateAvailableStockAction
{
    public function handle(Product $product): array
    {
        $cacheKey = "product:{$product->id}:available";

        return Cache::remember($cacheKey, 10, function () use ($product) {

            $reserved = (int) DB::table('holds')
                ->where('product_id', $product->id)
                ->where('used_in_order', false)
                ->where('expires_at', '>', Carbon::now())
                ->sum('qty');

            $available = $product->stock - $reserved;
            if ($available < 0) $available = 0;

            return [
                'product'   => $product,
                'reserved'  => $reserved,
                'available' => $available,
            ];
        });
    }

    public static function clearCache(int $productId)
    {
        Cache::forget("product:{$productId}:available");
    }

        public function handleByProductId(int $productId): array
    {
        $product = Product::findOrFail($productId);
        return $this->handle($product);
    }

}
