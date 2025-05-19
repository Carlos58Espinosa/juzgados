@section('styles')
    <style>
     


    </style>
  @endsection
  
  
  @extends('layout')

  @section('content')

  <div style="margin-right: -15px;">
    <div class="container-custom">

      <form action="{{action('VentasController@index')}}" method="GET" accept-charset="UTF-8" enctype="multipart/form-data">




        <div class="row">
          <div class="col-6 col-sm-3 order-1">

            <div class="div_busqueda_blanco">
              <div class="div_busqueda_azul">
                <div class="div_busqueda" align="center"> 
                  <p class="m-0">Año <span style="color:red">*</span></p>
                </div> 
              </div>
            </div>

            <div class="div_busqueda_blanco">
              <div class="div_busqueda_azul">
                <div class="div_busqueda2" align="center"> 
                  <select class="form-select" name="year" required>
                  @foreach($years as $r)
                    <option value="{{$r->year}}">{{$r->year}}</option>
                  @endforeach
                  </select>
                </div> 
              </div>
            </div>

          </div>

          <div class="col-12 col-sm-3 order-3 order-sm-2 mt-3 mt-sm-0">

            <div class="div_busqueda_blanco">
              <div class="div_busqueda_azul">
                <div class="div_busqueda" align="center"> 
                  <p class="m-0">Mes <span style="color:red">*</span></p>
                </div> 
              </div>
            </div>

            <div class="div_busqueda_blanco multi">
              <div class="div_busqueda_azul">
                <div class="div_busqueda2" align="center"> 
                  <select class="selectpicker multiselect_ventas" id="select_sucursales" title="Selecciona los meses" data-live-search="true" multiple name="meses_ids[]" value="{{old('meses_ids')}}">
                      <option value="1">Enero</option>
                      <option value="2">Febrero</option>
                      <option value="3">Marzo</option>
                      <option value="4">Abril</option>
                      <option value="5">Mayo</option>
                      <option value="6">Junio</option>
                      <option value="7">Julio</option>
                      <option value="8">Agosto</option>
                      <option value="9">Septiembre</option>
                      <option value="10">Octubre</option>
                      <option value="11">Noviembre</option>
                      <option value="12">Diciembre</option>
                  </select>
                </div> 
              </div>
            </div>


          </div>

          <div class="col-6 col-sm-3 order-2 order-sm-3 ">

            <div class="div_busqueda_blanco">
              <div class="div_busqueda_azul">
                <div class="div_busqueda" align="center"> 
                  <p class="m-0">Agrupación <span style="color:red">*</span></p>
                </div> 
              </div>
            </div>

            <div class="div_busqueda_blanco">
              <div class="div_busqueda_azul">
                <div class="div_busqueda2" align="center"> 
                  <select class="form-select" name="agrupacion" style="font-size: 16px !important;" required>
                    <option value="dia">Día</option>
                    <option value="mes">Mes</option>
                  </select>
                </div> 
              </div>
            </div>

          </div>

          <div class="col-3 order-4">
            <button type="submit" class="button_action boton_busqueda_reporte_ventas" title="Ver PDF"><img src="{{ asset('images/boton_buscar.png') }}"></button>
          </div>
        </div>

      </form>

      <br>


      <!--div class="div_table_index div_reporte_ventas"> 
        <table id="tabla_listado" class="table_index">
          <thead>
            <tr>
              <th>
                <div class="div_th_borde_blanco th_sucursal">
                  <div>Sucursal</div>
                </div>
              </th>
              <th>
                <div class="div_th_borde_blanco">
                  <div>Fecha</div>
                </div>
              </th>
              <th>
                <div class="div_th_borde_blanco">
                  <div>Total</div>
                </div>
              </th>
            </tr>
          </thead>
          <tbody>
            @foreach($registros as $r)
              <tr>  
                <td>{{$r->sucursal->nombre}}</td>  
                <td>{{ ($r->dia) ?  $r->dia . ' / ' : '' }}{{strlen($r->mes) == 1 ? '0'.$r->mes : $r->periodo}} / {{$r->year}}</td>
                <td>${{$r->total}}</td>  
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>

      <br-->


      <div class="card div_highcharts">
          <div class="card-body" >
            <div id="graph" style="font-size: 16px !important;"></div>
          </div>
      </div>

    </div>
  </div>




  @endsection


@section('scripts')
  <script type="text/javascript">

  $('document').ready(function(){

      var grafica = @json($grafica);
      //console.log(grafica['series']);

      Highcharts.chart('graph', {
          chart: {
              type: 'column'
          },
          legend: {
            itemStyle: {
                fontSize: '20px'
            }
          },
          title: {
              text: 'Ventas',
              style:{
                    fontSize: '25px'
              } 
          },
          subtitle: {
              text:''
          },
          xAxis: {
              categories: grafica['categorias'],
              crosshair: true,
              accessibility: {
                  description: 'Countries'
              },
              labels: {
                  style: {
                      fontSize:'15px'
                  }
              }
          },
          yAxis: {
              min: 0,
              title: {
                  text: 'Ventas ($)',
              },
              labels: {
                  style: {
                      fontSize:'15px'
                  }
              }
          },
          tooltip: {
              valueSuffix: '',
              valuePrefix: '$',
              style:{
                  fontSize: '15px'
              } 
          },
          plotOptions: {
              column: {
                  pointPadding: 0.2,
                  borderWidth: 0
              }
          },
          series: grafica['series']
      });

      //document.getElementById("div_header_sucursal").hidden = true;
  });

  </script> 

@endsection