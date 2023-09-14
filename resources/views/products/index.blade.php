@extends('layouts.admin')

@section('title', 'Product List')
@section('content-header', 'Product List')
@section('content-actions')
<a href="{{route('products.create')}}" class="btn btn-primary">Create Product</a>
@endsection
@section('css')
<link rel="stylesheet" href="{{ asset('plugins/sweetalert2/sweetalert2.min.css') }}">
@endsection
@section('content')
<div class="card product-list">
    <div class="card-body">
        <div class="row mb-3">
            <div class="col-md-5">
                <form action="{{route('products.index')}}">
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
        </div>
        <table class="table">
            <thead>
                <tr>
                    <th>Code</th>
                    <th>Description</th>
                    <th>Stock Dépot</th>
                    <th>Pieces/Carton</th>
                    <th>Prix</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($products as $product)
                @php
                    $stock = (posprice($product->quantity / $product->items_in_box)) . ' CRT Et ' . (posprice($product->quantity % $product->items_in_box)) . ' PCE';
                @endphp
                <tr>
                    <td>{{$product->code}}</td>
                    <td>{{$product->name}}</td>
                    <td>
                        <div>{{$stock}}</div>
                    </td>
                    <td>{{$product->items_in_box}}</td>
                    <td>{{posprice($product->sell_price)}} F.C</td>
                    <td>
                        <a href="{{ route('products.edit', $product) }}" class="btn btn-primary"><i
                                class="fas fa-edit"></i></a>
                        <a href="{{ route('assign.products', $product) }}" class="btn btn-success"><i
                                class="fas fa-truck"></i></a>
                        <button class="ml-1 btn btn-primary"
                            data-toggle="modal"
                            data-target="#updateStock"
                            data-product="{{ $product }}"
                            data-toggle="tooltip" data-placement="bottom" title="Ajouter au stock"
                        >
                            <i class="fas fa-plus"></i>
                        </button>
                        <button class="ml-5 btn btn-danger btn-delete" data-url="{{route('products.destroy', $product)}}"><i
                                class="fas fa-trash"></i></button>
                        {{-- <button class="btn btn-success btn-seccess"
                                data-toggle="modal"
                                data-target="#myModal"
                                data-item-id="{{ $product->id }}"
                                data-item-code="{{ $product->id }}"
                                data-item-quantity="{{ $product->quantity }}"
                            >
                            <i class="fas fa-truck"></i>
                        </button> --}}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        {{ $products->render() }}
    </div>

    <!-- Modal -->
    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header justify-content-center">
                    <h5 class="modal-title text-bold" id="exampleModalLabel">Article ( <span id="modalItemId"></span> ) vers Magasins</h5>
                    <div data-dismiss="modal" class="justify-content-end pl-4">
                        <i class="fas fa-times-circle"></i>
                    </div>
                </div>
                <div class="modal-header">
                    <div class="justify-content-center">
                        <h5 class="modal-title" id="exampleModalLabel">
                            Stock Depot: <strong id="modalQuantity" class="pr-2"></strong><small>Cartons</small>
                        </h5>
                    </div>
                </div>
                <div class="modal-body">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Magasin</th>
                                <th>Stock Magsin</th>
                                <th>Qte á Ajouter</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($shops as $shop)
                                <tr>
                                    <td>{{ $shop->name }}</td>
                                    <td id="product-{{ $product->id ?? ''}}-shop-{{ $shop->id }}-quantity"></td>
                                    <td id="action-button-{{ $shop->id }}">
                                        <input id="product-{{ $product->id ?? ''}}-shop-{{ $shop->id }}-increase" type="number"></input>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-success">Valider</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="updateStock" tabindex="-1" role="dialog" aria-labelledby="updateStockLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="updateStockLabel"><b class="">Ajouter Stock</b>: <span id="articleName"><span></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <h6>Stock Actuel: <u style="font-size: 1.2rem;" class="pr-1"><span id="boxes"></span><i class="ml-1">Cartons</i></u> ET <u style="font-size: 1.2rem;" class="pr-1"><span id="pces"></span><i class="ml-1">Pieces</i></u></h6>
                    <h6>Info: <i id="info"><span></span></i></h6>
                    <label class="mt-3" for="inputField">Nombre Cartons:</label>
                    <input type="number" id="inputField" max="20" class="form-control" placeholder="Entrez la valeur">

                    <label class="mt-2" for="inputField">Nombre Piéces:</label>
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
        $(document).on('click', '.btn-delete', function () {
            console.log('will delete');
            $this = $(this);
            const swalWithBootstrapButtons = Swal.mixin({
                customClass: {
                    confirmButton: 'btn btn-success',
                    cancelButton: 'btn btn-danger'
                },
                buttonsStyling: false
                })

                swalWithBootstrapButtons.fire({
                title: 'Etes-vous sur?',
                text: "Voulez-vous vraiment supprimer cet article?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Oui, Supprimer!',
                cancelButtonText: 'No',
                reverseButtons: true
                }).then((result) => {
                if (result.value) {
                    $.post($this.data('url'), {_method: 'DELETE', _token: '{{csrf_token()}}'}, function (res) {
                        $this.closest('tr').fadeOut(500, function () {
                            $(this).remove();
                        })
                    })
                }
            })
        })

        $('#updateStock').on('show.bs.modal', function (event) {
            $this = $(this);
            var button = $(event.relatedTarget); // Button that triggered the modal
            var product = button.data('product');
            var displayName = `${product.name} (code: ${product.code})`;
            var boxes = product.quantity / product.items_in_box;
            var pces = product.quantity % product.items_in_box;
            var info = `${product.items_in_box} Piéce dans chaque Carton`

            console.log('NOW ', product, boxes, pces);

            $(this).find('#articleName').text(displayName);
            $(this).find('#boxes').text(boxes ?? '');
            $(this).find('#pces').text(pces ?? '');
            $(this).find('#info').text(info ?? '');

            $('#confirmButton').click(function(){
                var quantity = $("#inputField").val();

                $.post("/api/set-quantity/store", {
                    _token: '{{csrf_token()}}',
                    product_id: product.id,
                    quantity_box: quantity_box,
                    quantity_pce: quantity_pce,
                    user_id: $userId,
                }, function (res) {
                    if (res.product) {
                        location.reload();
                    }
                })
            });
        });

        // $('#myModal').on('show.bs.modal', function (event) {
        //     $this = $(this);
        //     var $shops = {!! $shops !!};
        //     var button = $(event.relatedTarget); // Button that triggered the modal
        //     var itemId = button.data('item-id'); // Extract data-id attribute from button
        //     var itemCode = button.data('item-code'); // Extract data-code attribute from button
        //     var itemQuantity = button.data('item-quantity'); // Extract data-code attribute from button

        //     // set shops quanities
        //     // $.post(`/api/create-product-shop/${shop.id}/product/${itemId}`, {_token: '{{csrf_token()}}'}, function (res) {
        //     //     if (res.product) {
        //     //         $('#action-button-' + shop.id).remove();
        //     //         // $('#action-button-' + shop.id).replaceWith(deleteButton);
        //     //     }
        //     // })

        //     // set Store quantity


        //     console.log('RES ', itemId, itemCode);

        //     $(this).find('#modalItemId').text(itemCode);
        //     $(this).find('#modalQuantity').text(itemQuantity);

        //     $shops.forEach(shop => {
        //         $.get(`/api/get-quantity/shop/${shop.id}/product/${itemId}`, {_token: '{{csrf_token()}}'}, function (res) {
        //             if (res.product) {
        //                 console.log('RESS ', res.product.quantity);
        //                 console.log('RESS ', shop.id);
        //                 console.log('RESS ', itemId);
        //                 // $(`#product-1-shop-1-quantity`).text(777);
        //                 $('#product-' + itemId + '-shop-' + shop.id + '-quantity').text(777);
        //             }
        //         })
        //     });
        // });
    })
</script>
@endsection
