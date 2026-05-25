<?php

namespace App\Services;

use App\Repositories\ProductRepository;
use App\Models\InventoryLog;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class ProductService
{
    protected ProductRepository $productRepository;

    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    public function getFilteredProducts(array $filters, int $perPage = 12): LengthAwarePaginator
    {


        $query = $this->productRepository->getQuery();

        if (!empty($filters['search'])) {
            $query->where(function ($query) use ($filters) {
                $query->where('name', 'like', '%' . $filters['search'] . '%')
                    ->orWhere('description', 'like', '%' . $filters['search'] . '%')
                    ->orWhereHas('category', function ($q) use ($filters) {
                        $q->where('name', 'like', '%' . $filters['search'] . '%');
                    })
                    ->orWhereHas('brand', function ($q) use ($filters) {
                        $q->where('name', 'like', '%' . $filters['search'] . '%');
                    });
            });
        }

        if (!empty($filters['category'])) {
            $query->whereHas('category', function ($q) use ($filters) {
                $q->where('slug', $filters['category']);
            });
        }

        if (!empty($filters['brand'])) {
            $query->whereHas('brand', function ($q) use ($filters) {
                $q->where('slug', $filters['brand']);
            });
        }

        if (isset($filters['min_price'])) {
            $query->where('price', '>=', $filters['min_price']);
        }

        if (isset($filters['max_price'])) {
            $query->where('price', '<=', $filters['max_price']);
        }

        if (!empty($filters['rating'])) {
            $query->where('rating', '>=', $filters['rating']);
        }

        if (!empty($filters['is_deal'])) {
            $query->whereNotNull('discount_price')->whereColumn('discount_price', '<', 'price');
        }

        // Sorting
        $sort = $filters['sort'] ?? 'latest';
        switch ($sort) {
            case 'price_low':
                $query->orderBy('price', 'asc');
                break;
            case 'price_high':
                $query->orderBy('price', 'desc');
                break;
            case 'popularity':
                $query->orderBy('rating', 'desc');
                break;
            case 'latest':
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }

        return $query->paginate($perPage)->withQueryString();
    }

    public function getProductDetails(string $slug)
    {
        return $this->productRepository->findBySlug($slug);
    }

    public function getHomeProducts(): array
    {
        return [
            'featured' => $this->productRepository->getFeatured(8),
            'trending' => $this->productRepository->getTrendingDeals(8),
            'popular' => $this->productRepository->getPopular(8),
        ];
    }

    public function updateInventory(int $productId, int $quantity, string $type, string $description, ?int $userId = null): bool
    {
        $updated = $this->productRepository->updateStock($productId, $quantity);
        if ($updated) {
            InventoryLog::create([
                'product_id' => $productId,
                'user_id' => $userId,
                'quantity' => $quantity,
                'type' => $type,
                'description' => $description
            ]);
            return true;
        }
        return false;
    }
}
