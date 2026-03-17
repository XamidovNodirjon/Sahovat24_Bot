<?php

namespace App\Repositories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Collection;

class CategoryRepository
{
    /**
     * Get parent categories
     */
    public function getParents(): Collection
    {
        return Category::whereNull('parent_id')->get();
    }

    /**
     * Get child categories by parent_id
     */
    public function getByParentId(int $parentId): Collection
    {
        return Category::where('parent_id', $parentId)->get();
    }
}
