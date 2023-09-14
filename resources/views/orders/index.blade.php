@extends('layouts.admin')

@section('title', 'Factures')
@section('content-header', 'List Des Factures')
@section('content-actions')
{{-- <a href="{{route('shop.cart.index', $shop->id)}}" class="btn btn-success">
    <i class="fas fa-cart-plus pr-1"></i>Facturation
</a> --}}
@endsection

@section('content')
<div class="card">
    <div class="card-body">
        <div class="row mb-3">
            <div class="col-md-5"></div>
            <div class="col-md-7">
                <form action="{{route('orders.index')}}">
                    <div class="row">
                        <div class="col-md-4">
                            <select @if(!$user->is_admin) disabled @endif name="shop" id="" class="form-control">
                                <option value="0" @if($shopId == '0') selected @endif>Tous Les Magasins</option>
                                @foreach ($shops as $shop)
                                    <option value="{{ $shop->id }}" @if($shopId == $shop->id) selected @endif>{{ $shop->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <input type="date" name="start_date" class="form-control" value="{{request('start_date')}}" />
                        </div>
                        <div class="col-md-3">
                            <input type="date" name="end_date" class="form-control" value="{{request('end_date')}}" />
                        </div>
                        <div class="col-md-2">
                            <button class="btn btn-outline-primary" type="submit">Filtrer</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <table class="table">
            <thead>
                <tr>
                    <th>Numero Facture</th>
                    <th>Client</th>
                    <th>Total</th>
                    <th>Crée par</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($orders as $order)
                <tr>
                    <td>{{$order->order_number}}</td>
                    <td>{{$order->customer}}</td>
                    <td>{{ number_format($order->paid, 0, ',', '.') }} {{ config('settings.currency_symbol') }}</td>
                    <td>{{$order->user->first_name}}</td>
                    <td>{{$order->created_at}}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <th>Total</th>
                    <th></th>
                    <th>{{ number_format($total, 0, ',', '.') }} {{ config('settings.currency_symbol') }} </th>
                    <th></th>
                    <th></th>
                </tr>
            </tfoot>
        </table>
        {{ $orders->render() }}
    </div>
</div>
@endsection

