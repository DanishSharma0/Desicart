@php
    $categoryIcons = [
        'fruits' => ['icon' => 'fa-solid fa-apple-whole', 'color' => 'text-rose-500 bg-rose-50'],
        'vegetables' => ['icon' => 'fa-solid fa-carrot', 'color' => 'text-orange-500 bg-orange-50'],
        'dairy' => ['icon' => 'fa-solid fa-cheese', 'color' => 'text-yellow-600 bg-yellow-50'],
        'beverages' => ['icon' => 'fa-solid fa-glass-water', 'color' => 'text-indigo-500 bg-indigo-50'],
        'snacks' => ['icon' => 'fa-solid fa-cookie', 'color' => 'text-amber-700 bg-amber-50'],
        'bakery' => ['icon' => 'fa-solid fa-bread-slice', 'color' => 'text-amber-500 bg-orange-50'],
        'frozen-foods' => ['icon' => 'fa-solid fa-snowflake', 'color' => 'text-sky-400 bg-sky-50'],
        'household-items' => ['icon' => 'fa-solid fa-soap', 'color' => 'text-teal-500 bg-teal-50'],
    ];
@endphp

{{-- Outer spacing wrapper --}}
<div class="w-full px-3 sm:px-5 pt-3">

    {{-- Gradient glow border wrapper — ONLY around the main navbar nav-gradient-border--}}
    <div class="">
        <nav class="relative z-20 w-full bg-white/95 backdrop-blur-md transition-all duration-300 rounded-[18px]">
            <div id="falling-leaves-container" class="absolute inset-0 pointer-events-none z-0 overflow-hidden rounded-[18px]"></div>
            <div class="relative z-10 flex items-center justify-between gap-4 py-3.5 px-4 sm:px-6 max-w-7xl w-full mx-auto">
                <!-- Logo Section -->
                <a href="{{ route('home') }}" class="flex items-center gap-3 transition-transform duration-200 hover:scale-102 flex-shrink-0">
                    <img src="{{ asset('images/desicart_logo.png') }}" alt="FreshiMart Logo" class="h-10 w-10 object-contain rounded-xl shadow-premium-sm" />
                    <span class="text-xl font-extrabold tracking-tight text-slate-900">Desi<span class="text-orange-500">Cart</span></span>
                </a>

                <!-- Search Bar Section (Desktop) with animated border -->
                <div class="hidden lg:flex flex-1 justify-center max-w-xl px-4">
                    <form action="{{ route('products.index') }}" method="GET" class="w-full">
                        <div class="search-gradient-border">
                            <div class="relative flex items-center bg-white rounded-full">
                                <input name="search" type="text" value="{{ request('search') }}" placeholder="Search fresh groceries, organic produce..."
                                       class="w-full rounded-full border-none bg-white px-5 py-2.5 pr-12 text-sm shadow-none transition-all duration-300 focus:outline-none focus:ring-0"
                                       style="border: none !important; box-shadow: none !important;" />
                                <button type="submit" class="search-icon-btn absolute right-2 top-1/2 -translate-y-1/2">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                        <circle cx="11" cy="11" r="8"></circle>
                                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Navigation Actions Section -->
                <div class="flex items-center gap-4">
                    <!-- Wishlist (Desktop) -->
                    @if(!Auth::check() || !Auth::user()->hasRole('shopkeeper'))
                    <a href="{{ route('dashboard.wishlist') }}" class="hidden sm:flex items-center justify-center w-10 h-10 rounded-full border border-slate-100 hover:border-rose-100 hover:bg-rose-50/50 text-slate-600 hover:text-rose-600 transition-all duration-300 shadow-premium-sm">
                        <i class="fa-regular fa-heart text-sm"></i>
                    </a>
                    @endif

                    <!-- User Profile / Auth Actions -->
                    @auth
                        <div x-data="{open:false}" class="relative">
                            <button @click="open = !open" class="flex items-center justify-center w-10 h-10 rounded-full border border-slate-100 hover:border-emerald-100 hover:bg-emerald-50/50 text-slate-600 hover:text-emerald-700 transition-all duration-300 shadow-premium-sm focus:outline-none focus:ring-2 focus:ring-emerald-500/20">
                                <i class="fa-solid fa-user-circle text-lg"></i>
                            </button>
                            <div x-show="open" @click.outside="open = false"
                                 x-transition:enter="transition ease-out duration-100"
                                 x-transition:enter-start="transform opacity-0 scale-95"
                                 x-transition:enter-end="transform opacity-100 scale-100"
                                 x-transition:leave="transition ease-in duration-75"
                                 x-transition:leave-start="transform opacity-100 scale-100"
                                 x-transition:leave-end="transform opacity-0 scale-95"
                                 class="absolute right-0 mt-3 w-52 bg-white rounded-2xl shadow-premium-md border border-slate-100/60 py-2 z-50 overflow-hidden">
                                <div class="px-4 py-2 border-b border-slate-50 mb-1">
                                    <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Account</p>
                                    <p class="text-sm font-bold text-slate-800 truncate">{{ Auth::user()->name }}</p>
                                </div>
                                @if(Auth::user()->hasRole('shopkeeper'))
                                    <a href="{{ route('shopkeeper.dashboard') }}" class="flex items-center gap-2 px-4 py-2.5 text-sm text-slate-700 hover:bg-slate-50 hover:text-emerald-600 transition-colors"><i class="fa-solid fa-chart-line text-[14px]"></i> Dashboard</a>
                                @elseif(Auth::user()->hasRole('admin'))
                                    <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2 px-4 py-2.5 text-sm text-slate-700 hover:bg-slate-50 hover:text-emerald-600 transition-colors"><i class="fa-solid fa-chart-line text-[14px]"></i> Dashboard</a>
                                @else
                                    <a href="{{ route('dashboard') }}" class="flex items-center gap-2 px-4 py-2.5 text-sm text-slate-700 hover:bg-slate-50 hover:text-emerald-600 transition-colors"><i class="fa-solid fa-chart-line text-[14px]"></i> Dashboard</a>
                                @endif
                                <a href="{{ route('profile.edit') }}" class="flex items-center gap-2 px-4 py-2.5 text-sm text-slate-700 hover:bg-slate-50 hover:text-emerald-600 transition-colors"><i class="fa-solid fa-cog text-[14px]"></i> Settings</a>
                                <div class="h-px bg-slate-50 my-1"></div>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="flex items-center gap-2 w-full text-left px-4 py-2.5 text-sm text-red-650 hover:bg-red-50 transition-colors"><i class="fa-solid fa-right-from-bracket text-[14px]"></i> Logout</button>
                                </form>
                            </div>
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="text-sm font-bold text-slate-600 hover:text-emerald-600 transition-colors duration-200 px-3 py-2 rounded-full hover:bg-slate-50">Sign In</a>
                    @endauth

                    <!-- Cart Button -->
                    @if(!Auth::check() || !Auth::user()->hasRole('shopkeeper'))
                    <a href="{{ route('cart.index') }}" class="relative flex items-center gap-2 border border-slate-200 bg-white/95 text-slate-900 px-4 py-2.5 rounded-full transition-all duration-300 transform hover:-translate-y-0.5 shadow-soft-glow cart-glow-btn">
                        <i class="fa-solid fa-bag-shopping text-sm"></i>
                        <span class="hidden md:inline text-xs font-black tracking-wide uppercase">DesiCart</span>
                        @php
                            $cartItemCount = 0;
                            if (Auth::check()) {
                                $cart = \App\Models\Cart::where('user_id', Auth::id())->first();
                            } else {
                                $sessionId = session()->get('cart_session_id');
                                $cart = $sessionId ? \App\Models\Cart::where('session_id', $sessionId)->first() : null;
                            }
                            if ($cart) { $cartItemCount = $cart->items()->where('save_for_later', false)->sum('quantity'); }
                        @endphp
                        @if($cartItemCount > 0)
                            <span class="flex items-center justify-center min-w-5 h-5 bg-orange-500 border-2 border-emerald-600 text-[10px] font-bold text-white rounded-full px-1 shadow-premium-sm">{{ $cartItemCount }}</span>
                        @endif
                    </a>
                    @endif
                </div>
            </div>
        </nav>
    </div>

    {{-- Category Bar — SEPARATE from navbar, subtle background --}}
    <div class="category-bar-wrapper mt-1.5 rounded-xl">
        <div class="flex items-center gap-2.5 py-2 px-4 sm:px-6 max-w-7xl w-full mx-auto overflow-x-auto scrollbar-none">
            {{-- Browse label --}}
            <div class="flex items-center gap-1.5 text-[9px] font-extrabold uppercase tracking-widest text-slate-400 whitespace-nowrap flex-shrink-0">
                <i class="fa-solid fa-tags text-orange-400 text-[10px]"></i>
                Browse
            </div>

            <div class="w-px h-5 bg-slate-200/80 flex-shrink-0"></div>

            @php
                $isDealsActive = request()->routeIs('products.deals');
                $isAllActive = !request('category') && !$isDealsActive;
            @endphp
            <a href="{{ route('products.index') }}"
               class="flex items-center gap-1.5 px-3 py-1.5 rounded-full border text-xs font-bold transition-all duration-300 whitespace-nowrap hover:-translate-y-0.5
               {{ $isAllActive
                  ? 'bg-gradient-premium border-emerald-600 text-white shadow-sm'
                  : 'bg-white border-emerald-200/60 text-slate-700 hover:border-emerald-400 hover:text-emerald-700 hover:bg-emerald-50/30'
               }}">
                <span class="flex items-center justify-center w-4 h-4 rounded-full {{ $isAllActive ? 'bg-white/25 text-white' : 'text-emerald-600 bg-emerald-50' }} text-[8px]">
                    <i class="fa-solid fa-border-all"></i>
                </span>
                <span>All</span>
            </a>

            <a href="{{ route('products.deals') }}"
               class="flex items-center gap-1.5 px-3 py-1.5 rounded-full border text-xs font-bold transition-all duration-300 whitespace-nowrap hover:-translate-y-0.5
               {{ $isDealsActive
                  ? 'bg-gradient-premium border-emerald-600 text-white shadow-sm'
                  : 'bg-amber-50 border-amber-200/60 text-amber-700 hover:border-amber-400 hover:text-amber-800 hover:bg-amber-100/50'
               }}">
                <span class="flex items-center justify-center w-4 h-4 rounded-full {{ $isDealsActive ? 'bg-white/25 text-white' : 'text-amber-600 bg-white' }} text-[8px]">
                    <i class="fa-solid fa-bolt"></i>
                </span>
                <span>Deals</span>
            </a>

            @foreach($categories as $category)
                @php
                    $iconData = $categoryIcons[$category->slug] ?? ['icon' => 'fa-solid fa-tag', 'color' => 'text-emerald-500 bg-emerald-50'];
                    $isActive = request('category') === $category->slug;
                @endphp
                <a href="{{ route('products.index') }}?category={{ $category->slug }}"
                   class="flex items-center gap-1.5 px-3 py-1.5 rounded-full border text-xs font-bold transition-all duration-300 whitespace-nowrap hover:-translate-y-0.5
                   {{ $isActive
                      ? 'bg-gradient-premium border-emerald-600 text-white shadow-sm'
                      : 'bg-white border-emerald-200/60 text-slate-700 hover:border-emerald-400 hover:text-emerald-700 hover:bg-emerald-50/30'
                   }}">
                    <span class="flex items-center justify-center w-4 h-4 rounded-full {{ $isActive ? 'bg-white/25 text-white' : $iconData['color'] }} text-[8px]">
                        <i class="{{ $iconData['icon'] }}"></i>
                    </span>
                    <span>{{ $category->name }}</span>
                </a>
            @endforeach
        </div>
    </div>

    {{-- Promotional Auto-Scrolling Banner --}}
    <div class="mt-2 w-full bg-emerald-50/60 border border-emerald-100/50 rounded-xl overflow-hidden py-2 relative flex items-center">
        <div class="w-full overflow-hidden whitespace-nowrap">
            <div class="animate-marquee text-[11px] font-bold text-emerald-800 tracking-wide">
                
                {{-- Block 1 --}}
                <div class="flex gap-10 pr-10 min-w-max">
                    <span class="flex items-center gap-1.5"><i class="fa-solid fa-bolt text-amber-500 text-sm"></i> FLASH SALE: 20% OFF all organic fruits! Use code FRESH20</span>
                    <span class="flex items-center gap-1.5"><i class="fa-solid fa-truck-fast text-emerald-500 text-sm"></i> Free Delivery on orders over ₹500</span>
                    <span class="flex items-center gap-1.5"><i class="fa-solid fa-tags text-rose-500 text-sm"></i> Weekend Special: Buy 1 Get 1 Free on selected Snacks</span>
                    <span class="flex items-center gap-1.5"><i class="fa-solid fa-leaf text-green-500 text-sm"></i> Fresh mint leaves restocked daily!</span>
                    <span class="flex items-center gap-1.5"><i class="fa-solid fa-credit-card text-sky-500 text-sm"></i> 10% Cashback on HDFC Bank Cards</span>
                    <span class="flex items-center gap-1.5"><i class="fa-solid fa-basket-shopping text-orange-500 text-sm"></i> Mega Grocery Carnival starts this Monday!</span>
                </div>

                {{-- Block 2 (Exact Duplicate for Seamless Loop) --}}
                <div class="flex gap-10 pr-10 min-w-max" aria-hidden="true">
                    <span class="flex items-center gap-1.5"><i class="fa-solid fa-bolt text-amber-500 text-sm"></i> FLASH SALE: 20% OFF all organic fruits! Use code FRESH20</span>
                    <span class="flex items-center gap-1.5"><i class="fa-solid fa-truck-fast text-emerald-500 text-sm"></i> Free Delivery on orders over ₹500</span>
                    <span class="flex items-center gap-1.5"><i class="fa-solid fa-tags text-rose-500 text-sm"></i> Weekend Special: Buy 1 Get 1 Free on selected Snacks</span>
                    <span class="flex items-center gap-1.5"><i class="fa-solid fa-leaf text-green-500 text-sm"></i> Fresh mint leaves restocked daily!</span>
                    <span class="flex items-center gap-1.5"><i class="fa-solid fa-credit-card text-sky-500 text-sm"></i> 10% Cashback on HDFC Bank Cards</span>
                    <span class="flex items-center gap-1.5"><i class="fa-solid fa-basket-shopping text-orange-500 text-sm"></i> Mega Grocery Carnival starts this Monday!</span>
                </div>

            </div>
        </div>
    </div>

</div>
