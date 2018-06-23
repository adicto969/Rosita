<?php

$objBDSQL = new ConexionSRV();
$objBDSQL->conectarBD();

$objBDSQL2 = new ConexionSRV();
$objBDSQL2->conectarBD();

$bdM = new ConexionM();
$bdM->__constructM();

$Fch = $_POST['fcH'];
$Tn = $_POST['Tn'];
$Dep = $_POST['Dep'];
$BUS = $_POST['BUS'];


list($dia, $mes, $ayo) = explode('/', $Fch);
$Fch = $ayo.$mes.$dia;

if($DepOsub == 1)
{
	$ComSql = "LEFT (relch_registro.centro, ".$MascaraEm.") IN (SELECT DISTINCT LEFT (centro, ".$MascaraEm.")  FROM Llaves WHERE supervisor = ".$supervisor." )";
	$ComSql2 = "LEFT (centro, ".$MascaraEm.")IN (SELECT DISTINCT LEFT (centro, ".$MascaraEm.")  FROM Llaves WHERE supervisor = ".$supervisor." )";
}else {
	$ComSql = "relch_registro.centro IN (".$_SESSION['centros'].")";
	$ComSql2 = "centro IN (".$_SESSION['centros'].")";
}

$whereBUS = "";
if(!empty($BUS)){
	$whereBUS = "(relch_registro.codigo LIKE '%".$BUS."%' OR 
              empleados.ap_paterno+' '+empleados.ap_materno+' '+empleados.nombre LIKE '%".$BUS."%' OR 
              tabulador.actividad LIKE '%".$BUS."%') and ";
}

if($Dep == "todos" || $Dep == "todo" || $Dep == "TODOS" || $Dep == "TODO"){
	//relch_registro.num_conc,
	$query = "
		select DISTINCT relch_registro.codigo,
        empleados.ap_paterno+' '+empleados.ap_materno+' '+empleados.nombre AS nombre,
        tabulador.actividad,
        empleados.sueldo,
        convert (varchar(10), relch_registro.fecha, 103) as Fecha,
        '10' as num_conc,
        '8' as Horas,
        CONVERT( DECIMAL(10, 2), (empleados.sueldo * '2'))  as Importe

        from relch_registro

        INNER JOIN empleados on empleados.empresa = relch_registro.empresa and empleados.codigo = relch_registro.codigo
        INNER JOIN Llaves on Llaves.empresa = relch_registro.empresa and Llaves.codigo = relch_registro.codigo
        INNER JOIN tabulador on  tabulador.empresa = Llaves.empresa and  tabulador.ocupacion = Llaves.ocupacion

        where relch_registro.empresa =  '".$IDEmpresa."' and
        empleados.activo = 'S' and
        relch_registro.fecha = '".$Fch."'  and		
		".$whereBUS."
        relch_registro.tiponom = '".$Tn."'
        group by relch_registro.codigo, empleados.ap_paterno, empleados.ap_materno,
			empleados.nombre, tabulador.actividad, empleados.sueldo, relch_registro.fecha,
			relch_registro.num_conc, relch_registro.centro
        order by tabulador.actividad
	";
	//relch_registro.num_conc = 10 and
}else {
	//relch_registro.num_conc
	$query = "

		select DISTINCT relch_registro.codigo,
        empleados.ap_paterno+' '+empleados.ap_materno+' '+empleados.nombre AS nombre,
        tabulador.actividad,
        empleados.sueldo,
        convert (varchar(10), relch_registro.fecha, 103) as Fecha,
        '10' as num_conc,
        '8' as Horas,
        CONVERT( DECIMAL(10, 2), (empleados.sueldo * '2'))  as Importe

        from relch_registro

        INNER JOIN empleados on empleados.empresa = relch_registro.empresa and empleados.codigo = relch_registro.codigo
        INNER JOIN Llaves on Llaves.empresa = relch_registro.empresa and Llaves.codigo = relch_registro.codigo
        INNER JOIN tabulador on  tabulador.empresa = Llaves.empresa and  tabulador.ocupacion = Llaves.ocupacion

        where relch_registro.empresa =  '".$IDEmpresa."' and
        empleados.activo = 'S' and
        relch_registro.fecha = '".$Fch."'  and		
		".$whereBUS."
        relch_registro.tiponom = '".$Tn."' and
        ".$ComSql."
        group by relch_registro.codigo, empleados.ap_paterno, empleados.ap_materno,
			empleados.nombre, tabulador.actividad, empleados.sueldo, relch_registro.fecha,
			relch_registro.num_conc, relch_registro.centro
        order by relch_registro.codigo asc
	";
	//relch_registro.num_conc = 10 and

}


$numC = $objBDSQL->obtenfilas($query);

if($numC > 0){

	echo '
		<form id="frmDLaborados">
			<table class="responsive-table striped highlight centered">
				<thead>
					<tr>
						<th>Codigo</th>
						<th>Nombre</th>
						<th>Actividad</th>
						<th>Sueldo</th>
						<th>Fecha</th>
						<th>N. Conc</th>
						<th>Horas</th>
						<th>Importe</th>
						<th>Seleccion</th>
					</tr>
				</thead>
				<tbody>';

	$lr = 0;
	$objBDSQL->consultaBD($query);
	while($row = $objBDSQL->obtenResult())
	{
		$lr++;

		echo '
			<tr>
				<td>'.$row["codigo"].'</td>
				<td>'.utf8_encode($row["nombre"]).'</td>
				<td>'.utf8_encode($row["actividad"]).'</td>
				<td>'.$row["sueldo"].'</td>
				<td>'.$row["Fecha"].'</td>
				<td>'.$row["num_conc"].'</td>
				<td>'.$row["Horas"].'</td>
				<td>'.$row["Importe"].'</td>
		';

		$consultInt = "SELECT DLaborados FROM ajusteempleado WHERE IDEmpleado = '".$row["codigo"]."' AND ".$ComSql2." AND IDEmpresa = ".$IDEmpresa.";";
		$objBDSQL2->consultaBD2($consultInt);
		$datos = $objBDSQL2->obtenResult2();

		if($datos['DLaborados'] == 1){
			$c1 = "";
		}else {
			$c1 = 'checked="checked"';
		}

		echo '
				<td><p style="text-align: center;"><input type="checkbox" name="'.$row["codigo"].str_replace("/", "", $row["Fecha"]).'" value="'.$row["codigo"].str_replace("/", "", $row["Fecha"]).'" '.$c1.' id="'.$row["codigo"].str_replace("/", "", $row["Fecha"]).'"><label for="'.$row["codigo"].str_replace("/", "", $row["Fecha"]).'"></label></p></td>
			</tr>
		';

	}


	echo '

				</tbody>
			</table>
		</form>
		<button class="btn" style="margin: 20px;" onclick="GDLaborados()">GENERAR</button>
	';



}else {
	//echo "No Se Encontraron Resultados";
	echo '<div style="width: 100%" class="deep-orange accent-4"><h6 class="center-align" style="padding-top: 5px; padding-bottom: 5px; color: white;">No se encotro resultado !</h6></div>';
}
$objBDSQL->cerrarBD();
$objBDSQL2->cerrarBD();

?>
