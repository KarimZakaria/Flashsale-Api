<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Hold extends Model
{
    protected $fillable = ['product_id', 'qty', 'expires_at', 'used_in_order'];

    protected $casts = [
        'expires_at' => 'datetime',
        'used_in_order' => 'boolean'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }


}
