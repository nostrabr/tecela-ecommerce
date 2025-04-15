<?php 

//PEGA OS DADOS DO USUÁRIO ATUAL NA SESSION
$nivel_usuario = filter_var($_SESSION['nivel']);

//SE FOR USUÁRIO, DESLOGA
if($nivel_usuario == 'U'){
    echo "<script>location.href='logout.php';</script>";
}

//RETORNO DO FORM
if(isset($_SESSION['RETORNO'])){
    if($_SESSION['RETORNO']['ERRO'] == 'ERRO-EMAIL-LOGIN'){
        echo "<script>mensagemAviso('erro', 'E-mail ou Usuário já cadastrado.', 3000);</script>";
    }
}

?>

<!--SECTION CONFIGURAÇÕES-->
<section id="configuracoes-usuarios">

    <div class="container-fluid">

        <!-- ROW DO TÍTULO -->
        <div class="row">
            <div class="col-8">    
                <div id="admin-titulo-pagina">Usuários do sistema - Cadastro</div>
            </div>
            <div class="col-4 text-right">
                <button type="button" class="btn btn-dark btn-top-right" onclick="javascript: window.location.href = 'configuracoes-usuarios.php';">VOLTAR</button>
            </div>
        </div>

        <!-- FORM DE CADASTRO -->
        <form action="modulos/configuracoes/php/cadastro-usuario.php" method="POST">
            <div class="row">
                <div class="col-12 col-md-6">
                    <div class="form-group">
                        <label for="nome">Nome <span class="campo-obrigatorio">*</span></label>
                        <input type="text" class="form-control text-capitalize" name="nome" id="nome" maxlength="50" value="<?php if(isset($_SESSION['RETORNO']['nome'])){ echo $_SESSION['RETORNO']['nome']; } ?>" required>
                    </div>
                </div>
                <div class="col-12 col-md-6">
                    <div class="form-group">
                        <label for="email">E-mail <span class="campo-obrigatorio">*</span></label>
                        <input type="email" class="form-control text-lowercase" name="email" id="email" maxlength="50" value="<?php if(isset($_SESSION['RETORNO']['email'])){ echo $_SESSION['RETORNO']['email']; } ?>" required>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12 col-md-4">
                    <div class="form-group">
                        <label for="usuario">Usuário <span class="campo-obrigatorio">*</span></label>
                        <input type="text" class="form-control text-lowercase" name="usuario" id="usuario" maxlength="30" value="<?php if(isset($_SESSION['RETORNO']['usuario'])){ echo $_SESSION['RETORNO']['usuario']; } ?>" required >
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="form-group">
                        <label for="senha">Senha <span class="campo-obrigatorio">*</span></label>
                        <input type="password" class="form-control" name="senha" id="senha" maxlength="30" required>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="form-group">
                        <label for="nivel">Nível <span class="campo-obrigatorio">*</span></label>
                        <select type="text" class="form-control" name="nivel" id="nivel" required>
                            <?php if($nivel_usuario == 'A'){ ?>
                                <option value="U" selected>Usuário</option>
                            <?php } else if($nivel_usuario == 'M') { ?>
                                <option value="A" <?php if(isset($_SESSION['RETORNO']['nivel'])){ if($_SESSION['RETORNO']['nivel'] == 'A'){ echo 'selected'; }} ?>>Administrador</option>
                                <option value="U" <?php if(isset($_SESSION['RETORNO']['nivel'])){ if($_SESSION['RETORNO']['nivel'] == 'U'){ echo 'selected'; }} ?>>Usuário</option>
                            <?php } else if($nivel_usuario == 'S') { ?>
                                <option value="M" <?php if(isset($_SESSION['RETORNO']['nivel'])){ if($_SESSION['RETORNO']['nivel'] == 'M'){ echo 'selected'; }} ?>>Master</option>
                                <option value="A" <?php if(isset($_SESSION['RETORNO']['nivel'])){ if($_SESSION['RETORNO']['nivel'] == 'A'){ echo 'selected'; }} ?>>Administrador</option>
                                <option value="U" <?php if(isset($_SESSION['RETORNO']['nivel'])){ if($_SESSION['RETORNO']['nivel'] == 'U'){ echo 'selected'; }} ?>>Usuário</option>
                            <?php }?>
                        </select>   
                    </div>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-12 text-center text-md-right">
                    <div class="form-group">
                        <button type="submit" class="btn btn-dark btn-bottom">CADASTRAR</button>
                    </div>
                </div>
            </div>
        </form>

    </div>

</section>

<?php /* LIMPA A SESSION DE RETORNO */ unset($_SESSION['RETORNO']); ?>