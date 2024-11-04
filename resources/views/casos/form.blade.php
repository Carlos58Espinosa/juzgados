<div id="carouselExampleCaptions" class="carousel slide">
    
    <div class="carousel-inner">
        <div id="carrusel1" class="carousel-item active" style="display: inline-flex;">     

            <div>

                <input type="hidden" name="nuevos_campos_cad" id="nuevos_campos_cad" value="">
                <label style="margin-left: 200px;">Vista Previa</label>

                <br>

                <textarea name="texto_final" id="texto_final" required></textarea>

            </div>

            <div id="div_campos_plantilla" style="background: white; height:500px; width:630px; margin-left: 10px;"></div>
            
        </div>

        <div id="carrusel2" class="carousel-item" style="height: 500px;">
          <div style="margin-left:300px;" id ="div_summernote" class="form-group">

              <label style="margin-left: 200px;">Edición de Plantilla </label>

              <br>
              
              <textarea name="texto" id="summernote" required></textarea> 

          </div>
        </div>

    </div>

    <button class="carousel-control-prev boton_carrusel" type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide="prev" onclick="disableCarousel()">
      <span class="carousel-control-prev-icon" aria-hidden="true"></span>     
    </button>

    <button class="carousel-control-next boton_carrusel" type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide="next" onclick="disableCarousel()">
      <span class="carousel-control-next-icon" aria-hidden="true"></span>
    </button>

</div>