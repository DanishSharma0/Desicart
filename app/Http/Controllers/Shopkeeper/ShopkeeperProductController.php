<?php

namespace App\Http\Controllers\Shopkeeper;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;

class ShopkeeperProductController extends Controller
{
    public function index(Request $request): View
    {
        $products = $request->user()->products()->with(['category', 'brand', 'images'])->latest()->paginate(10);
        return view('shopkeeper.products.index', compact('products'));
    }

    public function create(): View
    {
        $categories = Category::all();
        $brands = Brand::all();
        return view('shopkeeper.products.create', compact('categories', 'brands'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'discount_price' => 'nullable|numeric|min:0|lt:price',
            'stock' => 'required|integer|min:0',
            'category_id' => 'required|exists:categories,id',
            'brand_id' => 'required|exists:brands,id',
            'image_url' => 'required|url',
        ]);

        $product = $request->user()->products()->create([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']) . '-' . uniqid(),
            'description' => $validated['description'],
            'price' => $validated['price'],
            'discount_price' => $validated['discount_price'] ?? null,
            'stock' => $validated['stock'],
            'category_id' => $validated['category_id'],
            'brand_id' => $validated['brand_id'],
            'rating' => 0,
        ]);

        ProductImage::create([
            'product_id' => $product->id,
            'url' => $validated['image_url'],
            'is_primary' => true,
        ]);

        return redirect()->route('shopkeeper.products.index')->with('success', 'Product created successfully.');
    }

    public function edit(Product $product): View
    {
        if ($product->shopkeeper_id !== auth()->id()) {
            abort(403);
        }

        $categories = Category::all();
        $brands = Brand::all();
        return view('shopkeeper.products.edit', compact('product', 'categories', 'brands'));
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        if ($product->shopkeeper_id !== auth()->id()) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'discount_price' => 'nullable|numeric|min:0|lt:price',
            'stock' => 'required|integer|min:0',
            'category_id' => 'required|exists:categories,id',
            'brand_id' => 'required|exists:brands,id',
        ]);

        $product->update([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']) . '-' . uniqid(),
            'description' => $validated['description'],
            'price' => $validated['price'],
            'discount_price' => $validated['discount_price'] ?? null,
            'stock' => $validated['stock'],
            'category_id' => $validated['category_id'],
            'brand_id' => $validated['brand_id'],
        ]);

        return redirect()->route('shopkeeper.products.index')->with('success', 'Product updated successfully.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        if ($product->shopkeeper_id !== auth()->id()) {
            abort(403);
        }

        $product->delete();

        return redirect()->route('shopkeeper.products.index')->with('success', 'Product deleted successfully.');
    }
}
