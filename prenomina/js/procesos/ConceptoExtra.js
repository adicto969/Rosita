$(document).ready(function(){
  REcargoColum();
  $("form").keypress(function(e){
    if(e.which == 13){
      return false;
    }
  });
});

function REcargoColum(){
  var valor = $("#tiponom").val();
  var resultado="";
  resultado = '<div class="progress">';
  resultado += '<div class="indeterminate"></div>';
  resultado += '</div>';
  $("#estado_consulta_ajax").html(resultado);
  $.ajax({
    method: "POST",
    url: "ajax.php?modo=ConceptoExtra",
    data: "TN="+valor
  }).done(function(datosC){
    $("#estado_consulta_ajax").html(datosC);
  });
  cambiarPeriodo();
  $('#pie').addClass("pieabajo");
}

function InserConExt(codigo, columna, id) {
  var valor = $("#"+id).val();
  $.ajax({
    method: "POST",
    url: "ajax.php?modo=InserConceptoExtra",
    data: "codigo="+codigo+"&columna="+columna+"&valor="+valor
  }).done(function(info){
    console.log(info);
  });
}

function cambiarPeriodo() {
	var conexion, variables, Periodo, tn;
  	Periodo = document.getElementById('periodo').value;
  	tn = document.getElementById('tiponom').value;
  	if(Periodo != ''){
      	variables = 'periodo='+Periodo+'&TN='+tn;
      	conexion = window.XMLHttpRequest ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
      	conexion.onreadystatechange = function() {
        if(conexion.readyState == 4 && conexion.status == 200){
         	if(conexion.responseText == 'Error'){
            	document.getElementById('estado_consulta_ajax').innerHTML = '<div style="width: 100%" class="deep-orange accent-4"><h6 class="center-align" style="padding-top: 5px; padding-bottom: 5px; color: white;">No hay fecha de este periodo !</h6></div>';

          	} else {

	            var fechaJSON = JSON.parse(conexion.responseText.replace(/\ufeff/g, ''));

	            document.getElementById('btnT').disabled  = false;
          	}
        }else if(conexion.readyState != 4){
          	document.getElementById('btnT').disabled  = true;
        }
      }
      	conexion.open('POST', 'ajax.php?modo=periodo', true);
      	conexion.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
      	conexion.send(variables);
    }else {
      	document.getElementById('estado_consulta_ajax').innerHTML = '<div style="width: 100%" class="deep-orange accent-4"><h6 class="center-align" style="padding-top: 5px; padding-bottom: 5px; color: white;">Todos los datos deben estar llenos !</h6></div>';
    }
}


function pruebaExcel() {
  var tn = $('#tiponom').val();
  var Dep = $('#Dep').val();
  if(tn > 0){
	if(Dep.length > 0){
	  $.ajax({
	    method: "POST",
	    url: "ajax.php?modo=GconceptoExtra",
	    data: "TN="+tn+"&centro="+Dep,
	    beforeSend: function() {
	      $('#textCargado').html("Procesando...");
	      $('#modal1').modal('open');
	    }
	  }).done(function(datosC){
	    console.log(datosC);
	    if(datosC == '1'){
	        $('#textCargado').html("ARCHIVO GENERADO");
	    }else{
	        $('#textCargado').html("ERROR AL GENERAR EL ARCHIVO");
	    }
	  }).fail(function(retorno){
	    $('#textCargado').html(retorno);
	  }).always(function(){
	    setTimeout(function(){
	      $('#textCargado').html("Procesando...");
	      $('#modal1').modal('close');
	    }, 1500);
	  });
	}else {
	  Materialize.toast('El Departamento es requerido', 4000);
	}
  }else {
     Materialize.toast('El tipo de nomina es requerido', 4000);
  }

}


function GenerarExcel(){
  var tn = $('#tiponom').val();
  var Dep = $('#Dep').val();

  if(tn > 0){
	if(Dep.length > 0){
		$.ajax({
		    method: 'POST',
		    url: 'ajax.php?modo=GenerarExcel',
		    data: "tipo=ConceptoExtra&TN="+tn+"&Dep="+Dep,
		    beforeSend: function(){
		      $('#textCargado').html("Procesando...");
		      $('#modal1').modal('open');
		    }
		  }).done(function(datosC){
		    console.log(datosC);
		    if(datosC.replace(/\ufeff/g, '') == '1'){
		        $('#textCargado').html("ARCHIVO GENERADO");
		    }else{
		        $('#textCargado').html("ERROR AL GENERAR EL ARCHIVO");
		    }
		  }).fail(function(retorno){
		    $('#textCargado').html(retorno);
		  }).always(function(){
		    setTimeout(function(){
		      $('#textCargado').html("Procesando...");
		      $('#modal1').modal('close');
		    }, 1500);
		  });
	}else {
	  Materialize.toast('El Departamento es requerido', 4000);
	}
  }else {
	Materialize.toast('El tipo de nomina es requerido', 4000);
  }

}
