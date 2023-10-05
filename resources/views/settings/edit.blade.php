@extends('layouts.admin')

@section('title', 'Update Settings')
@section('content-header', 'Update Settings')

@section('content')
<div class="card">
    <div class="card-body">
        <form action="{{ route('settings.store') }}" method="post">
            @csrf

            <div class="form-group">
                <label for="app_name">App name</label>
                <input type="text" name="app_name" class="form-control @error('app_name') is-invalid @enderror" id="app_name" placeholder="App name" value="{{ old('app_name', config('settings.app_name')) }}">
                @error('app_name')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>

            <div class="form-group">
                <label for="app_description">App description</label>
                <textarea name="app_description" class="form-control @error('app_description') is-invalid @enderror" id="app_description" placeholder="App description">{{ old('app_description', config('settings.app_description')) }}</textarea>
                @error('app_description')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>

            <div class="form-row">
                <div class="form-group mr-3">
                    <label for="min_discount_amount">Montant Minimal Reduction</label>
                    <input type="number" name="min_discount_amount" class="form-control @error('min_discount_amount') is-invalid @enderror" id="min_discount_amount" placeholder="Ex: 200.000" value="{{ old('min_discount_amount', config('settings.min_discount_amount')) }}">
                    @error('min_discount_amount')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="discount_percentage">% á Reduire</label>
                    <input type="number" step=".1" name="discount_percentage" class="form-control @error('discount_percentage') is-invalid @enderror" id="discount_percentage" placeholder="Ex: 15" value="{{ old('discount_percentage', config('settings.discount_percentage')) }}">
                    @error('discount_percentage')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>

            </div>


            <div class="form-group">
                <label for="currency_symbol">Currency symbol</label>
                <input type="text" name="currency_symbol" class="form-control @error('currency_symbol') is-invalid @enderror" id="currency_symbol" placeholder="Currency symbol" value="{{ old('currency_symbol', config('settings.currency_symbol')) }}">
                @error('currency_symbol')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>
            <div class="form-group">
                <label for="warning_quantity">Warning quantity</label>
                <input type="text" name="warning_quantity" class="form-control @error('warning_quantity') is-invalid @enderror" id="warning_quantity" placeholder="Warning quantity" value="{{ old('warning_quantity', config('settings.warning_quantity')) }}">
                @error('warning_quantity')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>
            <button type="submit" class="btn btn-primary">Change Setting</button>
        </form>
    </div>
</div>
@endsection
