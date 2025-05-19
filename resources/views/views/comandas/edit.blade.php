@extends('layout')

@section('content')



<form action="{{action('ComandaController@update', $registro->id)}}" method="POST" accept-charset="UTF-8" enctype="multipart/form-data">
@csrf
  
  <input type="hidden" name="_method" value="PUT">
  
  @include('comandas.form')

</form>
@endsection


@include('general.metodos_comandas')