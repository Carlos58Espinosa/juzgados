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
                  <label for="">Nombre: <span style="color:red">*</span></label>
                  <input style="text-transform: none;" type="text" class="form-control @error('nombre') is-invalid @enderror input100" name="nombre" value="{{$config->nombre}}" disabled>
                  @error('nombre')
                      <span class="invalid-feedback" role="alert">
                          <strong>{{ $message }}</strong>
                      </span>
                  @enderror
                </div>
              </div>

              <div style="margin-top: 10px;" class="form-group">
                <label style="text-align: center">Plantillas en la Configuración:</label>
                <div style="margin-top: 10px;" class=" table-responsive table-striped table-bordered">
                  <table id="tabla_plantillas" class="table" style="width: 100%; table-layout: fixed;font-size:16px;">
                      <thead>
                         <tr>
                            <th>Nombre de la Plantilla</th>
                            <th>Orden</th>
                          </tr>
                      </thead>
                      <tbody>
                       @foreach($config_plantillas as $cp)
                          <tr>
                            <td>{{$cp->plantilla->nombre}}</td>
                            <td>{{$cp->orden}}</td>
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