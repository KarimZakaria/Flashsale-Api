<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Actions\Hold\CreateHoldAction;

class HoldController extends Controller
{
    public function store(Request $request, CreateHoldAction $action)
    {
        $validated = $request->validate([
            'product_id' => 'required|integer',
            'qty'   => 'required|integer|min:1',
            'user_id'    => 'required|integer',
        ]);

        $hold = $action->execute(
            $validated['product_id'],
            $validated['qty'],
            $validated['user_id']
        );

        return response()->json([
            'status' => 'success',
            'data' => $hold
        ]);
    }
}
