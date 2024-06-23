<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="{{route('home')}}" class="brand-link">
        <img src="{{ asset('images/logo.png') }}" alt="AdminLTE Logo" class="brand-image img-circle elevation-3"
             style="opacity: .8">
        <span class="brand-text font-weight-light">{{ config('app.name') }}</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar user (optional) -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image">
                <img src="{{ auth()->user()->getAvatar() }}" class="img-circle elevation-2" alt="User Image">
            </div>
            <div class="info">
                <a href="#" class="d-block">{{ auth()->user()->getFullname() }}</a>
            </div>
        </div>

        @php
            $user = Auth::user();
        @endphp

        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <li class="nav-item has-treeview">
                    <a href="{{route('home')}}" class="nav-link">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>Acceuil</p>
                    </a>
                </li>
                @if ($user->is_admin)
                    <li class="nav-item has-treeview">
                        <a href="{{ route('products.index') }}" class="nav-link {{ activeSegment('products') }}">
                            <i class="nav-icon fas fa-shopping-basket"></i>
                            <p>Articles (Depot)</p>
                        </a>
                    </li>
                @endif
                <li class="nav-item has-treeview">
                    <a href="#" class="nav-link {{ activeSegment('shops') }}">
                        <i class="nav-icon fas fa-th-large"></i>
                        <p>Magasins</p>
                    </a>
                    <div class="pl-3">
                        @if ($user->is_admin)
                            @foreach (\App\Models\Shop::get() as $shop)
                                <div class="nav-item has-treeview">
                                    <a href="{{ route('shop.products.index', $shop->id) }}" class="nav-link {{ activeSegment("shop") }}">
                                        <i class="nav-icon fas fa-home"></i>
                                        <p>{{ $shop->name }}</p>
                                    </a>
                                </div>
                            @endforeach
                        @else
                            <div class="nav-item has-treeview">
                                @php
                                    $currentShop = \App\Models\Shop::where('name', $user->shop_name)->first();
                                @endphp
                                <a href="{{ route('shop.products.index', $currentShop->id) }}" class="nav-link {{ activeSegment("shop") }}">
                                    <i class="nav-icon fas fa-home"></i>
                                    <p>{{ $currentShop->name }}</p>
                                </a>
                            </div>
                        @endif
                    </div>
                </li>
                {{-- <li class="nav-item has-treeview">
                    <a href="{{ route('cart.index') }}" class="nav-link {{ activeSegment('cart') }}">
                        <i class="nav-icon fas fa-cart-plus"></i>
                        <p>Open POS</p>
                    </a>
                </li> --}}
                <li class="nav-item has-treeview">
                    <a href="{{ route('orders.index') }}" class="nav-link {{ activeSegment('orders') }}">
                        <i class="nav-icon fas fa-cart-plus"></i>
                        <p>Factures</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('orders.deleted.index') }}" class="nav-link {{ activeSegment('deleted') }}">
                        <i class="nav-icon fas fa-cart-plus text-danger"></i>
                        <p class="text-danger">Factures Supprimées</p>
                    </a>
                </li>{{--
                <li class="nav-item has-treeview">
                    <a href="{{ route('customers.index') }}" class="nav-link {{ activeSegment('customers') }}">
                        <i class="nav-icon fas fa-users"></i>
                        <p>Customers</p>
                    </a>
                </li> --}}
                {{-- <li class="nav-item has-treeview">
                    <a href="{{ route('shops.index') }}" class="nav-link {{ activeSegment('shops') }}">
                        <i class="nav-icon fas fa-users"></i>
                        <p>Gestion Mag</p>
                    </a>
                </li> --}}
                @if ($user->is_admin)
                    <li class="nav-item">
                        <a href="{{ route('shops.stock.movements') }}" class="nav-link {{ activeSegment('transfer') }}">
                            <i class="nav-icon fas fa-truck"></i>
                            <p>Articles reçu magasins</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('article.global.movements') }}" class="nav-link {{ activeSegment('historic') }}">
                            <i class="nav-icon fas fa-history"></i>
                            <p>Articles Mouvements Historic</p>
                        </a>
                    </li>
                    <li class="nav-item has-treeview">
                        <a href="{{ route('settings.index') }}" class="nav-link {{ activeSegment('settings') }}">
                            <i class="nav-icon fas fa-cogs"></i>
                            <p>Settings</p>
                        </a>
                    </li>
                @endif
                <li class="nav-item">
                    <a href="#" class="nav-link" onclick="document.getElementById('logout-form').submit()">
                        <i class="nav-icon fas fa-sign-out-alt"></i>
                        <p>Logout</p>
                        <form action="{{route('logout')}}" method="POST" id="logout-form">
                            @csrf
                        </form>
                    </a>
                </li>
            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>
