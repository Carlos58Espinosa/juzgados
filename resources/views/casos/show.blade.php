@extends('layout')

@section('content')
  
  <div>
      <a href="{{session('urlBack')}}" title="Regresar" class="btn boton_agregar"><i class="fas fa-long-arrow-alt-left"></i></a>
  </div>

  <br>

  <div style="margin-left:200px;" class="row">

    <div class="col-12 col-sm-6 col-md-4">
      <div class="form-group">
          <label for="">Nombre: </label>
          <p>{{$caso->nombre_cliente}}</p>                
      </div>
    </div>

    <div class="col-12 col-sm-6 col-md-4">
      <div class="form-group">
          <label for="">Última Actualización:</label>
          <p>{{date("d/m/Y", strtotime($caso->updated_at))}}</p>
      </div>
    </div>

  </div>

  <br>

  <div class="row" align="center">
         
      <label>Plantillas Contestadas :</label>

  </div>

  <br>

  <div class="row" align="center">

      <table id="table_index" class="table" style="width: 50%; font-size:16px; margin-left: 400px;">
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

                  <div class="div_btn_acciones">
                    <form method="POST" action="{{action('CasosController@viewCasosPdf')}}" target="_blank">
                    @csrf
                      <input type="hidden" name="plantilla_id" value="{{$p->plantillaId}}">
                      <input type="hidden" name="caso_id" value="{{$p->casoId}}">
                      <button class="btn" style="width:40px; margin: 0"><i class="far fa-file-pdf"></i></button>
                    </form>
                  </div>

                </td>
              </tr>
            @endforeach
          </tbody>
      </table>

  </div>

@stop