<?php 

//INICIA A SESSION
session_start();

//CONECTA AO BANCO
include_once '../../../../bd/conecta.php';

//RECEBE OS DADOS
$tipo_frete             = trim(strip_tags(filter_input(INPUT_POST, 'tipo-frete', FILTER_SANITIZE_STRING)));
$endereco               = trim(strip_tags(filter_input(INPUT_POST, 'endereco', FILTER_SANITIZE_STRING)));
$identificador_carrinho = filter_var($_SESSION['visitante']);
$identificador_cliente  = filter_var($_SESSION['identificador']);

//VERIFICA SE TODOS OS DADOS OBRIGATÓRIOS VIERAM PREENCHIDOS
if(!empty($tipo_frete) & !empty($endereco) & !empty($identificador_carrinho) & !empty($identificador_cliente)){

    //BUSCA CARRINHO
    $busca_carrinho = mysqli_query($conn, "SELECT id FROM carrinho WHERE identificador = '$identificador_carrinho'");
    $carrinho       = mysqli_fetch_array($busca_carrinho);

    if(mysqli_num_rows($busca_carrinho) > 0){

        //BUSCA PRODUTOS DO CARRINHO
        $busca_produtos_carrinho = mysqli_query($conn, "SELECT cp.*, p.nome FROM carrinho_produto AS cp INNER JOIN produto AS p ON cp.id_produto = p.id WHERE cp.status = 1 AND id_carrinho = ".$carrinho['id']);

        //BUSCA CLIENTE
        $busca_cliente           = mysqli_query($conn, "SELECT * FROM cliente WHERE identificador = '$identificador_cliente'");
        $cliente                 = mysqli_fetch_array($busca_cliente);
        
        //BUSCA CLIENTE
        $busca_endereco          = mysqli_query($conn, "SELECT ce.*, cd.nome AS nome_cidade, e.sigla AS sigla_estado FROM cliente_endereco AS ce INNER JOIN cidade AS cd ON ce.cidade = cd.id INNER JOIN estado AS e ON ce.estado = e.id WHERE ce.identificador = '$endereco' AND ce.id_cliente = ".$cliente['id']);
        $endereco                = mysqli_fetch_array($busca_endereco);

        //ESTANCIA A VARIÁVEL DO TEXTO QUE VAI PRO WHATS
        $texto_whatsapp = 'Olá!%0AMe chamo '.$cliente['nome'].' '.$cliente['sobrenome'].' e gostaria de um orçamento para a lista de produtos abaixo:%0A%0A';

        //BUSCA OS DADOS DA LOJA
        $busca_loja = mysqli_query($conn, "SELECT whatsapp FROM loja WHERE id = 1");
        $loja       = mysqli_fetch_array($busca_loja);
        
        if(mysqli_num_rows($busca_produtos_carrinho) > 0 & mysqli_num_rows($busca_cliente) > 0 & mysqli_num_rows($busca_endereco) > 0){
            
            //FUNÇÃO PRA GERAR CÓDIGOS ALEATÓRIOS
            function geraHash($tamanho = 8, $minusculas = true, $maiusculas = true, $numeros = true, $simbolos = true){

                $lmin = 'abcdefghijklmnopqrstuvwxyz';
                $lmai = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
                $num = '1234567890';
                $simb = '!@#$%*-';
                $retorno = '';
                $caracteres = '';

                if ($minusculas) $caracteres .= $lmin;
                if ($maiusculas) $caracteres .= $lmai;
                if ($numeros) $caracteres .= $num;
                if ($simbolos) $caracteres .= $simb;

                $len = strlen($caracteres);
                for ($n = 1; $n <= $tamanho; $n++) {
                    $rand = mt_rand(1, $len);
                    $retorno .= $caracteres[$rand-1];
                }

                return $retorno;

            }
            
            //GERA UM IDENTIFICADOR PARA O ORCAMENTO
            $identificador_orcamento = md5(date('Y-m-d H:i:s').$cliente['id'].$carrinho['id']);

            //GUARDA O ENDEREÇO POR EXTENSO DO LOCAL DA ENTREGA
            $endereco_extenso = $endereco['logradouro'].', '.$endereco['numero'];
            if($endereco['complemento'] != ''){ $endereco_extenso .= ' - '.$endereco['complemento']; }
            $endereco_extenso .= ' - '.$endereco['bairro'].'%0A';
            $endereco_extenso .= $endereco['nome_cidade'].'/'.$endereco['sigla_estado'].'%0A';
            $endereco_extenso .= 'CEP: '.$endereco['cep'];
            if($endereco['referencia'] != ''){ $endereco_extenso .= '%0A'.$endereco['referencia']; }       

            //ESTANCIA VARIÁVEIS PARA BUSCAR O FRETE E JÁ CALCULA O VALOR TOTAL DA COMPRA
            $frete_cep_destino = $endereco['cep'];
            $frete_quantidades = array();
            $frete_produtos    = array();
            $frete_tipo_frete  = $tipo_frete;
            $contador_produtos = 1;
            while($produtos_carrinho = mysqli_fetch_array($busca_produtos_carrinho)){
                
                $id_caracteristica_primaria   = '';
                $id_caracteristica_secundaria = '';
                $caracteristicas              = explode(',',$produtos_carrinho['ids_caracteristicas']);
                $n_caracteristicas            = count($caracteristicas);
                if($n_caracteristicas > 0){                                    
                    $produto_caracteristica_orcamento = '';
                    $sql_caracteristicas = '';
                    for($i = 0; $i < $n_caracteristicas; $i++){
                        if($i == 0){
                            $sql_caracteristicas .= "pc.id = ".$caracteristicas[$i];
                        } else {
                            $sql_caracteristicas .= " OR pc.id = ".$caracteristicas[$i];
                        }
                    }
                    $busca_caracteristicas = mysqli_query($conn, "
                        SELECT a.nome AS atributo_nome, c.nome AS caracteristica_nome, pc.id_caracteristica AS caracteristica_id 
                        FROM produto_caracteristica AS pc
                        INNER JOIN atributo AS a ON pc.id_atributo = a.id
                        INNER JOIN caracteristica AS c ON pc.id_caracteristica = c.id
                        WHERE ".$sql_caracteristicas
                    );
                    $contador_aux = 0;
                    while($caracteristica = mysqli_fetch_array($busca_caracteristicas)){
                        if($contador_aux == 0){
                            $id_caracteristica_primaria   = $caracteristica['caracteristica_id'];
                        } else if($contador_aux == 1){
                            $id_caracteristica_secundaria   = $caracteristica['caracteristica_id'];
                        }                                        
                        $produto_caracteristica_orcamento .= ' - '.$caracteristica['atributo_nome'].": ".$caracteristica['caracteristica_nome'];
                        $contador_aux++;
                    }
                    
                } else {
                    $produto_caracteristica_orcamento = '';
                }

                $texto_whatsapp .= $produtos_carrinho['quantidade'].'x - '.$produtos_carrinho['nome'].mb_strtoupper($produto_caracteristica_orcamento).'%0A';
                array_push($frete_quantidades, $produtos_carrinho['quantidade']);
                array_push($frete_produtos, $produtos_carrinho['id_produto']);
                $contador_produtos++;
            }
            
            //BUSCA O VALOR DO FRETE
            include_once '../../../frete/php/calcular_include.php';
            
            //PADRONIZA FRETE
            $valor_frete = str_replace(',', '.', $valor_frete);

            //INSERE O ORÇAMENTO NO BANCO
            mysqli_query($conn, "INSERT INTO orcamento (identificador, id_cliente, id_carrinho, endereco, valor_frete, tipo_frete) VALUES ('$identificador_orcamento','".$cliente['id']."','".$carrinho['id']."','$endereco_extenso','$valor_frete','$tipo_frete')");
            $id_orcamento = mysqli_insert_id($conn);

            //GERA UM CÓDIGO DE REFERÊNCIA PARA O PEDIDO
            $codigo_orcamento = geraHash(10,false,true,true,false);
            $codigo_orcamento = $codigo_orcamento.str_pad($id_orcamento, 5,'0',STR_PAD_LEFT);

            //ALTERA O CÓDIGO NO PEDIDO
            mysqli_query($conn, "UPDATE orcamento SET codigo = '$codigo_orcamento' WHERE id = ".$id_orcamento);

            //ALTERA A SESSION DO VISITANTE PARA NÃO GERAR CARRINHOS IGUAIS
            $_SESSION['visitante'] = md5(date("Y-m-d H:i:s").filter_input(INPUT_SERVER, "REMOTE_ADDR", FILTER_DEFAULT).filter_input(INPUT_SERVER, "HTTP_USER_AGENT", FILTER_DEFAULT));
            
            //INCREMENTA O ENDEREÇO NO TEXTO DO WHATS
            $texto_whatsapp .= '%0AEntrega em:%0A'.$endereco_extenso.'%0A%0ACotação do frete%0ATipo: '.mb_strtoupper($tipo_frete).'%0AValor: R$ '.str_replace('.', ',', $valor_frete).'%0A%0AORÇAMENTO: '.$codigo_orcamento;

        } else {
            
            //REDIRECIONA PARA A TELA DE LOGIN
            echo "<script>location.href='/';</script>";

        }

    } else {
            
        //REDIRECIONA PARA A TELA DE LOGIN
        echo "<script>location.href='/';</script>";
      
    }

} else {
                
    //REDIRECIONA PARA A TELA DE LOGIN
    echo "<script>location.href='/';</script>";
    
}

include_once '../../../../bd/desconecta.php';

?>

<form id="form-orcamento-whatsapp" style="display: none;" action="../../../envio-email/index.php" method="POST">
    <input type="hidden" name="tipo-envio" value="formulario-orcamento-whatsapp">
    <input type="hidden" name="orcamento" value="<?= $codigo_orcamento ?>">
    <input type="hidden" name="cliente" value="<?= $cliente['nome'].' '.$cliente['sobrenome'] ?>">
    <input type="hidden" name="email" value="<?= $cliente['email'] ?>">
    <input type="hidden" name="url-whatsapp" value="<?= 'https://wa.me/55'.preg_replace("/[^0-9]/", "", $loja['whatsapp']).'?text='.$texto_whatsapp ?>">
</form>

<?php echo "<script>document.getElementById('form-orcamento-whatsapp').submit();</script>"; ?>



