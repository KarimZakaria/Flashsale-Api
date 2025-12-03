<?php

namespace App\Repositories;

use App\Models\Hold;

class HoldRepository
{
    public function create(array $data)
    {
        return Hold::create($data);
    }

    public function getActiveHoldsForProduct(int $productId)
    {
        return Hold::where('product_id', $productId)
            ->where('expires_at', '>', now())
            ->get();
    }

    public function findById(int $id): ?Hold
    {
        return Hold::find($id);
    }

    public function markAsUsed(int $holdId)
    {
        $hold = $this->findById($holdId);
        if ($hold) {
            $hold->used_in_order = true;
            $hold->save();
        }
    }
}
