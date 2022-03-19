@extends('layouts.app')

@section('content')
    <div id="app">
        <orderadd-component query="{{$query}}" clientsarray="{{$clients_array}}" productsarray="{{$products_array}}"></orderadd-component>
    </div>
@endsection
