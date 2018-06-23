<?php
/**
 * Registro de usuarios
 */
$objBDSQL = new ConexionSRV();
$objBDSQL->conectarBD();

$bd1 = new ConexionM();
$bd1->__constructM();

$user = $_POST['user'];
$pass = $_POST['pass'];
$Spass = $_POST['Spass'];
$NEmpresa = $_POST['NEmpresa'];
$CDepto = $_POST['CDepto'];
$CSuper = $_POST['CSuper'];
$ADMIN = $_POST['admin'];
$AREA = $_POST['Area'];
$IDUser = "";

$sql1 = $bd1->query("SELECT Permiso FROM usuarios WHERE Pass = '$Spass' and Permiso = 1 LIMIT 1;");
if($bd1->rows($sql1) > 0){
  $sql2 = $bd1->query("SELECT User FROM usuarios WHERE User = '$user' LIMIT 1;");
  if($bd1->rows($sql2) > 0){
    echo '<div style="width: 100%" class="deep-orange accent-4"><h6 class="center-align" style="padding-top: 5px; padding-bottom: 5px; color: white;">El Usurio '.$user.' ya existe !</h6></div>';
  } else {
    $Insertar = "INSERT INTO usuarios VALUES (NULL, '$user', '$pass', '$ADMIN', '$AREA', 0);";
    $querySuperAndDep = "SELECT COUNT(*) AS num FROM Llaves WHERE supervisor = '$CSuper' AND centro = '$CDepto';";

    $resultSuperAndDep = $objBDSQL->consultaBD($querySuperAndDep);
    if($resultSuperAndDep['error'] == 1)
    {
      $file = fopen("log/log".date("d-m-Y").".txt", "a");
      fwrite($file, ":::::::::::::::::::::::ERROR SQL:::::::::::::::::::::::".PHP_EOL);
      fwrite($file, '['.date('d/m/Y h:i:s A').']'.' - '.$resultSuperAndDep['SQLSTATE'].PHP_EOL);
      fwrite($file, '['.date('d/m/Y h:i:s A').']'.' - '.$resultSuperAndDep['CODIGO'].PHP_EOL);
      fwrite($file, '['.date('d/m/Y h:i:s A').']'.' - '.$resultSuperAndDep['MENSAJE'].PHP_EOL);
      fwrite($file, '['.date('d/m/Y h:i:s A').']'.' - CONSULTA: '.$querySuperAndDep.PHP_EOL);
      fclose($file);

      $objBDSQL->cerrarBD();
      echo '<div style="width: 100%" class="deep-orange accent-4"><h6 class="center-align" style="padding-top: 5px; padding-bottom: 5px; color: white;">Error en la confirmacion de Supervisor y Departamento</h6></div>';
      exit();
    }

    $dataSuperAndDep = $objBDSQL->obtenResult();
    if(empty($dataSuperAndDep['num']))
    {
      echo '<div style="width: 100%" class="deep-orange accent-4"><h6 class="center-align" style="padding-top: 5px; padding-bottom: 5px; color: white;">No se encuentran departamentos con el supervisor asignado</h6></div>';
      $objBDSQL->cerrarBD();
      exit();
    }else {
      if((int)$dataSuperAndDep['num'] <= 0)
      {
        echo '<div style="width: 100%" class="deep-orange accent-4"><h6 class="center-align" style="padding-top: 5px; padding-bottom: 5px; color: white;">No se encuentran departamentos con el supervisor asignado</h6></div>';
        $objBDSQL->cerrarBD();
        exit();
      }
    }

    $objBDSQL->liberarC();
    $veriEmp = "SELECT COUNT(*) AS num FROM empresas WHERE empresa = '$NEmpresa';";
    $resultEmp = $objBDSQL->consultaBD($veriEmp);
    if($resultEmp['error'] == 1)
    {
      $file = fopen("log/log".date("d-m-Y").".txt", "a");
      fwrite($file, ":::::::::::::::::::::::ERROR SQL:::::::::::::::::::::::".PHP_EOL);
      fwrite($file, '['.date('d/m/Y h:i:s A').']'.' - '.$resultEmp['SQLSTATE'].PHP_EOL);
      fwrite($file, '['.date('d/m/Y h:i:s A').']'.' - '.$resultEmp['CODIGO'].PHP_EOL);
      fwrite($file, '['.date('d/m/Y h:i:s A').']'.' - '.$resultEmp['MENSAJE'].PHP_EOL);
      fwrite($file, '['.date('d/m/Y h:i:s A').']'.' - CONSULTA: '.$resultEmp.PHP_EOL);
      fclose($file);

      $objBDSQL->cerrarBD();
      echo '<div style="width: 100%" class="deep-orange accent-4"><h6 class="center-align" style="padding-top: 5px; padding-bottom: 5px; color: white;">Error al verificar la empresa!</h6></div>';
      exit();
    }
    
    $datosEmp = $objBDSQL->obtenResult();
    if(empty($datosEmp['num'])){
      echo '<div style="width: 100%" class="deep-orange accent-4"><h6 class="center-align" style="padding-top: 5px; padding-bottom: 5px; color: white;">La empresa ingresada no existe!</h6></div>';
      $objBDSQL->cerrarBD();
      exit();
    }else {
      if((int)$datosEmp['num'] <= 0){
        echo '<div style="width: 100%" class="deep-orange accent-4"><h6 class="center-align" style="padding-top: 5px; padding-bottom: 5px; color: white;">La empresa ingresada no existe!</h6></div>';
        $objBDSQL->cerrarBD();
        exit();
      }
    }

    if($bd1->query($Insertar)){

        $selectID = $bd1->query("SELECT ID FROM usuarios WHERE User = '$user' LIMIT 1;");
        $datosID = $bd1->recorrer($selectID);
        $IDUser = $datosID[0];
        $Server = str_replace("\\", "\\\\", S_DB_SERVER);
        $insertarConfig = "INSERT INTO config VALUES (NULL, '$Server', '$NEmpresa', '$CDepto', '".S_DB_NOMBRE."', '".S_DB_USER."', '".S_DB_PASS."', '$IDUser',1, 1, 1, 1, 5, '', 0, 1, '$CSuper');";
        if($bd1->query($insertarConfig)){
          echo "1";
          $bd1->close();
        }else {
            echo '<div style="width: 100%" class="deep-orange accent-4"><h6 class="center-align" style="padding-top: 5px; padding-bottom: 5px; color: white;">Error al agregar la configuracion del usuario '.$user.'. ('.$bd1->error.')</h6></div>';
            $eliminarUser = "DELETE FROM usuarios WHERE ID = $IDUser";
            $bd1->query($eliminarUser);
        }
    }else {
      echo '<div style="width: 100%" class="deep-orange accent-4"><h6 class="center-align" style="padding-top: 5px; padding-bottom: 5px; color: white;">Error al agregar el usuario '.$user.'. ('.$bd1->error.')</h6></div>';
    }
  }
}else {
  echo '<div style="width: 100%" class="deep-orange accent-4"><h6 class="center-align" style="padding-top: 5px; padding-bottom: 5px; color: white;">La Contraseña de Super Usuario no es correcta !</h6></div>';
}
?>
