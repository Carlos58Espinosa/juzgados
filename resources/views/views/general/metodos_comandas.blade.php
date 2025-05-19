<script>
function sumarRestarInventario(control_input_name, operador){
    let control_html = document.getElementById(control_input_name);
    switch(operador){
      case '-':
        control_html.value = parseInt(control_html.value) -1;
        break;
      case '+':
        control_html.value = parseInt(control_html.value) +1;
        break;
    }
    (control_html.value < 0) ?  control_html.value = 0 : '';  
    calcularTotal();
}

function calcularTotal(){
    var data = document.getElementsByTagName('input');
    var arr_controles = ['_token', 'total', 'sucursal_id', 'total_sin_descuento', 
      'con_descuento', 'descuento_porcentaje'];

    data = Object.values(data).filter((d) =>  d.value != 0 && !arr_controles.includes(d.name) && d.hidden == false);

    var total = 0;
    data = Array.from(new Set(data));

    data.forEach(function(element){
        (element.value > 0) ? total = total + (element.value * document.getElementById('precio_'+element.name).value ) : '';
    });

    document.getElementById('total').value = total;
    document.getElementById('total_sin_descuento').value = total;
    aplicarDescuento(document.getElementById('con_descuento').checked);
}

function aplicarDescuento(valor){
    let valor_sin_descuento = document.getElementById('total_sin_descuento').value;
    let descuento = parseInt(document.getElementById('descuento_porcentaje').value);

    if (valor) {
      //console.log('checked');
      descuento = (valor_sin_descuento * descuento)/100;
      document.getElementById('total').value = valor_sin_descuento - descuento;
    } else 
      //console.log('not checked');
      document.getElementById('total').value = valor_sin_descuento;
}

function sumarRestarDescuento(operador){
  let valor = parseInt(document.getElementById('descuento_porcentaje').value);
  switch(operador) {
      case '+':
        valor += 1;
        break;
      case '-':
        valor -= 1;
        if(valor < 0)
          valor = 0;
        break;
  }
  document.getElementById('descuento_porcentaje').value = valor;
  aplicarDescuento(document.getElementById('con_descuento').checked);
}

</script>