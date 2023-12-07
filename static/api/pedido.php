<?php
/*
error_reporting(E_ALL);
if ($_GET['error']) {
  ini_set('display_errors', '1');
} else {
  ini_set('display_errors', '0');
}

if(!isset($pedido_id)) $pedido_id=$_GET['pedido_id'];

$db_host = "localhost"; //192.185.131.105
$db_user = "cityciud_usercinva";
$db_pass = "E!sb,n?9o&=O";
$db_name = "cityciud_cinva";

//
$conn = @mysqli_connect($db_host, $db_user, $db_pass, $db_name);
mysqli_query($conn, "SET CHARACTER SET 'utf8'");

$mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name);
$mysqli->set_charset("utf8");

$hoy = date('Y-m-d');
*/

$rEmpresa = $mysqli->query("SELECT * FROM cinva_config LIMIT 1") or die($mysqli->error);
$rowEmpresa = $rEmpresa->fetch_array();

$rPedido = $mysqli->query("SELECT cinva_orders.*, cinva_clientes.* FROM cinva_orders, cinva_clientes WHERE cinva_orders.id='$pedido_id' AND cinva_clientes.id=cinva_orders.comprador_id LIMIT 1") or die($mysqli->error);
$rowPedido = $rPedido->fetch_array();

$rPedidoItems = $mysqli->query("SELECT * FROM cinva_order_products WHERE order_id='$pedido_id'") or die($mysqli->error);

?>
<p style="border-radius:10px; background: #ccc; padding:8px">
<strong>Pedido #<?php echo $rowPedido[0]; ?></strong>
  <br />
  <strong>Fecha:</strong> <?php echo $rowPedido['fecha']; ?>
</p>
<p style="border-radius:10px; border:solid 1px #333; padding:8px">

  <strong></strong> <?php echo $rowEmpresa['nombre']; ?>
  <br />
  Nit: <?php echo $rowEmpresa['nit']; ?>
  <br />
  <?php echo $rowEmpresa['email']; ?>
  <br />
  <?php echo $rowEmpresa['telefono']; ?>
  <br />
  <?php echo $rowEmpresa['direccion']; ?>  
</p>
<p style="border-radius:10px; border:solid 1px #333; padding:8px">
  <strong>Cliente:</strong>
  <br />
  <?php echo $rowPedido['nombre']; ?>
  <strong>Email:</strong> <?php echo $rowPedido['email']; ?>
  <br />
  <strong>Teléfono:</strong> <?php echo $rowPedido['telefono']; ?>
  <br />
  <strong>Dirección:</strong> <?php echo $rowPedido['direccion']; ?>
  <br />
  <small><?php echo $rowPedido['ciudad']; ?> - <?php echo $rowPedido['departamento']; ?></small>
 
</p>


<?php if($rowPedido['notas']!=''){ echo '<p style="border-radius:10px; background: #ccc; padding:8px">'.$rowPedido['notas'].'</p>'; }  ?>

 <table style="width: 550px; margin:0px auto; border:solid 1px #333; padding:8px">
  <tr>
   <th></th>
    <th>Imagen</th>
    <th>Producto</th>
    <th>Precio</th>
    <th>Cantidad</th>
    <th>Total</th>
  </tr>
  <?php 
  $p=0;
  while($rowPedidoItems = $rPedidoItems->fetch_array()){ 
   $p++;
   ?>
    <tr>
     <td><?php echo $p?></td>
     <td><img src="<?php echo $rowPedidoItems['image']; ?>" /></td>
     <td><?php echo $rowPedidoItems['name']; ?>
    <br />
    <small><?php echo $rowPedidoItems['ref']; ?></small>
    <br />
    <?php if($rowPedidoItems['size']!='' || $rowPedidoItems['color']!=''){
      echo $rowPedidoItems['size'].' '.$rowPedidoItems['color'].'<br />'; 
  }
    ?>
    <?php echo $rowPedidoItems['description']; ?>
    </td>
     <td style="text-align:right"><?php echo number_format($rowPedidoItems['price']); ?></td>
      <td style="text-align:center"><?php echo $rowPedidoItems['quantity'].' <small>'.$rowPedidoItems['unit'].'</small>'; ?></td>
      <td style="text-align:right"><?php echo number_format($rowPedidoItems['total']); ?></td>
    </tr>
    <?php } ?>
    <tr>
   <td></td>
    <td colspan="4">Total</td>    
    <td style="text-align:right"><strong>$ <?php echo number_format($rowPedido['total'])?></strong></td>
  </tr>
 </table>