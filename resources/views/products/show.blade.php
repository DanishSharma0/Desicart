<x-app-layout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        <!-- Breadcrumbs -->
        <nav class="flex mb-8 text-sm text-slate-500 font-medium" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    <a href="{{ route('home') }}" class="hover:text-emerald-600 transition-colors flex items-center">
                        <i class="fa-solid fa-house mr-2 text-xs"></i> Home
                    </a>
                </li>
                <li>
                    <div class="flex items-center">
                        <i class="fa-solid fa-chevron-right mx-2 text-[10px] text-slate-400"></i>
                        <a href="{{ route('products.index') }}"
                            class="hover:text-emerald-600 transition-colors">Products</a>
                    </div>
                </li>
                @if($product->category)
                    <li>
                        <div class="flex items-center">
                            <i class="fa-solid fa-chevron-right mx-2 text-[10px] text-slate-400"></i>
                            <a href="{{ route('products.index') }}?category={{ $product->category->slug }}"
                                class="hover:text-emerald-600 transition-colors">{{ $product->category->name }}</a>
                        </div>
                    </li>
                @endif
                <li aria-current="page">
                    <div class="flex items-center">
                        <i class="fa-solid fa-chevron-right mx-2 text-[10px] text-slate-400"></i>
                        <span class="text-slate-400 font-bold truncate max-w-xs">{{ $product->name }}</span>
                    </div>
                </li>
            </ol>
        </nav>

        <!-- Main Product Card -->
        <div class="bg-white border border-slate-100 rounded-3xl p-6 sm:p-8 shadow-sm mb-12">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">

                <!-- Left: Product Image Panel -->
                <div class="lg:col-span-5">
                    <div
                        class="bg-slate-50 rounded-3xl overflow-hidden aspect-square border border-slate-100 relative group">
                        @if($product->has_discount)
                            <div class="absolute left-4 top-4 z-10">
                                @php
                                    $saving = $product->price - $product->discount_price;
                                    $pct = round(($saving / $product->price) * 100);
                                @endphp
                                <span
                                    class="bg-amber-400 text-slate-950 text-xs font-black px-3.5 py-1.5 rounded-full shadow-md">
                                    Save {{ $pct }}%
                                </span>
                            </div>
                        @endif
                        <img src="{{ $product->primary_image_url }}" alt="{{ $product->name }}"
                            class="w-full h-full object-cover" id="main-product-image" />
                    </div>

                    <!-- Thumbnails list (if multiple exist) -->
                    @if($product->images->count() > 1)
                        <div class="grid grid-cols-4 gap-4 mt-4">
                            @foreach($product->images as $img)
                                <button onclick="document.getElementById('main-product-image').src='{{ $img->url }}'"
                                    class="aspect-square bg-slate-50 rounded-xl overflow-hidden border border-slate-100 hover:border-emerald-500 transition-colors p-1">
                                    <img src="{{ $img->url }}" class="w-full h-full object-cover rounded-lg" alt="Thumbnail" />
                                </button>
                            @endforeach
                        </div>
                    @endif
                </div>

                <!-- Right: Product Information Panel -->
                <div class="lg:col-span-7 flex flex-col">
                    <div class="mb-4">
                        @if($product->category)
                            <a href="{{ route('products.index') }}?category={{ $product->category->slug }}"
                                class="inline-block bg-slate-100 hover:bg-emerald-50 text-slate-600 hover:text-emerald-700 font-semibold text-xs px-3 py-1 rounded-full mb-3 uppercase tracking-wider">
                                {{ $product->category->name }}
                            </a>
                        @endif
                        @if($product->brand)
                            <span class="text-xs text-slate-400 font-semibold ml-2">Brand: <strong
                                    class="text-slate-700">{{ $product->brand->name }}</strong></span>
                        @endif
                        <div class="mt-2 mb-2 text-sm text-slate-500 font-semibold">
                            Sold by: <span
                                class="text-emerald-600">{{ $product->shopkeeper ? $product->shopkeeper->name : 'Grocify Platform' }}</span>
                        </div>
                        <h1 class="text-3xl font-black text-slate-900 leading-tight mb-2">{{ $product->name }}</h1>

                        <!-- Review Statistics -->
                        <div class="flex items-center space-x-3 mb-6">
                            <div class="flex items-center text-amber-400 text-sm">
                                <i class="fa-solid fa-star"></i>
                                <span class="text-slate-800 font-bold ml-1.5">{{ $product->rating }}</span>
                            </div>
                            <span class="h-4 w-px bg-slate-200"></span>
                            <a href="#reviews" class="text-sm font-semibold text-emerald-600 hover:text-emerald-700">
                                {{ $product->reviews->count() }} Client Reviews
                            </a>
                        </div>
                    </div>

                    <!-- Pricing Panel -->
                    <div
                        class="bg-slate-50 border border-slate-100 rounded-2xl p-5 mb-6 flex flex-wrap items-center justify-between gap-4">
                        <div>
                            @if($product->has_discount)
                                <div class="flex items-baseline space-x-2.5">
                                    <span
                                        class="text-3xl font-black text-emerald-600">${{ number_format($product->discount_price, 2) }}</span>
                                    <span
                                        class="text-lg text-slate-400 line-through">${{ number_format($product->price, 2) }}</span>
                                </div>
                                <span class="text-xs font-bold text-amber-600 block mt-1"><i
                                        class="fa-solid fa-tags mr-1"></i> You save
                                    ${{ number_format($product->price - $product->discount_price, 2) }}</span>
                            @else
                                <span
                                    class="text-3xl font-black text-slate-900">${{ number_format($product->price, 2) }}</span>
                            @endif
                        </div>

                        <!-- Stock Status Tag -->
                        <div>
                            @if($product->stock > 0)
                                @if($product->stock < 15)
                                    <span
                                        class="inline-flex items-center gap-1.5 text-xs font-bold text-red-600 bg-red-50 border border-red-100 px-3.5 py-1.5 rounded-full">
                                        <span class="w-1.5 h-1.5 rounded-full bg-red-600 animate-ping"></span>
                                        Low Stock ({{ $product->stock }} Left)
                                    </span>
                                @else
                                    <span
                                        class="inline-flex items-center gap-1.5 text-xs font-bold text-emerald-600 bg-emerald-50 border border-emerald-100 px-3.5 py-1.5 rounded-full">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                                        In Stock ({{ $product->stock }} units)
                                    </span>
                                @endif
                            @else
                                <span
                                    class="inline-flex items-center gap-1.5 text-xs font-bold text-slate-400 bg-slate-100 px-3.5 py-1.5 rounded-full">
                                    <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                                    Temporarily Out of Stock
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Short Description -->
                    <div class="text-slate-600 leading-relaxed mb-6">
                        <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Description</h3>
                        <p>{{ $product->description }}</p>
                    </div>

                    <!-- Buy and Cart actions -->
                    @if(!Auth::check() || !Auth::user()->hasRole('shopkeeper'))
                        @if($product->stock > 0)
                            <div class="mt-auto pt-6 border-t border-slate-100 flex flex-col sm:flex-row gap-4 items-center">

                                <!-- Quantity selector -->
                                <div class="w-full sm:w-auto flex items-center justify-between sm:justify-start border border-slate-200 rounded-full p-1 bg-slate-50"
                                    x-data="{ qty: 1 }">
                                    <button type="button" @click="if (qty > 1) qty--"
                                        class="w-10 h-10 rounded-full flex items-center justify-center text-slate-500 hover:bg-slate-200 transition-colors font-bold text-lg">&minus;</button>
                                    <input type="number" name="quantity" x-model="qty" readonly
                                        class="w-12 text-center bg-transparent border-0 focus:ring-0 text-sm font-bold text-slate-800" />
                                    <button type="button" @click="if (qty < {{ $product->stock }}) qty++"
                                        class="w-10 h-10 rounded-full flex items-center justify-center text-slate-500 hover:bg-slate-200 transition-colors font-bold text-lg">&plus;</button>

                                    <form action="{{ route('cart.store') }}" method="POST" id="add-to-cart-form" class="hidden">
                                        @csrf
                                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                                        <input type="hidden" name="quantity" :value="qty">
                                    </form>
                                </div>

                                <!-- Cart Submit Button -->
                                <button type="submit" form="add-to-cart-form"
                                    class="w-full sm:flex-grow bg-slate-900 hover:bg-emerald-600 text-white font-bold py-3.5 px-8 rounded-full shadow-lg hover:shadow-emerald-500/20 transition-all flex items-center justify-center space-x-3">
                                    <i class="fa-solid fa-basket-shopping text-base"></i>
                                    <span>Add to Basket</span>
                                </button>

                                <!-- Wishlist Toggle -->
                                <form action="{{ route('dashboard.wishlist.toggle', $product->id) }}" method="POST"
                                    class="w-full sm:w-auto">
                                    @csrf
                                    @php
                                        $isWishlisted = Auth::check() ? Auth::user()->wishlists()->where('product_id', $product->id)->exists() : false;
                                    @endphp
                                    <button type="submit"
                                        class="w-full sm:w-12 h-12 rounded-full border border-slate-200 flex items-center justify-center text-slate-500 hover:text-red-500 hover:bg-red-50 transition-all font-semibold gap-2">
                                        @if($isWishlisted)
                                            <i class="fa-solid fa-heart text-red-500 text-base"></i>
                                            <span class="inline sm:hidden">Wishlisted</span>
                                        @else
                                            <i class="fa-regular fa-heart text-base"></i>
                                            <span class="inline sm:hidden">Add to Wishlist</span>
                                        @endif
                                    </button>
                                </form>
                            </div>
                        @else
                            <div
                                class="mt-auto pt-6 border-t border-slate-100 flex flex-col sm:flex-row gap-4 items-center w-full">
                                <button disabled
                                    class="w-full bg-slate-100 text-slate-400 font-bold py-4 px-8 rounded-full cursor-not-allowed flex items-center justify-center gap-2">
                                    <i class="fa-solid fa-ban"></i>
                                    <span>Currently Out of Stock</span>
                                </button>

                                <form action="{{ route('dashboard.wishlist.toggle', $product->id) }}" method="POST"
                                    class="w-full sm:w-auto">
                                    @csrf
                                    @php
                                        $isWishlisted = Auth::check() ? Auth::user()->wishlists()->where('product_id', $product->id)->exists() : false;
                                    @endphp
                                    <button type="submit"
                                        class="w-full sm:w-12 h-12 rounded-full border border-slate-200 flex items-center justify-center text-slate-500 hover:text-red-500 hover:bg-red-50 transition-all font-semibold gap-2">
                                        @if($isWishlisted)
                                            <i class="fa-solid fa-heart text-red-500 text-base"></i>
                                        @else
                                            <i class="fa-regular fa-heart text-base"></i>
                                        @endif
                                    </button>
                                </form>
                            </div>
                        @endif
                    @endif
                </div>

            </div>
        </div>

        <!-- Section: Reviews -->
        <div id="reviews" class="bg-white border border-slate-100 rounded-3xl p-6 sm:p-8 shadow-sm">
            <h2 class="text-2xl font-bold text-slate-900 mb-8 flex items-center justify-between">
                <span>Customer Reviews</span>
                <span class="text-sm font-semibold text-slate-500">Average Rating: <strong
                        class="text-slate-800">{{ $product->rating }} / 5</strong></span>
            </h2>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">

                <!-- Review List (Left Column) -->
                <div class="lg:col-span-7 space-y-6">
                    @if($product->reviews->count() > 0)
                        @foreach($product->reviews as $review)
                            <div class="bg-slate-50 rounded-2xl p-5 border border-slate-100">
                                <div class="flex items-center justify-between mb-3">
                                    <div>
                                        <h4 class="font-bold text-slate-800">{{ $review->user->name }}</h4>
                                        <span
                                            class="text-[10px] text-slate-400 font-bold block">{{ $review->created_at->diffForHumans() }}</span>
                                    </div>
                                    <div class="flex text-amber-400 text-xs">
                                        @for($i = 1; $i <= 5; $i++)
                                            @if($i <= $review->rating)
                                                <i class="fa-solid fa-star"></i>
                                            @else
                                                <i class="fa-regular fa-star text-slate-300"></i>
                                            @endif
                                        @endfor
                                    </div>
                                </div>
                                @if($review->comment)
                                    <p class="text-sm text-slate-600 leading-relaxed">{{ $review->comment }}</p>
                                @else
                                    <p class="text-sm text-slate-400 italic">No comment provided.</p>
                                @endif
                            </div>
                        @endforeach
                    @else
                        <div class="text-center py-10 border-2 border-dashed border-slate-200 rounded-2xl bg-slate-50/50">
                            <i class="fa-regular fa-comment-dots text-slate-300 text-3xl mb-3 block"></i>
                            <p class="text-slate-500 text-sm">No reviews yet for this product. Be the first to share your
                                experience!</p>
                        </div>
                    @endif
                </div>

                <!-- Add/Update Review Form (Right Column) -->
                <div class="lg:col-span-5 bg-slate-50 border border-slate-100 rounded-2xl p-5">
                    @auth
                        <h3 class="text-lg font-bold text-slate-800 mb-4">Write a Review</h3>
                        <form action="{{ route('products.review', $product->id) }}" method="POST" class="space-y-4">
                            @csrf

                            <!-- Stars -->
                            <div>
                                <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Your
                                    Rating</label>
                                <select name="rating" required
                                    class="w-full bg-white border border-slate-200 rounded-xl px-3.5 py-2.5 text-sm focus:ring-emerald-500 focus:border-emerald-500 font-medium">
                                    <option value="5">5 Stars (Excellent)</option>
                                    <option value="4">4 Stars (Good)</option>
                                    <option value="3">3 Stars (Average)</option>
                                    <option value="2">2 Stars (Poor)</option>
                                    <option value="1">1 Star (Terrible)</option>
                                </select>
                            </div>

                            <!-- Comment -->
                            <div>
                                <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Review
                                    Comment</label>
                                <textarea name="comment" rows="4" placeholder="Share details of your experience..."
                                    class="w-full bg-white border border-slate-200 rounded-xl px-3.5 py-2.5 text-sm focus:ring-emerald-500 focus:border-emerald-500"></textarea>
                            </div>

                            <button type="submit"
                                class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-3 px-4 rounded-xl text-sm transition-colors shadow-sm">
                                Submit Review
                            </button>
                        </form>
                    @else
                        <div class="text-center py-6">
                            <i class="fa-solid fa-lock text-slate-300 text-2xl mb-3 block"></i>
                            <h4 class="font-bold text-slate-700 mb-2">Login Required</h4>
                            <p class="text-xs text-slate-500 mb-4">Please log in to your account to review this product.</p>
                            <a href="{{ route('login') }}"
                                class="inline-block bg-slate-900 hover:bg-slate-850 text-white font-bold text-xs px-5 py-2.5 rounded-full transition-colors">
                                Log In
                            </a>
                        </div>
                    @endauth
                </div>

            </div>
        </div>

    </div>
</x-app-layout>