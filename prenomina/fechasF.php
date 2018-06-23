<?php
$value = $_POST["value"];
$fecha = $_POST["fecha"];
$cantidad = $_POST["cantidad"];
$opcion = $_POST["opcion"];

$file = file_get_contents("fechasFestivas.json");
$json = json_decode($file, true);

switch ($opcion) {
    case 'modificar':
        modificar($value, $fecha, $cantidad, $json);
        break;
    case 'nuevo':
        $value = explode("/", $value)[0].explode("/", $value)[1];
        $value = str_replace("/", "", $value);
        $fecha = explode("/", $fecha)[0]."/".explode("/", $fecha)[1];
        nuevo($value, $fecha, $cantidad, $json);
        break;
    case 'eliminar':
        eliminar($value, $fecha, $cantidad, $json);
        break;
    default:
        # code...
        break;
}

function eliminar($value, $fecha, $cantidad, $json){

    for($j = 0; $j <= $cantidad; $j++){
        if($json["fechas"][$j]['value'] == $value){

            $json["fechas"][$j]["estatus"] = 0;
            $fh = fopen("fechasFestivas.json", 'w') or die("error al abrir el archivo");
            fwrite($fh, json_encode($json, JSON_UNESCAPED_UNICODE));
            fclose($fh);

        }
    }
}

function modificar($value, $fecha, $cantidad, $json){

    for($j = 0; $j <= $cantidad; $j++){
        if($json["fechas"][$j]['value'] == $value){

            $json["fechas"][$j]["fecha"] = $fecha;
            $fh = fopen("fechasFestivas.json", 'w') or die("error al abrir el archivo");
            fwrite($fh, json_encode($json, JSON_UNESCAPED_UNICODE));
            fclose($fh);

        }
    }
}

function nuevo($value, $fecha, $cantidad, $json){

    $json["fechas"][$cantidad]["value"] = $value;
    $fh = fopen("fechasFestivas.json", 'w') or die("error al abrir el archivo");
    fwrite($fh, json_encode($json, JSON_UNESCAPED_UNICODE));

    $json["fechas"][$cantidad]["fecha"] = $fecha;
    $fh = fopen("fechasFestivas.json", 'w') or die("error al abrir el archivo");
    fwrite($fh, json_encode($json, JSON_UNESCAPED_UNICODE));

    $json["fechas"][$cantidad]["estatus"] = 1;
    $fh = fopen("fechasFestivas.json", 'w') or die("error al abrir el archivo");
    fwrite($fh, json_encode($json, JSON_UNESCAPED_UNICODE));

    fclose($fh);
    echo "Bien echo";
}
?>