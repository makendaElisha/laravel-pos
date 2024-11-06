@extends('layouts.admin')

@section('title', 'Envoie Magasin')
@section('content-header', "Approvisionez Magasin : $product->name")

@section('content')

<div class="card">
    <div class="card-body">

        <div class="mb-5">
            @php
                $stock = (floor($product->quantity / $product->items_in_box)) . ' CRT Et ' . ($product->quantity % $product->items_in_box) . ' PCE';
            @endphp
            <h4>
                <u>Quantité au depot: <strong>{{ $stock }}</strong></u>
            </h4>
            <h6>
                <i>1 Carton = <strong>{{ $product->items_in_box }}</strong> Pieces</i>
            </h6>
        </div>

        <form action="{{ route('save.assign.products') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="product_id" value="{{ $product->id }}">

            <table class="table">
                <thead>
                    <tr>
                        <th>Magasin</th>
                        <th>Stock present au Magasin</th>
                        <th>Approvisioner Magasin (Pieces)</th>
                        <th>Approvisioner Petit Depot</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($shops as $shop)
                        @php
                            $stockShop = (floor($shop->quantity / $shop->product->items_in_box)) . ' CRT Et ' . ($shop->quantity % $shop->product->items_in_box) . ' PCE';
                        @endphp
                        <tr>
                            <td>{{ $shop->name }}</td>
                            <td>{{ $stockShop }}<span class="pl-1" style="font-size: 12px;"></span></td>
                            <td id="action-button-{{ $shop->id }}">
                                <input name="quantity[]" type="number">
                                <input type="hidden" name="shops[]" value="{{ $shop->id }}">
                            </td>
                            <td>
                                <input name="quantitiesPetitDepot[]" type="number">
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            @error('quantity_exceded')
                <b style="font-size: 1.2rem;" class="text-danger">Les quantités transferées sont supérieurs á la valeur du stock au Dépot!, Veuillez modifier</b>
                <span class="invalid-feedback" role="alert">
                    <strong>dds</strong>
                </span>
            @enderror

            <div class="d-flex flex-row justify-content-center mt-5">
                <button class="btn btn-primary" type="submit">
                    <i class="fas fa-plus-square pr-1"></i> Valider
                </button>
            </div>


        </form>
    </div>
</div>
@endsection

@section('js')
<script src="{{ asset('plugins/bs-custom-file-input/bs-custom-file-input.min.js') }}"></script>
<script src="https://code.jquery.com/jquery-3.7.0.slim.min.js" integrity="sha256-tG5mcZUtJsZvyKAxYLVXrmjKBVLd6VpVccqz/r4ypFE=" crossorigin="anonymous"></script>
<script>
    $(document).ready(function () {
        bsCustomFileInput.init();
    });
</script>
@endsection
