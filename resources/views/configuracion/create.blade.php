@extends('layout')

@section('content')
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js">
</script> <link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.24/themes/smoothness/jquery-ui.css" />
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.24/jquery-ui.min.js"></script>

<div class="main-content">
    <div class="section__content section__content--p30">
      <div class="container-fluid">
        <div class="card" id="card-section">

          <div>
            <a href="{{session('urlBack')}}" class="btn btn-info" style="width: 40px; margin-bottom: 10px;float: left"><i class="fas fa-long-arrow-alt-left"></i></a>
          </div>

          <form class="" action="{{action('ConfiguracionController@store')}}" method="post" accept-charset="UTF-8" enctype="multipart/form-data">
            @csrf

            <div class="row">

              <input type ="hidden" id="old_ids" name="old_ids[]">

              <div class="col-12 col-sm-6 col-md-4">
                <div class="form-group">
                  <label for="">Nombre: <span style="color:red">*</span></label>
                  <input style="text-transform: none;" type="text" class="form-control @error('nombre') is-invalid @enderror input100" required name="nombre" value="{{old('nombre')}}">
                  @error('nombre')
                      <span class="invalid-feedback" role="alert">
                          <strong>{{ $message }}</strong>
                      </span>
                  @enderror
                </div>
              </div>              

              <div class="col-12 col-sm-6 col-md-4">
                <div class="form-group">
                  <label for="">Plantillas:</label>
                  <select class="form-control selectpicker show-menu-arrow @error('plantillas') is-invalid @enderror input100" data-style="form-control" data-live-search="true" title="-- Selecciona las Plantillas --" multiple="multiple" name="plantillas_ids[]"              id="plantillas_ids_aux" onchange="fillTableTemplates()">
                  @foreach($plantillas as $plantilla)
                    <option  {{ (collect(old('plantillas_ids'))->contains($plantilla->id)) ? 'selected':'' }}  value="{{$plantilla->id}}">{{$plantilla->nombre}}<a href="" class="btn btn-link" style="width:40px; margin: 0"><i class="far fa-eye"></i></a></option>
                  @endforeach
                  </select>
                </div>
              </div>
          </div>
            
            <div style="margin-top: 100px;" class=" table-responsive table-striped table-bordered" >
              <table id="tabla_plantillas" class="table" name="plantillas_orden_ids[]" style="width: 100%; table-layout: fixed;font-size:16px;">
                  <thead>
                      <tr>
                        <th hidden>Id</th>
                        <th>Nombre</th>
                        <th>Orden</th>
                      </tr>
                  </thead>
                  <tbody>
                   
                  </tbody>
              </table>
            </div>

            <div class="col-12">
              <div class="form-group">
                <button type="submit" class="btn btn-success">Guardar</button>
              </div>
            </div>

            
          </form>
        </div>
      </div>
    </div>
  </div>

  <script>

  function fillTableTemplates(){
    var plantillas_ids_selected = $('#plantillas_ids_aux').val();  
    document.getElementById('old_ids').value = plantillas_ids_selected;   
    //console.log(document.getElementById('old_ids').value);
    var plantillas = @json($plantillas);
    //console.log(plantillas_ids_selected);
    //console.log(plantillas);

    $("#tabla_plantillas tr").remove(); 
    var cad = "<thead><tr><th hidden>id</th><th>Nombre</th><th>Orden</th><tr></thead>";
    document.getElementById("tabla_plantillas").insertRow(-1).innerHTML = cad;  

    var plantillas_selected = plantillas.filter(function (pl) {
      if (plantillas_ids_selected.includes(String(pl.id)))
        return pl;
    });

    //console.log("Plantillas Seleccionadas");
    //console.log(plantillas_selected);
    var orden = 1;
    plantillas_selected.forEach(function (item) {
        cad = "<tr>";
        cad += "<td hidden value=\""+item.id+"\">"+item.id+"</td>";
        cad += "<td>"+item.nombre+"</td>";
        cad += "<td>"+orden+"</td>";
        cad += "</tr>";
        orden +=1;  
        document.getElementById("tabla_plantillas").insertRow(-1).innerHTML = cad;   
    });
  }

  $(function () {
 
      if($('#plantillas_ids_aux').val().length > 0)
          fillTableTemplates();


      $("#tabla_plantillas").sortable({
          items: 'tr:not(tr:first-child)',
          dropOnEmpty: false,
          start: function (G, ui) {
              ui.item.addClass("select");
          },
          stop: function (G, ui) {
              ui.item.removeClass("select");
              $(this).find("tr").each(function (GFG) {
                  if (GFG > 0) {
                      $(this).find("td").eq(2).html(GFG);
                  }
              });
              var tabla_aux = document.getElementById("tabla_plantillas");
              var tabla_trs = tabla_aux.getElementsByTagName("tr");
              var arr_ids_final = []
              for (var i = 0; i < tabla_trs.length; i++) {
                var td = tabla_trs[i].getElementsByTagName("td")[0];
                if(td != undefined)
                  arr_ids_final.push(''+td.innerHTML);
              }
              document.getElementById('old_ids').value=arr_ids_final;
          } 
      });
      
  });
  </script>
  @stop