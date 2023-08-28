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
        <table class="table">
            <thead>
                <tr>
                    <th>Code</th>
                    <th>Description</th>
                    <th>Stock</th>
                    <th>Prix</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($products as $product)
                <tr>
                    <td>{{$product->id}}</td>
                    <td>{{$product->name}}</td>
                    <td>{{$product->quantity}}</td>
                    <td>{{$product->sell_price}} F.C</td>
                    <td>
                        <a href="{{ route('products.edit', $product) }}" class="btn btn-primary"><i
                                class="fas fa-edit"></i></a>
                        <button class="btn btn-danger btn-delete" data-url="{{route('products.destroy', $product)}}"><i
                                class="fas fa-trash"></i></button>
                        <button class="btn btn-success btn-seccess"
                                data-toggle="modal"
                                data-target="#myModal"
                                data-item-id="{{ $product->id }}"
                                data-item-code="{{ $product->id }}"
                            >
                            <i class="fas fa-truck"></i>
                        </button>
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
                </div>
                <div class="modal-body">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Magasin</th>
                                <th>Stock</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($shops as $shop)
                                <tr>
                                    <td>{{ $shop->name }}</td>
                                    <td>{{ $shop->id }}</td>
                                    <td id="stock-quantity-{{ $shop->id }}"></td>
                                    <td id="action-button-{{ $shop->id }}"></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Fermer</button>
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

        $('#myModal').on('show.bs.modal', function (event) {
            $this = $(this);
            var $shops = {!! $shops !!};
            var button = $(event.relatedTarget); // Button that triggered the modal
            var itemId = button.data('item-id'); // Extract data-id attribute from button
            var itemCode = button.data('item-code'); // Extract data-code attribute from button

            console.log('RES ', itemId, itemCode);

            $(this).find('#modalItemId').text(itemCode);

            $shops.forEach(shop => {
                var createButton = $('<button>', {
                    class: 'btn btn-success',
                    text: 'Créer',
                    click: function() {
                        $.post(`/api/create-product-shop/${shop.id}/product/${itemId}`, {_token: '{{csrf_token()}}'}, function (res) {
                            if (res.product) {
                                $('#action-button-' + shop.id).remove();
                                // $('#action-button-' + shop.id).replaceWith(deleteButton);
                            }
                        })
                    }
                });
                var deleteButton = $('<button>', {
                    class: 'btn btn-danger',
                    text: 'Supprimer',
                    click: function() {
                        alert('Delete clicked for shop with ID: ' + shop.id);
                    }
                });

                //Show action button
                $('#action-button-' + shop.id).append(createButton);

                //Get quantities
                $.get(`/api/get-quantity/shop/${shop.id}/product/${itemId}`, function(result, state) {
                    console.log('The result ', result);
                });

            });
        });
    })
</script>
@endsection
