var $_frmCarrosBusca;
var $_cboMarca, $_cboModelo, $_cboAcessorio, $_txtDescricao, $_txtPrecoMin, $_txtPrecoMax;

function Inicializar () {
  $_cboMarca = parseInt(document.querySelector("#cboMarca").value);
  $_cboModelo = parseInt(document.querySelector("#cboModelo").value);
  $_cboAcessorio = parseInt(document.querySelector("#cboAcessorio").value);
  $_txtDescricao = document.querySelector("#txtDescricao").value;
  $_txtPrecoMin = parseInt(document.querySelector("#txtPrecoMin").value);
  $_txtPrecoMax = parseInt(document.querySelector("#txtPrecoMax").value);
}

function ValidarCampos (evt) {
  Inicializar();
  if (!$_cboMarca && !$_cboModelo && !$_cboAcessorio && $_txtDescricao == "" && (!$_txtPrecoMin || $_txtPrecoMin == "") && (!$_txtPrecoMax || $_txtPrecoMax == "") ) {
    alert("Favor preencher algum campo para a pesquisa!!!");
    return false;
  }

  if ($_txtPrecoMin > $_txtPrecoMax) {
    alert("O Preço Máximo deve ser maior que o Mínimo!!!");
    return false;
  }
  return true;
}

function myAjax() {
      marca = $('#cboMarca option:selected').attr('value');
      modelo = $('#cboModelo option:selected').attr('value');
      acessorio = $('#cboAcessorio option:selected').attr('value');
      lista_tags = [];
      $.ajax({
           type: "POST",
           url: 'livesearch.php',
           dataType: 'json',
           data:{marca, modelo, acessorio},
           success:function(html) {
            htmlmarca = '<option value="0">Selecione</option>';
            htmlmodelo = '<option value="0">Selecione</option>';
            htmlacessorio = '<option value="0">Selecione</option>';
            marcas = html['marcas'];
            modelos = html['modelos'];
            acessorios = html['acessorio'];
            tags = html['tags'];
            for (i = 0; i < marcas.length; i++) {
                marca_i = marcas[i];
                if(parseInt(marca_i['id_marca']) == marca){
                  htmlmarca = htmlmarca + "<option value='"+marca_i['id_marca']+"'"+"selected='selected'>"+marca_i['marca']+"</option>";
                }
                else{
                  htmlmarca = htmlmarca + "<option value='"+marca_i['id_marca']+"'>"+marca_i['marca']+"</option>";
                }

            }
            for (i = 0; i < modelos.length; i++) {
                modelos_i = modelos[i];
                if(parseInt(modelos_i['id_modelo']) == modelo){
                  htmlmodelo = htmlmodelo + "<option value='"+modelos_i['id_modelo']+"'"+"selected='selected'>"+modelos_i['modelo']+"</option>";

                }
                else{
                  htmlmodelo = htmlmodelo + "<option value='"+modelos_i['id_modelo']+"'>"+modelos_i['modelo']+"</option>";
                }
            }
            for (i = 0; i < acessorios.length; i++) {
                acessorio_i = acessorios[i];
                if(parseInt(acessorio_i['id_acessorio']) == acessorio){
                  htmlacessorio = htmlacessorio + "<option value='"+acessorio_i['id_acessorio']+"'"+"selected='selected'>"+acessorio_i['acessorio']+"</option>";
                }
                else{
                  htmlacessorio = htmlacessorio + "<option value='"+acessorio_i['id_acessorio']+"'>"+acessorio_i['acessorio']+"</option>";
                }
            }
             $('#cboMarca').html(htmlmarca);
             $('#cboModelo').html(htmlmodelo);
             $('#cboAcessorio').html(htmlacessorio);
             $('#txtPrecoMin').attr('value', ((html['precos'])[0])['preco_min']);
             $('#txtPrecoMin').attr('min', ((html['precos'])[0])['preco_min']);
             $('#txtPrecoMin').attr('max', ((html['precos'])[0])['preco_max']);
             $('#txtPrecoMax').attr('value', ((html['precos'])[0])['preco_max']);
             $('#txtPrecoMax').attr('min', ((html['precos'])[0])['preco_min']);
             $('#txtPrecoMax').attr('max', ((html['precos'])[0])['preco_max']);
             for (i = 0; i < tags.length; i++) {
              lista_tags.push(tags[i]);
             }
             $("#txtDescricao").autocomplete({source: lista_tags});
             //ajaxGrafico();
           }

      });
 }

 function EsconderTabela() {
   $('#MostrarDetalhes').hide();
 }

 function ajaxGrafico() {
      marca = $('#cboMarca option:selected').attr('value');
      modelo = $('#cboModelo option:selected').attr('value');
      acessorio = $('#cboAcessorio option:selected').attr('value');
      descricao =  $("#txtDescricao").attr('value');
      max = $('#txtPrecoMax').attr('value');
      min = $('#txtPrecoMin').attr('value');
      ordem = $('#ascdesc option:selected').attr('value');
      labels_list = [];
      precos_list = [];
      $.ajax({
           type: "POST",
           url: 'graph.php',
           dataType: 'json',
           data:{marca, modelo, acessorio, descricao, acessorio, max, min},
           success:function(html) {
            $("#container").html('<canvas id="canvas"></canvas>');
            $("#container").show();
            retorno_lista = html['retorno'];
            titulo = html['titulo'];
            for (i = 0; i < retorno_lista.length; i++) {
              if(i == parseInt(ordem)){
                retorno = (retorno_lista[i])['valores'];
                dataset_retorno = (retorno_lista[i])['dataset'];
                for (i = 0; i < retorno.length; i++) {
                  ret = retorno[i];
                  label = ret['label'];
                  preco = ret['preco'];
                  labels_list.push(label);
                  precos_list.push(preco);
                }
              }
            }

            var color = Chart.helpers.color;
            var barChartData = {
                labels: labels_list,
                datasets: [{
                    label: dataset_retorno,
                    backgroundColor: color("rgb(0,0,99)").alpha(0.5).rgbString(),
                    borderColor: "rgb(0,0,99)",
                    borderWidth: 1,
                    data: precos_list
                }]

            };
            var ctx = document.getElementById("canvas").getContext("2d");
            window.myBar = new Chart(ctx, {
                type: 'bar',
                data: barChartData,
                options: {
                    responsive: true,
                    legend: {
                        position: 'top',
                    },
                    title: {
                        display: true,
                        text: titulo
                    }
                }
            });
            window.myBar.update();
           }

      });
 }

window.onload = function() {
  if(parseInt($("#foisubmit").attr("value")) == 1){
    ajaxGrafico();
  }
};
