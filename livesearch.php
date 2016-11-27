<?php
  //arquivo de conexão com o banco em MySQL
  require_once "inc/conectar.php";

  $marca = $_REQUEST["marca"];
  $modelo = $_REQUEST["modelo"];
  $acessorio = $_REQUEST["acessorio"];

  $conn = ConectarDB();

  $sql = "SELECT DISTINCT M.ID_MARCA, M.MARCA FROM CARRO C INNER JOIN MARCA M ON M.ID_MARCA = C.ID_MARCA";
  $sql .= " WHERE 1=1";
  if($acessorio > 0){
      $sql .= " AND C.ID_ACESSORIO = " . $acessorio;
    }
  $itensMarca = mysqli_query($conn, $sql) or die(mysqli_error($conn));

  $sql = "SELECT DISTINCT M.ID_MODELO, M.MODELO FROM CARRO C INNER JOIN MODELO M ON M.ID_MODELO = C.ID_MODELO";
  	$sql .= " WHERE 1=1";
  	if($marca > 0){
  		$sql .= " AND M.ID_MARCA = " . $marca;
  	}
  	if($acessorio > 0){
  		$sql .= " AND C.ID_ACESSORIO = " . $acessorio;
  	}
  $itensModelo = mysqli_query($conn, $sql) or die(mysqli_error($conn));

  $sql = "SELECT DISTINCT A.ID_ACESSORIO, A.ACESSORIO FROM CARRO C INNER JOIN ACESSORIO A ON A.ID_ACESSORIO = C.ID_ACESSORIO";
  if($modelo > 0 or $marca > 0){
  	$sql .= " WHERE 1=1";
  	if($modelo > 0){
  		$sql .= " AND ID_MODELO = " . $modelo;
  	}
  	if($marca > 0){
  		$sql .= " AND ID_MARCA = " . $marca;
  	}
  	if($acessorio > 0){
  		$sql .= " AND C.ID_ACESSORIO = " . $acessorio;
  	}
  }
  $itensAcessorio = mysqli_query($conn, $sql) or die(mysqli_error($conn));

  $sql = "SELECT MIN(PRECO) AS MIN, Max(PRECO) AS MAX FROM CARRO";
  if($modelo > 0 or $marca > 0){
  	$sql .= " WHERE 1=1";
  	if($modelo > 0){
  		$sql .= " AND ID_MODELO = " . $modelo;
  	}
  	if($marca > 0){
  		$sql .= " AND ID_MARCA = " . $marca;
  	}
  	if($acessorio > 0){
  		$sql .= " AND ID_ACESSORIO = " . $acessorio;
  	}
  }
  $precos = mysqli_query($conn, $sql) or die(mysqli_error($conn));

  $sql = "SELECT DISTINCT DESCRICAO FROM CARRO C";
  if($modelo > 0 or $marca > 0 or $acessorio > 0){
  	$sql .= " WHERE 1=1";
  	if($modelo > 0){
  		$sql .= " AND ID_MODELO = " . $modelo;
  	}
  	if($marca > 0){
  		$sql .= " AND ID_MARCA = " . $marca;
  	}
  	if($acessorio > 0){
  		$sql .= " AND ID_ACESSORIO = " . $acessorio;
  	}
  }
  $tags = mysqli_query($conn, $sql) or die(mysqli_error($conn));

$marca = array();
while ($item = mysqli_fetch_array($itensMarca)) {
	array_push($marca ,array('marca' => $item["MARCA"],
	   			 'id_marca' => $item['ID_MARCA']));
}

$modelo = array();
while ($item = mysqli_fetch_array($itensModelo)) {
	array_push($modelo ,array('modelo' => $item["MODELO"],
	   			 'id_modelo' => $item['ID_MODELO']));
}

$acessorio = array();
while ($item = mysqli_fetch_array($itensAcessorio)) {
	array_push($acessorio ,array('acessorio' => $item["ACESSORIO"],
	   			 'id_acessorio' => $item['ID_ACESSORIO']));
}
$precomaxmin = array();
while ($item = mysqli_fetch_array($precos)) {
	array_push($precomaxmin ,array('preco_min' => $item["MIN"],
	   			 'preco_max' => $item['MAX']));
}

$list_tags = array();
while ($item = mysqli_fetch_array($tags)) {
	$split = explode(" ", $item['DESCRICAO']);
	foreach($split as $i) {
		array_push($list_tags , $i);
	}
}

$retorno = array('marcas' => $marca,
				 'modelos' => $modelo,
				 'acessorio' => $acessorio,
				 'precos' => $precomaxmin,
				 'tags' => array_keys(array_flip($list_tags)));


echo json_encode($retorno);

DesconectarDB($conn);

  ?>
