@extends('layouts.admin')

@section('title', 'Créer Article')
@section('content-header', 'Créer Article')

@section('content')

<div class="card">
    <div class="card-body">

        <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="form-group">
                <label for="code">Code</label>
                <input type="text" name="code" class="form-control @error('code') is-invalid @enderror" id="code"
                    placeholder="code" value="{{ old('code') }}">
                @error('code')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>

            <div class="form-group">
                <label for="name">Nom</label>
                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" id="name"
                    placeholder="Nom" value="{{ old('name') }}">
                @error('name')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>

            <div class="form-group">
                <label for="description">Description</label>
                <textarea name="description" class="form-control @error('description') is-invalid @enderror"
                    id="description" placeholder="description">{{ old('description') }}</textarea>
                @error('description')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>

            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="sell_price">Prix de Vente (FC)</label>
                    <input type="number" name="sell_price" class="form-control @error('sell_price') is-invalid @enderror" id="sell_price"
                        placeholder="Le prix de vente par piece" value="{{ old('sell_price') }}">
                    @error('sell_price')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>
                <div class="form-group col-md-6">
                    <label for="buy_price">Prix d'achat (FC)</label>
                    <input type="number" name="buy_price" class="form-control @error('buy_price') is-invalid @enderror" id="buy_price"
                        placeholder="Le prix d'achat par piece" value="{{ old('buy_price') }}">
                    @error('buy_price')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>
            </div>

            <div class="form-row mt-2">
                <div class="form-group col-md-6">
                    <label for="quantity_pce">Nombre de Pieces (Stock á jour)</label>
                    <input type="number" name="quantity_pce" class="form-control @error('quantity_pce') is-invalid @enderror"
                        id="quantity_pce" placeholder="EX. 100" value="{{ old('quantity_pce') }}">
                    @error('quantity_pce')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>

                <div class="form-group col-md-6">
                    <label for="quantity_box">Nombre de Cartons (Stock á jour)</label>
                    <input type="number" name="quantity_box" class="form-control @error('quantity_box') is-invalid @enderror"
                        id="quantity_box" placeholder="EX. 10" value="{{ old('quantity_box') }}">
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
                        id="items_in_box" placeholder="Ex. 12" value="{{ old('items_in_box', 1) }}">
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
                        id="min_quantity" placeholder="Ex. 15" value="{{ old('min_quantity', 1) }}">
                    @error('min_quantity')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>
            </div>
            <button class="btn btn-primary" type="submit">Créer</button>
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
