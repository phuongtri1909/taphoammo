<?php

namespace App\Policies;

use App\Models\ProductValue;
use App\Models\User;
use App\Enums\ProductValueStatus;
use Illuminate\Database\Eloquent\Model;

class ProductValuePolicy
{

    private function loadRelationships(ProductValue $productValue): ProductValue
    {
        if (!$productValue->relationLoaded('productVariant')) {
            return ProductValue::with('productVariant.product.seller')->findOrFail($productValue->id);
        }
        return $productValue;
    }

    public function viewData(User $user, ProductValue $productValue): bool
    {
        if ($productValue->sold_to_user_id === $user->id) {
            return true;
        }

        $productValue = $this->loadRelationships($productValue);
        $seller = $productValue->productVariant?->product?->seller;
        if ($seller && $seller->id === $user->id) {
            return true;
        }

        return false;
    }

    public function view(User $user, ProductValue $productValue): bool
    {
        if ($user->hasRole(User::ROLE_ADMIN)) {
            return true;
        }

        $productValue = $this->loadRelationships($productValue);
        $seller = $productValue->productVariant?->product?->seller;
        if ($seller && $seller->id === $user->id) {
            return true;
        }

        if ($productValue->sold_to_user_id === $user->id) {
            return true;
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $user->hasRole(User::ROLE_SELLER);
    }

    public function update(User $user, ProductValue $productValue): bool
    {
        if ($productValue->status !== ProductValueStatus::AVAILABLE) {
            return false;
        }

        $productValue = $this->loadRelationships($productValue);
        $seller = $productValue->productVariant?->product?->seller;
        return $seller && $seller->id === $user->id;
    }

    public function delete(User $user, ProductValue $productValue): bool
    {
        if ($productValue->status !== ProductValueStatus::AVAILABLE) {
            return false;
        }

        $productValue = $this->loadRelationships($productValue);
        $seller = $productValue->productVariant?->product?->seller;
        return $seller && $seller->id === $user->id;
    }

    public function restore(User $user, ProductValue $productValue): bool
    {
        return false;
    }

    public function forceDelete(User $user, ProductValue $productValue): bool
    {
        return false;
    }
}
