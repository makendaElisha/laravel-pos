@extends('layouts.admin')

@section('content-header', 'Acceuil')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-4 col-6">
            <!-- small box -->
            @if ($user->is_admin)
                <div class="small-box bg-info" data-toggle="modal" data-target="#allDailySales">
            @else
                <div class="small-box bg-info">
            @endif
                <div class="inner">
                    <h4 style="font-weight: bold; padding-bottom: 5px;">Ventes Journalieres:</h4>

                    @if ($user->is_admin)
                        <table class="table">
                            <tr>
                                <td>Total Facture:</td>
                                <td style="font-weight: bold; font-size: 20px;">{{ number_format($dailySells, 0, ',', '.') }} F.C</td>
                            </tr>
                            <tr>
                                <td>Reduction:</td>
                                <td style="font-weight: bold; font-size: 20px;">{{ number_format($dailySellsDiscount, 0, ',', '.') }} F.C</td>
                            </tr>
                            <tr>
                                <td>Total Caisse:</td>
                                <td style="font-weight: bold; font-size: 20px;">{{ number_format($dailySellsAfterDiscount, 0, ',', '.') }} F.C</td>
                            </tr>
                        </table>
                    @else
                        <table class="table table-sm">
                            <tr>
                                <td>Total Facture:</td>
                                <td style="font-weight: bold; font-size: 15px;">{{ number_format($allShopSales, 0, ',', '.') }} F.C</td>
                            </tr>
                            <tr>
                                <td>Reduction:</td>
                                <td style="font-weight: bold; font-size: 15px;">{{ number_format($allShopSalesDiscount, 0, ',', '.') }} F.C</td>
                            </tr>
                            <tr>
                                <td>Total Caisse:</td>
                                <td style="font-weight: bold; font-size: 15px;">{{ number_format($allShopSalesAfterDiscount, 0, ',', '.') }} F.C</td>
                            </tr>
                        </table>
                    @endif
                </div>
                <div class="icon">
                    <i class="ion ion-bag"></i>
                </div>
                {{-- <a href="{{route('orders.index')}}" class="small-box-footer">More info <i
                        class="fas fa-arrow-circle-right"></i></a> --}}
            </div>
        </div>

        @if ($user->is_admin)
            <!-- ./col -->
            <div class="col-lg-4 col-6">
                <!-- small box -->
                <div class="small-box bg-danger" data-toggle="modal" data-target="#allLowStock">
                    <div class="inner">
                        {{-- <h3>{{config('settings.currency_symbol')}} {{number_format($income_today, 2)}}</h3> --}}
                        <h3>{{ count($lowStockProducts) }}</h3>

                        <p>Article á Faible Stock</p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-pie-graph"></i>
                    </div>
                    {{-- <a href="{{route('orders.index')}}" class="small-box-footer">More info <i
                            class="fas fa-arrow-circle-right"></i></a> --}}
                </div>
            </div>
            <!-- ./col -->
            <div class="col-lg-4 col-6">
                <!-- small box -->
                <div class="small-box bg-warning">
                    <div class="inner">
                        <h3>{{ $dailyBills }}</h3>

                        <p>Nombre de factures saisies</p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-person-add"></i>
                    </div>
                    {{-- <a href="{{ route('customers.index') }}" class="small-box-footer">More info <i
                            class="fas fa-arrow-circle-right"></i></a> --}}
                </div>
            </div>
            <!-- ./col -->
        @else
        <div class="col-lg-4 col-6">
            <!-- small box -->
            <div class="small-box bg-danger" data-toggle="modal" data-target="#allLowStock">
                <div class="inner">
                    {{-- <h3>{{config('settings.currency_symbol')}} {{number_format($income_today, 2)}}</h3> --}}
                    <h3>{{ count($lowStockProducts) }}</h3>

                    <p>Article á Faible Stock</p>
                </div>
                <div class="icon">
                    <i class="ion ion-pie-graph"></i>
                </div>
                {{-- <a href="{{route('orders.index')}}" class="small-box-footer">More info <i
                        class="fas fa-arrow-circle-right"></i></a> --}}
            </div>
        </div>

        @endif
    </div>
    @if (!$user->is_admin && count($shopProducts) > 0)
        <div class="row mt-5">
            <div class="col-8">
                <!-- small box -->
                <div class="small-box table-primary">
                    <div class="inner">
                        {{-- <h3>{{config('settings.currency_symbol')}} {{number_format($income_today, 2)}}</h3> --}}
                        <h3>Articles stock modifiés</h3>

                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Code</th>
                                    <th>Nom</th>
                                    <th>Quantité</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $ids = [];
                                @endphp
                                @foreach ($shopProducts as $shopProd)
                                @php
                                    $stock = (floor($shopProd->quantity / $shopProd->product->items_in_box)) . ' CRT Et ' . (floor($shopProd->quantity % $shopProd->product->items_in_box)) . ' PCE';
                                    $ids []= $shopProd->id;
                                @endphp
                                    <tr>
                                        <td>{{$shopProd->product->code}}</td>
                                        <td>{{$shopProd->product->name}}</td>
                                        <td>
                                            <div>{{$stock}}</div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                        <form method="POST" action="{{ route('home.seen', ['ids' => json_encode($ids)]) }}">
                            @csrf
                            <button type="submit">Accuser reception</button>
                        </form>
                    </div>
                    <div class="icon">
                        <i class="ion ion-pie-graph"></i>
                    </div>
                    {{-- <a href="{{route('orders.index')}}" class="small-box-footer">More info <i
                            class="fas fa-arrow-circle-right"></i></a> --}}
                </div>
            </div>
        </div>
    @endif
    <div class="modal fade" id="allDailySales" tabindex="-1" role="dialog" aria-labelledby="updateStockLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="updateStockLabel"><b class="">Ventes du Jour</b>: <span id=""><span>
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Magasin</th>
                                <th>Montant</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($allDailySales as $shop => $orderQuery)
                            <tr>
                                <td class="align-middle" style="font-weight: bold;">{{$shop}}:</td>
                                {{-- <td>{{$amount}}</td> --}}
                                <td>
                                    <table class="table table-sm">
                                        <tr>
                                            <td>Total Facture:</td>
                                            <td style="font-size: 15px;">{{ number_format($orderQuery->sum('total'), 0, ',', '.') }} F.C</td>
                                        </tr>
                                        <tr>
                                            <td>Reduction:</td>
                                            <td style="font-size: 15px;">{{ number_format($orderQuery->sum('discount'), 0, ',', '.') }} F.C</td>
                                        </tr>
                                        <tr>
                                            <td>Total Caisse:</td>
                                            <td style="font-size: 15px;">{{ number_format($orderQuery->sum('total') - $orderQuery->sum('discount'), 0, ',', '.') }} F.C</td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer d-flex flex-row justify-content-between">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Fermer</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="allLowStock" tabindex="-1" role="dialog" aria-labelledby="allLowStock"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="updateStockLabel"><b class="">Articles á Faible stock</b>: <span
                            id=""><span></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Code</th>
                                <th>Nom</th>
                                @if ($user->is_admin)
                                    <th>Stock (Depot)</th>
                                    <th>Modifier</th>
                                @else
                                    <th>Stock (Magasin)</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($lowStockProducts as $product)
                            <tr>
                                <td>{{$product->code}}</td>
                                <td>{{$product->name}}</td>
                                @if ($user->is_admin)
                                    @php
                                        $stock = (floor($product->quantity / $product->items_in_box)) . ' CRT Et ' . (floor($product->quantity % $product->items_in_box)) . ' PCE';
                                    @endphp
                                    <td>
                                        <div>{{$stock}}</div>
                                    </td>
                                    <td>
                                        <a href="{{ route('products.edit', $product->id) }}" class="btn btn-primary"><i
                                                class="fas fa-edit"></i></a>
                                    </td>
                                @else
                                    @php
                                        $stock = (floor($product->shop_quantity / $product->items_in_box)) . ' CRT Et ' . (floor($product->shop_quantity % $product->items_in_box)) . ' PCE';
                                    @endphp
                                    <td>
                                        <div>{{$stock}}</div>
                                    </td>
                                @endif

                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer d-flex flex-row justify-content-between">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Fermer</button>
                </div>
            </div>
        </div>
    </div>
    @endsection
