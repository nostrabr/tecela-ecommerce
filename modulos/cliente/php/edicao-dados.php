<?php

//INICIA A SESSÃO
session_start();

//RECEBE OS DADOS DO CADASTRO
$nome          = trim(strip_tags(mb_convert_case(filter_input(INPUT_POST, "nome", FILTER_SANITIZE_STRING), MB_CASE_TITLE, 'UTF-8')));
$sobrenome     = trim(strip_tags(mb_convert_case(filter_input(INPUT_POST, "sobrenome", FILTER_SANITIZE_STRING), MB_CASE_TITLE, 'UTF-8')));  
$nascimento    = trim(strip_tags(filter_input(INPUT_POST, "nascimento", FILTER_SANITIZE_STRING)));  
$telefone      = trim(strip_tags(filter_input(INPUT_POST, "telefone", FILTER_SANITIZE_STRING)));  
$celular       = trim(strip_tags(filter_input(INPUT_POST, "celular", FILTER_SANITIZE_STRING)));  
$identificador = filter_var($_SESSION['identificador']);
unset($_SESSION['RETORNO']); 

if(!empty($nome) & !empty($sobrenome) & !empty($identificador) & !empty($celular)){
  
    //FUNÇÃO QUE INVERTE A DATA
    function inverteData($data){
        $formata_data = explode("/",$data);
        return $formata_data[2]."-".$formata_data[1]."-".$formata_data[0];
    }  

    include_once '../../../bd/conecta.php';

    //VALIDA O CÓDIGO DO CLIENTE
    $busca_cliente = mysqli_query($conn, "SELECT id FROM cliente WHERE identificador = '$identificador'");

    //SE ESTIVER CERTO, PROSSEGUE
    if(mysqli_num_rows($busca_cliente) > 0){

        //ALTERA O FORMATO DA DATA
        if($nascimento != ''){
            $nascimento = inverteData($nascimento);
        }

        //ALTERA NO BANCO
        mysqli_query($conn, "UPDATE cliente SET nome = '$nome', sobrenome = '$sobrenome', nascimento = '$nascimento', telefone = '$telefone', celular = '$celular' WHERE identificador = '$identificador'");
        
        if(!mysqli_error($conn)){

            //PREENCHE A SESSION DE RETORNO COM ERRO
            $_SESSION['RETORNO'] = array(
                'ERRO'              => false,
                'STATUS'            => 'EDITADO-SUCESSO'
            );   
            
            //REDIRECIONA PARA A TELA DE DADOS DO CLIENTE
            echo "<script>location.href='../../../cliente-dados';</script>";
            

        } else {            

            //PREENCHE A SESSION DE RETORNO COM ERRO
            $_SESSION['RETORNO'] = array(
                'ERRO'              => 'ERRO-EDICAO',
                'STATUS'            => 'ERRO'
            );    
            
            //REDIRECIONA PARA A TELA DE DADOS DO CLIENTE
            echo "<script>location.href='../../../cliente-dados';</script>";

        }

    //SE ESTIVER ERRADO, VOLTA COM ERRO
    } else {        
        
        //REDIRECIONA PARA O LOGOUT
        echo "<script>location.href='../../../logout';</script>";
        
    }

    include_once '../../../bd/desconecta.php';
    
} else {    
        
    //REDIRECIONA PARA A TELA DE LOGIN
    echo "<script>location.href='/';</script>";

}
