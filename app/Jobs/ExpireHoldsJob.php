<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use App\Actions\Stock\CalculateAvailableStockAction;
use Carbon\Carbon;

class ExpireHoldsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle()
    {
        $now = Carbon::now();

        // Find expired holds not used in orders
        $expiredHolds = DB::table('holds')
            ->where('used_in_order', false)
            ->where('expires_at', '<', $now)
            ->get();

        foreach ($expiredHolds as $hold) {

            // Delete or mark expired
            DB::table('holds')->where('id', $hold->id)->delete();

            // Clear cache for the affected product
            CalculateAvailableStockAction::clearCache($hold->product_id);
        }
    }
}
