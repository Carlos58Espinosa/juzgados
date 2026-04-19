<!DOCTYPE html>
<html lang="es-ES">
  <head>
    <meta charset="utf-8">
    <title>Turi - Inforela</title>
    <style>
        /* Arial */
        @font-face {
            font-family: 'Arial';
            src: url('{{ storage_path("fonts/arial.ttf") }}') format('truetype');
            font-weight: normal;
        }

        @font-face {
            font-family: 'Arial';
            src: url('{{ storage_path("fonts/arialbd.ttf") }}') format('truetype');
            font-weight: bold;
        }

        @font-face {
            font-family: 'Arial';
            src: url('{{ storage_path("fonts/ariali.ttf") }}');
            font-style: italic;
        }

        @font-face {
            font-family: 'Arial';
            src: url('{{ storage_path("fonts/arialbi.ttf") }}');
            font-weight: bold;
            font-style: italic;
        }

        /* Courier New */
        @font-face {
            font-family: 'Courier New';
            src: url('{{ storage_path("fonts/cour.ttf") }}') format('truetype');
            font-weight: normal;
        }

        @font-face {
            font-family: 'Courier New';
            src: url('{{ storage_path("fonts/courbd.ttf") }}') format('truetype');
            font-weight: bold;
        }

        @font-face {
            font-family: 'Courier New';
            src: url('{{ storage_path("fonts/couri.ttf") }}');
            font-style: italic;
        }

        @font-face {
            font-family: 'Courier New';
            src: url('{{ storage_path("fonts/courbi.ttf") }}');
            font-weight: bold;
            font-style: italic;
        }

        /* Comic Sans MS */
        @font-face {
            font-family: 'Comic Sans MS';
            src: url('{{ storage_path("fonts/comic.ttf") }}') format('truetype');
            font-weight: normal;
        }

        @font-face {
            font-family: 'Comic Sans MS';
            src: url('{{ storage_path("fonts/comicbd.ttf") }}') format('truetype');
            font-weight: bold;
        }

        @font-face {
            font-family: 'Comic Sans MS';
            src: url('{{ storage_path("fonts/comici.ttf") }}') format('truetype');
            font-style: italic;
        }

        @font-face {
            font-family: 'Comic Sans MS';
            src: url('{{ storage_path("fonts/comicbi.ttf") }}') format('truetype');
            font-weight: bold;
            font-style: italic;
        }

        /* Times New Roman */
        @font-face {
            font-family: 'Times New Roman';
            src: url('{{ storage_path("fonts/times.ttf") }}') format('truetype');
            font-weight: normal;
        }

        @font-face {
            font-family: 'Times New Roman';
            src: url('{{ storage_path("fonts/timesbd.ttf") }}') format('truetype');
            font-weight: bold;
        }

        @font-face {
            font-family: 'Times New Roman';
            src: url('{{ storage_path("fonts/timesi.ttf") }}') format('truetype');
            font-style: italic;
        }

        @font-face {
            font-family: 'Times New Roman';
            src: url('{{ storage_path("fonts/timesbi.ttf") }}') format('truetype');
            font-weight: bold;
            font-style: italic;
        }
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
            margin: {{$caso->margenArrAba}}px 
                    {{$caso->margenDer}}px 
                    {{$caso->margenArrAba}}px 
                    {{$caso->margenIzq}}px;
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

        <main>
            <div>
                {!!$res!!}
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
