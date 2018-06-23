$(document).ready(function() {
	DLaborados();
	cargarFechas();
});

$( function() {
	$( ".Calend" ).datepicker();
} );

function DLaborados() {
	var conexion, variables, fecha, Tn, Dep, resultado, BUSCAR;
	fecha = document.getElementById('fch').value;
	Tn = document.getElementById('tiponom').value;
	Dep = document.getElementById('Dep').value;
	BUSCAR = document.getElementById('CE').value;


	if(fecha != ''){
		if(Tn != '' && Tn > 0 && Tn <= 6){
			if(Dep != ''){

				variables = "fcH="+fecha+"&Tn="+Tn+"&Dep="+Dep+"&BUS="+BUSCAR;
				conexion = window.XMLHttpRequest ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
				conexion.onreadystatechange = function(){
					if(conexion.readyState == 4 && conexion.status == 200){
						document.getElementById('estado_consulta_ajax').innerHTML = conexion.responseText;
						var Dimensiones = AHD();

						if(Dimensiones[3] > Dimensiones[1]){
						   	$('#pie').css("position", "inherit");
						}else {
						   	$('#pie').css("position", "absolute");
					  	}
					}else if(conexion.readyState != 4) {
						resultado = '<div class="progress">';
			            resultado += '<div class="indeterminate"></div>';
			            resultado += '</div>';
			            document.getElementById('estado_consulta_ajax').innerHTML = resultado;

			            var Dimensiones = AHD();

						if(Dimensiones[2] > 330){
					    	$('#pie').css("position", "inherit");
					  	}else {
					    	$('#pie').css("position", "absolute");
				  		}
					}
				}
				conexion.open('POST', 'ajax.php?modo=DLaborados', true);
				conexion.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
				conexion.send(variables);

			}else {
				Materialize.toast('Ingrese Un Departamento', 3000);
			}
		}else {
			Materialize.toast('Ingrese Un Tipo De Nomina', 3000);
		}
	}else {
		Materialize.toast('Ingrese Una Fecha', 3000);
	}
}

function GDLaborados() {
	var conexion, variables, fcH, Tn, Dep;

	fcH = document.getElementById('fch').value;
	Tn = document.getElementById('tiponom').value;
	Dep = document.getElementById('Dep').value;

	if(fcH != ''){
		if(Tn != '' && Tn > 0 && Tn <= 6){
			if(Dep != ''){

				variables = $('#frmDLaborados').serialize();
				variables += "&fcH="+fcH+"&Tn="+Tn+"&Dep="+Dep;
				conexion = window.XMLHttpRequest ? new XMLHttpRequest() : new ActiveObject('Microsoft.XMLHTTP');
				conexion.onreadystatechange = function(){
					if(conexion.readyState == 4 && conexion.status == 200)
					{
						if(conexion.responseText.replace(/\ufeff/g, '') == 1){
							document.getElementById('textCargado').innerHTML = "ARCHIVO GENERADO";

							setTimeout(function() {
								$('#modal1').modal('close');
								document.getElementById('textCargado').innerHTML = "Procesando...";
							}, 1500);
						}else {
							document.getElementById('textCargado').innerHTML = conexion.responseText;
							setTimeout(function() {
								$('#modal1').modal('close');
								document.getElementById('textCargado').innerHTML = "Procesando...";
							}, 2500);
						}


					}else if(conexion.readyState != 4)
					{
						$('#modal1').modal('open');
					}
				}
				conexion.open('POST', 'ajax.php?modo=GDLaborados', true);
				conexion.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
				conexion.send(variables);

			}else {
				Materialize.toast('Ingrese Un Departamento', 3000);
			}
		}else {
			Materialize.toast('Ingrese Un Tipo De Nomina', 3000);
		}
	}else {
		Materialize.toast('Ingrese Una Fecha', 3000);
	}
}

function cargarFechas() {
	$.ajax({
		url: 'fechasFestivas.json',
		type: 'POST',
		dataType: 'JSON',
		success: function(data) {						
			var infodata = "<input type='hidden' name='cantidadJ' value='"+data.fechas.length+"'/><thead><tr><th>Activo</th><th>Fecha</th><th></th></tr></thead><tbody>";
			for(var x = 0; x <= data.fechas.length-1; x++)
			{
				if(data.fechas[x].estatus == 1){
					infodata += "<tr><td><p style='margin: 0;padding: 0;text-align: center;'>";
					infodata += "<input type='radio' name='fecha' id='"+data.fechas[x].value+"' value='"+data.fechas[x].value+"'>";
					infodata += "<label for='"+data.fechas[x].value+"'></label>";
                	infodata += "</td><td>"+data.fechas[x].fecha+"</td>";
                	infodata += '<td><span style="background-color: red;padding: 2px 6px;border-radius: 50%;color: white;cursor: pointer;" onclick="eliminarFecha(&#39;'+data.fechas[x].value+'&#39;)">X</span></td></tr>';
				}			
			}
			infodata += "<tr><td></td><td><input type='text' class='Calend' name='fechaA' id='fechaA'></td><td></td></tr></tbody><input type='hidden' name='opcion' value='nuevo'/> ";
			$("#Tfechas").html(infodata);
			$( ".Calend" ).datepicker();

			$('input[type=radio][name=fecha]').change(function() {
				var date = new Date();
				var year = date.getFullYear();
				$('#fch').val(this.value.substr(0, 2)+"/"+this.value.substr(2, 2)+"/"+year);
				DLaborados();
			});
		}
	});
}

function eliminarFecha(codigo)
{
	var modo = "eliminar";
	var value = codigo;
	var cantidad = $('input[name=cantidadJ]').val();
	$.ajax({
		url: "fechasF.php",
		method: "POST",
		data: "value="+codigo+"&opcion="+modo+"&cantidad="+cantidad+"&fecha="+value
	}).done(function() {
		cargarFechas();
	});
}

function agregarFecha()
{	
	var modo = "nuevo";	
	var value = $('#fechaA').val();
	var cantidad = $('input[name=cantidadJ]').val();
	$.ajax({
		url: "fechasF.php",
		method: "POST",
		data: "value="+value+"&opcion="+modo+"&cantidad="+cantidad+"&fecha="+value
	}).done(function() {
		cargarFechas();
	});
}

