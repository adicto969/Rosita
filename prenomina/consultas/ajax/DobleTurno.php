<?php
$codigo = $_POST['codigo'];
$fecha = $_POST['fecha'];
$CenT = $_POST['centro'];
$Peri = $_POST['periodo'];
$TiN = $_POST['TN'];
$IDEmp = $_POST['IDEmp'];
$centroE = $CenT;

$resultV = array();
$resultV['error'] = 0;

$objBDSQL = new ConexionSRV();
$objBDSQL->conectarBD();

if($DepOsub == 1){
  $ComSql = "LEFT (Centro, ".$MascaraEm.") IN (SELECT DISTINCT LEFT (centro, ".$MascaraEm.")  FROM Llaves WHERE supervisor = ".$supervisor." )";
}else {
  $ComSql = "Centro IN (".$_SESSION['centros'].")";
}

$DobTurno = "SELECT TOP 1 valor
                FROM dobTurno
                WHERE codigo = ".$codigo."
                AND fecha = '".$fecha."'
                AND periodo = ".$Peri."
                AND tipoN = ".$TiN."
                AND IDEmpresa = ".$IDEmpresa;

$resultado = $objBDSQL->consultaBD($DobTurno);
if($resultado['error'] == 1)
{
  $file = fopen("log/log".date("d-m-Y").".txt", "a");
  fwrite($file, ":::::::::::::::::::::::ERROR SQL:::::::::::::::::::::::".PHP_EOL);
  fwrite($file, '['.date('d/m/Y h:i:s A').']'.' - '.$resultado['SQLSTATE'].PHP_EOL);
  fwrite($file, '['.date('d/m/Y h:i:s A').']'.' - '.$resultado['CODIGO'].PHP_EOL);
  fwrite($file, '['.date('d/m/Y h:i:s A').']'.' - '.$resultado['MENSAJE'].PHP_EOL);
  fwrite($file, '['.date('d/m/Y h:i:s A').']'.' - CONSULTA: '.$DobTurno.PHP_EOL);
  fclose($file);
  $resultV['error'] = 1;
  echo json_encode($resultV);
  /////////////////////////////
  $objBDSQL->cerrarBD();
  exit();
}

$datos = $objBDSQL->obtenResult();
$objBDSQL->liberarC();


/////////////////////////////////////////////////
##############Consultar centro del empleado
$consultaCentro = "SELECT LTRIM(RTRIM(centro)) AS centro
                   FROM Llaves
                   WHERE codigo = '$codigo'
                   AND empresa = '$IDEmpresa'
                   AND tiponom = '$TiN'";

 $consulta = $objBDSQL->consultaBD($consultaCentro);
 if($consulta['error'] == 1){
   $file = fopen("log/log".date("d-m-Y").".txt", "a");
   fwrite($file, ":::::::::::::::::::::::ERROR SQL:::::::::::::::::::::::".PHP_EOL);
   fwrite($file, '['.date('d/m/Y h:i:s A').']'.' - '.$consulta['SQLSTATE'].PHP_EOL);
   fwrite($file, '['.date('d/m/Y h:i:s A').']'.' - '.$consulta['CODIGO'].PHP_EOL);
   fwrite($file, '['.date('d/m/Y h:i:s A').']'.' - '.$consulta['MENSAJE'].PHP_EOL);
   fwrite($file, '['.date('d/m/Y h:i:s A').']'.' - CONSULTA: '.$consultaCentro);
   fclose($file);
   $resultV['error'] = 1;
   echo json_encode($resultV);
   /////////////////////////////
   $objBDSQL->cerrarBD();
   exit();
 }
 $rowCentro = $objBDSQL->obtenResult();
 if(!empty($rowCentro)){
   $centroE = $rowCentro['centro'];
 }
 $objBDSQL->liberarC();
 ##########################################################


if(!empty($datos)){
  $CsValor = $datos['valor'];

  if($CsValor == 0){
      $Update = "UPDATE dobTurno SET valor = 1 WHERE codigo = $codigo AND fecha = '".$fecha."' AND periodo = $Peri AND tipoN = $TiN AND IDEmpresa = $IDEmp AND Centro = '".$centroE."';";
  }else {
      $Update = "UPDATE dobTurno SET valor = 0 WHERE codigo = $codigo AND fecha = '".$fecha."' AND periodo = $Peri AND tipoN = $TiN AND IDEmpresa = $IDEmp AND Centro = '".$centroE."';";
  }

  $resultado = $objBDSQL->consultaBD($Update);
  if($resultado['error'] == 1)
  {
    $file = fopen("log/log".date("d-m-Y").".txt", "a");
    fwrite($file, ":::::::::::::::::::::::ERROR SQL:::::::::::::::::::::::".PHP_EOL);
    fwrite($file, '['.date('d/m/Y h:i:s A').']'.' - '.$resultado['SQLSTATE'].PHP_EOL);
    fwrite($file, '['.date('d/m/Y h:i:s A').']'.' - '.$resultado['CODIGO'].PHP_EOL);
    fwrite($file, '['.date('d/m/Y h:i:s A').']'.' - '.$resultado['MENSAJE'].PHP_EOL);
    fwrite($file, '['.date('d/m/Y h:i:s A').']'.' - CONSULTA: '.$Update);
    fclose($file);
    $resultV['error'] = 1;
    echo json_encode($resultV);
    /////////////////////////////
    $objBDSQL->cerrarBD();
    exit();
  }


}else {
  $CSInsert = "INSERT INTO dobTurno VALUES(".$codigo.", '".$fecha."', 1, ".$Peri.", ".$TiN.", ".$IDEmp.", '".$centroE."')";
  $resultado = $objBDSQL->consultaBD($CSInsert);
  if($resultado['error'] == 1)
  {
    $file = fopen("log/log".date("d-m-Y").".txt", "a");
    fwrite($file, ":::::::::::::::::::::::ERROR SQL:::::::::::::::::::::::".PHP_EOL);
    fwrite($file, '['.date('d/m/Y h:i:s A').']'.' - '.$resultado['SQLSTATE'].PHP_EOL);
    fwrite($file, '['.date('d/m/Y h:i:s A').']'.' - '.$resultado['CODIGO'].PHP_EOL);
    fwrite($file, '['.date('d/m/Y h:i:s A').']'.' - '.$resultado['MENSAJE'].PHP_EOL);
    fwrite($file, '['.date('d/m/Y h:i:s A').']'.' - CONSULTA: '.$CSInsert);
    fclose($file);
    $resultV['error'] = 1;
    echo json_encode($resultV);
    /////////////////////////////
    $objBDSQL->cerrarBD();
    exit();
  }

}

echo json_encode($resultV);
$objBDSQL->cerrarBD();

?>
