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

$hoy = date('Y-m-d');


function limpiar_tildes($texto)
{
  $no_permitidas = array("á", "é", "í", "ó", "ú", "Á", "É", "Í", "Ó", "Ú", "ñ", "Ñ", " ");
  $permitidas = array("a", "e", "i", "o", "u", "A", "E", "I", "O", "U", "n", "N", "-");
  $texto = str_replace($no_permitidas, $permitidas, $texto);
  return $texto;
}

function limpiar_numeros($numero)
{
  $no_permitidas = array(".", "-");
  $permitidas = array("", "");
  $numero = str_replace($no_permitidas, $permitidas, $numero);
  return $numero;
}

$data = json_decode(file_get_contents('php://input'), true);


$ref = $_GET['ref'];

if ($_SERVER['REQUEST_METHOD'] == 'GET') {

  if ($ref == 'test') {

    header("HTTP/1.1 200 OK");
    echo '[{"nombre":"Diego2"}]';
    //////++++

  } else if ($ref == 'download') {

    $archivo = $_GET['archivo'];
    if ($archivo == 1) {
      $archivoName = 'excelProveedores.xlsx';
      $folder = 'ca_proveedores';
      $orden = 'nombre';
    } else if ($archivo == 2) {
      $archivoName = 'excelClientes.xlsx';
      $folder = 'ca_clientes';
      $orden = 'nombre,apellido';
    }else if ($archivo == 3) {
      $archivoName = 'excelServicios.xlsx';
      $folder = 'ca_servicios';
      $orden = 'servicio';
    }else if ($archivo == 4) {
      $archivoName = 'excelHoteles.xlsx';
      $folder = 'ca_hoteles';
      $orden = 'hotel';
    }

    if (!$archivoName) {
      header("HTTP/1.1 404 ERROR");
      echo '[{"error":"El archivo no existe"}]';
    } else {

      //header("Content-Type: application/csv");
      header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
      header("Content-Disposition: attachment; filename=" . $hoy . $archivoName);
      readfile($archivoName);
      exit;
    }


    //////++++

  } else
    ///CA
    if ($ref == 'load') {

      $user_id = $_GET['user_id'];
      $time = $_GET['time_life'];
      $token = $_GET['token'];

      $tokenBase = md5($user_id . $time . $key_encrypt);

      if ($tokenBase != $token) {
        header("HTTP/1.1 202 ERROR");
        echo 'Error in token';
      } else {
        $id = $_GET['id'];
        $folder = $_GET['folder'];
        /// 
        $response = array();
        //echo "SELECT * FROM $folder WHERE id='$id' LIMIT 1 <br>";
        $result = $mysqli->query("SELECT * FROM $folder WHERE id='$id' LIMIT 1");
        $row = $result->fetch_array(MYSQLI_ASSOC);

        if ($row['activo'] == 1) {
          $row['activo'] = true;
        } else if ($row['activo'] == 0) {
          $row['activo'] = false;
        }
        unset($row['clave']);

        $response = $row;
        header("HTTP/1.1 200 OK");
        echo json_encode($response);
      }
    } else if ($ref == 'load-list') {

      $user_id = $_GET['user_id'];
      $time = $_GET['time_life'];
      $token = $_GET['token'];

      $tokenBase = md5($user_id . $time . $key_encrypt);

      if ($tokenBase != $token) {
        header("HTTP/1.1 202 ERROR");
        echo '{"error":"Error in token:' . $tokenBase . '-' . $token . '"}';
      } else {

        $folder = $_GET['folder'];
        $campo = $_GET['campo'];
        $campoV = $_GET['campoV'];
        $campo2 = $_GET['campo2'];
        $campoV2 = $_GET['campoV2'];
        $orden = $_GET['orden'];

        if (!$_GET['orden']) {
          $orden = 'id';
        }
        /// load categories
        $response = array();

        if ($campo2 != '') {
          $result = $mysqli->query("SELECT * FROM $folder WHERE $campo='$campoV' AND $campo2='$campoV2' ORDER BY $orden") or die($mysqli->error);
        } else if ($campo != '' && $campoV!=-1) {
          $result = $mysqli->query("SELECT * FROM $folder WHERE $campo='$campoV' ORDER BY $orden") or die($mysqli->error);
        }else if ($campo != '' && $campoV==-1) {
          //echo "SELECT * FROM $folder WHERE $campo!='' ORDER BY $orden";
          $result = $mysqli->query("SELECT * FROM $folder WHERE $campo!='' ORDER BY $orden") or die($mysqli->error);
        } else {
          $result = $mysqli->query("SELECT * FROM $folder ORDER BY $orden") or die($mysqli->error);
        }


        while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
          if ($row['activo']) {
            if ($row['activo'] == 1) {
              $row['activo'] = true;
            } else if ($row['activo'] == 0) {
              $row['activo'] = false;
            }
          }

          unset($row['clave']); ///quitamos este campo del array

          $response[] = $row;
        }


        header("HTTP/1.1 200 OK");
        //echo $fecha.'*'.$empresa_id;
        echo json_encode($response);
      }
    }else if ($ref == 'load-list-tarifas') {

      $user_id = $_GET['user_id'];
      $time = $_GET['time_life'];
      $token = $_GET['token'];

      $tokenBase = md5($user_id . $time . $key_encrypt);

      if ($tokenBase != $token) {
        header("HTTP/1.1 202 ERROR");
        echo '{"error":"Error in token:' . $tokenBase . '-' . $token . '"}';
      } else {
//echo '*****';
        $city = $_GET['city'];
        
        /// load categories
        $response = array();
        $listHoteles= array();
        $listTemporadas= array();
        //
        $h=0;
        $resultC = $mysqli->query("SELECT id, hotel FROM ca_hoteles WHERE ciudad_cod='$city' ORDER BY hotel") or die($mysqli->error);

        while ($rowC = $resultC->fetch_array(MYSQLI_ASSOC)) {
          $h++;
       $hotel_id=$rowC['id'];
       $listHoteles[$hotel_id]=$rowC['hotel'];
       //echo   $rowC['hotel'].' - '.$hotel_id.'<br>';
        //1 listado de temporadas
        $result = $mysqli->query("SELECT * FROM ca_temporadas WHERE ciudad_cod='0' GROUP BY temporada ORDER BY temporada") or die($mysqli->error);

        while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
$temporada_id=$row['id']; 
if($h==1) $listTemporadas[$temporada_id]=$row['temporada'];
$rT = $mysqli->query("SELECT * FROM ca_hotel_tarifas WHERE hotel_id='$hotel_id' AND temporada_id='$temporada_id' LIMIT 1") or die($mysqli->error);
$rowT = $rT->fetch_array();

if(mysqli_num_rows($rT)===0){
 $rowT['id']=0;
  $rowT['hotel_id']=$hotel_id;
  $rowT['temporada_id']=$temporada_id;
  $rowT['pax']=0;
$rowT['pax1']=0;
$rowT['pax2']=0;
$rowT['pax3']=0;
$rowT['pax4']=0;
$rowT['nino']=0;
$rowT['bebe']=0;
}

$response[] = $rowT;
        }

        ///
        $result2 = $mysqli->query("SELECT * FROM ca_temporadas WHERE ciudad_cod='$city' ORDER BY temporada") or die($mysqli->error);

        while ($row2 = $result2->fetch_array(MYSQLI_ASSOC)) {
  $temporada_id=$row2['id'];
  if($h==1) $listTemporadas[$temporada_id]=$row2['temporada'];

          $rT = $mysqli->query("SELECT * FROM ca_hotel_tarifas WHERE hotel_id='$hotel_id' AND temporada_id='$temporada_id' LIMIT 1") or die($mysqli->error);
$rowT = $rT->fetch_array();

if(mysqli_num_rows($rT)===0){
 $rowT['id']=0;
  $rowT['hotel_id']=$hotel_id;
  $rowT['temporada_id']=$temporada_id;
  $rowT['pax']=0;
$rowT['pax1']=0;
$rowT['pax2']=0;
$rowT['pax3']=0;
$rowT['pax4']=0;
$rowT['nino']=0;
$rowT['bebe']=0;
}
        $response[] = $rowT;
        }

      }
$res=array();
$res[]=$response;
$res[]=$listHoteles;
$res[]=$listTemporadas;

        header("HTTP/1.1 200 OK");
        //echo $fecha.'*'.$empresa_id;
        echo json_encode($res);
      }
    }else if ($ref == 'load-list-tarifas-cities') {

      $user_id = $_GET['user_id'];
      $time = $_GET['time_life'];
      $token = $_GET['token'];

      $tokenBase = md5($user_id . $time . $key_encrypt);

      if ($tokenBase != $token) {
        header("HTTP/1.1 202 ERROR");
        echo '{"error":"Error in token:' . $tokenBase . '-' . $token . '"}';
      } else {

               
        /// load categories
        $response = array();
        //listado ciudades
        $r = $mysqli->query("SELECT ciudad_cod,ciudad FROM ca_hoteles GROUP BY ciudad_cod ORDER BY ciudad") or die($mysqli->error);

        while ($row = $r->fetch_array(MYSQLI_ASSOC)) {
        $response[] = $row;
       

      }


        header("HTTP/1.1 200 OK");
        //echo $fecha.'*'.$empresa_id;
        echo json_encode($response);
      }
    }else if ($ref == 'load-list-filter-ciudad') {

      $user_id = $_GET['user_id'];
      $time = $_GET['time_life'];
      $token = $_GET['token'];

      $tokenBase = md5($user_id . $time . $key_encrypt);

      if ($tokenBase != $token) {
        header("HTTP/1.1 202 ERROR");
        echo '{"error":"Error in token:' . $tokenBase . '-' . $token . '"}';
      } else {

        $folder = $_GET['folder'];
        $campo = $_GET['campo'];

        /// load categories
        $response = array();

        $result = $mysqli->query("SELECT id, $campo AS `name`,ciudad_cod FROM $folder WHERE activo='1' ORDER BY `name`");
$err= $mysqli->error;

        while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
       $response[] = $row;
        }

        header("HTTP/1.1 200 OK");
        //echo '[{"error-'.$folder.'":"'.$err.'"}]';
        echo json_encode($response);
      }
    }
  /// 
}
/// FIN metodo GET  ******************




else if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $ref = $_GET['ref'];
  //echo '--'.$_REQUEST['params'];
  //
  if ($ref == 'test') {

    header("HTTP/1.1 200 OK");
    echo '[{"id":"' . $data['email'] . '","documento":"' . $data['password'] . '-' . $_SERVER['REQUEST_METHOD'] . '"}]';
  } else
    if ($ref == 'login') {
    $email = $data['email'];
    $password = $data['password'];
    $password2 = md5($password . $key_encrypt);
    $time = time() + (4 * 60 * 60); //4H 

    $myArray = array();
    $result = $mysqli->query("SELECT * FROM ca_users WHERE email='$email' AND clave='$password2' AND activo='1' LIMIT 1") or die($mysqli->error);

    $token = '';
    $rowR = [];
    $us = 0;
    while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
      $us++;
      $token = md5($row['id'] . $time . $key_encrypt);
      $rowR['id'] = $row['id'];
      $rowR['nombre'] = $row['nombre'];
      $rowR['email'] = $row['email'];
      $rowR['tipo'] = $row['tipo'];
      $rowR['permisos'] = $row['permisos'];
      $rowR['user_time_life'] = $time;
      $rowR['token'] = $token;
      $rowR['activo'] = $row['activo'];

      $myArray[] = $rowR;
    }

    $response = array();
    $response[] = $myArray;

    if ($us == 0) {
      header("HTTP/1.1 403 Error");
      echo '[{"error":"Error en el email o la clave"}]';
      // echo '[{"error":"'." email='$email' AND clave='$password2' AND  '$password' ".'"}]';
    } else {
      header("HTTP/1.1 200 OK");
      $result = json_encode($response);

      if ($result != '[]') {
        echo $result;
      } else {
        //echo $result; 
        echo '[{"error":"Error en el email o la clave..."}]';
      }
    }
  } else
    if ($ref == 'updatePassword') {
    $user_id = $data['user_id'];
    $time_life = $data['time_life'];
    $token = $data['token'];

    $tokenBase = md5($user_id . $time_life . $key_encrypt);

    if ($tokenBase != $token) {
      header("HTTP/1.1 202 ERROR");
      //echo '[{"error":"yess-'.$user_id.'+'.$time_life.'"}]'; 
      echo '[{"error":"error in token"}]';
    } else {
      $folder = $data['folder'];
      $id = $data['id'];
      $password = $data['pass'];
      $password2 = md5($password . 'X-d%gf34');

      $action = "UPDATE $folder SET clave='$password2' WHERE id='$id'";
      $mysqli->query($action) or die($mysqli->error);

      header("HTTP/1.1 200 OK");
      echo '[{"ok":"Se cambio la clave"}]';
    }
  }
  ///Fin Ingreso
  ///PMS
  else if ($ref == 'save') {

    $user_id = $data['user_id'];
    $time_life = $data['time_life'];
    $token = $data['token'];

    $tokenBase = md5($user_id . $time_life . $key_encrypt);

    if ($tokenBase != $token) {
      header("HTTP/1.1 202 ERROR");
      //echo '[{"error":"yess-'.$user_id.'+'.$time_life.'"}]'; 
      echo '[{"error":"error in token"}]';
    } else {
      /// se procesa
      $_GET['folder'] ? $folder = $_GET['folder'] : $folder = $data['folder'];

      $request = $data['request'];

      $insertA = "";
      $insertB = "";
      $update = "";
      $id = 0;
      foreach ($request as $campo => $valor) {
        // $update="$campo='$valor',";
        if ($campo != 'id') {
          $insertA .= $campo . ',';
          $insertB .= "'$valor',";
          $update .= "$campo='$valor',";
        } else {
          $id = $valor;
        }
      }


      $now = time();

      if ($id == 0 or $id > 1000000) {
        ///Nuevo Registro
        $rA = trim($insertA, ',');
        $rB = trim($insertB, ',');

        $action = "INSERT INTO $folder ($rA) VALUES ($rB)";
        $mysqli->query($action);
        $err = $mysqli->error;
        ///
        $resultID = $mysqli->query("SELECT id FROM $folder ORDER BY id DESC LIMIT 1") or die($mysqli->error);
        $rowID = $resultID->fetch_array();
        $id = $rowID['id'];
      } else {
        ///actualizar
        $u = trim($update, ',');
        $action = "UPDATE $folder SET $u WHERE id='$id'";
        $mysqli->query($action);
        $err = $mysqli->error;
      }

      /* header("HTTP/1.1 200 OK");
      //echo '+'.$action.'*';
      //echo '[{"error":"' . $err . '"}]';
      echo '[{"save":"ok:' . $action . '"}]';
return */


      $result = $mysqli->query("SELECT * FROM $folder WHERE id='$id' LIMIT 1");
      $response = array();
      while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
        $response[] = $row;
      }
      /* */
      ///
      header("HTTP/1.1 200 OK");
      //echo $fecha.'*'.$empresa_id;
      echo json_encode($response);
      //echo trim($update,',');
      //echo '[{"ok":"yess"}]';
    }


    //$result->close();
    //$mysqli->close();

  } else if ($ref == 'save-ficha') {

    $user_id = $data['user_id'];
    $time_life = $data['time_life'];
    $token = $data['token'];

    $tokenBase = md5($user_id . $time_life . $key_encrypt);

    if ($tokenBase != $token) {
      header("HTTP/1.1 202 ERROR");
      //echo '[{"error":"yess-'.$user_id.'+'.$time_life.'"}]'; 
      echo '[{"error":"error in token"}]';
    } else {
      /// se procesa
      $folder = $_GET['folder'];
      $request = $data['request'];

      $insertA = "";
      $insertB = "";
      $update = "";
      $id = 0;
      foreach ($request as $campo => $valor) {
        // $update="$campo='$valor',";
        if ($campo != 'id') {
          $insertA .= $campo . ',';
          $insertB .= "'$valor',";
          $update .= "$campo='$valor',";
        } else {
          $id = $valor;
        }
      }


      $now = time();

      if ($id == 0 or $id > 1000000) {
        ///Nuevo Registro
        $rA = trim($insertA, ',');
        $rB = trim($insertB, ',');

        $action = "INSERT INTO $folder ($rA) VALUES ($rB)";
        $mysqli->query($action) or die($mysqli->error);
        ///
        $resultID = $mysqli->query("SELECT id FROM $folder ORDER BY id DESC LIMIT 1") or die($mysqli->error);
        $rowID = $resultID->fetch_array();
        $id = $rowID['id'];
      } else {
        ///actualizar
        $u = trim($update, ',');
        $action = "UPDATE $folder SET $u WHERE id='$id'";
        $mysqli->query($action) or die($mysqli->error);
      }
      //header("HTTP/1.1 200 OK");
      //echo '+'.$action.'*';

      /// borramos los que no están
      //$mysqli->query("DELETE FROM menu WHERE cod!='$cod'") or die($mysqli->error); 
      //

      $result = $mysqli->query("SELECT * FROM $folder WHERE id='$id' LIMIT 1");
      $response = array();
      while ($row = $result->fetch_array(MYSQLI_ASSOC)) {

        $response[] = $row;
      }
      /* */
      ///
      header("HTTP/1.1 200 OK");
      //echo $fecha.'*'.$empresa_id;
      echo json_encode($response);
      //echo trim($update,',');
      //echo '[{"ok":"yess"}]';
    }


    //$result->close();
    //$mysqli->close();

  } else if ($ref == 'save-list') {

    $user_id = $data['user_id'];
    $time_life = $data['time_life'];
    $token = $data['token'];

    $tokenBase = md5($user_id . $time_life . $key_encrypt);

    if ($tokenBase != $token) {
      header("HTTP/1.1 202 ERROR");
      //echo '[{"error":"yess-'.$user_id.'+'.$time_life.'"}]'; 
      echo '[{"error":"error in token"}]';
    } else {
      /// se procesa
      $folder = $data['folder'];
      $list = $data['list'];
      $cod = time();
      //
      $columna = $data['campo'];
      $columna_id = $data['campoV'];
      $orden = $data['orden'];

      //
      $condicion = "";
      if ($data['campo']) {
        $condicion = "$columna='$columna_id'";
      }

      $utilizar_cod = 'no';
      if($folder=='ca_temporadas') $utilizar_cod = 'si';

      foreach ($list as $request) {

        $insertA = "";
        $insertB = "";
        $update = "";
        $id = 0;
        $ejecutar = 'si';
        foreach ($request as $campo => $valor) {

          if ($campo == 'cod') {
            $utilizar_cod = 'si';
          }
          // $update="$campo='$valor',";
          if ($campo != 'id' && $campo != 'cod') {

            $insertA .= $campo . ',';
            $insertB .= "'$valor',";

            if (($campo == $orden && $valor == '')) {
              $ejecutar = 'no';
            }

            $update .= "$campo='$valor',";
          } else if ($campo == 'id') {
            $id = $valor;
          }
        }


        $now = time();

        $act = '';

        if($orden!='' && $orden!= 'id' && $request[$orden] == '') $jecutar='no';

        if (($id == 0 or $id > 1000000) && $ejecutar == 'si') {
          ///Nuevo Registro
          if ($utilizar_cod == 'si') {
            $rA = $insertA . 'cod';
            $rB = $insertB . "'$cod'";
          } else {
            $rA = trim($insertA, ',');
            $rB = trim($insertB, ',');
          }

          $action = "INSERT INTO $folder ($rA) VALUES ($rB)";
          $mysqli->query($action);
          $err = $mysqli->error;

          ///
          if ($utilizar_cod == 'si') {
            $resultID = $mysqli->query("SELECT id FROM $folder WHERE cod='$cod' ORDER BY id DESC LIMIT 1");
          } else {
            $resultID = $mysqli->query("SELECT id FROM $folder ORDER BY id DESC LIMIT 1");
          }

          $rowID = $resultID->fetch_array();
          $id = $rowID['id'];
        } else if ($id > 0) { //$ejecutar == 'si'
          ///actualizar
          if ($utilizar_cod == 'si') {
            $u = $update . "cod='$cod'";
          } else {
            $u = trim($update, ',');
          }
          $action = "UPDATE $folder SET $u WHERE id='$id'";
          $mysqli->query($action);
          $err = $mysqli->error;
        }
        $act .= $action;
      }

      /* header("HTTP/1.1 200 OK");
      //echo '[{"save":"ok:' . $action . '"}]';
      echo '[{"save":"ok:' . $err . '"}]';
      return; */

      //borramos los que no están

      if ($utilizar_cod == 'si') {
        if ($condicion != '') {
          $condicion .= "AND";
        }

        if($folder=='ca_temporadas'){
$mysqli->query("DELETE FROM $folder WHERE $condicion cod!='$cod' and temporada!='Baja'") or die($mysqli->error);
        }else{
         $mysqli->query("DELETE FROM $folder WHERE $condicion cod!='$cod'") or die($mysqli->error); 
        }
        
      }


      //
      if ($data['respuesta'] == 'basica') {
        header("HTTP/1.1 200 OK");
        echo '[{"save":"ok"}]';
      } else {
        if ($utilizar_cod == 'si') {
          if ($condicion != '') {
            $condicion .= "AND cod='$cod'";
          } else {
            $condicion .= "cod='$cod'";
          }
        }

        if ($condicion != '') {
          $result = $mysqli->query("SELECT * FROM $folder WHERE $condicion ORDER BY $orden");
        } else {
          $result = $mysqli->query("SELECT * FROM $folder ORDER BY $orden");
        }

        $response = array();
        $pt = 0;
        while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
          $pt++;

          if ($row['activo'] == 1) {
            $row['activo'] = true;
          } else if ($row['activo'] == 0) {
            $row['activo'] = false;
          }
          unset($row['clave']); ///quitamos este campo del array

          $response[] = $row;
        }
        ///
        header("HTTP/1.1 200 OK");
        echo json_encode($response);
        //echo '[{"save":"ok--  ' . $action. '"}]';
        //
      }
    }
  } else if ($ref == 'save-list-list') {

    $user_id = $data['user_id'];
    $time_life = $data['time_life'];
    $token = $data['token'];

    $tokenBase = md5($user_id . $time_life . $key_encrypt);

    if ($tokenBase != $token) {
      header("HTTP/1.1 202 ERROR");
      //echo '[{"error":"yess-'.$user_id.'+'.$time_life.'"}]'; 
      echo '[{"error":"error in token"}]';
    } else {
      /// se procesa
      $folder = $_GET['folder'];
      $hotel_id = $data['hotel_id'];
      $list = $data['list'];
      $cod = time();
      foreach ($list as $requestB) {

        foreach ($requestB as $request) {
          $insertA = "";
          $insertB = "";
          $update = "";
          $id = 0;
          foreach ($request as $campo => $valor) {
            // $update="$campo='$valor',";
            if ($campo != 'id') {
              $insertA .= $campo . ',';
              $insertB .= "'$valor',";
              $update .= "$campo='$valor',";
            } else {
              $id = $valor;
            }
          }


          $now = time();

          if ($id == 0 or $id > 1000000) {
            ///Nuevo Registro
            $rA = $insertA . 'cod';
            $rB = $insertB . "'$cod'";

            $action = "INSERT INTO $folder ($rA) VALUES ($rB)";
            $mysqli->query($action) or die($mysqli->error);
            ///
            $resultID = $mysqli->query("SELECT id FROM $folder WHERE cod='$cod' ORDER BY id DESC LIMIT 1") or die($mysqli->error);
            $rowID = $resultID->fetch_array();
            $id = $rowID['id'];
          } else {
            ///actualizar
            $u = $update . "cod='$cod'";
            $action = "UPDATE $folder SET $u WHERE id='$id'";
            $mysqli->query($action) or die($mysqli->error);
          }
        }
      }
      //header("HTTP/1.1 200 OK");
      //echo '+'.$action.'*';

      //borramos los que no están
      $mysqli->query("DELETE FROM $folder WHERE hotel_id='$hotel_id' AND cod!='$cod'") or die($mysqli->error);
      //
      //echo "SELECT * FROM $folder WHERE hote_id='$hotel_id' AND cod='$cod' <br>";
      $response = array();

      if ($folder = 'hotel_cuartos') {
        $result = $mysqli->query("SELECT * FROM hotel_plantas WHERE hotel_id='$hotel_id' ORDER BY position");
        $p = 0;
        $x = 0;
        while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
          $p++;
          $planta_id = $row['id'];
          $habitaciones = $row['habitaciones'];
          $resultC = $mysqli->query("SELECT * FROM hotel_cuartos WHERE hotel_id='$hotel_id' AND planta_id='$planta_id' ORDER BY position");
          $h = 0;
          $subresponse = array();
          while ($rowC = $resultC->fetch_array(MYSQLI_ASSOC) or $habitaciones > $h) {
            $h++;
            $x++;
            if ($rowC['name'] == '') {
              $rowC['name'] = (100 * $p) + $h;
            }
            $rowC['hotel_id'] = $hotel_id;
            $rowC['planta_id'] = $planta_id;
            $rowC['position'] = $x;
            $subresponse[] = $rowC;
          }

          $response[] = $subresponse;
        }
      }

      /* */
      ///
      header("HTTP/1.1 200 OK");
      //echo $fecha.'*'.$empresa_id;
      echo json_encode($response);
      //echo trim($update,',');
      //echo '[{"ok":"yess"}]';
    }


    //$result->close();
    //$mysqli->close();

  } else if ($ref == 'save-tarifas_especiales') {

    $user_id = $data['user_id'];
    $time_life = $data['time_life'];
    $token = $data['token'];

    $tokenBase = md5($user_id . $time_life . $key_encrypt);

    if ($tokenBase != $token) {
      header("HTTP/1.1 202 ERROR");
      //echo '[{"error":"yess-'.$user_id.'+'.$time_life.'"}]'; 
      echo '[{"error":"error in token"}]';
    } else {
      /// se procesa
      $list = $data['list'];
      $cod = time();
      //
      $condicional = $data['condicionTarifa'];


      $x = 0;
      $mx = '';
      foreach ($list as $tt) {
        $x++;
        $fecha = $tt['fecha'];
        $habitacion_id = $tt['habitacion_id'];
        $condicional = $tt['condicional'];
        $pax1 = $tt['pax1'];
        $pax2 = $tt['pax2'];
        $pax3 = $tt['pax3'];
        $pax4 = $tt['pax4'];
        $ninos = $tt['ninos'];
        //
        if (!$data['fechas']) {
          $resultID = $mysqli->query("SELECT id FROM tarifas_especiales WHERE habitacion_id='$habitacion_id' AND fecha='$fecha' AND condicional='$condicional' LIMIT 1") or die($mysqli->error);
          $rowID = $resultID->fetch_array();
          $Tid = $rowID['id'];
          //

          if ($Tid == '') {
            $action = "INSERT INTO tarifas_especiales (habitacion_id, fecha, condicional, pax1, pax2, pax3, pax4, ninos, cod) VALUES ('$habitacion_id', '$fecha', '$condicional','$pax1', '$pax2', '$pax3', '$pax4', '$ninos', '$cod')";
            $mysqli->query($action) or die($mysqli->error);
          } else {
            $action = "UPDATE tarifas_especiales SET pax1='$pax1', pax2='$pax2', pax3='$pax3', pax4='$pax4', ninos='$ninos', cod='$cod' WHERE id='$Tid'";
            $mysqli->query($action) or die($mysqli->error);
          }
        } else if ($data['fechas']) {
          ///Bloque de fechas
          foreach ($data['fechas'] as $fx) {
            $fecha = $fx;

            $resultID = $mysqli->query("SELECT id FROM tarifas_especiales WHERE habitacion_id='$habitacion_id' AND fecha='$fecha' AND condicional='$condicional' LIMIT 1") or die($mysqli->error);
            $rowID = $resultID->fetch_array();
            $Tid = $rowID['id'];
            //

            if ($Tid == '') {
              $action = "INSERT INTO tarifas_especiales (habitacion_id, fecha, condicional, pax1, pax2, pax3, pax4, ninos, cod) VALUES ('$habitacion_id', '$fecha', '$condicional','$pax1', '$pax2', '$pax3', '$pax4', '$ninos', '$cod')";
              $mysqli->query($action) or die($mysqli->error);
            } else {
              $action = "UPDATE tarifas_especiales SET pax1='$pax1', pax2='$pax2', pax3='$pax3', pax4='$pax4', ninos='$ninos', cod='$cod' WHERE id='$Tid'";
              $mysqli->query($action) or die($mysqli->error);
            }
          }
          /// fin Bloque

        }
      }


      ///
      header("HTTP/1.1 200 OK");
      //echo '[{"actualización":"completa::' . $action . '"}]';
      echo '[{"Tarifas Especiales":"actualizadas"}]';
    }
  } else if ($ref == 'save-menu') {

    $user_id = $data['user_id'];
    $time_life = $data['time_life'];
    $token = $data['token'];
    //$documento=limpiar_numeros($data['documento']);
    /// primero validamos el token
    $tokenBase = md5($user_id . $time_life . $key_encrypt);

    if ($tokenBase != $token) {
      header("HTTP/1.1 202 ERROR");
      //echo '[{"error":"yess-'.$user_id.'+'.$time_life.'"}]'; 
      echo '[{"error":"yes"}]';
    } else {
      ///company_id
      $rCi = $mysqli->query("SELECT company_id FROM users WHERE id='$user_id' LIMIT 1 ");
      $rowCi = $rCi->fetch_array(MYSQLI_ASSOC);
      $company_id = $rowCi['company_id'];
      /// run
      $cod = time();
      $a = 0;
      $datos = '';
      foreach ($data['menu'] as $menu) {
        $a++;
        $m_id = $menu['id'];
        $m_menu_id = $menu['menu_id'];
        $m_menu = $menu['menu'];
        $m_type = $menu['type'];
        $m_link = $menu['link'];
        $m_head = $menu['head'];
        $m_foot = $menu['foot'];
        $m_side = $menu['side'];
        $m_position = $menu['position'];
        $m_submenu = $menu['submenu'];
        $m_metadescription = $menu['metadescription'];
        $m_metakeywords = $menu['metakeywords'];
        //
        if ($m_id > 1000000) {
          ///Nuevo Registro
          $mysqli->query("INSERT INTO menu (company_id,menu_id,menu,type,link,head,foot,side,position,submenu,metadescription,metakeywords,cod) VALUES ('$company_id','$m_menu_id','$m_menu','$m_type','$m_link','$m_head','$m_foot','$m_slide','$m_position','$m_submenu','$m_metadescription','$m_metakeywords','$cod')") or die($mysqli->error);
        } else {
          ///actualizar
          $mysqli->query("UPDATE menu SET company_id='$company_id', menu_id='$m_menu_id',menu='$m_menu',type='$m_type',link='$m_link',head='$m_head',foot='$m_foot',side='$m_slide',position='$m_position',submenu='$m_submenu',metadescription='$m_metadescription',metakeywords='$m_metakeywords',cod='$cod' WHERE id='$m_id'") or die($mysqli->error);
        }
      }
      /// borramos los que no están
      $mysqli->query("DELETE FROM menu WHERE company_id='$company_id' AND cod!='$cod'") or die($mysqli->error);
      //
      $menu = array();
      $result = $mysqli->query("SELECT * FROM menu WHERE menu_id='0' AND company_id='$company_id' ORDER BY position ASC ");
      $limit = mysqli_num_rows($result) + 1;
      while ($row = $result->fetch_array(MYSQLI_ASSOC)) {

        if ($row['head'] == 0) {
          $row['head'] = false;
        } else {
          $row['head'] = true;
        }
        if ($row['foot'] == 0) {
          $row['foot'] = false;
        } else {
          $row['foot'] = true;
        }
        if ($row['side'] == 0) {
          $row['side'] = false;
        } else {
          $row['side'] = true;
        }
        if ($row['submenu'] == 0) {
          $row['submenu'] = false;
        } else {
          $row['submenu'] = true;
        }

        /// cargamos el submenu
        $m_id = $row['id'];
        $submenu = array();
        $resultSM = $mysqli->query("SELECT * FROM menu WHERE menu_id='$m_id' ORDER BY position ASC ");
        while ($rowSM = $resultSM->fetch_array(MYSQLI_ASSOC)) {
          if ($rowSM['head'] == 0) {
            $rowSM['head'] = false;
          } else {
            $rowSM['head'] = true;
          }
          if ($rowSM['foot'] == 0) {
            $rowSM['foot'] = false;
          } else {
            $rowSM['foot'] = true;
          }
          if ($rowSM['side'] == 0) {
            $rowSM['side'] = false;
          } else {
            $rowSM['side'] = true;
          }
          if ($rowSM['submenu'] == 0) {
            $rowSM['submenu'] = false;
          } else {
            $rowSM['submenu'] = true;
          }
          $submenu[] = $rowSM;
        }

        $row['submenus'] = $submenu;
        $menu[] = $row;
      }

      ///
      header("HTTP/1.1 200 OK");
      //echo $fecha.'*'.$empresa_id;
      echo json_encode($menu);

      //echo '[{"ok":"yess"}]';
    }


    //$result->close();
    //$mysqli->close();

  } else if ($ref == 'save-form') {

    $user_id = $data['user_id'];
    $time_life = $data['time_life'];
    $token = $data['token'];

    $tokenBase = md5($user_id . $time_life . $key_encrypt);

    if ($tokenBase != $token) {
      header("HTTP/1.1 202 ERROR");
      //echo '[{"error":"yess-'.$user_id.'+'.$time_life.'"}]'; 
      echo '[{"error":"error in token"}]';
    } else {
      ///company_id
      $rCi = $mysqli->query("SELECT company_id FROM users WHERE id='$user_id' LIMIT 1 ");
      $rowCi = $rCi->fetch_array(MYSQLI_ASSOC);
      $company_id = $rowCi['company_id'];
      /// run
      $cod = time();
      $a = 0;
      $datos = '';
      foreach ($data['listForm'] as $form) {
        $a++;
        $m_id = $form['id'];
        $m_menu_id = $form['menu_id'];
        $m_name = $form['name'];
        $m_type = $form['type'];
        $m_required = $form['required'];
        $m_position = $form['position'];

        //
        if ($m_id > 1000000) {
          ///Nuevo Registro
          $mysqli->query("INSERT INTO form (company_id,menu_id,`name`,`type`,`required`,position,cod) VALUES ('$company_id','$m_menu_id','$m_name','$m_type','$m_required','$m_position','$cod')") or die($mysqli->error);
        } else {
          ///actualizar
          $mysqli->query("UPDATE form SET company_id='$company_id', menu_id='$m_menu_id',`name`='$m_name',`type`='$m_type',`required`='$m_required',position='$m_position',cod='$cod' WHERE id='$m_id'") or die($mysqli->error);
        }
      }
      /// borramos los que no están
      $mysqli->query("DELETE FROM form WHERE company_id='$company_id' AND cod!='$cod'") or die($mysqli->error);
      //
      $formL = array();
      $result = $mysqli->query("SELECT * FROM form WHERE menu_id='$m_menu_id' AND company_id='$company_id' ORDER BY position ASC ");

      while ($row = $result->fetch_array(MYSQLI_ASSOC)) {


        if ($row['required'] == 0) {
          $row['required'] = false;
        } else {
          $row['required'] = true;
        }

        $formL[] = $row;
      }

      ///
      header("HTTP/1.1 200 OK");
      //echo $fecha.'*'.$empresa_id;
      echo json_encode($formL);

      //echo '[{"ok":"yess"}]';
    }


    //$result->close();
    //$mysqli->close();

  } else if ($ref == 'save-form-answer') {

    $user_id = $data['user_id'];
    $time_life = $data['time_life'];
    $token = $data['token'];

    $tokenBase = md5($user_id . $time_life . $key_encrypt);

    if ($tokenBase != $token) {
      header("HTTP/1.1 202 ERROR");
      //echo '[{"error":"yess-'.$user_id.'+'.$time_life.'"}]'; 
      echo '[{"error":"error in token"}]';
    } else {

      foreach ($data['listForm'] as $form) {
        $a++;
        $company_id = $form['company_id'];
        $m_id = $form['id'];
        $m_response = $form['response'];
        $m_state = $form['state'];
        $now = date('Y-m-d H:i');
        if ($m_response == '') {
          $now = '';
        }
        //
        ///actualizar
        $mysqli->query("UPDATE forms_received SET response='$m_response',date_response='$now', `state`='$m_state' WHERE id='$m_id'") or die($mysqli->error);
      }
      //
      $date1 = $_GET['date1'];
      $date2 = $_GET['date2'];
      $formL = array();
      $result = $mysqli->query("SELECT * FROM forms_received WHERE company_id='$company_id' AND `date`>='$date1' AND `date`<='$date2' ORDER BY `date` DESC ");

      while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
        $formL[] = $row;
      }

      ///
      header("HTTP/1.1 200 OK");
      //echo $fecha.'*'.$empresa_id;
      echo json_encode($formL);

      //echo '[{"ok":"yess"}]';
    }
  } else if ($ref == 'save-content') {

    $user_id = $data['user_id'];
    $time_life = $data['time_life'];
    $token = $data['token'];
    $content = $data['content'];
    $cont_id = $data['cont_id'];
    $tokenBase = md5($user_id . $time_life . $key_encrypt);

    if ($tokenBase != $token) {
      header("HTTP/1.1 202 ERROR");
      //echo '[{"error":"yess-'.$user_id.'+'.$time_life.'"}]'; 
      echo '[{"error":"error in token"}]';
    } else {
      /// se procesa
      $cod = time();

      $c_id = $content['id'];
      $c_menu_id = $cont_id;
      $c_title = $content['title'];
      $c_subtitle = $content['subtitle'];
      $c_text1 = $content['text1'];
      $c_text2 = $content['text2'];
      $c_text3 = $content['text3'];
      $c_text4 = $content['text4'];
      $c_image1 = $content['image1'];
      $c_image2 = $content['image2'];
      $c_image3 = $content['image3'];
      $c_image4 = $content['image4'];
      $c_video = $content['video'];
      $c_position = $content['position'];
      $c_link = $content['link'];
      //echo $cont_id.'**'.$c_menu_id.'+';
      $nuevo = 'no';
      $resultB = $mysqli->query("SELECT id FROM content_blocks WHERE menu_id='$cont_id' LIMIT 1");
      if (mysqli_num_rows($resultB) == 0) {
        $nuevo = 'si';
      }

      if ($c_id == 0 or $nuevo == 'si') {
        ///Nuevo Registro
        $action = "INSERT INTO content_blocks (menu_id,	title,	subtitle,	text1,	text2,	text3,	text4,	image1,	image2,	image3,	image4,	video,	position,	link) VALUES ('$c_menu_id',	'$c_title',	'$c_subtitle',	'$c_text1',	'$c_text2',	'$c_text3',	'$c_text4',	'$c_image1',	'$c_image2',	'$c_image3',	'$c_image4',	'$c_video',	'$c_position',	'$c_link')";
        $mysqli->query($action) or die($mysqli->error);
        ///

      } else if ($c_id != 1000000) {
        ///actualizar
        $action = "UPDATE content_blocks SET title='$c_title',	subtitle='$c_subtitle',	text1='$c_text1',	text2='$c_text2',	text3='$c_text3',	text4='$c_text4',	image1='$c_image1',	image2='$c_image2',	image3='$c_image3',	image4='$c_image4',	video='$c_video',	position='$c_position',	link='$c_link' WHERE id='$c_id'";
        $mysqli->query($action) or die($mysqli->error);
      }

      //echo '+'.$action.'*';

      /// borramos los que no están
      //$mysqli->query("DELETE FROM menu WHERE cod!='$cod'") or die($mysqli->error); 
      //

      $result = $mysqli->query("SELECT * FROM content_blocks WHERE menu_id='$cont_id' LIMIT 1");
      $new_cont = array();
      while ($row = $result->fetch_array(MYSQLI_ASSOC)) {

        $new_cont[] = $row;
      }

      ///
      header("HTTP/1.1 200 OK");
      //echo $fecha.'*'.$empresa_id;
      echo json_encode($new_cont);

      //echo '[{"ok":"yess"}]';
    }


    //$result->close();
    //$mysqli->close();

  }
  /// 
  else if ($ref == 'save-prod') {

    $user_id = $data['user_id'];
    $time_life = $data['time_life'];
    $token = $data['token'];
    $product = $data['product'];
    $product_id = $product['id'];
    $tokenBase = md5($user_id . $time_life . $key_encrypt);

    if ($tokenBase != $token) {
      header("HTTP/1.1 202 ERROR");
      //echo '[{"error":"yess-'.$user_id.'+'.$time_life.'"}]'; 
      echo '[{"error":"error in token"}]';
    } else {
      /// se procesa
      $cod = time();

      $c_id = $product['id'];
      $c_category_id = $product['category_id'];
      $c_product = $product['product'];
      $c_ref = $product['ref'];
      $c_description = $product['description'];
      $c_description2 = $product['description2'];
      $c_price = $product['price'];
      $c_size = $product['size'];
      $c_color = $product['color'];
      $c_image1 = $product['image1'];
      $c_image2 = $product['image2'];
      $c_image3 = $product['image3'];
      $c_image4 = $product['image4'];
      $c_position = $product['position'];
      $c_active = $product['active'];

      $now = time();

      if ($c_id == 0 or $c_id > 1000000) {
        ///Nuevo Registro
        $action = "INSERT INTO products (category_id,product,ref,description,description2,price,size,color,image1,image2,image3,image4,position,active,cod) VALUES ('$c_category_id','$c_product','$c_ref','$c_description','$c_description2','$c_price','$c_size','$c_color','$c_image1','$c_image2','$c_image3','$c_image4','$c_position','$c_active','$now')";
        $mysqli->query($action) or die($mysqli->error);
        ///

      } else {
        ///actualizar
        $action = "UPDATE products SET category_id='$c_category_id',product='$c_product',ref='$c_ref',description='$c_description',description2='$c_description2',price='$c_price',size='$c_size',color='$c_color',image1='$c_image1',image2='$c_image2',image3='$c_image3',image4='$c_image4',position='$c_position',active='$c_active', cod='$now' WHERE id='$c_id'";
        $mysqli->query($action) or die($mysqli->error);
      }
      //header("HTTP/1.1 200 OK");
      //echo '+'.$action.'*';

      /// borramos los que no están
      //$mysqli->query("DELETE FROM menu WHERE cod!='$cod'") or die($mysqli->error); 
      //

      $result = $mysqli->query("SELECT * FROM products WHERE cod='$now' LIMIT 1");
      $new_prod = array();
      while ($row = $result->fetch_array(MYSQLI_ASSOC)) {

        $new_prod[] = $row;
      }

      ///
      header("HTTP/1.1 200 OK");
      //echo $fecha.'*'.$empresa_id;
      echo json_encode($new_prod);

      //echo '[{"ok":"yess"}]';
    }


    //$result->close();
    //$mysqli->close();

  }
  /// 
  else if ($ref == 'save-category') {

    $user_id = $data['user_id'];
    $time_life = $data['time_life'];
    $token = $data['token'];

    $tokenBase = md5($user_id . $time_life . $key_encrypt);

    if ($tokenBase != $token) {
      header("HTTP/1.1 202 ERROR");
      //echo '[{"error":"yess-'.$user_id.'+'.$time_life.'"}]'; 
      echo '[{"error":"Error in Token"}]';
    } else {
      ///company_id
      $rCi = $mysqli->query("SELECT company_id FROM users WHERE id='$user_id' LIMIT 1 ");
      $rowCi = $rCi->fetch_array(MYSQLI_ASSOC);
      $company_id = $rowCi['company_id'];
      /// se procesa
      $cod = time();
      $a = 0;
      $datos = '';
      foreach ($data['categories'] as $category) {
        $a++;
        $m_id = $category['id'];
        $m_category = $category['category'];
        $m_description = $category['description'];
        $m_position = $category['position'];
        $m_active = $category['active'];
        $m_image = $category['image'];

        if ($m_id > 1000000) {
          ///Nuevo Registro
          $mysqli->query("INSERT INTO categories (company_id,category,`description`,position,`image`,active,cod) VALUES ('$company_id','$m_category','$m_description','$m_position','$m_image','$m_active','$cod')") or die($mysqli->error);
        } else {
          ///actualizar
          $mysqli->query("UPDATE categories SET company_id='$company_id', category='$m_category', `description`='$m_description',position='$m_position',`image`='$m_image',active='$m_active',cod='$cod' WHERE id='$m_id'") or die($mysqli->error);
        }
      }
      /// borramos los que no están
      $mysqli->query("DELETE FROM categories WHERE company_id='$company_id' AND cod!='$cod'") or die($mysqli->error);
      //
      $categories = array();

      $result = $mysqli->query("SELECT * FROM categories WHERE company_id='$company_id' ORDER BY active DESC, position ASC ");
      $c = 0;
      while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
        $c++;
        if ($row['active'] == 0) {
          $row['active'] = false;
        } else {
          $row['active'] = true;
        }


        $categories[] = $row;
      }


      ///
      header("HTTP/1.1 200 OK");
      //echo $fecha.'*'.$empresa_id;
      if ($c > 0) {
        echo json_encode($categories);
      } else {
        echo '[{"error":"Categories empty"}]';
      }

      //echo '[{"ok":"yess"}]';
    }


    //$result->close();
    //$mysqli->close();

  }
  /// FIN Save category

  else if ($ref == 'save-product') {

    $user_id = $data['user_id'];
    $time_life = $data['time_life'];
    $token = $data['token'];

    $tokenBase = md5($user_id . $time_life . $key_encrypt);

    if ($tokenBase != $token) {
      header("HTTP/1.1 202 ERROR");
      //echo '[{"error":"yess-'.$user_id.'+'.$time_life.'"}]'; 
      echo '[{"error":"Error in Token"}]';
    } else {
      /// se procesa
      $cod = time();
      $a = 0;
      $datos = '';
      foreach ($data['products'] as $product) {
        $a++;
        $m_id = $product['id'];
        $m_category_id = $product['category_id'];

        $m_product = $product['product'];
        $m_ref = $product['ref'];
        $m_description = $product['description'];
        $m_description2 = $product['description2'];
        $m_price = $product['price'];
        $m_size = $product['size'];
        $m_color = $product['color'];
        $m_image1 = $product['image1'];
        $m_image2 = $product['image2'];
        $m_image3 = $product['image3'];
        $m_image4 = $product['image4'];
        $m_position = $product['position'];
        $m_active = $product['active'];

        if ($m_id > 1000000) {
          ///Nuevo Registro
          $mysqli->query("INSERT INTO products (category_id,product,ref,`description`,description2,price,size,color,position,active,cod) VALUES ('$m_category_id','$m_product','$m_ref','$m_description','$m_description2','$m_price','$m_size','$m_color','$m_position','$m_active','$cod')") or die($mysqli->error);
        } else {
          ///actualizar
          $mysqli->query("UPDATE products SET category_id='$m_category_id',product='$m_product',ref='$m_ref',`description`='$m_description',description2='$m_description2',price='$m_price',size='$m_size',color='$m_color',position='$m_position',active='$m_active',cod='$cod' WHERE id='$m_id'") or die($mysqli->error);
        }
      }
      /// borramos los que no están
      $mysqli->query("DELETE FROM products WHERE category_id='$m_category_id' and cod!='$cod'") or die($mysqli->error);
      //
      $products = array();

      $result = $mysqli->query("SELECT * FROM products WHERE category_id='$m_category_id' ORDER BY active DESC, position ASC , product ASC");
      $c = 0;
      while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
        $c++;
        if ($row['active'] == 0) {
          $row['active'] = false;
        } else {
          $row['active'] = true;
        }


        $products[] = $row;
      }


      ///
      header("HTTP/1.1 200 OK");
      //echo $fecha.'*'.$empresa_id;
      if ($c > 0) {
        echo json_encode($products);
      } else {
        echo '[{"error":"Products empty"}]';
      }

      //echo '[{"ok":"yess"}]';
    }


    //$result->close();
    //$mysqli->close();

  } else if ($ref == 'downloadCSV') {

    $user_id = $data['user_id'];
    $time_life = $data['time_life'];
    $token = $data['token'];

    $tokenBase = md5($user_id . $time_life . $key_encrypt);

    if ($tokenBase != $token) {
      header("HTTP/1.1 202 ERROR");

      echo '[{"error":"Error in Token:' . $user_id . '+' . $time_life . '+' . $token . '"}]';
      //echo '[{"error":"Error in Token"}]';
    } else {
      header("HTTP/1.1 202 ERROR");

      echo '[{"error":"Error in Token:' . $user_id . '+' . $time_life . '+' . $token . '"}]';
      return

        $table = $data['table'];
      $hotel_id = $data['hotel_id'];
      /// se procesa
      $cod = time();
      $now_time = date('Y-m-d h:i');

      if ($table == 'products') {
        $query = $mysqli->query("SELECT products.id, products.category_id, products.product, products.ref, products.description, products.description2, products.price FROM categories, products WHERE categories.hotel_id='$hotel_id' AND products.category_id=categories.id ORDER BY categories.position, products.position");
      }

      //+++++
      $delimiter = ",";
      $filename = 'ListadoProductos-' . $hotel_id . '_' . $now_time . ".csv";

      //create a file pointer
      $f = fopen('php://memory', 'w');

      //set column headers
      $fields = array('id', 'categoria id', 'producto', 'ref', 'descripcion', 'descripcion full', 'precio');

      fputcsv($f, $fields, $delimiter);

      //output each row of the data, format line as csv and write to file pointer
      while ($row = $query->fetch_assoc()) {
        //$status = ($row['status'] == '1')?'Active':'Inactive';
        //$lineData = array($row['id'], $row['name'], $row['email'], $row['phone'], $row['created'], $status);
        $lineData = array($row['id'], $row['category_id'], $row['product'], $row['ref'], $row['description'], $row['description2'], $row['price']);
        fputcsv($f, $lineData, $delimiter);
      }

      //move back to beginning of file
      fseek($f, 0);

      //set headers to download file rather than displayed
      header('Content-Type: text/csv');
      header('Content-Disposition: attachment; filename="' . $filename . '";');

      //output all remaining data on a file pointer
      fpassthru($f);
      ///++++


      ///
      header("HTTP/1.1 200 OK");
      //echo $fecha.'*'.$empresa_id;
      echo '[{"ok":"yes"}]';
    }


    //$result->close();
    //$mysqli->close();

  } else if ($ref == 'excel-create') {

    $user_id = $data['user_id'];
    $time_life = $data['time_life'];
    $token = $data['token'];
    $tokenBase = md5($user_id . $time_life . $key_encrypt);
    //

    //$_FILE['uploadFile']
    if ($tokenBase != $token) {
      header("HTTP/1.1 202 ERROR");
      //echo '[{"error":"yess-'.$user_id.'+'.$time_life.'"}]'; 
      echo '[{"error":"In token"}]';
    } else {
      $folder = $data['folder'];
      $orden = $data['orden'];
      $archivo = $data['archivo'];

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
        $objPHPExcel->getActiveSheet()->setCellValue($columna . '1', $nombre_columna);
        $columna++;
      }

      // Escritura de los datos en la hoja de cálculo
      $fila = 2;
      while ($fila_datos = mysqli_fetch_array($resultado)) {
        $columna = 'A';
        foreach ($columnas as $nombre_columna) {
          $objPHPExcel->getActiveSheet()->setCellValue($columna . $fila, $fila_datos[$nombre_columna]);
          $columna++;
        }
        $fila++;
      }

      $objPHPExcel->getActiveSheet()->setTitle('Reporte ' . $hoy);
      // Guardado de la hoja de cálculo
      $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
      $objWriter->save($archivo);


      header("HTTP/1.1 200 OK");
      echo '[{"excel":"actualizado ' . $archivo . '"}]';

      header("HTTP/1.1 200 OK");
      echo '[{"excel":"actualizado ' . $archivo . '"}]';
    }
  }
  /// END save product
  else if ($ref == 'upload') { /// for pages

    $user_id = $_POST['user_id'];
    $time_life = $_POST['time_life'];
    $token = $_POST['token'];
    $tokenBase = md5($user_id . $time_life . $key_encrypt);
    //

    //$_FILE['uploadFile']
    if ($tokenBase != $token) {
      header("HTTP/1.1 202 ERROR");
      //echo '[{"error":"yess-'.$user_id.'+'.$time_life.'"}]'; 
      echo '[{"error":"In token"}]';
    } else {
      /// type
      $id = $_POST['id'];
      $position = $_POST['position'];
      $folder = $_POST['folder'];
      $prefix = $_POST['prefix'];
      $w = 800;
      $h = 600;
      if ($folder == 'ca_users') {
        $w = 600;
      }else if($folder == 'ca_servicios') {
        $w = 1200;
      }

      $hM = ($h / 2);
      $wM = ($w / 2);

      /// VErot Upload
      $error = '';
      if ($_FILES['uploadFile']['name'] != '') {
        if ($_FILES['uploadFile']['type'] != "image/pjpeg" and $_FILES['uploadFile']['type'] != "image/jpeg" and $_FILES['uploadFile']['type'] != "image/png") {
          $error = 'Sólo se acepta JPG o PNG';
        } else {
          //
          $path = $_FILES['uploadFile']['name'];
          $ext = pathinfo($path, PATHINFO_EXTENSION);
          $ext = strtolower($ext);
          //
          if ($folder == 'ca_users' || $folder == 'ca_servicios') {
            $path_preview = $id . '.' . $ext . '?t=' . time();
            $pathBase=$id;
          }else {
            $path_preview = $id . '_' . $position . '.' . $ext . '?t=' . time();
            $pathBase=$id . '_' . $position;
          } 
          //
          include('verot-upload/src/class.upload.php');
          ini_set("max_execution_time", 0);
          $handle = new \Verot\Upload\Upload($_FILES['uploadFile']);
          if ($handle->uploaded) {
            ///PC version

            $handle->file_new_name_body = $pathBase;
            $handle->file_overwrite = true;

            $handle->image_resize          = true;
            $handle->image_x               = $w;
            //$handle->image_ratio_y         = true;

            $handle->image_y               = $h;
            $handle->image_ratio_crop       = true;

            if ($ext == 'png') {
              $handle->png_compression = 9;
            } else {
              $handle->jpeg_quality = 85;
            }

            $handle->process('../maker-files/imagenes/' . $folder . '/');
            /// Movil Version
            $handle->file_new_name_body = 'M' . $pathBase;
            $handle->image_resize          = true;
            $handle->file_overwrite = true;
            $handle->image_x               = $wM;
            //$handle->image_ratio_y         = true;

            $handle->image_y               = $hM;
            $handle->image_ratio_crop       = true;

            if ($ext == 'png') {
              $handle->png_compression = 9;
            } else {
              $handle->jpeg_quality = 85;
            }

            $handle->process('../maker-files/imagenes/' . $folder . '/');
          } else {
            ///error
            $error = 'si';
          }
        }
      }
      /// The End Verot;


      if ($error == '') {
        //
        $row_name = 'imagen' . $position;
        if ($folder == 'ca_users') {
          $row_name = 'imagen';
        }

        $action = "UPDATE $folder SET `$row_name`='$path_preview' WHERE id='$id'";
        $mysqli->query($action);
        $err= $mysqli->error;
        //
        $result = $mysqli->query("SELECT * FROM $folder WHERE id='$id' LIMIT 1");
        $new_cont = array();
        while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
          if ($row['activo'] == 1) {
            $row['activo'] = true;
          } else if ($row['activo'] == 0) {
            $row['activo'] = false;
          }
          $new_cont[] = $row;
        }
        //  
        header("HTTP/1.1 200 OK");
        //echo '[{"' . $row_name . '":"' . $path_preview . '"}]';
        //echo '[{"error":"' . $err . '"}]';
        echo json_encode($new_cont);
      } else {
        header("HTTP/1.1 400 ERROR");
        echo '[{"error":"' . $error . '"}]';
      }
    }
  } else if ($ref == 'upload-excel') { /// for pages

    $user_id = $_POST['user_id'];
    $time_life = $_POST['time_life'];
    $token = $_POST['token'];
    $tokenBase = md5($user_id . $time_life . $key_encrypt);
    //

    //$_FILE['uploadFile']
    if ($tokenBase != $token) {
      header("HTTP/1.1 202 ERROR");
      //echo '[{"error":"yess-'.$user_id.'+'.$time_life.'"}]'; 
      echo '[{"error":"In token"}]';
    } else {
      ///
      $folder = $_POST['folder'];
      $file = $_POST['file'];
      //"application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"
      // header("HTTP/1.1 200 OK");
      // echo '[{"upload":"' . $_FILES['uploadFile']['type'] . '"}]';
      if ($_FILES['uploadFile']['type'] != "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet") {
        header("HTTP/1.1 400 ERROR");
        echo '[{"error":"El tipo de archivo no es aceptado"}]';
      } else {

        $target_dir = "./"; // directorio donde se guardará el archivo subido
        //$target_file = $target_dir . basename($_FILES["uploadFile"]["name"]);
        $target_file = $target_dir . $file;
        if (move_uploaded_file($_FILES["uploadFile"]["tmp_name"], $target_file)) {
          /// despues de subirlo se lee
          include_once 'PHPExcel.php';
          include 'PHPExcel/IOFactory.php';

          // Leer el archivo de Excel
          $objReader = PHPExcel_IOFactory::createReader('Excel2007');
          $objPHPExcel = $objReader->load($target_file);

          // Obtener la hoja activa y leer la primera fila para obtener los nombres de los campos
          $worksheet = $objPHPExcel->getActiveSheet();
          $highestColumn = $worksheet->getHighestColumn();
          $highestRow = $worksheet->getHighestRow();
          $fields = array();
          for ($col = 'A'; $col <= $highestColumn; $col++) {
            $value = $worksheet->getCell($col . 1)->getValue();
            $fields[] = $value;
          }

          // Leer las filas restantes y procesarlas
          for ($row = 2; $row <= $highestRow; $row++) {
            $values = array();
            for ($col = 'A'; $col <= $highestColumn; $col++) {
              $value = $worksheet->getCell($col . $row)->getValue();
              $values[] = $value;
            }
            $row_data = array_combine($fields, $values);
            $id = $row_data['id'];
            $nombre = $row_data['nombre'];
            $email = $row_data['email'];

            // Verificar si el registro ya existe
            $query = "SELECT * FROM $folder WHERE id = $id";
            $result = $mysqli->query($query);

            if ($result->num_rows > 0) {
              // Actualizar el registro existente
              $set_values = array();
              foreach ($fields as $field_name) {
                if ($field_name != 'id') {
                  $set_values[] = "$field_name = '" . $row_data[$field_name] . "'";
                }
              }
              $set_values_str = implode(', ', $set_values);
              $query = "UPDATE $folder SET $set_values_str WHERE id = $id";
              $mysqli->query($query);
            } else {
              // Insertar un nuevo registro
              $field_names_str = implode(', ', $fields);
              $values_str = implode("', '", $values);
              $query = "INSERT INTO $folder ($field_names_str) VALUES ('$values_str')";
              $mysqli->query($query);
            }
          }
          header("HTTP/1.1 200 OK");
          echo '[{"upload":"Se a subido el archivo ' . $file . '"}]';
        } else {
          header("HTTP/1.1 400 ERROR");
          echo '[{"error":"Ha ocurrido un error al subir el archivo"}]';
        }
      }
    }
  } else if ($ref == 'upload-archivo') { /// for pages

    $user_id = $_POST['user_id'];
    $time_life = $_POST['time_life'];
    $token = $_POST['token'];
    $tokenBase = md5($user_id . $time_life . $key_encrypt);
    //
    if ($tokenBase != $token) {
      header("HTTP/1.1 202 ERROR");
      //echo '[{"error":"yess-'.$user_id.'+'.$time_life.'"}]'; 
      echo '[{"error":"In token"}]';
    } else {
      ///
      $folder = $_POST['folder'];
      $folder_id = $_POST['folder_id'];
      $file = $_POST['file'];
      $ref = $_POST['ref'];
      //"application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"
      // header("HTTP/1.1 200 OK");
      // echo '[{"upload":"' . $_FILES['uploadFile']['size'] . '"}]';
      // return;


      if ($_FILES['uploadFile']['size'] > 10000000) {
        header("HTTP/1.1 400 ERROR");
        echo '[{"error":"El archivo pesa más de 10 Megas"}]';
      } else {
        $filename = $_FILES['uploadFile']['name'];
        $extension = pathinfo($filename, PATHINFO_EXTENSION);
        $extension=strtolower($extension);
        //
        $target_dir = "../maker-files/archivos/"; // directorio donde se guardará el archivo subido
        //$target_file = $target_dir . basename($_FILES["uploadFile"]["name"]);
        $target_file = $target_dir .$folder_id.'-'. limpiar_tildes($ref) . '.' . $extension;
        $target_file_name = $folder_id.'-'.limpiar_tildes($ref) . '.' . $extension;
$target_file_name_base = $folder_id.'-'.limpiar_tildes($ref);
        
        if (strtolower($ref) === 'logo') {
          ///inicio logo
          $w = 800;
          $h = 600;

          $hM = ($h / 2);
          $wM = ($w / 2);

          /// VErot Upload
          $error = '';
          
            if ($_FILES['uploadFile']['type'] != "image/pjpeg" and $_FILES['uploadFile']['type'] != "image/jpeg" and $_FILES['uploadFile']['type'] != "image/png") {
              $error = 'Sólo se acepta JPG o PNG';
            } else {

              //
              include('verot-upload/src/class.upload.php');
              ini_set("max_execution_time", 0);
              $handle = new \Verot\Upload\Upload($_FILES['uploadFile']);
              if ($handle->uploaded) {
                ///PC version

                $handle->file_new_name_body = $target_file_name_base;
                $handle->file_overwrite = true;

                $handle->image_resize          = true;
                $handle->image_x               = $w;

                $handle->image_y               = $h;
                $handle->image_ratio_crop       = true;

                if ($extension == 'png') {
                  $handle->png_compression = 9;
                } else {
                  $handle->jpeg_quality = 85;
                }

                $handle->process($target_dir);
                /// Movil Version
                $handle->file_new_name_body = 'M' . $target_file_name_base;
                $handle->image_resize          = true;
                $handle->file_overwrite = true;
                $handle->image_x               = $wM;
                //$handle->image_ratio_y         = true;

                $handle->image_y               = $hM;
                $handle->image_ratio_crop       = true;

                if ($extension == 'png') {
                  $handle->png_compression = 9;
                } else {
                  $handle->jpeg_quality = 85;
                }

                $handle->process($target_dir);
              } else {
                ///error
                $error = 'Error al subir la imagen';
              }
            }
         

          if ($error === '') {
            if (move_uploaded_file($_FILES["uploadFile"]["tmp_name"], $target_file)) {
              /// despues de subirlo se registra
              $archivo = $target_file_name;
              $action = "INSERT INTO ca_archivos (tabla,tabla_id,ref,archivo,fecha) VALUES ('$folder', '$folder_id', '$ref','$archivo', '$hoy')";
              $mysqli->query($action) or die($mysqli->error);

              header("HTTP/1.1 200 OK");
              echo '[{"upload":"Se a subido el archivo: ' . $ref . '"}]';
            } else {
              header("HTTP/1.1 400 ERROR");
              echo '[{"error":"Ha ocurrido un error al subir el archivo"}]';
            }
          } else {
            header("HTTP/1.1 202 OK");
            echo '[{"error":"' . $error . '"}]';
          }
          ///fin logo
        } else {
          if (move_uploaded_file($_FILES["uploadFile"]["tmp_name"], $target_file)) {
            /// despues de subirlo se registra
            $archivo = $target_file_name;
            $action = "INSERT INTO ca_archivos (tabla,tabla_id,ref,archivo,fecha) VALUES ('$folder', '$folder_id', '$ref','$archivo', '$hoy')";
            $mysqli->query($action) or die($mysqli->error);

            header("HTTP/1.1 200 OK");
            echo '[{"upload":"Se a subido el archivo: ' . $ref . '"}]';
          } else {
            header("HTTP/1.1 400 ERROR");
            echo '[{"error":"Ha ocurrido un error al subir el archivo"}]';
          }
        }
      }
    }
  } else if ($ref == 'delete-archivo') { /// for pages

    $user_id = $_POST['user_id'];
    $time_life = $_POST['time_life'];
    $token = $_POST['token'];
    $tokenBase = md5($user_id . $time_life . $key_encrypt);
    //
    if ($tokenBase != $token) {
      header("HTTP/1.1 202 ERROR");
      //echo '[{"error":"yess-'.$user_id.'+'.$time_life.'"}]'; 
      echo '[{"error":"In token"}]';
    } else {
      ///
      $archivo = $_POST['archivo'];
      $id = $_POST['id'];

      // header("HTTP/1.1 200 OK");
      // echo '[{"upload":"' . $_FILES['uploadFile']['size'] . '"}]';
      // return;

      $action = "DELETE FROM ca_archivos WHERE id='$id'";
      $mysqli->query($action) or die($mysqli->error);
      ///
      $ruta = "../maker-files/archivos/" . $archivo;
      if (file_exists($ruta)) {
        unlink($ruta);
      }

      header("HTTP/1.1 200 OK");
      echo '[{"delete":"Se a borrado el archivo: ' . $archivo . '"}]';
    }
  } else if ($ref == 'upload-product') { /// for Product

    $user_id = $_POST['user_id'];
    $time_life = $_POST['time_life'];
    $token = $_POST['token'];
    $tokenBase = md5($user_id . $time_life . $key_encrypt);
    //
    $id = $_POST['id'];
    $position = $_POST['position'];
    //$_FILE['uploadFile']
    if ($tokenBase != $token) {
      header("HTTP/1.1 202 ERROR");
      //echo '[{"error":"yess-'.$user_id.'+'.$time_life.'"}]'; 
      echo '[{"error":"In token"}]';
    } else {
      /// type


      $h = 600;
      $hM = $h / 2;

      /// VErot Upload
      $error = '';
      if ($_FILES['uploadFile']['name'] != '') {
        if ($_FILES['uploadFile']['type'] != "image/pjpeg" and $_FILES['uploadFile']['type'] != "image/jpeg" and $_FILES['uploadFile']['type'] != "image/png") {
          $error = 'Only accept JPG or PNG';
        } else {
          //
          $path = $_FILES['uploadFile']['name'];
          $ext = pathinfo($path, PATHINFO_EXTENSION);
          $ext = strtolower($ext);
          //
          $path_preview = $id . '_' . $position . '.' . $ext . '?t=' . time();
          //
          include('verot-upload/src/class.upload.php');
          ini_set("max_execution_time", 0);
          $handle = new \Verot\Upload\Upload($_FILES['uploadFile']);
          if ($handle->uploaded) {
            ///PC version
            $handle->image_resize          = true;
            $handle->file_new_name_body = $id . '_' . $position;
            $handle->file_overwrite = true;

            $handle->image_ratio_x         = true;
            $handle->image_y               = $h;

            $handle->process('../maker-files/images/products/');
            /// Movil Version
            $handle->image_resize          = true;
            $handle->file_new_name_body = 'M' . $id . '_' . $position;
            $handle->file_overwrite = true;

            $handle->image_ratio_x         = true;
            $handle->image_y               = $hM;

            $handle->process('../maker-files/images/products/');
          } else {
            ///error
            $error = 'si';
          }
        }
      }
      /// The End Verot 


      if ($error == '') {
        //
        $row_name = 'image' . $position;

        $action = "UPDATE products SET `$row_name`='$path_preview' WHERE id='$id'";
        $mysqli->query($action) or die($mysqli->error);
        //
        $result = $mysqli->query("SELECT * FROM products WHERE id='$id' LIMIT 1");
        $new_prod = array();
        while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
          //$row['ruta']=$path_preview;
          $new_prod[] = $row;
        }
        //  
        header("HTTP/1.1 200 OK");
        //echo '[{"ruta":"'.$path_preview.'"}]'; 
        echo json_encode($new_prod);
      } else {
        header("HTTP/1.1 202 OK");
        echo '[{"error":"' . $error . '"}]';
      }
    }
  } else if ($ref == 'upload-category') { /// for Product

    $user_id = $_POST['user_id'];
    $time_life = $_POST['time_life'];
    $token = $_POST['token'];
    $tokenBase = md5($user_id . $time_life . $key_encrypt);
    //
    $id = $_POST['id'];
    $position = $_POST['position'];
    //$_FILE['uploadFile']
    if ($tokenBase != $token) {
      header("HTTP/1.1 202 ERROR");
      //echo '[{"error":"yess-'.$user_id.'+'.$time_life.'"}]'; 
      echo '[{"error":"In token"}]';
    } else {
      /// type

      $w = 1800;
      $wM = $w / 2;

      /// VErot Upload
      $error = '';
      if ($_FILES['uploadFile']['name'] != '') {
        if ($_FILES['uploadFile']['type'] != "image/pjpeg" and $_FILES['uploadFile']['type'] != "image/jpeg" and $_FILES['uploadFile']['type'] != "image/png") {
          $error = 'Only accept JPG or PNG';
        } else {
          //
          $path = $_FILES['uploadFile']['name'];
          $ext = pathinfo($path, PATHINFO_EXTENSION);
          $ext = strtolower($ext);
          //
          $path_preview = 'C' . $id . '.' . $ext . '?t=' . time();
          //
          include('verot-upload/src/class.upload.php');
          ini_set("max_execution_time", 0);
          $handle = new \Verot\Upload\Upload($_FILES['uploadFile']);
          if ($handle->uploaded) {
            ///PC version
            $handle->image_resize          = true;
            $handle->file_new_name_body = 'C' . $id;
            $handle->file_overwrite = true;

            $handle->image_ratio_y         = true;
            $handle->image_x               = $w;

            $handle->process('../maker-files/images/products/');
            /// Movil Version
            $handle->image_resize          = true;
            $handle->file_new_name_body = 'MC' . $id;
            $handle->file_overwrite = true;

            $handle->image_ratio_y         = true;
            $handle->image_x               = $wM;

            $handle->process('../maker-files/images/products/');
          } else {
            ///error
            $error = 'si';
          }
        }
      }
      /// The End Verot 


      if ($error == '') {
        //

        $action = "UPDATE categories SET `image`='$path_preview' WHERE id='$id'";
        $mysqli->query($action) or die($mysqli->error);
        //
        $result = $mysqli->query("SELECT * FROM categories WHERE id='$id' LIMIT 1");
        $new_cat = array();
        while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
          //$row['ruta']=$path_preview;
          $new_cat[] = $row;
        }
        //  
        header("HTTP/1.1 200 OK");
        //echo '[{"ruta":"'.$path_preview.'"}]'; 
        echo json_encode($new_cat);
      } else {
        header("HTTP/1.1 202 OK");
        echo '[{"error":"' . $error . '"}]';
      }
    }
  } else if ($ref == 'save-formWeb') {

    $company_id = $_GET['company_id'];
    $tokenWeb = $_GET['tokenWeb'];

    $tokenB = md5($company_id . 'Fr-96(');

    if ($tokenWeb != $tokenB) {
      header("HTTP/1.1 202 ERROR");
      //echo '[{"error":"yess-'.$user_id.'+'.$time_life.'"}]'; 
      echo '[{"error":"error in token"}]';
    } else {
      $page = $data['page'];
      $request = '';
      $a = 0;
      foreach ($data['listForm'] as $f) {
        foreach ($f as $row => $value) {
          if ($row == 'name') {
            $a++;
            $request .= $a . '. ' . $value . ': ';
          }
          if ($row == 'response') {
            $request .= $value . '<br>';
          }

          //
        }
      }

      /*
 header("HTTP/1.1 202 ERROR");
 echo '[{"error":"'.$request.'"}]';
 return;
 */
      //echo 'Z:'+$request;
      $now = date('Y-m-d H:i:s');

      ///Nuevo Registro
      $mysqli->query("INSERT INTO forms_received (company_id,`page`,`date`,request) VALUES ('$company_id','$page','$now','$request')") or die($mysqli->error);



      ///
      header("HTTP/1.1 200 OK");

      echo '[{"ok":"Form Send: ' . $data['sendTo'] . '"}]';

      /// send
      if ($data['sendTo'] != '') {
        $rCi = $mysqli->query("SELECT company FROM companies WHERE id='$company_id' LIMIT 1 ");
        $rowCi = $rCi->fetch_array(MYSQLI_ASSOC);
        $company = $rowCi['company'];


        $message = '<strong>' . $now . '</strong><br>Web Page: ' . $page . '<br>Request: <br>' . $request;
        ob_start();
        include("mail.php");
        $html = ob_get_contents();
        ob_end_clean();

        $subjet = $page . ' Form';


        $from_email = $data['sendTo'];
        // echo $html;
        $send_email = $data['sendTo'];
        if (strtoupper(substr(PHP_OS, 0, 3) == 'WIN')) {
          $eol = "\r\n";
        } elseif (strtoupper(substr(PHP_OS, 0, 3) == 'MAC')) {
          $eol = "\r";
        } else {
          $eol = "\n";
        }
        $header = "Content-type: text/html" . $eol;
        //dirección del remitente
        $header .= 'From: ' . $company . ' <' . $from_email . '>' . $eol;
        $header .= 'Reply-To: ' . $company . ' <' . $from_email . '>' . $eol;
        $header .= "Message-ID:<" . time() . " TheSystem@" . $_SERVER['SERVER_NAME'] . ">" . $eol;
        $header .= "X-Mailer: PHP v" . phpversion() . $eol;
        $header .= 'MIME-Version: 1.0' . $eol;
        //////
        mail($send_email, $subjet, $html, $header);
      }
      //the end send

    }


    //$result->close();
    //$mysqli->close();

  }
}

/// FIN metodo POST

else if ($_SERVER['REQUEST_METHOD'] == 'PUT') {
  $ref == $_GET['ref'];

  if ($ref == 'test') {

    header("HTTP/1.1 200 OK");
    echo '[{"id":' . $data['a'] . ',"documento":"' . $data['b'] . '","nombre":"' . $data['c'] . '-' . $_SERVER['REQUEST_METHOD'] . '"},{"id":' . ($data['a'] + 1) . ',"documento":"KP' . $data['b'] . '","nombre":"KP' . $data['c'] . '-' . $_SERVER['REQUEST_METHOD'] . '"}]';
  } else
    if ($ref == 'buyer') {

    $id = $data['id'];
    $empresa_id = $data['empresa_id'];
    $documento = $data['documento'];
    $nombre = $data['nombre'];
    $apellido = $data['apellido'];
    $celular = $data['celular'];
    $email = $data['email'];
    $ciudad = $data['ciudad'];
    $barrio = $data['barrio'];
    $direccion = $data['direccion'];
    $publicidad = $data['publicidad'];


    if ($id != 0) {
      $mysqli->query("UPDATE compradores SET documento='$documento',nombre='$nombre', apellido='$apellido', celular='$celular', email='$email', ciudad='$ciudad', barrio='$barrio', direccion='$direccion', publicidad='$publicidad' WHERE id='$id'") or die($mysqli->error);
      header("HTTP/1.1 200 OK");
      echo $id;
    } else {
      $mysqli->query("INSERT INTO compradores (empresa_id, documento,nombre, apellido, celular, email, ciudad, barrio, direccion, publicidad) VALUES('$empresa_id', '$documento', '$nombre', '$apellido', '$celular', '$email', '$ciudad', '$barrio', '$direccion', '$publicidad')") or die($mysqli->error);
      $result = $mysqli->query("SELECT id FROM compradores WHERE documento='$documento' LIMIT 1") or die($mysqli->error);
      $row = $result->fetch_array();
      header("HTTP/1.1 200 OK");
      echo $row['id'];
      $result->close();
    }


    //$mysqli->close();

  }
}
/// FIN metodo PUT

else if ($_SERVER['REQUEST_METHOD'] == 'DELETE') {
  $ref = $_REQUEST['ref'];
  //echo $_REQUEST['tipo'].'++'.$_GET['ref'];
  //echo $tipo.'--'.$_SERVER['REQUEST_METHOD'].'++'.$_REQUEST['tipo'];
  if ($ref == 'test') {

    header("HTTP/1.1 200 OK");
    echo '[{"id":' . $data['a'] . ',"documento":"' . $data['b'] . '","nombre":"' . $data['c'] . '-' . $_SERVER['REQUEST_METHOD'] . '"},{"id":' . ($data['a'] + 1) . ',"documento":"KP' . $data['b'] . '","nombre":"KP' . $data['c'] . '-' . $_SERVER['REQUEST_METHOD'] . '"}]';
  } else
if ($ref == 'delete') {
    $user_id = $data['user_id'];
    $time = $data['time_life'];
    $token = $data['token'];

    $tokenBase = md5($user_id . $time . $key_encrypt);

    if ($tokenBase != $token) {
      header("HTTP/1.1 402 ERROR");
      echo 'Error in token';
    } else {
      $hotel_id = $data['hotel_id'];
      $campo = $data['campo'];
      $valor = $data['valor'];
      $folder = $data['folder'];

      $mysqli->query("DELETE $folder WHERE $campo='$valor' ") or die($mysqli->error);
      header("HTTP/1.1 200 OK");
      echo '{"resultado":"borrado"}';
    }
  } else
  if ($ref == 'deleteMulti') {
    $user_id = $data['user_id'];
    $time = $data['time_life'];
    $token = $data['token'];

    $tokenBase = md5($user_id . $time . $key_encrypt);

    if ($tokenBase != $token) {
      header("HTTP/1.1 402 ERROR");
      echo 'Error in token';
    } else {
      $hotel_id = $data['hotel_id'];
      $campo = $data['campo'];
      $valor = $data['valor'];
      $campo2 = $data['campo2'];
      $valor2 = $data['valor2'];
      $folder = $data['folder'];

      $mysqli->query("DELETE FROM $folder WHERE $campo='$valor' AND $campo2='$valor2' ") or die($mysqli->error);
      header("HTTP/1.1 200 OK");
      echo '{"resultado":"borrado:' . $campo . '"}';
    }
  } else
  if ($ref == 'deleteTarifasEspeciales') {
    $user_id = $data['user_id'];
    $time = $data['time_life'];
    $token = $data['token'];

    $tokenBase = md5($user_id . $time . $key_encrypt);

    if ($tokenBase != $token) {
      header("HTTP/1.1 402 ERROR");
      echo 'Error in token';
    } else {
      $hotel_id = $data['hotel_id'];
      $folder = $data['folder'];

      if ($data['fecha']) { /// uno
        $fecha = $data['fecha'];
        $result = $mysqli->query("SELECT id FROM habitaciones WHERE hotel_id='$hotel_id' ") or die($mysqli->error);
        while ($row = $result->fetch_array()) {
          $habitacion_id = $row['id'];
          $mysqli->query("DELETE FROM $folder WHERE fecha='$fecha' AND habitacion_id='$habitacion_id' ") or die($mysqli->error);
        }
      } else if ($data['fechas']) { ///bloque

        foreach ($data['fechas'] as $fecha) {
          $result = $mysqli->query("SELECT id FROM habitaciones WHERE hotel_id='$hotel_id' ") or die($mysqli->error);
          while ($row = $result->fetch_array()) {
            $habitacion_id = $row['id'];
            $mysqli->query("DELETE FROM $folder WHERE fecha='$fecha' AND habitacion_id='$habitacion_id' ") or die($mysqli->error);
          }
        }
      }


      header("HTTP/1.1 200 OK");
      echo '{"resultado":"borrado Tarifas Especiales"}';
    }
  }
}

//En caso de que ninguna de las opciones anteriores se haya ejecutado
//
else {
  header("HTTP/1.1 202 Error");
}
//header("HTTP/1.1 200 OK");
