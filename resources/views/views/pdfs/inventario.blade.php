<!DOCTYPE html>
<html lang="es-ES">
  <head>
    <meta charset="utf-8">
    <title>Alitas Posadas</title>
    <style>
        .span_param {
            border:1px solid #0dcaf0;
            border-radius: 5px;
            padding: 1px;
        }
        body {           
        }
        table {
            width: 100%;
            margin-bottom: 15px;
            text-align: left;
            table-layout:fixed;       
        }
        th {
            color: #FFF;
            background-color: #2E2E2D;
            padding: 2px 5px;
            font-size: 9px;
        }
        td {
            padding-left: 5px;
            padding-top: 5px;
        }
        .bold {
          font-weight: bold;
        }

    
        @page {
            margin: 50px 50px;   
        }

        header {
           align-content: center;
           align-items: center;
        }

        footer {
        }
       
  </style>
  </head>
    
    <body>
        <header> 
            <div align="center" style="width: fit-content;">  
                <img src="{{$logos[0]}}"  style="height: 90px; width: 130px;" />
            </div>  
        </header>
         <!-- <footer>
            Footer
        </footer> -->

        <main style="margin-top:50px; padding-left: 100px;">
            <div>
                <table id="tabla_index" class="table table-striped table-hover">
                      <thead>
                        <tr>           
                          <th>Producto</th>          
                          <th>Presentación</th>
                          <th>Detalle</th>
                          <th>Sucursal</th>
                          <th>Cantidad</th>
                        </tr>
                      </thead>
                      <tbody>
                        @foreach($registros as $r)
                          <tr id="{{$r->id}}" value="{{$r->sucursal_id}}">            
                            <td>{{$r->producto->etiqueta_producto->nombre}}</td>           
                            <td>{{( $r->producto->presentacion != null ) ? $r->producto->presentacion->nombre : ''}}</td>
                            <td>{{$r->producto->detalle}}</td>
                            <td>{{$r->sucursal->nombre}}</td> 
                            <td>{{$r->cantidad}}</td>            
                          </tr>
                        @endforeach
                      </tbody>
                </table>
            </div>
        </main>
  
      <!-- Scripts -->
      <script src="//cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
      <script src="//cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.1/js/bootstrap.min.js"></script>
      <script type="text/php">
        if (isset($pdf)) {
            //$text = "page {PAGE_NUM} / {PAGE_COUNT}";
            //$width = $fontMetrics->get_text_width($text, $font, $size) / 2;
            //$x = ($pdf->get_width() - $width) / 2;
            //$y = $pdf->get_height() - 35;

            $text = "{PAGE_NUM}";
            $size = 10;
            $font = $fontMetrics->get_font("helvetica", "normal");
            $x = $pdf->get_width() - $GLOBALS['y_paginado'];
            $y = $pdf->get_height() - 30;
            $pdf->page_text($x, $y, $text, $font, $size);
        }
      </script>
    </body>   
</html>
