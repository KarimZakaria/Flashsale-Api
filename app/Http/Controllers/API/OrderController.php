<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Actions\Order\CreateOrderAction;

class OrderController extends Controller
{
    public function store(Request $request, CreateOrderAction $action)
    {
        $order = $action->execute($request->hold_id, $request->payment_data);
        return response()->json($order);
    }

}
