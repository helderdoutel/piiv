<!DOCTYPE html>

<?php
  $marca = $_POST["cboMarca"];
  $modelo = $_POST["cboModelo"];
  $descricao = $_POST["txtDescricao"];
  $acessorio = $_POST["cboAcessorio"];
  $precoMin = $_POST["txtPrecoMin"];
  $precoMax = $_POST["txtPrecoMax"];
?>

<html lang="pt">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <title>Resultado da Busca</title>
  <body>
    <h1>Dados de Entrada</h1>
    <label> <b>Marca: </b><?= $marca ?> </label>

    <br>

    <label> <b>Modelo: </b> <?= $modelo ?> </label>

    <br>

    <label> <b>Descrição: </b> <?= $descricao ?> </label>

    <br>

    <label> <b>Acessório: </b> <?= $acessorio ?> </label>

    <br>

    <label> <b>Preço Mínimo: </b> <?= $precoMin ?> </label>

    <br>

    <label> <b>Preço Máximo: </b> <?= $precoMax ?> </label>
  </body>
</html>

<?php
  foreach($_POST as $campo => $valor){
    unset($_POST[$campo]);
  }
?>
