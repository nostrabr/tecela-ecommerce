<!--CSS-->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.3/css/bootstrap.css">
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.21/css/dataTables.bootstrap4.min.css">
                  
<?php 

//PEGA OS DADOS DO USUÁRIO ATUAL NA SESSION
$nivel_usuario         = filter_var($_SESSION['nivel']);
$identificador_usuario = filter_var($_SESSION['identificador']);

//SE FOR USUÁRIO E TENTOU ACESSAR, DESLOGA
if($nivel_usuario == 'U'){
    echo "<script>location.href='logout.php';</script>";
}

?>

<!--SECTION LISTA DESIGN-->
<section id="configuracoes-design">

    <div class="container-fluid">

        <!-- ROW DO TÍTULO -->
        <div class="row">
            <div class="col-7">    
                <div id="admin-titulo-pagina">Design da loja</div>
            </div>
            <div class="col-5 text-right">
                <button type="button" class="btn btn-dark btn-top-right" onclick="javascript: window.location.href = 'configuracoes.php';">VOLTAR</button>
            </div>
        </div>

        <!-- ROW DA TABELA -->
        <div class="row">
            <div class="col-12">   
                <table id="admin-lista-tres" class="table table-hover">
                    <thead>
                        <tr>
                            <th scope="col">CONFIGURAÇÃO</th>
                        </tr>
                    </thead>
                    <tbody>     
                        <tr class="cursor-pointer" title="Editar" onclick="javascript: window.location.href = 'configuracoes-design-menu.php';">
                            <td class="text-capitalize">Menu <small>(Menu principal do site)</small></td>
                        </tr>   
                        <tr class="cursor-pointer" title="Editar" onclick="javascript: window.location.href = 'configuracoes-design-barra-categorias.php';">
                            <td class="text-capitalize">Barra de categorias <small>(Configurar a barra de categorias que aparece no topo da página)</small></td>
                        </tr>
                        <tr class="cursor-pointer" title="Editar" onclick="javascript: window.location.href = 'configuracoes-design-banners.php';">
                            <td class="text-capitalize">Banner principal <small>(Banner grande da página inicial)</small></td>
                        </tr> 
                        <tr class="cursor-pointer" title="Editar" onclick="javascript: window.location.href = 'configuracoes-design-banners-secundarios.php';">
                            <td class="text-capitalize">Banners secundários <small>(Banners pequenos que vem logo abaixo do banner grande da página inicial)</small></td>
                        </tr>
                        <tr class="cursor-pointer" title="Editar" onclick="javascript: window.location.href = 'configuracoes-design-banners-produto.php';">
                            <td class="text-capitalize">Banners produto <small>(Banner aleatório na página de produto)</small></td>
                        </tr>
                        <tr class="cursor-pointer" title="Editar" onclick="javascript: window.location.href = 'configuracoes-design-informacoes-adicionais.php';">
                            <td class="text-capitalize">Informações adicionais <small>(Informações adicionais na página inicial)</small></td>
                        </tr> 
                        <tr class="cursor-pointer" title="Editar" onclick="javascript: window.location.href = 'configuracoes-design-navegue-categorias.php';">
                            <td class="text-capitalize">Sessão de categorias <small>(Sessão 'Navegue pelas categorias' na tela inicial da loja)</small></td>
                        </tr> 
                        <tr class="cursor-pointer" title="Editar" onclick="javascript: window.location.href = 'configuracoes-design-contato.php';">
                            <td class="text-capitalize">Contato <small>(Opções para as informações de contato da loja)</small></td>
                        </tr>  
                        <!--
                        <tr class="cursor-pointer" title="Editar" onclick="javascript: window.location.href = 'configuracoes-design-produto.php';">
                            <td class="text-capitalize">Produto <small>(Opções para os produtos da loja)</small></td>
                        </tr>  
                        -->
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