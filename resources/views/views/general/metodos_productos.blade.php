<script>

//CREACION y EDICION de Producto
function mostrarFormularios(indice){
  var tipos = @json($tipos_productos);
  document.getElementById('div_presentacion').hidden = true;
  document.getElementById('div_paquete').hidden = true;
  document.getElementById('div_sucursales').hidden = true;
  document.getElementById('div_sucursales_precios').hidden = true;
  switch(tipos[indice].nombre_aux){
      case 'bruto':
          document.getElementById('div_presentacion').hidden = false;
          document.getElementById('div_sucursales').hidden = false;
        break;
      case 'orden':
          document.getElementById('div_presentacion').hidden = false;
          document.getElementById('div_sucursales_precios').hidden = false;
        break;
      case 'orden_precio':
        document.getElementById('presentacion').value = null;
        document.getElementById('unidad_valor').value = null;
        document.getElementById('div_sucursales_precios').hidden = false;
        break;
      case 'orden_varias':
        document.getElementById('presentacion').value = null;
        document.getElementById('unidad_valor').value = null;
        document.getElementById('div_sucursales_precios').hidden = false;
        document.getElementById('div_paquete').hidden = false;
        break;    
  }
}

function seleccionSucursal(indice){
  console.log(indice);
  document.getElementById("sucursal_id_bd").value=indice;
}
</script>
