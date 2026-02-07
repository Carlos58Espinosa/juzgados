@extends('layout')

@section('content')


<!-- Leyenda de colores -->
<div class="container mt-3 mb-2">
    <div class="d-flex gap-3 align-items-center">
        <div class="d-flex align-items-center gap-1">
            <div style="width:20px; height:20px; background-color:#4e73df; border-radius:3px;"></div>
            <span>Alta</span>
        </div>
        <div class="d-flex align-items-center gap-1">
            <div style="width:20px; height:20px; background-color:#1cc88a; border-radius:3px;"></div>
            <span>Terminado</span>
        </div>
    </div>
</div>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css">
<script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>

@include('calendario.create')

@include('calendario.edit')

<div id="calendar" style="max-width:900px; margin:auto;"></div>

<!-- FullCalendar JS -->
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>
@stop
