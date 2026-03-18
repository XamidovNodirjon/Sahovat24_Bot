<?php

namespace App\Repositories;

use App\Models\Product;

class ProductRepository
{
    /**
     * Find product by user and status
     */
    public function findPendingDraftByUser(int $userId): ?Product
    {
        return Product::with('images', 'user')
            ->where('user_id', $userId)
            ->where('status', 'draft') // Adding a temporary draft status before publishing
            ->latest()
            ->first();
    }

    /**
     * Update or create product
     */
    public function updateOrCreateDraft(int $userId, array $data): Product
    {
        return Product::updateOrCreate(
            ['user_id' => $userId, 'status' => 'draft'],
            array_merge(['user_id' => $userId], $data)
        );
    }

    /**
     * Get all products for a user (excluding drafts)
     */
    public function getUserProducts(int $userId)
    {
        return Product::with('images', 'user')
            ->where('user_id', $userId)
            ->whereNotIn('status', ['draft'])
            ->latest()
            ->get();
    }

    /**
     * Get approved products in a category (no location filter) with pagination
     */
    public function getAllByCategory(int $categoryId, int $offset = 0, int $limit = 2)
    {
        return Product::with('images', 'user')
            ->where('category_id', $categoryId)
            ->where('status', 'approved')
            ->latest()
            ->offset($offset)
            ->limit($limit)
            ->get();
    }

    /**
     * Delete a product by ID (only if it belongs to user and is not active)
     */
    public function deleteById(int $productId, int $userId): bool
    {
        $product = Product::where('id', $productId)
            ->where('user_id', $userId)
            ->first();

        if ($product) {
            $product->delete();
            return true;
        }
        return false;
    }

    /**
     * Get approved products within a radius (km) for a given category.
     * Uses Haversine formula via raw SQL with pagination.
     */
    public function getNearbyByCategory(int $categoryId, float $lat, float $lng, float $radiusKm = 50, int $offset = 0, int $limit = 2)
    {
        $haversine = "(6371 * acos(
            cos(radians(?)) *
            cos(radians(latitude)) *
            cos(radians(longitude) - radians(?)) +
            sin(radians(?)) *
            sin(radians(latitude))
        ))";

        return Product::with('images', 'user')
            ->selectRaw("*, {$haversine} AS distance", [$lat, $lng, $lat])
            ->where('category_id', $categoryId)
            ->where('status', 'approved')
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->whereRaw("{$haversine} <= ?", [$lat, $lng, $lat, $radiusKm])
            ->orderBy('distance')
            ->offset($offset)
            ->limit($limit)
            ->get();
    }
}
