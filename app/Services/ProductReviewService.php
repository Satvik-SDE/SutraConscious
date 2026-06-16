<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Product;
use App\Models\ProductReview;
use App\Models\User;

class ProductReviewService
{
    public function userHasPurchased(User $user, Product $product): bool
    {
        return $this->paidOrdersForProductQuery($user, $product)->exists();
    }

    public function latestPaidOrderId(User $user, Product $product): ?int
    {
        return $this->paidOrdersForProductQuery($user, $product)
            ->latest('paid_at')
            ->value('id');
    }

    public function userReview(User $user, Product $product): ?ProductReview
    {
        return ProductReview::query()
            ->where('product_id', $product->id)
            ->where('user_id', $user->id)
            ->first();
    }

    public function displayName(User $user): string
    {
        $parts = preg_split('/\s+/', trim($user->name)) ?: [];

        if ($parts === []) {
            return 'Customer';
        }

        if (count($parts) === 1) {
            return $parts[0];
        }

        return $parts[0] . ' ' . strtoupper(substr(end($parts), 0, 1)) . '.';
    }

    /** @param  array{rating: int, body?: string|null}  $data */
    public function upsert(User $user, Product $product, array $data): ProductReview
    {
        if (! $this->userHasPurchased($user, $product)) {
            abort(403, 'Only verified buyers can review this product.');
        }

        return ProductReview::updateOrCreate(
            [
                'product_id' => $product->id,
                'user_id' => $user->id,
            ],
            [
                'order_id' => $this->latestPaidOrderId($user, $product),
                'rating' => $data['rating'],
                'body' => filled($data['body'] ?? null) ? trim($data['body']) : null,
                'reviewer_name' => $this->displayName($user),
                'is_published' => true,
            ],
        );
    }

    protected function paidOrdersForProductQuery(User $user, Product $product)
    {
        return Order::query()
            ->where('user_id', $user->id)
            ->where('payment_status', Order::PAYMENT_PAID)
            ->whereHas('items.variant', fn ($query) => $query->where('product_id', $product->id));
    }
}
