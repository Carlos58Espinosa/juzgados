<!---------------------- Banco de Datos --------------->
<div class="offcanvas offcanvas-end" id="demo" style="margin-top:120px; width: 650px; height: 300px; overflow: hidden; overflow-y: scroll;">
        
  <input type="hidden" name="nuevos_campos_cad" id="nuevos_campos_cad" value="">
  <input type ="hidden" id="grupo_id" name="grupo_id">

  <div id="div_campos_plantilla"></div>

</div>

<div class="container-fluid mt-3">
  <button class="btn btn-primary" type="button" data-bs-toggle="offcanvas" data-bs-target="#demo"><i class="far fa-keyboard"></i>           Banco de Datos</button>
</div>

<!-- --------------------------------------------------------------- -->   
            
<!---------------------- Contenedor de: Texto de Plantillas (SummerNote) ------------------>
<br>

<div id="div_textos_summernote" class="row" style="margin-top: 0px;" hidden>

    <div class="col-12 col-sm-6 col-md-6">
      <div class="form-group">
        <label for="">Vista Previa: </label>
        <br>
        <textarea style="height: 480px; width:300px" required name="texto_final" id="texto_final"></textarea>
      </div>
    </div>

    <div class="col-12 col-sm-6 col-md-6">
      <label for=""> Editar Plantilla: <input style="margin-top:20px; margin-left:10px; transform: scale(1.5);" type="checkbox" class="check-active" id="check_edit_template" onclick="hiddenSummernote()"></label>
      <div id ="div_summernote" class="form-group">                    
        <textarea required name="texto" id="summernote"></textarea>       
      </div>
    </div>  

    <br>
    <br> 
    <br>        

    <div class="row">
      <div class="form-group">
        <button type="submit" class="btn btn-success">Guardar</button>
      </div>
    </div>        

</div>
<!-- --------------------------------------------------------------- -->
  
