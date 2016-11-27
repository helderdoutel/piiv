<!DOCTYPE html>

<?php
  //arquivo de conexão com o banco em MySQL
  require_once "inc/conectar.php";

  if(!isset($_REQUEST["offset"])) {
	  $_REQUEST["offset"] = 0;
  }
  if(!isset($_REQUEST["ascdesc"])) {
	  $_REQUEST["ascdesc"] = 0;
  }
  if(!isset($_REQUEST["order"])) {
	  $_REQUEST["order"] = 0;
  }

  $conn = ConectarDB();
  //carregando as combos
  $sql = "SELECT ID_MARCA, MARCA FROM MARCA";
  $itensMarca = mysqli_query($conn, $sql);

  $sql = "SELECT ID_MODELO, MODELO FROM MODELO";
  $itensModelo = mysqli_query($conn, $sql);

  $sql = "SELECT ID_ACESSORIO, ACESSORIO FROM ACESSORIO";
  $itensAcessorio = mysqli_query($conn, $sql);
  $offset = intval($_REQUEST["offset"]);
  //caso o botão Buscar foi clicado
  if(isset($_REQUEST["btnBuscar"])){
    $issubmit = 1;
  }
  else{
    $issubmit = 0;
  }
  $ascdesc = intval($_REQUEST["ascdesc"]);
  if (isset($_REQUEST["btnBuscar"])) {
    $marca = intval($_REQUEST["cboMarca"]);
    $modelo = intval($_REQUEST["cboModelo"]);
    $descricao = $_REQUEST["txtDescricao"];
    $acessorio = intval($_REQUEST["cboAcessorio"]);
    $precoMin = intval($_REQUEST["txtPrecoMin"]);
    $precoMax = intval($_REQUEST["txtPrecoMax"]);
    $order = intval($_REQUEST["order"]);

    if(!$offset or $offset < 0){
      $offset = 0;
    }
    if(!$order){
      $order = 0;
    }

    if (!$marca && !$modelo && !$acessorio && $descricao == "" && (!$precoMin || $precoMin == "") && (!$precoMax || $precoMax == "")) {
      $camposNaoPreenchidos = true;
    }
    else {
      //preenchendo a query de consulta
      $campoAnterior = false;
      //select
      $sql = "SELECT";
      $sql .= " CA.ID_CARRO AS CODIGO";
      $sql .= ", MA.MARCA AS MARCA";
      $sql .= ", MO.MODELO AS MODELO";
      $sql .= ", CA.DESCRICAO AS DESCRICAO";
      $sql .= ", AC.ACESSORIO AS ACESSORIO";
      $sql .= ", CA.PRECO AS PRECO";
      $sql .= " FROM CARRO CA";

      //inner join
      $sql .= " INNER JOIN MARCA MA ON MA.ID_MARCA = CA.ID_MARCA";
      $sql .= " INNER JOIN MODELO MO ON MO.ID_MODELO = CA.ID_MODELO";
      $sql .= " INNER JOIN ACESSORIO AC ON AC.ID_ACESSORIO = CA.ID_ACESSORIO";

      //where
      $sql .= " WHERE";
      if ($marca > 0) {
        $sql .= " CA.ID_MARCA = " . $marca;
        $campoAnterior = true;
      }
      if ($modelo > 0) {
        if ($campoAnterior) {
          $sql .= " AND CA.ID_MODELO = " . $modelo;
        }
        else {
          $sql .= " CA.ID_MODELO = " . $modelo;
          $campoAnterior = true;
        }
      }
      if ($descricao != "") {
        if ($campoAnterior) {
          $sql .= " AND CA.DESCRICAO LIKE '%" . $modelo . "%'";
        }
        else {
          $sql .= " CA.DESCRICAO LIKE '%" . $modelo . "%'";
          $campoAnterior = true;
        }
      }
      if ($acessorio > 0) {
        if ($campoAnterior) {
          $sql .= " AND CA.ID_ACESSORIO = " . $acessorio;
        }
        else {
          $sql .= " CA.ID_ACESSORIO = " . $acessorio;
          $campoAnterior = true;
        }
      }
      if ($precoMin > 0) {
        if ($campoAnterior) {
          $sql .= " AND CA.PRECO >= " . $precoMin;
        }
        else {
          $sql .= " CA.PRECO >= " . $precoMin;
          $campoAnterior = true;
        }
      }
      if ($precoMax > 0) {
        if ($campoAnterior) {
          $sql .= " AND CA.PRECO <= " . $precoMax;
        }
        else {
          $sql .= " CA.PRECO <= " . $precoMax;
          $campoAnterior = true;
        }
      }
      if($order == 0){
        $sql .= " ORDER BY CA.ID_CARRO ASC";
      }
      if($order == 1){
        $sql .= " ORDER BY CA.ID_MARCA ASC";
      }
      if($order == 2){
        $sql .= " ORDER BY CA.ID_MODELO ASC";
      }
      if($order == 3){
        $sql .= " ORDER BY CA.DESCRICAO ASC";
      }
      if($order == 4){
        $sql .= " ORDER BY CA.ID_ACESSORIO ASC";
      }
      if($order == 5){
        $sql .= " ORDER BY CA.PRECO ASC";
      }

      $sql .= " LIMIT 50 OFFSET ".$offset.";";
      $itensCarro = mysqli_query($conn, $sql) or die(mysqli_error($conn));
    }
  }
  else{
    $marca = 0;
    $modelo = 0;
    $descricao = "";
    $acessorio = 0;
    $precoMin = 0;
    $precoMax = 0;
  }
?>

<html lang="pt">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <title>Busca de Carros</title>

  <link rel="stylesheet" href="css/busca.css">
  <link rel="stylesheet" href="css/jquery-ui.css">
  <link rel="stylesheet" href="css/jquery-ui.structure.css">
  <link rel="stylesheet" href="css/jquery-ui.theme.css">
  <script type="text/javascript" src="js/jquery-3.1.1.min.js"></script>
  <script type="text/javascript" src="js/jquery-ui.js"></script>
  <script type="text/javascript" src="js/Chart.js"></script>
  <script type="text/javascript" src="js/Chart.bundle.js"></script>
  <script type="text/javascript" src="js/busca.js"></script>
  <script type="text/javascript" src="js/samples/utils.js"></script>
  <style>
              canvas {
                  -moz-user-select: none;
                  -webkit-user-select: none;
                  -ms-user-select: none;
              }
  </style>
</head>
<body>

  <div id="Carros" class="Carros Carros-antes">
    <h1 id="CarrosTitulo" class="CarrosTitulo">Sistemas de Carros Novos</h1>

    <div id="CarrosEntrada" class="CarrosEntrada">
      <h2 id="lblBuscarCarros" class="CarrosSubtitulo">Buscar Carros</h2>
      <form action="index.php" id="frmCarrosBusca" class="CarrosBusca" name="CarrosBusca" method="POST" onsubmit="return ValidarCampos(this)">
        <input type="hidden" id="foisubmit" name="foisubmit" value="<?= $issubmit ?>">
        <fieldset id="fdsMarca" class="CarrosBuscaControle">
          <label for="cboMarca" id="lblMarca" class="CarrosBuscaControleDescricao">Marca:</label>
          <select name="cboMarca" id="cboMarca" class="CarrosBuscaControleCampo" onchange="javascript: myAjax()">
            <option value="0">Selecione</option>
            <?php
              while ($item = mysqli_fetch_array($itensMarca)) {
                if(intval($marca) == intval($item["ID_MARCA"])){
            ?>
              <option value=" <?= $item["ID_MARCA"] ?> " selected="selected"> <?= $item["MARCA"] ?> </option>
            <?php
            }
            else{
            ?>
                <option value=" <?= $item["ID_MARCA"] ?> "> <?= $item["MARCA"] ?> </option>
            <?php
          }
              }
            ?>
          </select>
        </fieldset>
        <br>
        <fieldset id="fdsModelo" class="CarrosBuscaControle">
          <label for="cboModelo" id="lblModelo" class="CarrosBuscaControleDescricao">Modelo:</label>
          <select name="cboModelo" id="cboModelo" class="CarrosBuscaControleCampo" onchange="javascript: myAjax()">
            <option value="0">Selecione</option>
            <?php
              while ($item = mysqli_fetch_array($itensModelo)) {
                if(intval($modelo) == intval($item["ID_MODELO"])){
            ?>
                <option value=" <?= $item["ID_MODELO"] ?> "  selected="selected"> <?= $item["MODELO"] ?> </option>
            <?php
            }
            else{
            ?>
              <option value=" <?= $item["ID_MODELO"] ?> "> <?= $item["MODELO"] ?> </option>
            <?php
          }
              }
            ?>
          </select>
        </fieldset>
        <br>
        <fieldset id="fdsDescricao" class="CarrosBuscaControle">
          <label for="txtDescricao" id="lblDescricao" class="CarrosBuscaControleDescricao">Descrição:</label>
          <input type="text" name="txtDescricao" id="txtDescricao" name="txtDescricao" class="CarrosBuscaControleCampo" value="<?= $descricao ?>">
        </fieldset>
        <br>
        <fieldset id="fdsAcessorio" class="CarrosBuscaControle">
          <label for="cboAcessorio" id="lblAcessorio" class="CarrosBuscaControleDescricao">Acessório:</label>
          <select name="cboAcessorio" id="cboAcessorio" class="CarrosBuscaControleCampo" onchange="javascript: myAjax()">
            <option value="0">Selecione</option>
            <?php
              while ($item = mysqli_fetch_array($itensAcessorio)) {
                if(intval($acessorio) == intval($item["ID_ACESSORIO"])){
            ?>
                <option value=" <?= $item["ID_ACESSORIO"] ?> " selected="selected"> <?= $item["ACESSORIO"] ?> </option>
            <?php
            }
            else{
            ?>
                <option value=" <?= $item["ID_ACESSORIO"] ?> "> <?= $item["ACESSORIO"] ?> </option>
            <?php
          }
              }
            ?>
          </select>
        </fieldset>
        <br>
        <fieldset id="fdsPrecoMin" class="CarrosBuscaControle">
          <label for="txtPrecoMin" id="lblPrecoMin" class="CarrosBuscaControleDescricao">Preço Mínimo:</label>
          <input type="number" name="txtPrecoMin" id="txtPrecoMin" name="txtPrecoMin" class="CarrosBuscaControleCampo" value="<?= $precoMin ?>" min="0" step="100">
        </fieldset>
        <br>
        <fieldset id="fdsPrecoMax" class="CarrosBuscaControle">
          <label for="txtPrecoMax" id="lblPrecoMax" class="CarrosBuscaControleDescricao">Preço Máximo:</label>
          <input type="number" name="txtPrecoMax" id="txtPrecoMax" name="txtPrecoMax" class="CarrosBuscaControleCampo" value="<?= $precoMax ?>" min="0" step="100">
        </fieldset>
        <br>
        <label class="CarrosBuscaControleDescricao">Classificar gráfico por:</label>
        <select name="ascdesc" id="ascdesc" onchange="ajaxGrafico();">
          <?php
          if($ascdesc == 0){
          ?>
          <option value="0" selected="selected">Mais Caros</option>
          <option value="1">Mais Baratos</option>
          <?php
          }else{
          ?>
            <option value="0">Mais Caros</option>
            <option value="1" selected="selected">Mais Baratos</option>
          <?php
          }
          ?>
          </select>
          <input type="button" value="Gerar gráfico" onclick="EsconderTabela();ajaxGrafico();">
          <br>
        <input type="submit" name="btnBuscar" id="btnBuscar" class="CarrosBuscaBotao" name="btnBuscar" value="Mostrar Detalhes">
      </form>
    </div> <!-- Fim CarrosEntrada -->

    <br>
    <br>

    <!-- RESULTADO DA PESQUISA -->

    <div id="CarrosResultado" class="CarrosResultado">
    <div id="container" style="width: 75%;">
                  <canvas id="canvas"></canvas>
              </div>
	<br>
	<br>
      <div id="MostrarDetalhes">
      <?php
        if (isset($_REQUEST["btnBuscar"])) {
      ?>
          <!--<h2 id="lblResultadoPesquisa" class="CarrosSubtitulo">Resultado da Pesquisa</h2>-->
      <?php
          if(isset($msg)) {
      ?>
            <p>Favor preencher algum campo para a pesquisa!!!</p>
      <?php
          }
          else {
            $linhas = mysqli_num_rows($itensCarro);
            if ($linhas == 0) {
      ?>
              <p>Nenhum registro encontrado.</p>
      <?php
            }
            else {
      ?>
              <table id="TabelaPrecosCarros" class="TabelaPrecosCarros">
                <caption id="TabelaPrecosCarrosTitulo" class="TabelaPrecosCarrosTitulo"><!-- Titulo da Tabela --></caption>
                <thead id="TabelaPrecosCarrosCabecalho" class="TabelaPrecosCarrosCabecalho">
                  <th><a href="index.php?cboMarca=<?= $marca?>&cboModelo=<?= $modelo ?>&txtDescricao=<?= $descricao ?>&cboAcessorio=<?= $acessorio ?>&txtPrecoMin=<?= $precoMin ?>&txtPrecoMax=<?= $precoMax ?>&btnBuscar=true&offset=0&order=0&ascdesc=<?= $ascdesc ?>" class="Cabecalho <?php if($order == 0) echo 'CabecalhoSelecionado'; ?>">CÓDIGO</a></th>
                  <th><a href="index.php?cboMarca=<?= $marca?>&cboModelo=<?= $modelo ?>&txtDescricao=<?= $descricao ?>&cboAcessorio=<?= $acessorio ?>&txtPrecoMin=<?= $precoMin ?>&txtPrecoMax=<?= $precoMax ?>&btnBuscar=true&offset=0&order=1&ascdesc=<?= $ascdesc ?>" class="Cabecalho <?php if($order == 1) echo 'CabecalhoSelecionado'; ?>">MARCA</a></th>
                  <th><a href="index.php?cboMarca=<?= $marca?>&cboModelo=<?= $modelo ?>&txtDescricao=<?= $descricao ?>&cboAcessorio=<?= $acessorio ?>&txtPrecoMin=<?= $precoMin ?>&txtPrecoMax=<?= $precoMax ?>&btnBuscar=true&offset=0&order=2&ascdesc=<?= $ascdesc ?>" class="Cabecalho <?php if($order == 2) echo 'CabecalhoSelecionado'; ?>">MODELO</a></th>
                  <th><a href="index.php?cboMarca=<?= $marca?>&cboModelo=<?= $modelo ?>&txtDescricao=<?= $descricao ?>&cboAcessorio=<?= $acessorio ?>&txtPrecoMin=<?= $precoMin ?>&txtPrecoMax=<?= $precoMax ?>&btnBuscar=true&offset=0&order=3&ascdesc=<?= $ascdesc ?>" class="Cabecalho <?php if($order == 3) echo 'CabecalhoSelecionado'; ?>">DESCRIÇÃO</a></th>
                  <th><a href="index.php?cboMarca=<?= $marca?>&cboModelo=<?= $modelo ?>&txtDescricao=<?= $descricao ?>&cboAcessorio=<?= $acessorio ?>&txtPrecoMin=<?= $precoMin ?>&txtPrecoMax=<?= $precoMax ?>&btnBuscar=true&offset=0&order=4&ascdesc=<?= $ascdesc ?>" class="Cabecalho <?php if($order == 4) echo 'CabecalhoSelecionado'; ?>">ACESSÓRIO</a></th>
                  <th><a href="index.php?cboMarca=<?= $marca?>&cboModelo=<?= $modelo ?>&txtDescricao=<?= $descricao ?>&cboAcessorio=<?= $acessorio ?>&txtPrecoMin=<?= $precoMin ?>&txtPrecoMax=<?= $precoMax ?>&btnBuscar=true&offset=0&order=5&ascdesc=<?= $ascdesc ?>" class="Cabecalho <?php if($order == 5) echo 'CabecalhoSelecionado'; ?>">PREÇO</a></th>
                </thead>
                <tfoot id="TabelaPrecosCarrosRodape" class="TabelaPrecosCarrosRodape">
                  <td colspan="6" align="center"><a href="index.php?cboMarca=<?= $marca?>&cboModelo=<?= $modelo ?>&txtDescricao=<?= $descricao ?>&cboAcessorio=<?= $acessorio ?>&txtPrecoMin=<?= $precoMin ?>&txtPrecoMax=<?= $precoMax ?>&btnBuscar=true&offset=<?= intval($offset)-50 ?>&order=<?= $order ?>" class="Rodape">Anterior</a> | <a href="index.php?cboMarca=<?= $marca?>&cboModelo=<?= $modelo ?>&txtDescricao=<?= $descricao ?>&cboAcessorio=<?= $acessorio ?>&txtPrecoMin=<?= $precoMin ?>&txtPrecoMax=<?= $precoMax ?>&btnBuscar=true&offset=<?= intval($offset)+50 ?>&order=<?= $order ?>" class="Rodape">Próximo</a></td>
                </tfoot>
                <tbody id="TabelaPrecosCarrosConteudo" class="TabelaPrecosCarrosConteudo">
                  <!-- Conteúdo da Tabela -->
      <?php
                    while ($item = mysqli_fetch_array($itensCarro)) {
      ?>
                      <tr>
                        <td> <?= $item["CODIGO"] ?> </td>
                        <td> <?= $item["MARCA"] ?> </td>
                        <td> <?= $item["MODELO"] ?> </td>
                        <td> <?= $item["DESCRICAO"] ?> </td>
                        <td> <?= $item["ACESSORIO"] ?> </td>
                        <td> <?= $item["PRECO"] ?> </td>
                      </tr>
      <?php
                    }
      ?>
                </tbody>
              </table>
              <?php
            }
          }
        }
      ?>
    </div> <!-- Fim MostrarDetalhes -->
    </div> <!-- Fim CarrosResultado -->

  </div>
</body>
</html>

<?php
  DesconectarDB($conn);
?>

<!--
Referências:
https://diveintohtml5.com.br/forms.html
http://www.maujor.com/tutorial/centralizando-com-css-um-guia-completo.php

http://www.guj.com.br/t/resolvido-cancelar-submit-de-formulario/300942/3
http://www.w3schools.com/jsref/event_onsubmit.asp

http://www.devmedia.com.br/php-forms-manipulando-dados-de-formularios/29392
http://phpbrasil.com/artigo/qaM3e7Dhtpsk/criando-um-formulario-de-busca-complexo-com-php-e-mysql
http://www.joseanmatias.com.br/preenchendo-um-selectcombobox-com-dados-do-mysql
https://www.gigasystems.com.br/artigo/29/preenchendo-um-select-em-html-dinamicamente-com-banco-de-dados

http://www.dellanio.com/diferenca-entre-comandos-ddl-dml-dcl-e-tcl/
https://www.tutorialspoint.com/mysql/mysql-data-types.htm
https://developer.mozilla.org/pt-BR/docs/Web/API/Element/classList
-->
