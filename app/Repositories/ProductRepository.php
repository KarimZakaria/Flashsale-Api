<?php
namespace App\Repositories;

use App\Models\Product;

class ProductRepository
{
    public function findById(int $id): ?Product
    {
        return Product::find($id);
    }

    public function findForUpdate(int $id)
    {
        return Product::where('id', $id)->lockForUpdate()->first();
    }
    // sonnet 4.5 opus
}