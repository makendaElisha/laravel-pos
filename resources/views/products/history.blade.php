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
                    <th>Quantités</th>
                    <th>Article</th>
                    <th>Action réalisée</th>
                    <th>Magasin</th>
                    <th>Date Du mouvement</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($movements as $move)
                @php
                    $stock = (floor($move->product->quantity / $move->product->items_in_box)) . ' CRT Et ' . (floor($move->product->quantity % $move->product->items_in_box)) . ' PCE';
                @endphp
                <tr>
                    <td>{{$move->quantity}}</td>
                    <td>[code: {{$move->product->code ?? ''}}] {{$move->product->name ?? ''}}</td>
                    <td>{{movementTitle($move->type)}}</td>
                    <td>{{$move->shop->name ?? ''}}</td>
                    <td>{{$move->created_at}}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        {{ $movements->render() }}
    </div>
</div>
@endsection
