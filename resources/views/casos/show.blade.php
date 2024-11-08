@extends('layout')

@section('content')
  
  <div>
      <a href="{{session('urlBack')}}" title="Regresar" class="btn boton_agregar"><i class="fas fa-long-arrow-alt-left"></i></a>
  </div>

  <br>

  <div align="center">

      <label for="">Nombre: </label>
      <p>{{$caso->nombre_cliente}}</p>     
          
      <label for="">Última Actualización:</label>
      <p>{{date("d/m/Y", strtotime($caso->updated_at))}}</p>

      <br>

      <label>Plantillas Contestadas :</label>

  </div>

  <br>

  <div  align="center">

      <table id="table_index" class="table" style="width: 50%; font-size:16px;">
          <thead>
             <tr>
                <th>Nombre de la Plantilla</th>
                <th>Acciones</th>
              </tr>
          </thead>
          <tbody>
           @foreach($plantillas as $p)
              <tr>
                <td>{{$p->plantilla->nombre}}</td>
                <td>

                  <div class="div_btn_acciones">
                    <form method="GET" action="{{action('CasosController@viewCasosPdf')}}" target="_blank">
                    @csrf
                      <input type="hidden" name="plantilla_id" value="{{openssl_encrypt($p->plantillaId, 'AES-128-CTR', 'GeeksforGeeks', 0, '1234567891011121')}}">
                      <input type="hidden" name="caso_id" value="{{openssl_encrypt($p->casoId, 'AES-128-CTR', 'GeeksforGeeks', 0, '1234567891011121')}}">
                      <button class="btn" title="Ver PDF"><i class="far fa-file-pdf"></i></button>
                    </form>
                  </div>

                </td>
              </tr>
            @endforeach
          </tbody>
      </table>

  </div>

@stop