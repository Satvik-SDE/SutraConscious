<?php

namespace App\Services;

use App\Models\Product;
use App\Models\User;
use App\Models\WishlistItem;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class WishlistService
{
    /** @var list<int>|null */
    protected ?array $cachedProductIds = null;

    /** @return list<int> */
    public function productIds(): array
    {
        if ($this->cachedProductIds !== null) {
            return $this->cachedProductIds;
        }

        return $this->cachedProductIds = $this->query()->pluck('product_id')->all();
    }

    public function count(): int
    {
        return count($this->productIds());
    }

    public function has(Product|int $product): bool
    {
        $id = $product instanceof Product ? $product->id : $product;

        return $this->query()->where('product_id', $id)->exists();
    }

    public function toggle(Product $product): bool
    {
        $existing = $this->query()->where('product_id', $product->id)->first();

        if ($existing) {
            $existing->delete();
            $this->flushCache();

            return false;
        }

        WishlistItem::create([
            ...$this->ownerAttributes(),
            'product_id' => $product->id,
        ]);

        $this->flushCache();

        return true;
    }

    public function remove(Product $product): void
    {
        $this->query()->where('product_id', $product->id)->delete();
        $this->flushCache();
    }

    /** @return Collection<int, WishlistItem> */
    public function items(): Collection
    {
        return $this->query()
            ->with(['product.images', 'product.variants', 'product.category'])
            ->latest()
            ->get();
    }

    public function mergeSessionIntoUser(User $user): void
    {
        $sessionId = Session::getId();

        $guestItems = WishlistItem::query()
            ->where('session_id', $sessionId)
            ->whereNull('user_id')
            ->get();

        foreach ($guestItems as $item) {
            WishlistItem::firstOrCreate([
                'user_id' => $user->id,
                'product_id' => $item->product_id,
            ], [
                'session_id' => null,
            ]);

            $item->delete();
        }

        $this->flushCache();
    }

    protected function flushCache(): void
    {
        $this->cachedProductIds = null;
    }

    protected function query()
    {
        if (Auth::check()) {
            return WishlistItem::query()->where('user_id', Auth::id());
        }

        return WishlistItem::query()
            ->where('session_id', Session::getId())
            ->whereNull('user_id');
    }

    /** @return array{user_id: ?int, session_id: ?string} */
    protected function ownerAttributes(): array
    {
        if (Auth::check()) {
            return [
                'user_id' => Auth::id(),
                'session_id' => null,
            ];
        }

        return [
            'user_id' => null,
            'session_id' => Session::getId(),
        ];
    }
}
