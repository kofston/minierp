@extends('layouts.app')

@section('content')
    <div id="app">
        <offeradd-component query="{{$query}}" clientsarray="{{$clients_array}}" productsarray="{{$products_array}}"></offeradd-component>
    </div>
@endsection
