<?php 

//PEGA OS DADOS
$identificador_usuario         = filter_input(INPUT_GET,"id",FILTER_SANITIZE_STRING);
$nivel_usuario                 = filter_var($_SESSION['nivel']);
$identificador_usuario_session = filter_var($_SESSION['identificador']);

//SE TENTAR ACESSAR COMO USUÁRIO, DESLOGA DO SISTEMA
if($nivel_usuario == 'U'){
    echo "<script>location.href='logout.php';</script>";
}

//RETORNO DO FORM
if(isset($_SESSION['RETORNO'])){

    if($_SESSION['RETORNO']['ERRO'] == 'ERRO-EMAIL-LOGIN'){
        echo "<script>mensagemAviso('erro', 'E-mail ou Usuário já cadastrado.', 3000);</script>";
    }

    $nome    = $_SESSION['RETORNO']['nome'];
    $email   = $_SESSION['RETORNO']['email'];
    $login   = $_SESSION['RETORNO']['usuario'];
    $nivel   = $_SESSION['RETORNO']['nivel'];

} else {

    //BUSCA USUÁRIO
    $busca_usuario = mysqli_query($conn, 'SELECT nome, email, login, nivel FROM usuario WHERE identificador = "'.$identificador_usuario.'"'); 

    //VERIFICA SE ENCONTROU O USUÁRIO
    if(mysqli_num_rows($busca_usuario) == 0){
        echo "<script>location.href='configuracoes-usuarios.php';</script>";
    } else {

        $usuario = mysqli_fetch_array($busca_usuario);
        $nome    = $usuario['nome'];
        $email   = $usuario['email'];
        $login   = $usuario['login'];
        $nivel   = $usuario['nivel'];

    }
    
}

?>

<!--SECTION CONFIGURAÇÕES-->
<section id="configuracoes-usuarios">

    <div class="container-fluid">

        <!-- ROW DO TÍTULO -->
        <div class="row">
            <div class="col-8">    
                <div id="admin-titulo-pagina">Usuários do sistema - Edição</div>
            </div>
            <div class="col-4 text-right">
                <button type="button" class="btn btn-dark btn-top-right" onclick="javascript: window.location.href = 'configuracoes-usuarios.php';">VOLTAR</button>
            </div>
        </div>

        <!-- FORM DE EDIÇÃO -->
        <form action="modulos/configuracoes/php/edicao-usuario.php" method="POST">
            <input type="hidden" name="identificador" value="<?= $identificador_usuario ?>">
            <div class="row">
                <div class="col-12 col-md-6">
                    <div class="form-group">
                        <label for="nome">Nome <span class="campo-obrigatorio">*</span></label>
                        <input type="text" class="form-control text-capitalize" name="nome" id="nome" maxlength="50" value="<?= $nome ?>" required>
                    </div>
                </div>
                <div class="col-12 col-md-6">
                    <div class="form-group">
                        <label for="email">E-mail <span class="campo-obrigatorio">*</span></label>
                        <input type="email" class="form-control text-lowercase" name="email" id="email" maxlength="50" value="<?= $email ?>" required>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12 col-md-4">
                    <div class="form-group">
                        <label for="usuario">Usuário <span class="campo-obrigatorio">*</span></label>
                        <input type="text" class="form-control text-lowercase" name="usuario" id="usuario" value="<?= $login ?>" maxlength="30" required >
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="form-group">
                        <label for="senha">Senha</label>
                        <input type="password" class="form-control" name="senha" id="senha" maxlength="30">
                        <small>Preencha caso queira alterar</small>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="form-group">
                        <label for="nivel">Nível <span class="campo-obrigatorio">*</span></label>
                        <select type="text" class="form-control" name="nivel" id="nivel" required>                        
                            <?php if($identificador_usuario != $identificador_usuario_session){ ?>
                                <?php if($nivel_usuario == 'A'){ ?>
                                    <option value="U" selected>Usuário</option>
                                <?php } else if($nivel_usuario == 'M') { ?>
                                    <option value="A" <?php if($nivel == 'A'){ echo 'selected'; } ?>>Administrador</option>
                                    <option value="U" <?php if($nivel == 'U'){ echo 'selected'; } ?>>Usuário</option>
                                <?php } else if($nivel_usuario == 'S') { ?>
                                    <option value="M" <?php if($nivel == 'M'){ echo 'selected'; } ?>>Master</option>
                                    <option value="A" <?php if($nivel == 'A'){ echo 'selected'; } ?>>Administrador</option>
                                    <option value="U" <?php if($nivel == 'U'){ echo 'selected'; } ?>>Usuário</option>
                                <?php } ?>
                            <?php } else { ?>
                                    <option value="<?= $nivel ?>" selected><?php if($nivel == 'A'){ echo 'Administrador'; } else if($nivel == 'M'){ echo 'Master'; } ?></option>
                            <?php } ?>
                        </select>   
                    </div>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-12 text-center text-md-right">
                    <div class="form-group">
                        <button type="submit" class="btn btn-dark btn-bottom">SALVAR</button>
                    </div>
                </div>
            </div>
        </form>

    </div>

</section>

<?php /* LIMPA A SESSION DE RETORNO */ unset($_SESSION['RETORNO']); ?>