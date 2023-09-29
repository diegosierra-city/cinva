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
$db_user = "cityciud_usercinva";
$db_pass = "E!sb,n?9o&=O";
$db_name = "cityciud_cinva";

//
$conn = @mysqli_connect($db_host, $db_user, $db_pass, $db_name);
mysqli_query($conn, "SET CHARACTER SET 'utf8'");

$mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name);
$mysqli->set_charset("utf8");





$folder='cinva_proveedores';
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

<div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3">
		{#each listElemento as item, i}
			{#if item[0] != 'id'}
				<!-- content here -->
				
					
					{#if (item[0] !== 'ma' && item[0] !== 'mi' && item[0] !== 'ju' && item[0] !== 'vi' && item[0] !== 'sa' && item[0] !== 'do' && item[0] !== 'cma' && item[0] !== 'cmi' && item[0] !== 'cju' && item[0] !== 'cvi' && item[0] !== 'csa' && item[0] !== 'cdo' && item[0] !== 'imagen')}

<div class="px-2">
					
					
				
					

					{#if item[0] === 'tipo_documento' || item[0] === 'titular_tipo_documento'}
						<select class="inputA" bind:value={item[1]}>
							{#each Documents as documento}
								<!-- content here -->
								<option value={documento.cod}>{documento.doc}</option>
							{/each}
						</select>
					{:else if item[0] === 'pais'}
						<select class="inputA" bind:value={item[1]}>
							{#each Countries as pais}
								<!-- content here -->
								<option value={pais.iata}>{pais.pais}</option>
							{/each}
						</select>
					{:else if item[0] === 'tipo_proveedor'}
						<select class="inputA" bind:value={item[1]}>
							{#each Proveedores as proveedor}
								<!-- content here -->
								<option value={proveedor.cod}>{proveedor.proveedor}</option>
							{/each}
						</select>
						{:else if item[0] === 'tipo_cliente'}
						<select class="inputA" bind:value={item[1]}>
							{#each Clientes as cliente}
								<!-- content here -->
								<option value={cliente.cod}>{cliente.cliente}</option>
							{/each}
						</select>
					{:else if item[0] === 'activo'}
						<select class="inputA" bind:value={item[1]}>
							<option value={true}>Activo</option>
							<option value={false}>Inactivo</option>
						</select>

					{:else if typeof item[1] === 'number'}
						<!-- content here -->
						<input type="number" class="inputA" bind:value={item[1]} />
					{:else}
						<!-- else content here -->
						<input type="text" class="inputA" bind:value={item[1]} />
					{/if}
</div>
						{/if}
				
			

			{/if}
		{:else}
			<!-- empty list -->
		{/each}
	</div>