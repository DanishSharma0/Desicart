<?php

namespace App\Http\Controllers;

use App\Services\ProductService;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    protected ProductService $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    public function index(Request $request)
    {
        $filters = $request->only(['search', 'category', 'brand', 'min_price', 'max_price', 'rating', 'sort']);
        $products = $this->productService->getFilteredProducts($filters, 12);
        
        $categories = Category::withCount('products')->get();
        $brands = Brand::withCount('products')->get();

        return view('products.index', [
            'products' => $products,
            'categories' => $categories,
            'brands' => $brands,
            'filters' => $filters
        ]);
    }

    public function show($slug)
    {
        $product = $this->productService->getProductDetails($slug);
        if (!$product) {
            abort(404);
        }

        $relatedProducts = Product::where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->limit(4)
            ->get();

        return view('products.show', [
            'product' => $product,
            'related' => $relatedProducts
        ]);
    }

    public function storeReview(Request $request, int $productId)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000'
        ]);

        $review = Review::updateOrCreate(
            [
                'user_id' => Auth::id(),
                'product_id' => $productId
            ],
            [
                'rating' => $request->rating,
                'comment' => $request->comment
            ]
        );

        // Update product average rating
        $product = Product::find($productId);
        if ($product) {
            $avg = $product->reviews()->avg('rating');
            $product->rating = round($avg, 1);
            $product->save();
        }

        return redirect()->back()->with('success', 'Thank you for your review!');
    }

    public function deals(Request $request)
    {
        $filters = $request->only(['search', 'category', 'brand', 'min_price', 'max_price', 'rating', 'sort']);
        $filters['is_deal'] = true;
        
        $products = $this->productService->getFilteredProducts($filters, 12);
        
        $categories = Category::withCount('products')->get();
        $brands = Brand::withCount('products')->get();

        return view('products.deals', [
            'products' => $products,
            'categories' => $categories,
            'brands' => $brands,
            'filters' => $filters
        ]);
    }
}
