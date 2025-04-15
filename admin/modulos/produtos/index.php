<!--CSS-->
<script>abreLoader();</script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.3/css/bootstrap.css">
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.21/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" href="modulos/produtos/css/style.css">
                  
<?php 

//PEGA OS DADOS DO USUÁRIO ATUAL NA SESSION
$nivel_usuario         = filter_var($_SESSION['nivel']);
$identificador_usuario = filter_var($_SESSION['identificador']);

//BUSCA TODAS OS PRODUTOS CADASTRADOS
$produtos = mysqli_query($conn, 'SELECT p.id, p.status, p.identificador, p.nome, p.sku, p.estoque, p.promocao, p.relevancia, p.sku,   
(SELECT pi.imagem FROM produto_imagem AS pi WHERE pi.id_produto = p.id AND pi.capa = 1) AS capa, 
(SELECT pc.nome FROM categoria AS pc WHERE pc.id = p.id_categoria) AS categoria,  
(SELECT pm.nome FROM marca AS pm WHERE pm.id = p.id_marca) AS marca
FROM produto AS p 
WHERE p.status != 2
ORDER BY p.nome ASC'); 

//LIMPA O DIRETÓRIO DE IMAGENS TEMPORÁRIAS AO INICIAR A PÁGINA
$pasta = "modulos/produtos/arquivos/temp/".$_SESSION['identificador'].'/';
if(is_dir($pasta)){
    $diretorio = dir($pasta);
    while($arquivo = $diretorio->read()){
        if(($arquivo != '.') && ($arquivo != '..')){
            unlink($pasta.$arquivo);
        }
    }
}

//VERIFICA SE TEM PROMOÇÃO VENCIDA E DESATIVA
$busca_produtos_promocao = mysqli_query($conn, "SELECT produto.id AS id_produto, promocao.id AS id_promocao, validade, promocao.status FROM produto INNER JOIN promocao ON produto.id = promocao.id_produto WHERE promocao = 1 AND validade < DATE(NOW())");
while($produtos_promocao = mysqli_fetch_array($busca_produtos_promocao)){
	if((strtotime($produtos_promocao['validade']) < strtotime(date('Y-m-d'))) & $produtos_promocao['status'] == 1){

		//ALTERA O STATUS DA PROMOÇÃO NO PRODUTO
		mysqli_query($conn, "UPDATE produto SET promocao = 0 WHERE id = ".$produtos_promocao["id_produto"]);

		//ENCERRA A PROMOÇÃO
		mysqli_query($conn, "UPDATE promocao SET data_desativacao = NOW(), status = 0 WHERE id = ".$produtos_promocao['id_promocao']);

	}
}

    
//FUNÇÃO QUE ACERTA O NOME DO PRODUTO OU CATEGORIA PARA URL
function urlProduto($nome){    
    if($nome != ''){
        $caracteres_proibidos_url = array('(',')','.',',','+','%','$','@','!','#','*','[',']','{','}','?',';',':','|','<','>','=','ª','º','°','§','¹','²','³','£','¢','¬');
        $caracteres_por_espaco    = array(' - ');
        $caracteres_por_hifen     = array(' ','/','#39;','#34;');
        return mb_strtolower(str_replace('--','-',str_replace($caracteres_proibidos_url,'', str_replace($caracteres_por_hifen,'-', str_replace($caracteres_por_espaco,' ', preg_replace("/&([a-z])[a-z]+;/i", "$1", htmlentities(trim(preg_replace('/(\'|")/', "-", $nome)))))))));
    } else {
        return "categoria";
    }
}

function removeAspas($nome){
    return str_replace('&','',str_replace(array('#39;','#34;'), '',$nome));
}

?>

<!--SECTION PRODUTOS-->
<section id="produtos">

    <div class="container-fluid">

        <!-- ROW DO TÍTULO -->
        <div class="row">
            <div class="col-7">    
                <div id="admin-titulo-pagina">Produtos</div>
            </div>
            <div class="col-5 text-right">
                <button type="button" class="btn btn-dark btn-top-right" onclick="javascript: window.location.href = 'produtos-cadastra.php';">NOVO PRODUTO</button>
            </div>
        </div>

        <!-- ROW DA TABELA -->
        <div class="row">
            <div class="col-12">   
                <table id="admin-lista" class="table table-hover">
                    <thead>
                        <tr>
                            <th scope="col"></th>
                            <th scope="col" class="d-none d-md-table-cell">CAPA</th>
                            <th scope="col" class="d-none d-md-table-cell">SKU</th>
                            <th scope="col">NOME</th>
                            <th scope="col" class="d-none d-md-table-cell">CATEGORIA</th>
                            <th scope="col" class="d-none d-md-table-cell">MARCA</th>
                            <th scope="col" class="d-none d-md-table-cell">ESTOQUE</th>   
                            <th scope="col" class="d-none d-md-table-cell">RELEVÂNCIA</th>                          
                            <th scope="col" class="<?php if($modo_whatsapp){ if($loja['modo_whatsapp_preco'] == 0){ echo 'd-none'; } } else { echo 'd-none d-md-table-cell'; } ?> text-right">PROMOÇÃO</th>                            
                            <th scope="col" class="text-right">AÇÕES</th>
                        </tr>
                    </thead>
                    <tbody>      
                        <?php while($produto = mysqli_fetch_array($produtos)){ ?>  
                            <tr id="produto-<?= $produto['identificador'] ?>" class="cursor-pointer" title="Editar" onclick="javascript: edita('<?= $produto['identificador'] ?>');">
                                <?php if($produto['status'] == 1){ ?>  
                                    <td class="text-left align-middle" id="status-<?= $produto['identificador'] ?>"><a class="botao-status" href="javascript: trocaStatus('<?= $produto['identificador'] ?>',<?= $produto['status'] ?>)" title="Desativar"><img class="status-ativado" src="<?= $loja['site'] ?>imagens/status-ativo.png" alt="Ativo"></a><span class="d-none">ligado on ativo 1</span></td>
                                <?php } else if($produto['status'] == 0){ ?>     
                                    <td class="text-left align-middle" id="status-<?= $produto['identificador'] ?>"><a class="botao-status" href="javascript: trocaStatus('<?= $produto['identificador'] ?>',<?= $produto['status'] ?>)" title="Ativar"><img class="status-desativado" src="<?= $loja['site'] ?>imagens/status-inativo.png" alt="Inativo"></a><span class="d-none">desligado off inativo 0</span></td>
                                <?php } ?> 
                                <td class="tabela-imagem-miniatura text-capitalize d-none d-md-table-cell"><img src="<?php if($produto['capa'] != ''){ ?><?= $loja['site'] ?>imagens/produtos/pequena/<?= $produto['capa'] ?><?php } else { ?><?= $loja['site'] ?>imagens/produto_sem_foto.png<?php } ?>" alt="<?= $produto['nome'] ?>"></td>
                                <td class="text-capitalize align-middle d-none d-md-table-cell table-row-sku"><?= $produto['sku'] ?></td>
                                <td class="text-capitalize align-middle"><a class="admin-lista-nome-produto" href="<?= $loja['site'] ?>produto/<?= urlProduto($produto['categoria']) ?>/<?= urlProduto($produto['nome']) ?>/<?= $produto['id'] ?>" target="_blank"><?= $produto['nome'] ?></a></td>
                                <td class="text-capitalize align-middle d-none d-md-table-cell"><?= $produto['categoria'] ?></td>
                                <td class="text-capitalize align-middle d-none d-md-table-cell"><?= $produto['marca'] ?></td>
                                <td class="text-capitalize align-middle d-none d-md-table-cell"><?= $produto['estoque'] ?></td>  
                                <td class="text-capitalize align-middle d-none d-md-table-cell"><?= $produto['relevancia'] ?></td>                 
                                <td class="text-capitalize align-middle <?php if($modo_whatsapp){ if($loja['modo_whatsapp_preco'] == 0){ echo 'd-none'; } } else { echo 'd-none d-md-table-cell'; } ?>"><a id="promocao-<?= $produto['identificador'] ?>" class="float-right btn-promocao <?php if($produto['promocao'] == 1){ echo 'promocao-ativada'; } ?>" href="javascript: promocaoProduto('<?= $produto['identificador'] ?>','<?= removeAspas($produto['nome']) ?>','<?= $produto['promocao'] ?>');" title="<?php if($produto['promocao'] == 1){ echo 'Ver'; } else { echo 'Ativar'; } ?> promoção"><?php if($produto['promocao'] == 1){ echo 'Ver'; } else { echo 'Ativar'; } ?> Promoção</a><span class="d-none"><?php if($produto['promocao'] == 1){ echo 'ligado on ativo 1'; } else { echo 'desligado off inativo 0'; } ?></span></td>
                                <td class="text-right align-middle"><a class="botao-excluir" href="javascript: exclui('<?= $produto['identificador'] ?>','<?= removeAspas($produto['nome']) ?>')" title="Excluir"><img class="acao-excluir" src="<?= $loja['site'] ?>imagens/acao-excluir.png" alt="Excluir"></a></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>

</section>

<!-- MODAL ADIÇÃO DE PROMOÇÃO -->
<div class="modal fade" id="modal-add-promocao" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered  modal-sm" role="document">
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLongTitle">Promoção</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">
            <input type="hidden" id="identificador-produto">
            <div class="row mb-2">
                <div class="col-12">
                    Produto: <span id="nome-produto"></span>
                </div>
            </div>
            <div class="row">
                <div class="col-12 mb-2 mt-3">
                    <div class="custom-control custom-radio custom-control-inline">
                        <input type="radio" id="opcao-porcentagem" name="desconto-opcao" class="custom-control-input" value="P" checked>
                        <label class="custom-control-label" for="opcao-porcentagem">Porcentagem</label>
                    </div>
                    <div class="custom-control custom-radio custom-control-inline">
                        <input type="radio" id="opcao-valor" name="desconto-opcao" class="custom-control-input" value="V">
                        <label class="custom-control-label" for="opcao-valor">Valor</label>
                    </div>
                </div>
                <div class="col-12">
                    <div class="form-group">
                        <input type="number" id="porcentagem-desconto" class="form-control">
                    </div>
                </div>
                <div class="col-12">
                    <div class="form-group">
                        <label for="validade">Validade</label>
                        <input type="text" id="validade" class="form-control">
                        <small>Formato: dd/mm/aaaa</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-dark btn-desativa-promocao d-none" onclick="javascript: promocaoProduto($('#identificador-produto').val(),$('#nome-produto').html(),2);">Encerrar</button>
            <button type="button" class="btn btn-dark btn-edita-promocao d-none" onclick="javascript: alterarStatusPromocaoProduto(2,$('#identificador-produto').val());">Editar</button>
            <button type="button" class="btn btn-dark btn-cadastra-promocao" onclick="javascript: alterarStatusPromocaoProduto(1,'');">Ativar</button>
        </div>
        </div>
    </div>
</div>

<!--SCRIPTS-->
<script type="text/javascript" src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/1.10.21/js/dataTables.bootstrap4.min.js"></script>
<script type="text/javascript" src="modulos/crons/js/scripts.js"></script>
<script type="text/javascript" src="modulos/produtos/js/scripts.js"></script>