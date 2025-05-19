@extends('layout')

@section('content')

<div class="">

<form action="{{action('ComandaController@store')}}" method="post" accept-charset="UTF-8" enctype="multipart/form-data">
  @csrf

  @include('comandas.form')

</form>

</div>

<script>
$('document').ready(function(){
    const checkbox = document.getElementById('con_descuento');

    checkbox.addEventListener('change', (event) => {
        aplicarDescuento(event.currentTarget.checked);
    });
});
</script>


@endsection


@include('general.metodos_comandas')