<?php

use App\Models\Order;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Order::query()
            ->select(['id', 'items'])
            ->orderBy('id')
            ->chunkById(100, function ($orders) {
                foreach ($orders as $order) {
                    $items = is_array($order->items) ? $order->items : [];
                    $updated = false;

                    foreach ($items as $index => $item) {
                        if (! is_array($item) || isset($item['type'])) {
                            continue;
                        }

                        $items[$index]['type'] = Order::ITEM_TYPE_COURSE;
                        $updated = true;
                    }

                    if (! $updated) {
                        continue;
                    }

                    DB::table('orders')
                        ->where('id', $order->id)
                        ->update(['items' => json_encode($items, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)]);
                }
            });
    }

    public function down(): void
    {
        Order::query()
            ->select(['id', 'items'])
            ->orderBy('id')
            ->chunkById(100, function ($orders) {
                foreach ($orders as $order) {
                    $items = is_array($order->items) ? $order->items : [];
                    $updated = false;

                    foreach ($items as $index => $item) {
                        if (! is_array($item) || ($item['type'] ?? null) !== Order::ITEM_TYPE_COURSE) {
                            continue;
                        }

                        unset($items[$index]['type']);
                        $updated = true;
                    }

                    if (! $updated) {
                        continue;
                    }

                    DB::table('orders')
                        ->where('id', $order->id)
                        ->update(['items' => json_encode($items, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)]);
                }
            });
    }
};
