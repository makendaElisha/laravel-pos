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
        <table class="table">
            <thead>
                <tr>
                    <th>Code</th>
                    <th>Description</th>
                    <th>Stock Magasin</th>
                    <th>Prix</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($products as $shopProd)
                <tr>
                    <td>{{$shopProd->product->code}}</td>
                    <td>{{$shopProd->product->name}}</td>
                    <td>{{$shopProd->quantity}}</td>
                    <td>{{$shopProd->product->sell_price}} F.C</td>
                    <td>
                        @if ($shopProd->transfer_quantity > 0)
                            @if ($user->is_admin)
                                <button class="btn btn-danger"
                                    data-toggle="modal"
                                    data-target="#updateStock"
                                    data-product="{{ $shopProd }}"
                                    data-toggle="tooltip" data-placement="bottom" title="Annuler Transfer"
                                >
                                    <i class="fas fa-exclamation-triangle"></i>
                                </button>
                            @else
                                <button class="btn btn-success"
                                    data-toggle="modal"
                                    data-target="#updateStock"
                                    data-product="{{ $shopProd }}"
                                    data-toggle="tooltip" data-placement="bottom" title="Valider Transfer"
                                >
                                    <i class="fas fa-exclamation-triangle"></i>
                                </button>
                            @endif
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
                    @if ($user->is_admin)
                        <h5 class="modal-title" id="updateStockLabel"><b class="text-danger">ANNULER Transfer</b>: <span id="articleName"><span></h5>
                    @else
                        <h5 class="modal-title" id="updateStockLabel"><b class="text-success">Valider Transfer</b>: <span id="articleName"><span></h5>
                    @endif
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <h6>Quantité Envoyée: <u id="articleStock" style="font-size: 1.2rem;" class="pr-1"></u><i>CRT</i></h6>
                    <label for="inputField">Quantité:</label>
                    <input type="number" id="inputField" max="20" class="form-control" placeholder="Entrez la valeur">
                </div>
                <div class="modal-footer d-flex flex-row justify-content-between">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                    <button type="button" class="btn btn-primary" id="confirmButton">Valider</button>
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
            var itemId = prodShop?.product.id;
            var displayName = prodShop.product ? `${prodShop.product.name} (code: ${prodShop.product.code})` : '';

            $(this).find('#articleName').text(displayName);
            $(this).find('#articleStock').text(prodShop.transfer_quantity ?? '');

            $('#confirmButton').click(function(){
                var quantity = $("#inputField").val();

                $.post("/api/set-quantity/shop", {
                    _token: '{{csrf_token()}}',
                    is_admin:  is_admin,
                    product_id: itemId,
                    shop_prod_id: prodShop.id,
                    trans_prod_id: prodShop.transfer_id,
                    shop_id: $shop.id,
                    quantity: quantity,
                    user_id: $userId,
                }, function (res) {
                    if (res.product) {
                        location.reload();
                    }
                })
            });
        });
    })
</script>
@endsection
