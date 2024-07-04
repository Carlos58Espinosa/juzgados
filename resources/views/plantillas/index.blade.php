@extends('layout')

@section('content')
<div class="main-content">
  <div class="section__content section__content--p30">
    <div class="container-fluid">
        <div class="card" id="card-section">
            <div class="input-group mb-2">
                <a href="{{action('PlantillasController@create')}}" class="btn btn-info" style="width: 40px; margin-bottom: 10px;"><i class="fas fa-plus"></i></a>
            </div>

            <div class="table-responsive table-striped table-bordered" style="font-size: 14px; padding: 0;">
            <table id="table" class="table" style="width: 100%; table-layout: fixed;">
              <thead>
                <tr>
                  <th style="width:70px; text-align: center;">Nombre de la Plantilla</th>
                  <th style="width:275px; text-align: center;">Texto / Contenido</th>
                  <th style="width:50px; text-align: center;"></th>
                </tr>
              </thead>
              <tbody>
                @foreach($plantillas as $plantilla)
                  <tr>
                    <td>{{$plantilla->nombre}}</td>
                    <td > <div style="height: 150px; overflow-y: scroll;">{!!$plantilla->texto!!} </div></td>
                    <td>
                      <div class="row" style="margin-right: 0; margin-left: 0;">

                        <div class="col-4" style="padding: 0;">
                          <a href="{{action('PlantillasController@show',$plantilla->id)}}" class="btn btn-link" style="width:40px; margin: 0"><i class="far fa-eye"></i></a>
                        </div>
  
                        <div class="col-4" style="padding: 0;">
                          <form method="POST" action="{{action('PlantillasController@viewPdf')}}" target="_blank">
                          @csrf
                            <!-- <input type="hidden"  name="texto" value="{{$plantilla->texto}}"> -->
                            <input type="hidden"  name="id" value="{{$plantilla->id}}">
                            <button class="btn btn-link m-0 p-0" style="width:40px; margin: 0"><i class="far fa-file-pdf"></i></button>
                          </form>
                        </div>

                        <div class="col-4" style="padding: 0;">
                          <a href="{{action('PlantillasController@edit',$plantilla->id)}}" class="btn btn-link" style="width:40px; margin: 0"><i class="far fa-edit"></i></a>
                        </div>

                        <div class="col-4 active" style="padding: 0;">
                          <button class="delete-alert btn btn-link" data-reload="1" data-table="#table" data-message1="No podrás recuperar el registro." data-message2="¡Borrado!" data-message3="El registro ha sido borrado." data-method="DELETE" data-action="{{action('PlantillasController@destroy',$plantilla->id)}}" style="width:40px; margin: 0; padding: 0;"><i class="far fa-trash-alt"></i></button>
                        </div>

                      </div>
                    </td>
                  </tr>
                  @endforeach
              </tbody>
            </table>
          </div>
        </div>
    </div>
  </div>             
</div>
@stop