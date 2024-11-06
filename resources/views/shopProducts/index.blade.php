@extends('layouts.admin')

@php
    $title = 'Product List - ' . $shop->name
@endphp

@section('title', $title)
@section('content-header', $title)
@section('content-actions')
<a href="{{route('shop.cart.index', $shop->id)}}" class="btn btn-success">
    <i class="fas fa-cart-plus pr-1"></i>Facturation
</a>
@endsection
@section('css')
<link rel="stylesheet" href="{{ asset('plugins/sweetalert2/sweetalert2.min.css') }}">
@endsection
@section('content')
<div class="card product-list">
    <div class="card-body">
        <div class="row mb-3">
            <div class="col-md-5">
                <form action="{{route('shop.products.index', $shop->id)}}">
                    <div class="row">
                        <div class="col-md-6">
                            <input type="text" placeholder="Recherche par code ou nom" name="search" class="form-control" value="{{request('search')}}" />
                        </div>
                        <div class="col-md-2">
                            <button class="btn btn-outline-primary" type="submit">Chercher</button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="col-md-2">
                <a href="{{route('products.shop.list.pdf', ["shop" => $shop->id])}}" target="_blank"><button class="btn btn-primary" type="submit">List PDF Shop</button></a>
            </div>
        </div>
        <table class="table">
            <thead>
                <tr>
                    <th>Code</th>
                    <th>Description</th>
                    <th>Stock Magasin</th>
                    <th>Stock Petit Depot</th>
                    <th>Pieces/Carton</th>
                    <th>Prix</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($products as $shopProd)
                @php
                // dump($shopProd);
                    $stock = (floor($shopProd->quantity / $shopProd->product->items_in_box)) . ' CRT Et ' . (floor($shopProd->quantity % $shopProd->product->items_in_box)) . ' PCE';
                    $stockPetitDepot = (floor($shopProd->petit_depot_qty / $shopProd->product->items_in_box)) . ' CRT Et ' . (floor($shopProd->petit_depot_qty % $shopProd->product->items_in_box)) . ' PCE';
                @endphp
                <tr>
                    <td>{{$shopProd->product->code}}</td>
                    <td>{{$shopProd->product->name}}</td>
                    <td>
                        <div>{{$stock}}</div>
                    </td>
                    <td>
                        <div>{{$stockPetitDepot}}</div>
                    </td>
                    <td>{{$shopProd->product->items_in_box}}</td>
                    <td>{{posprice($shopProd->sell_price)}} F.C</td>
                    <td>
                        @if ($user->is_admin)
                            <button class="btn btn-primary"
                                data-toggle="modal"
                                data-target="#updateStock"
                                data-product="{{ $shopProd }}"
                                data-inbox="{{ $shopProd->product->items_in_box }}"
                                data-quantity="{{ $stock }}"
                                data-toggle="tooltip" data-placement="bottom" title="Modifier Stock au Magasin"
                            >
                                <i class="fas fa-edit"></i>
                            </button>

                            <button class="btn btn-primary"
                                data-toggle="modal"
                                data-target="#transferPetitDepot"
                                data-product="{{ $shopProd }}"
                                data-inbox="{{ $shopProd->product->items_in_box }}"
                                data-quantity="{{ $stockPetitDepot }}"
                                data-toggle="tooltip" data-placement="bottom" title="Petit Depot vers Magasin"
                            >
                                <i class="fas fa-exchange"></i>
                            </button>
                        @else
                            {{-- <button class="btn btn-success"
                                data-toggle="modal"
                                data-target="#updateStock"
                                data-product="{{ $shopProd }}"
                                data-toggle="tooltip" data-placement="bottom" title="Valider Transfer"
                            >
                                <i class="fas fa-exclamation-triangle"></i>
                            </button> --}}
                        @endif
                        {{-- <a href="{{ route('products.edit', $shopProd->product) }}" class="btn btn-primary"><i
                                class="fas fa-edit"></i></a> --}}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        {{ $products->render() }}
    </div>
    <div class="modal fade" id="updateStock" tabindex="-1" role="dialog" aria-labelledby="updateStockLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="updateStockLabel"> {{ $shop->name }}: <div id="articleName"><div></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-header">
                    <h6><b>Quantité Presente:</b> <u id="articleStock" style="font-size: 1.2rem;" class="pr-1"></u></h6>
                    <i id="articleDetail"></i>
                </div>
                <div class="modal-body">
                    <h5 class="modal-title" id="updateStockLabel"><b class="text-danger">Modification du stock!</b> <div id="articleName"><div></h5>
                    <div class="form-row mt-2">
                        <div class="form-group col-md-6">
                            <label for="inputBox">Nombre des CARTONS</label>
                            <input type="number" id="inputBox" max="20" class="form-control" placeholder="Combien des cartons">
                        </div>

                        <div class="form-group col-md-6">
                            <label for="inputPce">Nombre des PIECES:</label>
                            <input type="number" id="inputPce" max="20" class="form-control" placeholder="Combien des pieces">
                        </div>
                    </div>
                </div>
                <div class="modal-footer d-flex flex-row justify-content-between">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                    <button type="button" class="btn btn-primary" id="confirmButton">Valider</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="transferPetitDepot" tabindex="-1" role="dialog" aria-labelledby="updateStockLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-bold" id="updateStockLabelP"> Petit Depot {{ $shop->name }}: <div class="text-sm" id="articleNameP"><div></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-header">
                    <h6><b>Stock au Petit Depot:</b> <u id="articleStockP" style="font-size: 1.2rem;" class="pr-1"></u></h6>
                    <i id="articleDetailP"></i>
                </div>
                <div class="modal-body">
                    <h5 class="modal-title" id="updateStockLabelP"><b class="text-danger">Transfer Du Petit Depot vers Magasin!</b> <div id="articleNameP"><div></h5>
                    <div class="form-row mt-2">
                        <div class="form-group col-md-6">
                            <label for="inputBoxP">Nombre des CARTONS</label>
                            <input type="number" id="inputBoxP" max="20" class="form-control" placeholder="Combien des cartons">
                        </div>

                        <div class="form-group col-md-6">
                            <label for="inputPceP">Nombre des PIECES:</label>
                            <input type="number" id="inputPceP" max="20" class="form-control" placeholder="Combien des pieces">
                        </div>
                    </div>
                </div>
                <div class="modal-footer d-flex flex-row justify-content-between">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                    <button type="button" class="btn btn-primary" id="confirmButtonP">Valider</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script src="{{ asset('plugins/sweetalert2/sweetalert2.min.js') }}"></script>
<script src="https://code.jquery.com/jquery-3.7.0.slim.min.js" integrity="sha256-tG5mcZUtJsZvyKAxYLVXrmjKBVLd6VpVccqz/r4ypFE=" crossorigin="anonymous"></script>

<script>
    $(document).ready(function () {
        $('#updateStock').on('show.bs.modal', function (event) {
            $this = $(this);
            var $shop = {!! $shop !!};
            var $user = {!! $user !!};
            var is_admin = $user.is_admin ? 1 : 0;
            var $userId = {!! $userId !!};
            var button = $(event.relatedTarget); // Button that triggered the modal
            var prodShop = button.data('product');
            var qtyText = button.data('quantity');
            var inbox = button.data('inbox');
            var itemId = prodShop?.product.id;
            var displayName = prodShop.product ? `${prodShop.product.name} (code: ${prodShop.product.code})` : '';

            // Get box and pce
            var current_box = 0;
            var current_pce = prodShop.quantity;

            if (inbox && inbox > 0) {
                current_box = Math.floor(Number(prodShop.quantity) / Number(inbox));
                current_pce = Number(prodShop.quantity) % Number(inbox);
            }

            $(this).find('#articleName').text(displayName);
            $(this).find('#articleStock').text(qtyText ?? '');
            $(this).find('#articleDetail').text(`(1 Carton = ${inbox} Piece)`);

            $(this).find('#inputBox').val(current_box);
            $(this).find('#inputPce').val(current_pce);

            $('#confirmButton').click(function(){
                var quantity_box = $("#inputBox").val();
                var quantity_pce = $("#inputPce").val();

                $.post("/api/set-quantity/shop", {
                    _token: '{{csrf_token()}}',
                    is_admin:  is_admin,
                    product_id: itemId,
                    shop_prod_id: prodShop.id,
                    trans_prod_id: prodShop.transfer_id,
                    shop_id: $shop.id,
                    quantity_box: quantity_box,
                    quantity_pce: quantity_pce,
                    user_id: $userId,
                }, function (res) {
                    console.log('RRRRR ', res);
                    if (res.product) {
                        location.reload();
                    }
                })
            });
        });


        $('#transferPetitDepot').on('show.bs.modal', function (event) {
            $this = $(this);
            var $shop = {!! $shop !!};
            var $user = {!! $user !!};
            var is_admin = $user.is_admin ? 1 : 0;
            var $userId = {!! $userId !!};
            var button = $(event.relatedTarget); // Button that triggered the modal
            var prodShop = button.data('product');
            var qtyText = button.data('quantity');
            var inbox = button.data('inbox');
            var itemId = prodShop?.product.id;
            var displayName = prodShop.product ? `${prodShop.product.name} (code: ${prodShop.product.code})` : '';

            // Get box and pce
            var current_box = 0;
            var current_pce = prodShop.petit_depot_qty;

            if (inbox && inbox > 0) {
                current_box = Math.floor(Number(prodShop.petit_depot_qty) / Number(inbox));
                current_pce = Number(prodShop.petit_depot_qty) % Number(inbox);
            }

            $(this).find('#articleNameP').text(displayName);
            $(this).find('#articleStockP').text(qtyText ?? '');
            $(this).find('#articleDetailP').text(`(1 Carton = ${inbox} Piece)`);

            $(this).find('#inputBoxP').val(current_box);
            $(this).find('#inputPceP').val(current_pce);

            $('#confirmButtonP').click(function(){
                var quantity_box = $("#inputBoxP").val();
                var quantity_pce = $("#inputPceP").val();

                $.post("/api/set-quantity-petit-depot/shop", {
                    _token: '{{csrf_token()}}',
                    is_admin:  is_admin,
                    product_id: itemId,
                    shop_prod_id: prodShop.id,
                    trans_prod_id: prodShop.transfer_id,
                    shop_id: $shop.id,
                    quantity_box: quantity_box,
                    quantity_pce: quantity_pce,
                    user_id: $userId,
                }, function (res) {
                    console.log('RRRRR ', res);
                    if (res.product) {
                        location.reload();
                    }
                })
            });
        });
    })
</script>
@endsection
