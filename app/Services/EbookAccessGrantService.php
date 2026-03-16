<?php

namespace App\Services;

use App\Models\EbookAccessPlan;
use App\Models\EbookCollection;
use App\Models\Order;
use App\Models\UserEbookAccess;
use Carbon\Carbon;

class EbookAccessGrantService
{
    public function grantForOrder(Order $order): void
    {
        if ($order->status !== 'paid' || ! $order->user_id) {
            return;
        }

        foreach ((array) $order->items as $item) {
            $type = (string) ($item['type'] ?? '');
            $id = (int) ($item['id'] ?? 0);

            if ($type === Order::ITEM_TYPE_EBOOK_PLAN && $id > 0) {
                $this->grantPlan($order, $id);
            }

            if ($type === Order::ITEM_TYPE_EBOOK_COLLECTION && $id > 0) {
                $this->grantCollection($order, $id);
            }
        }
    }

    private function grantPlan(Order $order, int $planId): void
    {
        $plan = EbookAccessPlan::find($planId);
        if (! $plan) {
            return;
        }

        [$startsAt, $expiresAt] = $this->resolveWindow(
            (int) $order->user_id,
            $plan->access_scope,
            $plan->ebook_collection_id,
            $plan->duration_days
        );

        UserEbookAccess::firstOrCreate(
            [
                'order_id' => $order->id,
                'source_type' => UserEbookAccess::SOURCE_TYPE_PLAN,
                'source_id' => $plan->id,
            ],
            [
                'user_id' => $order->user_id,
                'access_scope' => $plan->access_scope,
                'ebook_collection_id' => $plan->ebook_collection_id,
                'starts_at' => $startsAt,
                'expires_at' => $expiresAt,
                'status' => true,
            ]
        );
    }

    private function grantCollection(Order $order, int $collectionId): void
    {
        $collection = EbookCollection::find($collectionId);
        if (! $collection) {
            return;
        }

        [$startsAt, $expiresAt] = $this->resolveWindow(
            (int) $order->user_id,
            EbookAccessPlan::SCOPE_COLLECTION,
            $collection->id,
            $collection->access_days
        );

        UserEbookAccess::firstOrCreate(
            [
                'order_id' => $order->id,
                'source_type' => UserEbookAccess::SOURCE_TYPE_COLLECTION,
                'source_id' => $collection->id,
            ],
            [
                'user_id' => $order->user_id,
                'access_scope' => EbookAccessPlan::SCOPE_COLLECTION,
                'ebook_collection_id' => $collection->id,
                'starts_at' => $startsAt,
                'expires_at' => $expiresAt,
                'status' => true,
            ]
        );
    }

    private function resolveWindow(int $userId, string $scope, ?int $collectionId, ?int $durationDays): array
    {
        $startsAt = Carbon::now();

        if ($durationDays) {
            $currentExpiry = UserEbookAccess::query()
                ->where('user_id', $userId)
                ->where('access_scope', $scope)
                ->when($scope === EbookAccessPlan::SCOPE_COLLECTION, function ($query) use ($collectionId) {
                    $query->where('ebook_collection_id', $collectionId);
                })
                ->active()
                ->max('expires_at');

            if ($currentExpiry) {
                $currentExpiry = Carbon::parse($currentExpiry);
                if ($currentExpiry->greaterThan($startsAt)) {
                    $startsAt = $currentExpiry;
                }
            }

            return [$startsAt, $startsAt->copy()->addDays($durationDays)];
        }

        return [$startsAt, null];
    }
}
