<!DOCTYPE html>
<html lang="es-ES">
  <head>
    <meta charset="utf-8">
    <title>Turi - Inforela</title>
    <style>
        body {
            font-family:"Trebuchet MS", Helvetica, sans-serif;
            font-size: 14px;            
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
            margin: 100px 100px;
        }

        header {
            position: fixed;
            top: -60px;
            height: 90px;
            /*background-color: lightblue;*/
            color: white;
            text-align: center;
            line-height: 35px;
        }

        footer {
            position: fixed;
            bottom: -60px;
            height: 50px;
            background-color: #752727;
            color: white;
            text-align: center;
            line-height: 35px;
        }
       
  </style>
  </head>
    
    <body>
        <header>
            <div>  
                <img align="left" src="../public/images/mexico.png"  style="margin-top:0px; padding: 0px; height: 90px; width: 130px; margin-left:0px;" />
                <img align="right" src="../public/images/{{$estado}}.png"  style="margin-top:0px; padding: 0px; height: 90px; width: 220px; margin-left:400px;" />
            </div>  
        </header>
         <!-- <footer>
            Footer
        </footer> -->

        <main style="margin-top:50px;">
            <div>
                {!!$res!!}
            </div>
        </main>
  
      <!-- Scripts -->
      <script src="//cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
      <script src="//cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.1/js/bootstrap.min.js"></script>
    </body>
</html>
