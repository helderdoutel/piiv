<?php
  //arquivo de conexão com o banco em MySQL
  require_once "inc/conectar.php";

  if (!isset($_REQUEST["max"])) {
    $_REQUEST["max"] = 0;
  }
  if (!isset($_REQUEST["min"])) {
    $_REQUEST["min"] = 0;
  }

  $marca = $_REQUEST["marca"];
  $modelo = $_REQUEST["modelo"];
  $acessorio = $_REQUEST["acessorio"];
  $descricao = $_REQUEST["descricao"];
  $max = $_REQUEST["max"];
  $min = $_REQUEST["min"];

  $conn = ConectarDB();

  $titulo = 'Modelos';
  $valores = array();
  $dataset = array();
  $sql = "SELECT C.ID_CARRO, MO.MODELO, MA.MARCA, C.PRECO FROM CARRO C INNER JOIN MODELO MO ON C.ID_MODELO = MO.ID_MODELO INNER JOIN MARCA MA ON MA.ID_MARCA = C.ID_MARCA WHERE 1=1";
    if ($marca > 0) {
        $sql .= " AND C.ID_MARCA = " . $marca;
      }
      if ($modelo > 0) {
          $sql .= " AND C.ID_MODELO = " . $modelo;
        }
      if ($descricao != "") {
          $sql .= " AND C.DESCRICAO LIKE '%" . $descricao . "%'";
        }
      if ($acessorio > 0) {
          $sql .= " AND C.ID_ACESSORIO = " . $acessorio;
      }
      if ($min > 0) {
          $sql .= " AND C.PRECO >= " . $min;
      }
      if ($max > 0) {
          $sql .= " AND C.PRECO <= " . $max;
      }
  $sql .= " ORDER BY PRECO DESC LIMIT 10";
  $itens = mysqli_query($conn, $sql);
  while($item = mysqli_fetch_array($itens)){
    array_push($valores ,array('label' => '['.$item["ID_CARRO"].'] '.$item["MODELO"].' - '.$item["MARCA"],
                               'preco' => $item['PRECO']));
  }
  array_push($dataset, array('valores' => $valores,
                             'dataset' => 'Mais caros'));
  $sql = "";
  $sql = "SELECT C.ID_CARRO, MO.MODELO, MA.MARCA, C.PRECO FROM CARRO C INNER JOIN MODELO MO ON C.ID_MODELO = MO.ID_MODELO INNER JOIN MARCA MA ON MA.ID_MARCA = C.ID_MARCA WHERE 1=1";
    if ($marca > 0) {
        $sql .= " AND C.ID_MARCA = " . $marca;
      }
      if ($modelo > 0) {
          $sql .= " AND C.ID_MODELO = " . $modelo;
        }
      if ($descricao != "") {
          $sql .= " AND C.DESCRICAO LIKE '%" . $descricao . "%'";
        }
      if ($acessorio > 0) {
          $sql .= " AND C.ID_ACESSORIO = " . $acessorio;
      }
      if ($min > 0) {
          $sql .= " AND C.PRECO >= " . $min;
      }
      if ($max > 0) {
          $sql .= " AND C.PRECO <= " . $max;
      }
  $sql .= " ORDER BY PRECO ASC LIMIT 10";
  $itens = mysqli_query($conn, $sql);
  $valores = array();
  while($item = mysqli_fetch_array($itens)){
    array_push($valores ,array('label' => '['.$item["ID_CARRO"].'] '.$item["MODELO"].' - '.$item["MARCA"],
                               'preco' => $item['PRECO']));
  }
  array_push($dataset, array('valores' => $valores,
                             'dataset' => 'Mais baratos'));

$retorno = array('retorno' => $dataset,
                 'titulo' => $titulo);


echo json_encode($retorno);

DesconectarDB($conn);

  ?>
