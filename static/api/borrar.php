<?php
header('Access-Control-Allow-Origin: *');
header("Access-Control-Expose-Headers: Content-Length, X-JSON");
header("Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, Accept, Accept-Language, X-Authorization");
header('Access-Control-Max-Age: 86400');


error_reporting(E_ALL);
if ($_GET['error']) {
  ini_set('display_errors', '1');
} else {
  ini_set('display_errors', '0');
}

$key_encrypt = 'j89+uI9-R';
/*
$db_host = "localhost";
$db_user = "u928799310_web";
$db_pass = "UjZfg-Wqh-895.+";
$db_name = "u928799310_PMS";
*/
//

$db_host = "localhost"; //192.185.131.105
$db_user = "u898899309_user_ca";
$db_pass = "3[cj|VBkrkWS"; //R6!D[d$0RoDf
$db_name = "u898899309_ca";

//
$conn = @mysqli_connect($db_host, $db_user, $db_pass, $db_name);
mysqli_query($conn, "SET CHARACTER SET 'utf8'");

$mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name);
$mysqli->set_charset("utf8");





$folder='ca_proveedores';
$orden='nombre';
$archivo='excelProveedores.xlsx';
$hoy = date('Y-m-d');

include 'PHPExcel.php';
include 'PHPExcel/Writer/Excel2007.php';

$resultado = $mysqli->query("SELECT * FROM $folder ORDER BY $orden");

// Creación del objeto PHPExcel
$objPHPExcel = new PHPExcel();

// Obtención de los nombres de las columnas
$columnas = array();
$campos = mysqli_fetch_fields($resultado);
foreach ($campos as $campo) {
    $columnas[] = $campo->name;
}

// Escritura de los encabezados de columna en la hoja de cálculo
$columna = 'A';
foreach ($columnas as $nombre_columna) {
    $objPHPExcel->getActiveSheet()->setCellValue($columna.'1', $nombre_columna);
    $columna++;
}

// Escritura de los datos en la hoja de cálculo
$fila = 2;
while ($fila_datos = mysqli_fetch_array($resultado)) {
    $columna = 'A';
    foreach ($columnas as $nombre_columna) {
        $objPHPExcel->getActiveSheet()->setCellValue($columna.$fila, $fila_datos[$nombre_columna]);
        $columna++;
    }
    $fila++;
}

$objPHPExcel->getActiveSheet()->setTitle('Reporte '.$hoy);
// Guardado de la hoja de cálculo
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save($archivo);


header("HTTP/1.1 200 OK");
echo '[{"excel":"actualizado '.$archivo.'"}]';
?>