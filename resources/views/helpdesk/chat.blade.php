@extends('layouts.app')

@section('content')
    <div id="app">
        <helpdeskchat-component query="{{$query}}"></helpdeskchat-component>
    </div>
@endsection
