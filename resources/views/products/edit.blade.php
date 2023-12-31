@extends('layouts.admin')

@section('title', 'Modifier Article')
@section('content-header', 'Modifier Article')

@section('content')

<div class="card">
    <div class="card-body">

        <form action="{{ route('products.update', $product) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="code">Code</label>
                <input type="text" name="code" class="form-control @error('code') is-invalid @enderror" id="code"
                    placeholder="code" value="{{ old('code', $product->code) }}">
                @error('code')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>

            <div class="form-group">
                <label for="name">Nom</label>
                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" id="name"
                    placeholder="Nom" value="{{ old('name', $product->name) }}">
                @error('name')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>

            <div class="form-group row mb-4">
                <div class="form-group col-md-12">
                    <label for="buy_price">Prix d'achat (FC)</label>
                    <input type="number" name="buy_price" class="form-control @error('buy_price') is-invalid @enderror" id="buy_price"
                        placeholder="Le prix d'achat par piece" value="{{ old('buy_price', $product->buy_price) }}">
                    @error('buy_price')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>
            </div>

            <div class="row mb-2">
                <div class="col-3"><hr></div>
                <div class="col-auto"><b>Les Prix de vente Par Magazin:</b></div>
                <div class="col"><hr></div>
            </div>

            <div class="form-group row">
                <label for="sell_price_lushi" class="col-3 col-form-label">LUBUMBASHI:</label>
                <div class="col-4">
                    <input type="number" name="sell_price_lushi" class="form-control mx-sm-3 @error('sell_price_lushi') is-invalid @enderror" id="sell_price_lushi"
                        placeholder="Le prix de vente par piece" value="{{ old('sell_price_lushi', $product->sell_price_lushi) }}">
                    @error('sell_price_lushi')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>
            </div>

            <div class="form-group row">
                <label for="sell_price_kolwezi" class="col-3 col-form-label">KOLWEZI:</label>
                <div class="col-4">
                    <input type="number" name="sell_price_kolwezi" class="form-control mx-sm-3 @error('sell_price_kolwezi') is-invalid @enderror" id="sell_price_kolwezi"
                        placeholder="Le prix de vente par piece" value="{{ old('sell_price_kolwezi', $product->sell_price_kolwezi) }}">
                    @error('sell_price_kolwezi')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>
            </div>

            <div class="form-group row">
                <label for="sell_price_kilwa" class="col-3 col-form-label">KILWA:</label>
                <div class="col-4">
                    <input type="number" name="sell_price_kilwa" class="form-control mx-sm-3 @error('sell_price_kilwa') is-invalid @enderror" id="sell_price_kilwa"
                        placeholder="Le prix de vente par piece" value="{{ old('sell_price_kilwa', $product->sell_price_kilwa) }}">
                    @error('sell_price_kilwa')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>
            </div>

            <div class="row mt-2">
                <div class="col"><hr></div>
            </div>

            <div class="form-row mt-2">
                <div class="form-group col-md-6">
                    <label for="quantity_pce">Nombre de Pieces (Stock á jour)</label>
                    <input type="number" name="quantity_pce" class="form-control @error('quantity_pce') is-invalid @enderror"
                        id="quantity_pce" placeholder="Ex. 100" value="{{ old('quantity_pce', $product->quantity_pce) }}">
                    @error('quantity_pce')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>

                <div class="form-group col-md-6">
                    <label for="quantity_box">Nombre de Cartons (Stock á jour)</label>
                    <input type="number" name="quantity_box" class="form-control @error('quantity_box') is-invalid @enderror"
                        id="quantity_box" placeholder="Ex. 10" value="{{ old('quantity_box', $product->quantity_box) }}">
                    @error('quantity_box')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>
            </div>

            <div class="form-row mt-2">
                <div class="form-group col-md-6">
                    <label for="items_in_box">Combien de piece par carton?</label>
                    <input type="number" name="items_in_box" class="form-control @error('items_in_box') is-invalid @enderror"
                        id="items_in_box" placeholder="Ex. 12" value="{{ old('items_in_box', $product->items_in_box) }}">
                    @error('items_in_box')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>
            </div>

            <div class="form-row mt-2">
                <div class="form-group col-md-6">
                    <label for="min_quantity">Stock Minimal des pieces (Faible stock)</label>
                    <input type="number" name="min_quantity" class="form-control @error('min_quantity') is-invalid @enderror"
                        id="min_quantity" placeholder="Ex. 15" value="{{ old('min_quantity', $product->min_quantity) }}">
                    @error('min_quantity')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>
            </div>

            {{-- <div class="form-row mt-2">
                <div class="form-group col-md-6">
                    <label for="quantity">Stock á Jour</label>
                    <input type="text" name="quantity" class="form-control @error('quantity') is-invalid @enderror"
                        id="quantity" placeholder="Quantity" value="{{ old('quantity', $product->quantity) }}">
                    @error('quantity')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>
            </div> --}}

            <button class="btn btn-primary" type="submit">Update</button>
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
