<!DOCTYPE html>
<html lang="es-ES">

  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <meta charset="utf-8">
    <link rel="stylesheet" href="css/style.css">
    <title>Turi - Inforela</title>
    <style>         
        .span_param {
            border:1px solid #0dcaf0;
            border-radius: 5px;
            padding: 1px;
        }
        body {
            font-family:Arial Black ;        
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
            margin: {{$caso->margenArrAba}}px {{$caso->margenDerIzq}}px;   
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
        <main>    
            <div>        
                {!! $res !!}
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
