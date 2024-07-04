@extends('layout')

@section('content')
<div class="main-content">
  <div class="section__content section__content--p30">
    <div class="container-fluid">
        <div class="card" id="card-section">
            <div class="input-group mb-2">
                <a href="{{action('ConfiguracionController@create')}}" class="btn btn-info" style="width: 40px; margin-bottom: 10px;"><i class="fas fa-plus"></i></a>
            </div>

            <div class="table-responsive table-striped table-bordered" style="font-size: 14px; padding: 0;">
            <table id="table" class="table" style="width: 100%; table-layout: fixed;">
              <thead>
                <tr>
                  <th style="width:200px; text-align: center;">Nombre de la Plantilla</th>
                  <th style="width:125px; text-align: center;"></th>
                </tr>
              </thead>
              <tbody>
                @foreach($configuraciones as $config)
                  <tr>
                    <td>{{$config->nombre}}</td>
                    <td>
                      <div class="row" style="margin-right: 0; margin-left: 0;">
                        <div class="col-4" style="padding: 0;">
                          <a href="{{action('ConfiguracionController@show',$config->id)}}" class="btn btn-link" style="width:40px; margin: 0"><i class="far fa-eye"></i></a>
                        </div>

                        <div {{ isset($_GET['active']) ? $_GET['active'] == 0 ? 'hidden' : '' : '' }} class="col-4 active" style="padding: 0;">
                          <a href="{{action('ConfiguracionController@edit',$config->id)}}" class="btn btn-link" style="width:40px; margin: 0"><i class="far fa-edit"></i></a>
                        </div>

                        <div class="col-4 active" style="padding: 0;">
                          <button class="delete-alert btn btn-link" data-reload="1" data-table="#table" data-message1="No podrás recuperar el registro." data-message2="¡Borrado!" data-message3="El registro ha sido borrado." data-method="DELETE" data-action="{{action('ConfiguracionController@destroy',$config->id)}}" style="width:40px; margin: 0; padding: 0;"><i class="far fa-trash-alt"></i></button>
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