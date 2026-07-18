<div class="sidebar d-flex flex-column p-0" id="sidebar">
    <div class="sidebar-brand">
        <div class="sidebar-brand-content">
            <div class="logo-icon bg-transparent border-0" style="padding: 0; width: 30px; height: 30px; overflow: hidden; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                <img src="/eay-logo.png" alt="Logo" style="object-fit: contain; width: 100%; height: 100%;" />
            </div>
            <span>{{ config('app.name') }}</span>
        </div>
        <button class="btn text-white d-md-none p-0 bg-transparent border-0" id="sidebarClose">
            <i class="bi bi-x-lg fs-4"></i>
        </button>
    </div>

    <div class="nav flex-column mb-auto mt-3">

        <a href="{{ route('admin.dashboard') }}"
            class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <i class="bi bi-grid-fill me-2"></i>
            Dashboard
        </a>

        {{--        <a href="{{ route('admin.dashboard') }}" --}}
        {{--           class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"> --}}
        {{--            <i class="bi bi-grid-fill me-2"></i> --}}
        {{--            Dashboard --}}
        {{--        </a> --}}

        @php $activeAreas = request()->routeIs('admin.areas.*'); @endphp
        <a href="#areasSubmenu" data-bs-toggle="collapse"
            class="nav-link d-flex align-items-center justify-content-between {{ $activeAreas ? 'active' : 'collapsed' }}"
            aria-expanded="{{ $activeAreas ? 'true' : 'false' }}">
            <div><i class="bi bi-geo-alt me-2"></i> Areas</div>
            <i class="bi bi-chevron-down" style="font-size: 0.8rem;"></i>
        </a>
        <div class="collapse {{ $activeAreas ? 'show' : '' }}" id="areasSubmenu">
            <div class="nav flex-column ms-3 ps-3 border-start border-white-50">
                <a href="{{ route('admin.areas.index') }}"
                    class="nav-link small py-1 {{ request()->routeIs('admin.areas.index') ? 'active' : '' }}">
                    View All
                </a>
                <a href="{{ route('admin.areas.create') }}"
                    class="nav-link small py-1 {{ request()->routeIs('admin.areas.create') ? 'active' : '' }}">
                    Add New
                </a>
            </div>
        </div>

        @php $activeDealers = request()->routeIs('admin.dealers.*'); @endphp
        <a href="#dealersSubmenu" data-bs-toggle="collapse"
            class="nav-link d-flex align-items-center justify-content-between {{ $activeDealers ? 'active' : 'collapsed' }}"
            aria-expanded="{{ $activeDealers ? 'true' : 'false' }}">
            <div><i class="bi bi-people me-2"></i> Dealers</div>
            <i class="bi bi-chevron-down" style="font-size: 0.8rem;"></i>
        </a>
        <div class="collapse {{ $activeDealers ? 'show' : '' }}" id="dealersSubmenu">
            <div class="nav flex-column ms-3 ps-3 border-start border-white-50">
                <a href="{{ route('admin.dealers.index') }}"
                    class="nav-link small py-1 {{ request()->routeIs('admin.dealers.index') ? 'active' : '' }}">
                    View All
                </a>
                <a href="{{ route('admin.dealers.create') }}"
                    class="nav-link small py-1 {{ request()->routeIs('admin.dealers.create') ? 'active' : '' }}">
                    Add New
                </a>
            </div>
        </div>

        @php $activeCustomers = request()->routeIs('admin.customer-applications.*'); @endphp
        <a href="#customersSubmenu" data-bs-toggle="collapse"
            class="nav-link d-flex align-items-center justify-content-between {{ $activeCustomers ? 'active' : 'collapsed' }}"
            aria-expanded="{{ $activeCustomers ? 'true' : 'false' }}">
            <div><i class="bi bi-people-fill me-2"></i> Retail Customers</div>
            <i class="bi bi-chevron-down" style="font-size: 0.8rem;"></i>
        </a>
        <div class="collapse {{ $activeCustomers ? 'show' : '' }}" id="customersSubmenu">
            <div class="nav flex-column ms-3 ps-3 border-start border-white-50">
                <a href="{{ route('admin.customer-applications.index') }}"
                    class="nav-link small py-1 {{ request()->routeIs('admin.customer-applications.index') ? 'active' : '' }}">
                    Applications
                </a>
            </div>
        </div>

        @php 
            $activeB2B = request()->routeIs('admin.dealer-applications.*') || request()->routeIs('admin.dealer-orders.*'); 
        @endphp
        <a href="#b2bSubmenu" data-bs-toggle="collapse"
            class="nav-link d-flex align-items-center justify-content-between {{ $activeB2B ? 'active' : 'collapsed' }}"
            aria-expanded="{{ $activeB2B ? 'true' : 'false' }}">
            <div><i class="bi bi-briefcase me-2"></i> B2B Portal</div>
            <i class="bi bi-chevron-down" style="font-size: 0.8rem;"></i>
        </a>
        <div class="collapse {{ $activeB2B ? 'show' : '' }}" id="b2bSubmenu">
            <div class="nav flex-column ms-3 ps-3 border-start border-white-50">
                <a href="{{ route('admin.dealer-applications.index') }}"
                    class="nav-link small py-1 {{ request()->routeIs('admin.dealer-applications.*') ? 'active' : '' }}">
                    Applications
                </a>
                <a href="{{ route('admin.dealer-orders.index') }}"
                    class="nav-link small py-1 {{ request()->routeIs('admin.dealer-orders.*') ? 'active' : '' }}">
                    Wholesale Orders
                </a>
            </div>
        </div>

        @php $activeOrders = request()->routeIs('admin.orders.*'); @endphp
        <a href="{{ route('admin.orders.index') }}"
            class="nav-link {{ $activeOrders ? 'active' : '' }}">
            <i class="bi bi-cart-fill me-2"></i>
            Retail Orders
        </a>

        @php $activeOrderStatus = request()->routeIs('admin.order-statuses.*'); @endphp
        <a href="#orderStatusSubmenu" data-bs-toggle="collapse"
            class="nav-link d-flex align-items-center justify-content-between {{ $activeOrderStatus ? 'active' : 'collapsed' }}"
            aria-expanded="{{ $activeOrderStatus ? 'true' : 'false' }}">
            <div><i class="bi bi-list-stars me-2"></i> Order Statuses</div>
            <i class="bi bi-chevron-down" style="font-size: 0.8rem;"></i>
        </a>
        <div class="collapse {{ $activeOrderStatus ? 'show' : '' }}" id="orderStatusSubmenu">
            <div class="nav flex-column ms-3 ps-3 border-start border-white-50">
                <a href="{{ route('admin.order-statuses.index') }}"
                    class="nav-link small py-1 {{ request()->routeIs('admin.order-statuses.index') ? 'active' : '' }}">
                    View All
                </a>
                <a href="{{ route('admin.order-statuses.create') }}"
                    class="nav-link small py-1 {{ request()->routeIs('admin.order-statuses.create') ? 'active' : '' }}">
                    Add New
                </a>
            </div>
        </div>

        @php 
            $activeHomeSettings = request()->routeIs('admin.banners.*') || request()->routeIs('admin.showcase-videos.*') || request()->routeIs('admin.home-categories.*') || request()->routeIs('admin.footer-settings.*'); 
        @endphp
        <a href="#homeSettingsSubmenu" data-bs-toggle="collapse"
            class="nav-link d-flex align-items-center justify-content-between {{ $activeHomeSettings ? 'active' : 'collapsed' }}"
            aria-expanded="{{ $activeHomeSettings ? 'true' : 'false' }}">
            <div><i class="bi bi-house-gear me-2"></i> Home Settings</div>
            <i class="bi bi-chevron-down" style="font-size: 0.8rem;"></i>
        </a>
        <div class="collapse {{ $activeHomeSettings ? 'show' : '' }}" id="homeSettingsSubmenu">
            <div class="nav flex-column ms-3 ps-3 border-start border-white-50">
                <!-- Banners Group -->
                <div class="mb-2">
                    <span class="small fw-bold px-2 py-1 d-block text-uppercase" style="font-size: 0.7rem; letter-spacing: 0.05em; color: #38bdf8;">Banners</span>
                    <a href="{{ route('admin.banners.index') }}"
                        class="nav-link small py-1 ps-3 {{ request()->routeIs('admin.banners.index') ? 'active' : '' }}">
                        View All
                    </a>
                    <a href="{{ route('admin.banners.create') }}"
                        class="nav-link small py-1 ps-3 {{ request()->routeIs('admin.banners.create') ? 'active' : '' }}">
                        Add New
                    </a>
                </div>

                <!-- Showcase Videos Group -->
                <div class="mb-2">
                    <span class="small fw-bold px-2 py-1 d-block text-uppercase" style="font-size: 0.7rem; letter-spacing: 0.05em; color: #38bdf8;">Showcase Videos</span>
                    <a href="{{ route('admin.showcase-videos.index') }}"
                        class="nav-link small py-1 ps-3 {{ request()->routeIs('admin.showcase-videos.index') ? 'active' : '' }}">
                        View All
                    </a>
                    <a href="{{ route('admin.showcase-videos.create') }}"
                        class="nav-link small py-1 ps-3 {{ request()->routeIs('admin.showcase-videos.create') ? 'active' : '' }}">
                        Add New
                    </a>
                </div>

                <!-- Category Cards Group -->
                <div class="mt-2 mb-1">
                    <span class="small fw-bold px-2 py-1 d-block text-uppercase" style="font-size: 0.7rem; letter-spacing: 0.05em; color: #38bdf8;">Category Cards</span>
                    <a href="{{ route('admin.home-categories.index') }}"
                        class="nav-link small py-1 ps-3 {{ request()->routeIs('admin.home-categories.index') ? 'active' : '' }}">
                        View All
                    </a>
                    <a href="{{ route('admin.home-categories.create') }}"
                        class="nav-link small py-1 ps-3 {{ request()->routeIs('admin.home-categories.create') ? 'active' : '' }}">
                        Add New
                    </a>
                </div>

                <!-- Footer Settings Group -->
                <div class="mt-2 mb-1">
                    <span class="small fw-bold px-2 py-1 d-block text-uppercase" style="font-size: 0.7rem; letter-spacing: 0.05em; color: #38bdf8;">Footer Settings</span>
                    <a href="{{ route('admin.footer-settings.edit') }}"
                        class="nav-link small py-1 ps-3 {{ request()->routeIs('admin.footer-settings.edit') ? 'active' : '' }}">
                        Manage Footer
                    </a>
                </div>
            </div>
        </div>

        @php 
            $activePages = request()->routeIs('admin.pages.*'); 
        @endphp
        <a href="#pagesSubmenu" data-bs-toggle="collapse"
            class="nav-link d-flex align-items-center justify-content-between {{ $activePages ? 'active' : 'collapsed' }}"
            aria-expanded="{{ $activePages ? 'true' : 'false' }}">
            <div><i class="bi bi-file-earmark-text me-2"></i> Corporate Pages</div>
            <i class="bi bi-chevron-down" style="font-size: 0.8rem;"></i>
        </a>
        <div class="collapse {{ $activePages ? 'show' : '' }}" id="pagesSubmenu">
            <div class="nav flex-column ms-3 ps-3 border-start border-white-50">
                <a href="{{ route('admin.pages.edit', 'about') }}"
                    class="nav-link small py-1 {{ request()->route() && request()->route()->parameter('page') === 'about' ? 'active' : '' }}">
                    About Us
                </a>
                <a href="{{ route('admin.pages.edit', 'faq') }}"
                    class="nav-link small py-1 {{ request()->route() && request()->route()->parameter('page') === 'faq' ? 'active' : '' }}">
                    FAQ Page
                </a>
                <a href="{{ route('admin.pages.edit', 'privacy') }}"
                    class="nav-link small py-1 {{ request()->route() && request()->route()->parameter('page') === 'privacy' ? 'active' : '' }}">
                    Privacy Policy
                </a>
                <a href="{{ route('admin.pages.edit', 'terms') }}"
                    class="nav-link small py-1 {{ request()->route() && request()->route()->parameter('page') === 'terms' ? 'active' : '' }}">
                    Terms of Service
                </a>
            </div>
        </div>

        @php $activeCategories = request()->routeIs('admin.categories.*'); @endphp
        <a href="#categoriesSubmenu" data-bs-toggle="collapse"
            class="nav-link d-flex align-items-center justify-content-between {{ $activeCategories ? 'active' : 'collapsed' }}"
            aria-expanded="{{ $activeCategories ? 'true' : 'false' }}">
            <div><i class="bi bi-tags me-2"></i> Categories</div>
            <i class="bi bi-chevron-down" style="font-size: 0.8rem;"></i>
        </a>
        <div class="collapse {{ $activeCategories ? 'show' : '' }}" id="categoriesSubmenu">
            <div class="nav flex-column ms-3 ps-3 border-start border-white-50">
                <a href="{{ route('admin.categories.index') }}"
                    class="nav-link small py-1 {{ request()->routeIs('admin.categories.index') ? 'active' : '' }}">
                    View All
                </a>
                <a href="{{ route('admin.categories.create') }}"
                    class="nav-link small py-1 {{ request()->routeIs('admin.categories.create') ? 'active' : '' }}">
                    Add New
                </a>
            </div>
        </div>

        @php $activeProducts = request()->routeIs('admin.products.*'); @endphp
        <a href="#productsSubmenu" data-bs-toggle="collapse"
            class="nav-link d-flex align-items-center justify-content-between {{ $activeProducts ? 'active' : 'collapsed' }}"
            aria-expanded="{{ $activeProducts ? 'true' : 'false' }}">
            <div><i class="bi bi-box-seam me-2"></i> Products</div>
            <i class="bi bi-chevron-down" style="font-size: 0.8rem;"></i>
        </a>
        <div class="collapse {{ $activeProducts ? 'show' : '' }}" id="productsSubmenu">
            <div class="nav flex-column ms-3 ps-3 border-start border-white-50">
                <a href="{{ route('admin.products.index') }}"
                    class="nav-link small py-1 {{ request()->routeIs('admin.products.index') ? 'active' : '' }}">
                    View All
                </a>
                <a href="{{ route('admin.products.create') }}"
                    class="nav-link small py-1 {{ request()->routeIs('admin.products.create') ? 'active' : '' }}">
                    Add New
                </a>
            </div>
        </div>

        @php
            $activeBuilder = request()->routeIs('admin.builder-models.*') || request()->routeIs('admin.builder-patterns.*') || request()->routeIs('admin.builder-logos.*');
        @endphp
        <a href="#builderSubmenu" data-bs-toggle="collapse"
            class="nav-link d-flex align-items-center justify-content-between {{ $activeBuilder ? 'active' : 'collapsed' }}"
            aria-expanded="{{ $activeBuilder ? 'true' : 'false' }}">
            <div><i class="bi bi-layers me-2"></i> 3D Builder</div>
            <i class="bi bi-chevron-down" style="font-size: 0.8rem;"></i>
        </a>
        <div class="collapse {{ $activeBuilder ? 'show' : '' }}" id="builderSubmenu">
            <div class="nav flex-column ms-3 ps-3 border-start border-white-50">
                <span class="text-white-50 small fw-bold px-3 py-1" style="font-size: 0.75rem;">Models</span>
                <a href="{{ route('admin.builder-models.index') }}"
                    class="nav-link small py-1 {{ request()->routeIs('admin.builder-models.index') ? 'active' : '' }}">
                    View All Models
                </a>
                <a href="{{ route('admin.builder-models.create') }}"
                    class="nav-link small py-1 {{ request()->routeIs('admin.builder-models.create') ? 'active' : '' }}">
                    Add New Model
                </a>
                <span class="text-white-50 small fw-bold px-3 py-1 mt-2" style="font-size: 0.75rem;">Patterns</span>
                <a href="{{ route('admin.builder-patterns.index') }}"
                    class="nav-link small py-1 {{ request()->routeIs('admin.builder-patterns.index') ? 'active' : '' }}">
                    View All Patterns
                </a>
                <a href="{{ route('admin.builder-patterns.create') }}"
                    class="nav-link small py-1 {{ request()->routeIs('admin.builder-patterns.create') ? 'active' : '' }}">
                    Add New Pattern
                </a>
                <span class="text-white-50 small fw-bold px-3 py-1 mt-2" style="font-size: 0.75rem;">Logos</span>
                <a href="{{ route('admin.builder-logos.index') }}"
                    class="nav-link small py-1 {{ request()->routeIs('admin.builder-logos.index') ? 'active' : '' }}">
                    View All Logos
                </a>
                <a href="{{ route('admin.builder-logos.create') }}"
                    class="nav-link small py-1 {{ request()->routeIs('admin.builder-logos.create') ? 'active' : '' }}">
                    Add New Logo
                </a>
            </div>
        </div>

        @php $activeSizeCharts = request()->routeIs('admin.size-charts.*'); @endphp
        <a href="{{ route('admin.size-charts.index') }}"
            class="nav-link {{ $activeSizeCharts ? 'active' : '' }}">
            <i class="bi bi-rulers me-2"></i>
            Size Charts
        </a>

        @php $activeCoupons = request()->routeIs('admin.coupons.*'); @endphp
        <a href="{{ route('admin.coupons.index') }}"
            class="nav-link {{ $activeCoupons ? 'active' : '' }}">
            <i class="bi bi-ticket-perforated-fill me-2"></i>
            Coupons
        </a>

        @php $activeContactQueries = request()->routeIs('admin.contact-queries.*'); @endphp
        <a href="{{ route('admin.contact-queries.index') }}"
            class="nav-link {{ $activeContactQueries ? 'active' : '' }}">
            <i class="bi bi-chat-left-text-fill me-2"></i>
            Contact Queries
        </a>
        {{--        @php $activeRiders = request()->routeIs('admin.riders*'); @endphp --}}
        {{--        <a href="#ridersSubmenu" data-bs-toggle="collapse" --}}
        {{--           class="nav-link d-flex align-items-center justify-content-between {{ $activeRiders ? 'active' : 'collapsed' }}" --}}
        {{--           aria-expanded="{{ $activeRiders ? 'true' : 'false' }}"> --}}
        {{--            <div><i class="bi bi-bicycle me-2"></i> Riders</div> --}}
        {{--            <i class="bi bi-chevron-down" style="font-size: 0.8rem;"></i> --}}
        {{--        </a> --}}
        {{--        <div class="collapse {{ $activeRiders ? 'show' : '' }}" id="ridersSubmenu"> --}}
        {{--            <div class="nav flex-column ms-3 ps-3 border-start border-white-50"> --}}
        {{--                <a href="{{ route('admin.riders.index') }}" --}}
        {{--                   class="nav-link small py-1 {{ request()->routeIs('admin.riders.index') ? 'active' : '' }}">View --}}
        {{--                    All</a> --}}
        {{--                <a href="{{ route('admin.riders.create') }}" --}}
        {{--                   class="nav-link small py-1 {{ request()->routeIs('admin.riders.create') ? 'active' : '' }}">Add --}}
        {{--                    New</a> --}}
        {{--            </div> --}}
        {{--        </div> --}}

        {{--        <a href="{{ route('admin.rider-enquiries.index') }}" --}}
        {{--           class="nav-link {{ request()->routeIs('admin.rider-enquiries.*') ? 'active' : '' }}"> --}}
        {{--            <i class="bi bi-card-checklist me-2"></i> --}}
        {{--            Rider Enquiries --}}
        {{--        </a> --}}

        {{--        <a href="{{ route('admin.users.index') }}" --}}
        {{--           class="nav-link {{ request()->routeIs('admin.users*') ? 'active' : '' }}"> --}}
        {{--            <i class="bi bi-people me-2"></i> --}}
        {{--            Users --}}
        {{--        </a> --}}

        {{--        @php $activeServices = request()->routeIs('admin.services*'); @endphp --}}
        {{--        <a href="#servicesSubmenu" data-bs-toggle="collapse" --}}
        {{--           class="nav-link d-flex align-items-center justify-content-between {{ $activeServices ? 'active' : 'collapsed' }}" --}}
        {{--           aria-expanded="{{ $activeServices ? 'true' : 'false' }}"> --}}
        {{--            <div><i class="bi bi-gear me-2"></i> Services</div> --}}
        {{--            <i class="bi bi-chevron-down" style="font-size: 0.8rem;"></i> --}}
        {{--        </a> --}}

        {{--        <div class="collapse {{ $activeServices ? 'show' : '' }}" id="servicesSubmenu"> --}}
        {{--            <div class="nav flex-column ms-3 ps-3 border-start border-white-50"> --}}
        {{--                <a href="{{ route('admin.services.index') }}" --}}
        {{--                   class="nav-link small py-1 {{ request()->routeIs('admin.services.index') ? 'active' : '' }}">View --}}
        {{--                    All</a> --}}
        {{--                <a href="{{ route('admin.services.create') }}" --}}
        {{--                   class="nav-link small py-1 {{ request()->routeIs('admin.services.create') ? 'active' : '' }}">Add --}}
        {{--                    New</a> --}}
        {{--            </div> --}}
        {{--        </div> --}}

        {{--        @php $activeEnquiries = request()->routeIs('admin.enquiries*'); @endphp --}}
        {{--        <a href="#enquiriesSubmenu" data-bs-toggle="collapse" --}}
        {{--           class="nav-link d-flex align-items-center justify-content-between {{ $activeEnquiries ? 'active' : 'collapsed' }}" --}}
        {{--           aria-expanded="{{ $activeEnquiries ? 'true' : 'false' }}"> --}}
        {{--            <div><i class="bi bi-question-circle me-2"></i> Enquiries</div> --}}
        {{--            <i class="bi bi-chevron-down" style="font-size: 0.8rem;"></i> --}}
        {{--        </a> --}}
        {{--        <div class="collapse {{ $activeEnquiries ? 'show' : '' }}" id="enquiriesSubmenu"> --}}
        {{--            <div class="nav flex-column ms-3 ps-3 border-start border-white-50"> --}}
        {{--                <a href="{{ route('admin.enquiries.index') }}" --}}
        {{--                   class="nav-link small py-1 {{ request()->routeIs('admin.enquiries.index') ? 'active' : '' }}">View --}}
        {{--                    All</a> --}}
        {{--            </div> --}}
        {{--        </div> --}}

        {{--        @php $activeServiceItems = request()->routeIs('admin.service_items*'); @endphp --}}
        {{--        <a href="#serviceItemsSubmenu" data-bs-toggle="collapse" --}}
        {{--           class="nav-link d-flex align-items-center justify-content-between {{ $activeServiceItems ? 'active' : 'collapsed' }}" --}}
        {{--           aria-expanded="{{ $activeServiceItems ? 'true' : 'false' }}"> --}}
        {{--            <div><i class="bi bi-tag me-2"></i> Service Items</div> --}}
        {{--            <i class="bi bi-chevron-down" style="font-size: 0.8rem;"></i> --}}
        {{--        </a> --}}
        {{--        <div class="collapse {{ $activeServiceItems ? 'show' : '' }}" id="serviceItemsSubmenu"> --}}
        {{--            <div class="nav flex-column ms-3 ps-3 border-start border-white-50"> --}}
        {{--                <a href="{{ route('admin.service_items.index') }}" --}}
        {{--                   class="nav-link small py-1 {{ request()->routeIs('admin.service_items.index') ? 'active' : '' }}">View --}}
        {{--                    All</a> --}}
        {{--                <a href="{{ route('admin.service_items.create') }}" --}}
        {{--                   class="nav-link small py-1 {{ request()->routeIs('admin.service_items.create') ? 'active' : '' }}">Add --}}
        {{--                    New</a> --}}
        {{--            </div> --}}
        {{--        </div> --}}

        {{--        <hr class="my-1"> --}}

        {{--        <a href="{{ config('application.accounting_software_link') }}" --}}
        {{--           class="nav-link"> --}}
        {{--            <i class="bi bi-grid-fill me-2"></i> --}}
        {{--            Accounting Software --}}
        {{--        </a> --}}

        <hr class="my-4">

        {{-- <a href="#" class="nav-link {{ request()->routeIs('admin.settings*') ? 'active' : '' }}">
            <i class="bi bi-gear me-2"></i>
            Settings
        </a> --}}

        <form method="POST" action="{{ route('admin.logout') }}" class="px-3 mb-3 mt-3">
            @csrf
            <button type="submit"
                class="btn btn-outline-danger w-100 d-flex align-items-center justify-content-center">
                <i class="bi bi-box-arrow-right me-2"></i> Logout
            </button>
        </form>
    </div>
</div>
