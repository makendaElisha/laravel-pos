@extends('layouts.admin')

@section('title', 'Movements')
@section('content-header', 'Mouvement Du Stock')
@section('content-actions')
@endsection

@section('content')
<div class="card">
    <div class="card-body">
        <div class="row mb-3">
            <div class="col-md-12">
                <form action="{{route('article.global.movements')}}">
                    <div class="row">
                        <div class="col-md-4">
                            <select @if(!$user->is_admin) disabled @endif name="shop" id="" class="form-control">
                                <option value="0" @if($shopId=='0' ) selected @endif>Tous Les Magasins</option>
                                @foreach ($shops as $shop)
                                <option value="{{ $shop->id }}" @if($shopId==$shop->id) selected @endif>{{ $shop->name
                                    }}</option>
                                @endforeach
                                <option value="100" @if($shopId=='100') selected @endif>DEPOT</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <input type="text" placeholder="code" name="code" class="form-control"
                                value="{{request('code')}}" />
                        </div>
                        <div class="col-md-2">
                            <input type="date" name="start_date" class="form-control"
                                value="{{request('start_date')}}" />
                        </div>
                        <div class="col-md-2">
                            <input type="date" name="end_date" class="form-control" value="{{request('end_date')}}" />
                        </div>
                        <div class="col-md-2">
                            <button class="btn btn-primary" type="submit">Filtrer</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <table class="table">
            <thead>
                <tr>
                    <th>Article</th>
                    <th>Qté du Mouvement</th>
                    <th>Action réalisée</th>
                    <th>Qté Avant Action</th>
                    <th>Qté Apres Action</th>
                    <th>Magasin/Depot</th>
                    <th>Date Du mouvement</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($movements as $move)
                @php
                    $stock = (floor($move->quantity / $move->product->items_in_box)) . ' CRT Et ' . (floor($move->quantity % $move->product->items_in_box)) . ' PCE';
                    $stockBefore = (floor($move->quantity_before / $move->product->items_in_box)) . ' CRT Et ' . (floor($move->quantity_before % $move->product->items_in_box)) . ' PCE';
                    $stockAfter = (floor($move->quantity_after / $move->product->items_in_box)) . ' CRT Et ' . (floor($move->quantity_after % $move->product->items_in_box)) . ' PCE';
                @endphp
                <tr>
                    <td>[code: {{$move->product->code ?? ''}}] {{$move->product->name ?? ''}}</td>
                    <td>{{$stock}}</td>
                    <td>{{movementTitle($move->type)}}</td>
                    <td>{{$stockBefore}}</td>
                    <td>{{$stockAfter}}</td>
                    @php
                        $currentMovType = $move->type;
                        $displayType = $move->shop->name ?? "DEPOT";

                        $storeTypes = [
                            \app\models\StockMouvement::STORE_INCREASE,
                            \app\models\StockMouvement::STORE_RETURN,
                            \app\models\StockMouvement::STORE_DECREASED,
                            \app\models\StockMouvement::MANUAL_EDIT,
                            \app\models\StockMouvement::INIT_STOCK
                        ];

                        if ($move->type == \app\models\StockMouvement::SHOP_PETIT_DEPOT_INCREASE) {
                            $displayType = $move->shop->name != null ? "Petit Depot $displayType" : "";
                        }

                        if ($currentMovType && in_array($currentMovType, $storeTypes)) {
                            $displayType = "DEPOT";
                        }
                    @endphp
                    <td>{{$displayType}}</td>
                    <td>{{$move->created_at}}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        {{ $movements->render() }}
    </div>
</div>
@endsection
