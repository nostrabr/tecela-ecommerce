<?php

//INICIA A SESSÃO
session_start();

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

$tipo      = trim(strip_tags(filter_input(INPUT_POST, "tipo", FILTER_SANITIZE_STRING)));   
$id        = trim(strip_tags(filter_input(INPUT_POST, "id", FILTER_SANITIZE_NUMBER_INT)));   
$visitante = filter_var($_SESSION['visitante']);
$html      = '';

if(!empty($tipo) & !empty($id) & mb_strlen($visitante) == 32){

    include_once '../../../bd/conecta.php';

    $busca_loja = mysqli_query($conn, "SELECT nome, site FROM loja WHERE id = 1");
    $loja       = mysqli_fetch_array($busca_loja);

    if($tipo == 'L'){

        $busca_avaliacoes = mysqli_query($conn, "
            SELECT a.*, c.nome AS nome_cliente, c.sobrenome AS sobrenome_cliente
            FROM avaliacao AS a
            LEFT JOIN pedido AS p ON p.id = a.id_pedido
            LEFT JOIN cliente AS c ON c.id = p.id_cliente
            WHERE a.tipo = 'EXPERIENCIA-COMPRA' AND a.status = 1 AND a.mostrar_avaliacao = 1 AND a.id < '$id'
            ORDER BY a.id DESC
            LIMIT 5
        ");

        if(mysqli_num_rows($busca_avaliacoes) > 0){
            
            while($avaliacao_site = mysqli_fetch_array($busca_avaliacoes)){
                
                $html_replica = '';
                if($avaliacao_site['replica'] != ''){
                    $html_replica = 
                    '<li class="site-avaliacoes-avaliacao-replica-titulo">'.$loja['nome'].' respondeu:</li>
                    <li class="site-avaliacoes-avaliacao-replica">'.$avaliacao_site['replica'].'</li>
                    <li class="site-avaliacoes-avaliacao-replica-data">'.date('d/m/Y H:i', strtotime($avaliacao_site['data_replica'])).'</li>';
                }

                $html .= '
                <div id="'.$avaliacao_site['id'].'" class="row">
                    <div class="col-12">
                        <div class="site-avaliacoes-avaliacao">
                            <ul>
                                <li class="site-avaliacoes-avaliacao-estrelas avaliacao-loja">
                                    <span class="d-none">Nota '.$avaliacao_site['nota'].'</span>                                         
                                    <ul>';
                                        if($avaliacao_site['nota'] >= 1){ $html .= '<li><img class="estrela estrela-preta" estrela="1" id="estrela-1" src="imagens/avaliacao-estrela.png" title="Muito ruim" alt="1 estrela"></li>'; }
                                        if($avaliacao_site['nota'] >= 2){ $html .= '<li><img class="estrela estrela-preta" estrela="2" id="estrela-2" src="imagens/avaliacao-estrela.png" title="Ruim" alt="2 estrelas"></li>'; }
                                        if($avaliacao_site['nota'] >= 3){ $html .= '<li><img class="estrela estrela-preta" estrela="3" id="estrela-3" src="imagens/avaliacao-estrela.png" title="Regular" alt="3 estrelas"></li>'; }
                                        if($avaliacao_site['nota'] >= 4){ $html .= '<li><img class="estrela estrela-preta" estrela="4" id="estrela-4" src="imagens/avaliacao-estrela.png" title="Boa" alt="4 estrelas"></li>'; }
                                        if($avaliacao_site['nota'] >= 5){ $html .= '<li><img class="estrela estrela-preta" estrela="5" id="estrela-5" src="imagens/avaliacao-estrela.png" title="Muito boa" alt="5 estrelas"></li>'; }                    
                            $html .= '</ul>
                                </li>
                                <li class="site-avaliacoes-avaliacao-cliente">'.$avaliacao_site['nome_cliente'].' '.substr($avaliacao_site['sobrenome_cliente'], 0, 1).'.</li>
                                <li class="site-avaliacoes-avaliacao-produto">Produto: '.$avaliacao_site['produto_nome'].'</li>
                                <li class="site-avaliacoes-avaliacao-data"><i>Data: '.date('d/m/Y', strtotime($avaliacao_site['data_cadastro'])).'</i></li>
                                <li class="site-avaliacoes-avaliacao-comentario">'.$avaliacao_site['comentario'].'</li>
                                '.$html_replica.'
                            </ul>
                        </div>
                    </div>
                </div>';

                $ultimo_id = $avaliacao_site['id'];

            }

            $busca_avaliacoes_loja_aux = mysqli_query($conn, "
                SELECT a.id
                FROM avaliacao AS a
                WHERE a.tipo = 'EXPERIENCIA-COMPRA' AND a.status = 1 AND a.mostrar_avaliacao = 1 AND a.id < $ultimo_id
                ORDER BY a.id DESC
            ");   
                           
            if(mysqli_num_rows($busca_avaliacoes_loja_aux) > 0){
                $tem = true;
            } else {
                $tem = false;
            }

            //RETORNA PRO AJAX QUE A SESSÃO É INVÁLIDA
            $dados[] = array(
                "status" => "SUCESSO",
                "html"   => $html,
                'ultimo' => $ultimo_id,
                'tem'    => $tem
            );
            echo json_encode($dados);

        } else {
            
            //RETORNA PRO AJAX QUE A SESSÃO É INVÁLIDA
            $dados[] = array(
                "status" => "ACABOU"
            );
            echo json_encode($dados);

        }

    } else if($tipo == 'P'){

        $busca_avaliacoes = mysqli_query($conn, "
            SELECT a.*, c.nome AS nome_cliente, c.sobrenome AS sobrenome_cliente, pd.nome AS produto_nome, pd.id AS produto_id, pc.nome AS produto_categoria
            FROM avaliacao AS a
            LEFT JOIN pedido AS p ON p.id = a.id_pedido
            LEFT JOIN cliente AS c ON c.id = p.id_cliente
            LEFT JOIN produto AS pd ON pd.id = a.id_produto
            LEFT JOIN categoria AS pc ON pc.id = pd.id_categoria
            WHERE a.tipo = 'PRODUTO' AND a.status = 1 AND a.mostrar_avaliacao = 1 AND a.id < '$id'
            ORDER BY a.id DESC
            LIMIT 5
        ");

        if(mysqli_num_rows($busca_avaliacoes) > 0){

            while($avaliacao_produto = mysqli_fetch_array($busca_avaliacoes)){

                $html_replica = '';
                if($avaliacao_produto['replica'] != ''){
                    $html_replica = 
                    '<li class="site-avaliacoes-avaliacao-replica-titulo">'.$loja['nome'].' respondeu:</li>
                    <li class="site-avaliacoes-avaliacao-replica">'.$avaliacao_produto['replica'].'</li>
                    <li class="site-avaliacoes-avaliacao-replica-data">'.date('d/m/Y H:i', strtotime($avaliacao_produto['data_replica'])).'</li>';
                }

                $html .= '
                <div id="'.$avaliacao_produto['id'].'" class="row">
                    <div class="col-12">
                        <div class="site-avaliacoes-avaliacao">
                            <ul>
                                <li class="site-avaliacoes-avaliacao-estrelas avaliacao-loja">
                                    <span class="d-none">Nota '.$avaliacao_produto['nota'].'</span>                                         
                                    <ul>';
                                        if($avaliacao_produto['nota'] >= 1){ $html .= '<li><img class="estrela estrela-preta" estrela="1" id="estrela-1" src="imagens/avaliacao-estrela.png" title="Muito ruim" alt="1 estrela"></li>'; }
                                        if($avaliacao_produto['nota'] >= 2){ $html .= '<li><img class="estrela estrela-preta" estrela="2" id="estrela-2" src="imagens/avaliacao-estrela.png" title="Ruim" alt="2 estrelas"></li>'; }
                                        if($avaliacao_produto['nota'] >= 3){ $html .= '<li><img class="estrela estrela-preta" estrela="3" id="estrela-3" src="imagens/avaliacao-estrela.png" title="Regular" alt="3 estrelas"></li>'; }
                                        if($avaliacao_produto['nota'] >= 4){ $html .= '<li><img class="estrela estrela-preta" estrela="4" id="estrela-4" src="imagens/avaliacao-estrela.png" title="Boa" alt="4 estrelas"></li>'; }
                                        if($avaliacao_produto['nota'] >= 5){ $html .= '<li><img class="estrela estrela-preta" estrela="5" id="estrela-5" src="imagens/avaliacao-estrela.png" title="Muito boa" alt="5 estrelas"></li>'; }                    
                            $html .= '</ul>
                                </li>
                                <li class="site-avaliacoes-avaliacao-cliente">'.$avaliacao_produto['nome_cliente'].' '.substr($avaliacao_produto['sobrenome_cliente'], 0, 1).'.</li>
                                <li class="site-avaliacoes-avaliacao-produto">Produto: <a href="'.$loja['site'].'produto/'.urlProduto($avaliacao_produto['produto_categoria']).'/'.urlProduto($avaliacao_produto['produto_nome']).'/'.$avaliacao_produto['produto_id'].'" target="_blank">'.$avaliacao_produto['produto_nome'].'</a></li>
                                <li class="site-avaliacoes-avaliacao-data"><i>Data: '.date('d/m/Y', strtotime($avaliacao_produto['data_cadastro'])).'</i></li>
                                <li class="site-avaliacoes-avaliacao-comentario">'.$avaliacao_produto['comentario'].'</li>
                                '.$html_replica.'
                            </ul>
                        </div>
                    </div>
                </div>';

                $ultimo_id = $avaliacao_produto['id'];

            }
            
            $busca_avaliacoes_produtos_aux = mysqli_query($conn, "
                SELECT a.id
                FROM avaliacao AS a
                WHERE a.tipo = 'PRODUTO' AND a.status = 1 AND a.mostrar_avaliacao = 1 AND a.id < $ultimo_id
                ORDER BY a.id DESC
            ");

            if(mysqli_num_rows($busca_avaliacoes_produtos_aux) > 0){
                $tem = true;
            } else {
                $tem = false;
            }

            //RETORNA PRO AJAX QUE A SESSÃO É INVÁLIDA
            $dados[] = array(
                "status" => "SUCESSO",
                "html"   => $html,
                'ultimo' => $ultimo_id,
                'tem'    => $tem
            );
            echo json_encode($dados);
            

        } else {
            
            //RETORNA PRO AJAX QUE A SESSÃO É INVÁLIDA
            $dados[] = array(
                "status" => "ACABOU"
            );
            echo json_encode($dados);

        }

    } else {

        //RETORNA PRO AJAX QUE A SESSÃO É INVÁLIDA
        $dados[] = array(
            "status" => "ERRO"
        );
        echo json_encode($dados);

    }
    
    include_once '../../../bd/desconecta.php';

} else {

    //VERIFICA SE VEIO DO AJAX
    if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                
        //RETORNA PRO AJAX QUE A SESSÃO É INVÁLIDA
        $dados[] = array(
            "status" => "ERRO"
        );
        echo json_encode($dados);
        
    } else {
        
        //REDIRECIONA PARA A TELA DE LOGIN
        echo "<script>location.href='/';</script>";
        
    }

}
