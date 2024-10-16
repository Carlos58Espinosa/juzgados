@extends('layout')

@section('content')
<div class="main-content">
  <div class="section__content section__content--p30">
    <div class="container-fluid">
      <div class="card" id="card-section">

            <div>
                <a href="{{action('PlantillasController@create')}}" class="btn btn-info" style="width: 40px; margin-bottom: 10px;"><i class="fas fa-plus"></i></a>
            </div>

            <table id="table" class="table table-striped">
                <thead>
                  <tr class="table-success">
                    <th>Nombre de la Plantilla</th>
                    <th width="50%">Texto / Contenido</th>
                    <th>Acciones</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($plantillas as $plantilla)
                    <tr>
                      <td>{{$plantilla->nombre}}</td>
                      <td > <div style="height: 150px; overflow-y: scroll;">{!!$plantilla->texto!!} </div></td>
                      <td>
                        <div class="row">

                          <div class="col-3">
                            <a class="btn btn-outline-info" href="{{action('PlantillasController@show',$plantilla->id)}}"><i class="far fa-eye"></i></a>
                          </div>
    
                          <div class="col-3">
                            <form method="POST" action="{{action('PlantillasController@viewPdf')}}" target="_blank">
                            @csrf
                              <input type="hidden"  name="id" value="{{$plantilla->id}}">
                              <button class="btn btn-outline-info"><i class="far fa-file-pdf"></i></button>
                            </form>
                          </div>

                          <div class="col-3">
                             <a class="btn btn-outline-info" href="{{action('PlantillasController@edit',$plantilla->id)}}"><i class="far fa-edit"></i></a>
                          </div>

                          <div class="col-3 active">
                            <button class="delete-alert btn btn-outline-info" data-reload="1" data-table="#table" data-message1="No podrás recuperar el registro." data-message2="¡Borrado!" data-message3="El registro ha sido borrado." data-method="DELETE" data-action="{{action('PlantillasController@destroy',$plantilla->id)}}"><i class="far fa-trash-alt"></i></button>
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
@stop