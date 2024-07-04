@extends('layout')

@section('content')
<div class="main-content">
  <div class="section__content section__content--p30">
    <div class="container-fluid">
      <div class="card" id="card-section">

          <div>
            <a href="{{session('urlBack')}}" class="btn btn-info" style="width: 40px; margin-bottom: 10px;float: left"><i class="fas fa-long-arrow-alt-left"></i></a>
          </div>

          <div class="col-12 col-sm-6 col-md-4">
            <div class="form-group">
                <label for="">Nombre: </label>
                <p>{{$caso->nombre_cliente}}</p>                
            </div>
          </div>

          <div class="col-12 col-sm-6 col-md-4">
            <div class="form-group">
                <label for="">Fecha de Creación:</label>
                <p>{{$caso->created_at}}</p>
            </div>
          </div>

          <div style="margin-top: 10px;" class="form-group">
            <label style="text-align: center">Plantillas :</label>
            <div style="margin-top: 10px;" class=" table-responsive table-striped table-bordered">
              <table id="tabla_plantillas" class="table" style="width: 100%; table-layout: fixed;font-size:16px;">
                  <thead>
                     <tr>
                        <th>Nombre de la Plantilla</th>
                        <th></th>
                      </tr>
                  </thead>
                  <tbody>
                   @foreach($plantillas as $p)
                      <tr>
                        <td>{{$p->plantilla->nombre}}</td>
                        <td>

                          <div class="col-4" style="padding: 0;">
                            <form method="POST" action="{{action('CasosController@viewCasosPdf')}}" target="_blank">
                            @csrf
                              <input type="hidden" name="plantilla_id" value="{{$p->plantillaId}}">
                              <input type="hidden" name="caso_id" value="{{$p->casoId}}">
                              <button class="btn btn-link m-0 p-0" style="width:40px; margin: 0"><i class="far fa-file-pdf"></i></button>
                            </form>
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
</div>
@stop