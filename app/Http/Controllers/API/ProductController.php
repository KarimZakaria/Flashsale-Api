<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Repositories\ProductRepository;
use App\Actions\Stock\CalculateAvailableStockAction;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;


class ProductController extends Controller
{
    public function show($id, ProductRepository $repo, CalculateAvailableStockAction $calc): JsonResponse
    {
        $cacheKey = "product:{$id}:stock";
        $lockKey  = "product:{$id}:stock:lock";
        $ttl = 8;

        $cached = Cache::get($cacheKey);
        if ($cached) {
            return response()->json($cached);
        }

        $lock = Cache::lock($lockKey, 10);

        if ($lock->get()) {
            try {
                $cached = Cache::get($cacheKey);
                if ($cached) {
                    return response()->json($cached);
                }

                $product = $repo->findById($id);
                if (! $product) {
                    return response()->json(['message' => 'Product not found'], 404);
                }

                $data = $calc->handle($product);

                Cache::put($cacheKey, $data, $ttl);

                return response()->json($data);
            } finally {
                $lock->release();
            }
        } else {
            try {
                usleep(150000);
            } catch (\Throwable $e) {}
            $cached = Cache::get($cacheKey);
            if ($cached) return response()->json($cached);

            try {
                $product = $repo->findById($id);
                if (! $product) {
                    return response()->json(['message' => 'Product not found'], 404);
                }
                $data = $calc->handle($product);
                return response()->json($data);
            } catch (\Throwable $e) {
                Log::error('product.show fallback failed', ['err' => $e->getMessage()]);
                return response()->json(['message' => 'Could not compute stock'], 500);
            }
        }
    }
}
