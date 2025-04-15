<!--CSS-->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.3/css/bootstrap.css">
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.21/css/dataTables.bootstrap4.min.css">
                  
<?php 

//PEGA OS DADOS DO USUÁRIO ATUAL NA SESSION
$nivel_usuario         = filter_var($_SESSION['nivel']);
$identificador_usuario = filter_var($_SESSION['identificador']);

//SE FOR MASTER OU SUPER BUSCA TODOS
if($nivel_usuario == 'M' | $nivel_usuario == 'S'){
    $usuarios = mysqli_query($conn, 'SELECT identificador, status, nome, nivel, (SELECT nome FROM usuario AS u2 WHERE u1.cadastrado_por = u2.identificador) AS por FROM usuario AS u1 WHERE nivel != "S" ORDER BY nome'); 

//SE FOR ADMINISTRADOR BUSCA SÓ OS USUÁRIOS CADASTRADOS POR ELE
} else if($nivel_usuario == 'A'){
    $usuarios = mysqli_query($conn, 'SELECT identificador, status, nome, nivel, (SELECT nome FROM usuario AS u2 WHERE u1.cadastrado_por = u2.identificador) AS por FROM usuario AS u1 WHERE ((nivel = "U" AND cadastrado_por = "'.$identificador_usuario.'") OR (nivel = "A" AND identificador = "'.$identificador_usuario.'")) ORDER BY nome'); 

//SE TENTAR ACESSAR COMO USUÁRIO, DESLOGA DO SISTEMA
} else if($nivel_usuario == 'U'){
    echo "<script>location.href='logout.php';</script>";
}

?>

<!--SECTION CONFIGURAÇÕES-->
<section id="configuracoes-usuarios">

    <div class="container-fluid">

        <!-- ROW DO TÍTULO -->
        <div class="row">
            <div class="col-7">    
                <div id="admin-titulo-pagina">Usuários do sistema</div>
            </div>
            <div class="col-5 text-right">
                <button type="button" class="btn btn-dark btn-top-right mb-1 mb-md-0" onclick="javascript: window.location.href = 'configuracoes-cadastra-usuarios.php';">NOVO USUÁRIO</button>
                <button type="button" class="btn btn-dark btn-top-right ml-0 ml-md-1" onclick="javascript: window.location.href = 'configuracoes.php';">VOLTAR</button>
            </div>
        </div>

        <!-- ROW DA TABELA -->
        <div class="row">
            <div class="col-12">   
                <table id="admin-lista" class="table table-hover">
                    <thead>
                        <tr>
                            <th scope="col">NOME</th>
                            <th scope="col" class="d-none d-md-table-cell">NÍVEL DE ACESSO</th>
                            <th scope="col" class="d-none d-md-table-cell">CADASTRADO POR</th>
                            <th scope="col" class="text-right">AÇÕES</th>
                        </tr>
                    </thead>
                    <tbody>      
                        <?php while($usuario = mysqli_fetch_array($usuarios)){ ?>
                            <tr class="cursor-pointer" id="usuario-<?= $usuario['identificador'] ?>" title="Editar" onclick="javascript: edita('<?= $usuario['identificador'] ?>');">
                                <td class="text-capitalize"><?= $usuario['nome'] ?></td>
                                <td class="d-none d-md-table-cell"><?php if($usuario['nivel'] == 'M'){ echo "Master"; } else if($usuario['nivel'] == 'A'){ echo "Administrador"; } else if($usuario['nivel'] == 'U'){ echo "Usuário"; } ?></td>
                                <td class="text-capitalize d-none d-md-table-cell"><?= $usuario['por'] ?></td>
                                <?php if($identificador_usuario != $usuario['identificador']){ ?>  
                                    <?php if($usuario['status'] == 1){ ?>  
                                        <td class="text-right" id="status-<?= $usuario['identificador'] ?>"><a class="botao-status" href="javascript: trocaStatus('<?= $usuario['identificador'] ?>',<?= $usuario['status'] ?>)" title="Desativar"><img class="status-ativado" src="<?= $loja['site'] ?>imagens/status-ativo.png" alt="Ativo"></a><span class="d-none">ligado on ativo 1</span></td>
                                    <?php } else if($usuario['status'] == 0){  ?>     
                                        <td class="text-right" id="status-<?= $usuario['identificador'] ?>"><a class="botao-status" href="javascript: trocaStatus('<?= $usuario['identificador'] ?>',<?= $usuario['status'] ?>)" title="Ativar"><img class="status-desativado" src="<?= $loja['site'] ?>imagens/status-inativo.png" alt="Inativo"></a><span class="d-none">desligado off inativo 1</span></td>
                                    <?php } ?>
                                <?php } else { ?>  
                                    <td></td>
                                <?php } ?>     
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>

</section>

<!--SCRIPTS-->
<script type="text/javascript" src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/1.10.21/js/dataTables.bootstrap4.min.js"></script>
<script type="text/javascript" src="modulos/configuracoes/js/scripts.js"></script>